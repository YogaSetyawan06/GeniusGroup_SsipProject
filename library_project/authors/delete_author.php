<?php
require_once '../config.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
  $author_id = sanitize_input($conn, $_GET['id']);


  $sql_check = "SELECT COUNT(*) as book_count FROM books WHERE author_id = ?";
  if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
    mysqli_stmt_bind_param($stmt_check, "i", $author_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $book_count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($book_count > 0) {
      $_SESSION['message'] = "Cannot delete author. There are still books associated with this author.";
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



  $sql_delete = "DELETE FROM authors WHERE author_id = ?";
  if ($stmt_delete = mysqli_prepare($conn, $sql_delete)) {
    mysqli_stmt_bind_param($stmt_delete, "i", $author_id_param);
    $author_id_param = $author_id;

    if (mysqli_stmt_execute($stmt_delete)) {
      $_SESSION['message'] = "Author deleted successfully.";
      $_SESSION['message_type'] = "success";
    } else {
      $_SESSION['message'] = "Failed to delete author. Error: " . mysqli_error($conn);
      $_SESSION['message_type'] = "danger";
    }
    mysqli_stmt_close($stmt_delete);
  } else {
    $_SESSION['message'] = "Failed to prepare delete statement. Error: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
  }
} else {
  $_SESSION['message'] = "Invalid Author ID for deletion.";
  $_SESSION['message_type'] = "warning";
}

header("location: index.php");
exit();
