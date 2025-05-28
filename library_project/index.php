<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="jumbotron">
  <h1 class="display-4">Welcome to Arda Sakhi Setyawan Library!</h1>
  <p class="lead">A library system to manage data for books, authors, and publishers.</p>
  <hr class="my-4">
  <p>Use the navigation above to get started.</p>
  <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>books/" role="button">View Book List</a>
</div>

<?php require_once 'includes/footer.php'; ?>