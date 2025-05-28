<?php
require_once '../config.php';

$publisher_name = '';
$publisher_address = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $publisher_name = sanitize_input($conn, $_POST['publisher_name']);
  $publisher_address = sanitize_input($conn, $_POST['publisher_address']);


  if (empty($publisher_name)) {
    $errors[] = "Publisher name cannot be empty.";
  }

  if (empty($errors)) {
    $sql = "INSERT INTO publishers (publisher_name, publisher_address) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_address);
      $param_name = $publisher_name;
      $param_address = $publisher_address;

      if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Publisher added successfully.";
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

<h2 class="mt-4 mb-3">Add New Publisher</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <div class="form-group">
    <label for="publisher_name">Publisher Name <span class="text-danger">*</span></label>
    <input type="text" name="publisher_name" id="publisher_name" class="form-control" value="<?php echo htmlspecialchars($publisher_name); ?>" required>
  </div>
  <div class="form-group">
    <label for="publisher_address">Publisher Address</label>
    <textarea name="publisher_address" id="publisher_address" class="form-control" rows="3"><?php echo htmlspecialchars($publisher_address); ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../includes/footer.php'; ?>