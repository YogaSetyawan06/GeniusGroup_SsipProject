<?php
require_once '../config.php';

$book_title = $author_id = $publisher_id = $publication_year = $isbn = $page_count = $synopsis = '';
$errors = [];


$authors_list = mysqli_query($conn, "SELECT author_id, author_name FROM authors ORDER BY author_name ASC");
$publishers_list = mysqli_query($conn, "SELECT publisher_id, publisher_name FROM publishers ORDER BY publisher_name ASC");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $book_title = sanitize_input($conn, $_POST['book_title']);
  $author_id = sanitize_input($conn, $_POST['author_id']);
  $publisher_id = sanitize_input($conn, $_POST['publisher_id']);
  $publication_year = sanitize_input($conn, $_POST['publication_year']);
  $isbn = sanitize_input($conn, $_POST['isbn']);
  $page_count = sanitize_input($conn, $_POST['page_count']);
  $synopsis = sanitize_input($conn, $_POST['synopsis']);

  if (empty($book_title)) $errors[] = "Book title cannot be empty.";
  if (empty($author_id)) $errors[] = "Author must be selected.";
  if (empty($publisher_id)) $errors[] = "Publisher must be selected.";
  if (!empty($publication_year) && !is_numeric($publication_year)) $errors[] = "Publication year must be a number.";
  if (!empty($page_count) && !is_numeric($page_count)) $errors[] = "Page count must be a number.";


  if (!empty($isbn)) {
    $sql_check_isbn = "SELECT book_id FROM books WHERE isbn = ? AND book_id != 0";
    if ($stmt_check_isbn = mysqli_prepare($conn, $sql_check_isbn)) {
      mysqli_stmt_bind_param($stmt_check_isbn, "s", $isbn);
      mysqli_stmt_execute($stmt_check_isbn);
      mysqli_stmt_store_result($stmt_check_isbn);
      if (mysqli_stmt_num_rows($stmt_check_isbn) > 0) {
        $errors[] = "ISBN is already registered for another book.";
      }
      mysqli_stmt_close($stmt_check_isbn);
    }
  }


  if (empty($errors)) {
    $sql = "INSERT INTO books (book_title, author_id, publisher_id, publication_year, isbn, page_count, synopsis) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param(
        $stmt,
        "siisiss",
        $book_title,
        $author_id,
        $publisher_id,
        $publication_year,
        $isbn,
        $page_count,
        $synopsis
      );


      $param_publication_year = !empty($publication_year) ? $publication_year : null;
      $param_page_count = !empty($page_count) ? $page_count : null;
      $param_isbn = !empty($isbn) ? $isbn : null;
      $param_synopsis = !empty($synopsis) ? $synopsis : null;


      $publication_year_for_bind = $param_publication_year;
      $page_count_for_bind = $param_page_count;
      $isbn_for_bind = $param_isbn;
      $synopsis_for_bind = $param_synopsis;


      mysqli_stmt_bind_param(
        $stmt,
        "siisiss",
        $book_title,
        $author_id,
        $publisher_id,
        $publication_year_for_bind,
        $isbn_for_bind,
        $page_count_for_bind,
        $synopsis_for_bind
      );


      if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Book added successfully.";
        $_SESSION['message_type'] = "success";
        header("location: index.php");
        exit();
      } else {
        $_SESSION['message'] = "An error occurred: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
      }
      mysqli_stmt_close($stmt);
    } else {
      $_SESSION['message'] = "Error preparing statement: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
  } else {
    $_SESSION['message'] = implode("<br>", $errors);
    $_SESSION['message_type'] = "danger";
  }
}
require_once '../includes/header.php';
?>

<h2 class="mt-4 mb-3">Add New Book</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <div class="form-group">
    <label for="book_title">Book Title <span class="text-danger">*</span></label>
    <input type="text" name="book_title" id="book_title" class="form-control" value="<?php echo htmlspecialchars($book_title); ?>" required>
  </div>

  <div class="form-group">
    <label for="author_id">Author <span class="text-danger">*</span></label>
    <select name="author_id" id="author_id" class="form-control" required>
      <option value="">-- Select Author --</option>
      <?php while ($author = mysqli_fetch_assoc($authors_list)): ?>
        <option value="<?php echo $author['author_id']; ?>" <?php echo ($author_id == $author['author_id']) ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($author['author_name']); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="publisher_id">Publisher <span class="text-danger">*</span></label>
    <select name="publisher_id" id="publisher_id" class="form-control" required>
      <option value="">-- Select Publisher --</option>
      <?php while ($publisher = mysqli_fetch_assoc($publishers_list)): ?>
        <option value="<?php echo $publisher['publisher_id']; ?>" <?php echo ($publisher_id == $publisher['publisher_id']) ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($publisher['publisher_name']); ?>
        </option>
      <?php endwhile; ?>
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

  <button type="submit" class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
if ($authors_list) mysqli_data_seek($authors_list, 0);
if ($publishers_list) mysqli_data_seek($publishers_list, 0);
require_once '../includes/footer.php';
?>