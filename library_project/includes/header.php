<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ArSaWan Library System</title>

  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>assets/css/style.css">


<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <a class="navbar-brand" href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>index.php">ArSaWan Library</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>books/">Book Management</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>authors/">Author Management</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>publishers/">Publisher Management</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <?php
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
      ?>
    <?php endif; ?>