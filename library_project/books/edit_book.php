<?php
require_once '../config.php';

$book_id = '';
$book_title = $author_id_selected = $publisher_id_selected = $publication_year = $isbn = $page_count = $synopsis = '';
$errors = [];


$authors_list_query = mysqli_query($conn, "SELECT author_id, author_name FROM authors ORDER BY author_name ASC");
$publishers_list_query = mysqli_query($conn, "SELECT publisher_id, publisher_name FROM publishers ORDER BY publisher_name ASC");


if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $book_id = sanitize_input($conn, $_GET['id']);
  $sql_select_book = "SELECT * FROM books WHERE book_id = ?";
  if ($stmt_select_book = mysqli_prepare($conn, $sql_select_book)) {
    mysqli_stmt_bind_param($stmt_select_book, "i", $book_id);
    if (mysqli_stmt_execute($stmt_select_book)) {
      $result_book = mysqli_stmt_get_result($stmt_select_book);
      if (mysqli_num_rows($result_book) == 1) {
        $book = mysqli_fetch_assoc($result_book);
        $book_title = $book['book_title'];
        $author_id_selected = $book['author_id'];
        $publisher_id_selected = $book['publisher_id'];
        $publication_year = $book['publication_year'];
        $isbn = $book['isbn'];
        $page_count = $book['page_count'];
        $synopsis = $book['synopsis'];
      } else {
        $_SESSION['message'] = "Book not found.";
        $_SESSION['message_type'] = "warning";
        header("location: index.php");
        exit();
      }
    } else {
      $_SESSION['message'] = "Error fetching book data: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
      header("location: index.php");
      exit();
    }
    mysqli_stmt_close($stmt_select_book);
  }
} else if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $_SESSION['message'] = "Invalid Book ID.";
  $_SESSION['message_type'] = "danger";
  header("location: index.php");
  exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $book_id = sanitize_input($conn, $_POST['book_id']);
  $book_title = sanitize_input($conn, $_POST['book_title']);
  $author_id_selected = sanitize_input($conn, $_POST['author_id']);
  $publisher_id_selected = sanitize_input($conn, $_POST['publisher_id']);
  $publication_year = sanitize_input($conn, $_POST['publication_year']);
  $isbn = sanitize_input($conn, $_POST['isbn']);
  $page_count = sanitize_input($conn, $_POST['page_count']);
  $synopsis = sanitize_input($conn, $_POST['synopsis']);

  if (empty($book_title)) $errors[] = "Book title cannot be empty.";
  if (empty($author_id_selected)) $errors[] = "Author must be selected.";
  if (empty($publisher_id_selected)) $errors[] = "Publisher must be selected.";
  if (!empty($publication_year) && !is_numeric($publication_year)) $errors[] = "Publication year must be a number.";
  if (!empty($page_count) && !is_numeric($page_count)) $errors[] = "Page count must be a number.";


  if (!empty($isbn)) {
    $sql_check_isbn = "SELECT book_id FROM books WHERE isbn = ? AND book_id != ?";
    if ($stmt_check_isbn = mysqli_prepare($conn, $sql_check_isbn)) {
      mysqli_stmt_bind_param($stmt_check_isbn, "si", $isbn, $book_id);
      mysqli_stmt_execute($stmt_check_isbn);
      mysqli_stmt_store_result($stmt_check_isbn);
      if (mysqli_stmt_num_rows($stmt_check_isbn) > 0) {
        $errors[] = "ISBN is already registered for another book.";
      }
      mysqli_stmt_close($stmt_check_isbn);
    }
  }

  if (empty($errors)) {
    $sql_update = "UPDATE books SET book_title = ?, author_id = ?, publisher_id = ?, 
                       publication_year = ?, isbn = ?, page_count = ?, synopsis = ? 
                       WHERE book_id = ?";

    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
      $param_publication_year = !empty($publication_year) ? $publication_year : null;
      $param_page_count = !empty($page_count) ? $page_count : null;
      $param_isbn = !empty($isbn) ? $isbn : null;
      $param_synopsis = !empty($synopsis) ? $synopsis : null;

      mysqli_stmt_bind_param(
        $stmt_update,
        "siisissi",
        $book_title,
        $author_id_selected,
        $publisher_id_selected,
        $param_publication_year,
        $param_isbn,
        $param_page_count,
        $param_synopsis,
        $book_id
      );

      if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['message'] = "Book updated successfully.";
        $_SESSION['message_type'] = "success";
        header("location: index.php");
        exit();
      } else {
        $_SESSION['message'] = "Error updating book: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
      }
      mysqli_stmt_close($stmt_update);
    } else {
      $_SESSION['message'] = "Error preparing update statement: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
  } else {
    $_SESSION['message'] = implode("<br>", $errors);
    $_SESSION['message_type'] = "danger";
  }
}
require_once '../includes/header.php';
?>

<h2 class="mt-4 mb-3">Edit Book</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $book_id); ?>" method="post">
  <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">

  <div class="form-group">
    <label for="book_title">Book Title <span class="text-danger">*</span></label>
    <input type="text" name="book_title" id="book_title" class="form-control" value="<?php echo htmlspecialchars($book_title); ?>" required>
  </div>

  <div class="form-group">
    <label for="author_id">Author <span class="text-danger">*</span></label>
    <select name="author_id" id="author_id" class="form-control" required>
      <option value="">-- Select Author --</option>
      <?php
      if ($authors_list_query && mysqli_num_rows($authors_list_query) > 0) {
        mysqli_data_seek($authors_list_query, 0);
        while ($author = mysqli_fetch_assoc($authors_list_query)): ?>
          <option value="<?php echo $author['author_id']; ?>" <?php echo ($author_id_selected == $author['author_id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($author['author_name']); ?>
          </option>
      <?php endwhile;
      } ?>
    </select>
  </div>

  <div class="form-group">
    <label for="publisher_id">Publisher <span class="text-danger">*</span></label>
    <select name="publisher_id" id="publisher_id" class="form-control" required>
      <option value="">-- Select Publisher --</option>
      <?php
      if ($publishers_list_query && mysqli_num_rows($publishers_list_query) > 0) {
        mysqli_data_seek($publishers_list_query, 0);
        while ($publisher = mysqli_fetch_assoc($publishers_list_query)): ?>
          <option value="<?php echo $publisher['publisher_id']; ?>" <?php echo ($publisher_id_selected == $publisher['publisher_id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($publisher['publisher_name']); ?>
          </option>
      <?php endwhile;
      } ?>
    </select>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="publication_year">Publication Year</label>
      <input type="number" name="publication_year" id="publication_year" class="form-control" placeholder="YYYY" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($publication_year); ?>">
    </div>
    <div class="form-group col-md-6">
      <label for="isbn">ISBN</label>
      <input type="text" name="isbn" id="isbn" class="form-control" value="<?php echo htmlspecialchars($isbn); ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="page_count">Page Count</label>
    <input type="number" name="page_count" id="page_count" class="form-control" min="1" value="<?php echo htmlspecialchars($page_count); ?>">
  </div>

  <div class="form-group">
    <label for="synopsis">Synopsis</label>
    <textarea name="synopsis" id="synopsis" class="form-control" rows="4"><?php echo htmlspecialchars($synopsis); ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Update Book</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../includes/footer.php';
?>