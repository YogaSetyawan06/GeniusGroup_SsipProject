<?php
require_once '../config.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $publisher_id = sanitize_input($conn, $_GET['id']);


  $sql_check = "SELECT COUNT(*) as book_count FROM books WHERE publisher_id = ?";
  if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
    mysqli_stmt_bind_param($stmt_check, "i", $publisher_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $book_count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($book_count > 0) {
      $_SESSION['message'] = "Cannot delete publisher. There are still books associated with this publisher.";
      $_SESSION['message_type'] = "danger";
      header("location: index.php");
      exit();
    }
  } else {
    $_SESSION['message'] = "Error checking book relations: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
    header("location: index.php");
    exit();
  }


  $sql_delete = "DELETE FROM publishers WHERE publisher_id = ?";
  if ($stmt_delete = mysqli_prepare($conn, $sql_delete)) {
    mysqli_stmt_bind_param($stmt_delete, "i", $publisher_id_param);
    $publisher_id_param = $publisher_id;

    if (mysqli_stmt_execute($stmt_delete)) {
      $_SESSION['message'] = "Publisher deleted successfully.";
      $_SESSION['message_type'] = "success";
    } else {
      $_SESSION['message'] = "Failed to delete publisher. Error: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt_delete);
  } else {
    $_SESSION['message'] = "Failed to prepare delete statement. Error: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
  }
} else {
  $_SESSION['message'] = "Invalid Publisher ID for deletion.";
  $_SESSION['message_type'] = "warning";
}

header("location: index.php");
exit();
