<?php
require_once '../config.php';

$publisher_id = '';
$publisher_name = '';
$publisher_address = '';
$errors = [];


if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $publisher_id = sanitize_input($conn, $_GET['id']);

  $sql_select = "SELECT publisher_name, publisher_address FROM publishers WHERE publisher_id = ?";
  if ($stmt_select = mysqli_prepare($conn, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $publisher_id);
    if (mysqli_stmt_execute($stmt_select)) {
      mysqli_stmt_store_result($stmt_select);
      if (mysqli_stmt_num_rows($stmt_select) == 1) {
        mysqli_stmt_bind_result($stmt_select, $db_publisher_name, $db_publisher_address);
        mysqli_stmt_fetch($stmt_select);
        $publisher_name = $db_publisher_name;
        $publisher_address = $db_publisher_address;
      } else {
        $_SESSION['message'] = "Publisher not found.";
        $_SESSION['message_type'] = "warning";
        header("location: index.php");
        exit();
      }
    } else {
      $_SESSION['message'] = "Error fetching data: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
      header("location: index.php");
      exit();
    }
    mysqli_stmt_close($stmt_select);
  }
} else if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $_SESSION['message'] = "Invalid Publisher ID.";
  $_SESSION['message_type'] = "danger";
  header("location: index.php");
  exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $publisher_id = sanitize_input($conn, $_POST['publisher_id']);
  $publisher_name = sanitize_input($conn, $_POST['publisher_name']);
  $publisher_address = sanitize_input($conn, $_POST['publisher_address']);

  if (empty($publisher_name)) {
    $errors[] = "Publisher name cannot be empty.";
  }

  if (empty($errors)) {
    $sql_update = "UPDATE publishers SET publisher_name = ?, publisher_address = ? WHERE publisher_id = ?";
    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
      mysqli_stmt_bind_param($stmt_update, "ssi", $param_name, $param_address, $param_id);
      $param_name = $publisher_name;
      $param_address = $publisher_address;
      $param_id = $publisher_id;

      if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['message'] = "Publisher data updated successfully.";
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

<h2 class="mt-4 mb-3">Edit Publisher</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <input type="hidden" name="publisher_id" value="<?php echo $publisher_id; ?>">
  <div class="form-group">
    <label for="publisher_name">Publisher Name <span class="text-danger">*</span></label>
    <input type="text" name="publisher_name" id="publisher_name" class="form-control" value="<?php echo htmlspecialchars($publisher_name); ?>" required>
  </div>
  <div class="form-group">
    <label for="publisher_address">Publisher Address</label>
    <textarea name="publisher_address" id="publisher_address" class="form-control" rows="3"><?php echo htmlspecialchars($publisher_address); ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../includes/footer.php'; ?>