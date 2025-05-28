<?php
require_once '../config.php';
require_once '../includes/header.php';

$sql = "SELECT * FROM authors ORDER BY author_name ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mt-4 mb-3">Author Management</h2>
<a href="add_author.php" class="btn btn-success mb-3">Add New Author</a>

<?php if (mysqli_num_rows($result) > 0): ?>
  <table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Author Name</th>
        <th>Country of Origin</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['author_id']; ?></td>
          <td><?php echo htmlspecialchars($row['author_name']); ?></td>
          <td><?php echo htmlspecialchars($row['country_of_origin']); ?></td>
          <td>
            <a href="edit_author.php?id=<?php echo $row['author_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_author.php?id=<?php echo $row['author_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this author?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <div class="alert alert-info">No author data found.</div>
<?php endif; ?>

<?php
mysqli_free_result($result);
require_once '../includes/footer.php';
?>