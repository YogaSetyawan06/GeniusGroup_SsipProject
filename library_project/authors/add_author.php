<?php
require_once '../config.php';

$author_name = '';
$country_of_origin = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $author_name = sanitize_input($conn, $_POST['author_name']);
  $country_of_origin = sanitize_input($conn, $_POST['country_of_origin']);


  if (empty($author_name)) {
    $errors[] = "Author name cannot be empty.";
  }

  if (empty($errors)) {
    $sql = "INSERT INTO authors (author_name, country_of_origin) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_country);
      $param_name = $author_name;
      $param_country = $country_of_origin;

      if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Author added successfully.";
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

<h2 class="mt-4 mb-3">Add New Author</h2>

<?php if (!empty($errors) && $_SERVER["REQUEST_METHOD"] != "POST"):
?>
  <div class="alert alert-danger">
    <?php foreach ($errors as $error): ?>
      <p><?php echo $error; ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <div class="form-group">
    <label for="author_name">Author Name <span class="text-danger">*</span></label>
    <input type="text" name="author_name" id="author_name" class="form-control" value="<?php echo htmlspecialchars($author_name); ?>" required>
  </div>
  <div class="form-group">
    <label for="country_of_origin">Country of Origin</label>
    <input type="text" name="country_of_origin" id="country_of_origin" class="form-control" value="<?php echo htmlspecialchars($country_of_origin); ?>">
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../includes/footer.php'; ?>