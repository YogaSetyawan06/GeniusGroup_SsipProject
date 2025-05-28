<?php
require_once '../config.php';

$author_id = '';
$author_name = '';
$country_of_origin = '';
$errors = [];


if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $author_id = sanitize_input($conn, $_GET['id']);

  $sql_select = "SELECT author_name, country_of_origin FROM authors WHERE author_id = ?";
  if ($stmt_select = mysqli_prepare($conn, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $author_id);
    if (mysqli_stmt_execute($stmt_select)) {
      mysqli_stmt_store_result($stmt_select);
      if (mysqli_stmt_num_rows($stmt_select) == 1) {
        mysqli_stmt_bind_result($stmt_select, $db_author_name, $db_country_of_origin);
        mysqli_stmt_fetch($stmt_select);
        $author_name = $db_author_name;
        $country_of_origin = $db_country_of_origin;
      } else {
        $_SESSION['message'] = "Author not found.";
        $_SESSION['message_type'] = "warning";
        header("location: index.php");
        exit();
      }
    } else {
      $_SESSION['message'] = "Error fetching data: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt_select);
  }
} else if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $_SESSION['message'] = "Invalid Author ID.";
  $_SESSION['message_type'] = "danger";
  header("location: index.php");
  exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $author_id = sanitize_input($conn, $_POST['author_id']);
  $author_name = sanitize_input($conn, $_POST['author_name']);
  $country_of_origin = sanitize_input($conn, $_POST['country_of_origin']);

  if (empty($author_name)) {
    $errors[] = "Author name cannot be empty.";
  }

  if (empty($errors)) {
    $sql_update = "UPDATE authors SET author_name = ?, country_of_origin = ? WHERE author_id = ?";
    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
      mysqli_stmt_bind_param($stmt_update, "ssi", $param_name, $param_country, $param_id);
      $param_name = $author_name;
      $param_country = $country_of_origin;
      $param_id = $author_id;

      if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['message'] = "Author data updated successfully.";
        $_SESSION['message_type'] = "success";
        header("location: index.php");
        exit();
      } else {
        $_SESSION['message'] = "Error during update: " . mysqli_error($conn);
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

<h2 class="mt-4 mb-3">Edit Author</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <input type="hidden" name="author_id" value="<?php echo $author_id; ?>">
  <div class="form-group">
    <label for="author_name">Author Name <span class="text-danger">*</span></label>
    <input type="text" name="author_name" id="author_name" class="form-control" value="<?php echo htmlspecialchars($author_name); ?>" required>
  </div>
  <div class="form-group">
    <label for="country_of_origin">Country of Origin</label>
    <input type="text" name="country_of_origin" id="country_of_origin" class="form-control" value="<?php echo htmlspecialchars($country_of_origin); ?>">
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../includes/footer.php'; ?>