<?php
require_once '../config.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $book_id = sanitize_input($conn, $_GET['id']);

  $sql = "DELETE FROM books WHERE book_id = ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $book_id_param);
    $book_id_param = $book_id;

    if (mysqli_stmt_execute($stmt)) {
      $_SESSION['message'] = "Book deleted successfully.";
      $_SESSION['message_type'] = "success";
    } else {
      $_SESSION['message'] = "Failed to delete book. Error: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
  } else {
    $_SESSION['message'] = "Failed to prepare delete statement. Error: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
  }
} else {
  $_SESSION['message'] = "Invalid Book ID for deletion.";
  $_SESSION['message_type'] = "warning";
}

header("location: index.php");
exit();
