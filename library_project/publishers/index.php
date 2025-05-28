<?php
require_once '../config.php';
require_once '../includes/header.php';

$sql = "SELECT * FROM publishers ORDER BY publisher_name ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mt-4 mb-3">Publisher Management</h2>
<a href="add_publisher.php" class="btn btn-success mb-3">Add New Publisher</a>

<?php if (mysqli_num_rows($result) > 0): ?>
  <table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Publisher Name</th>
        <th>Address</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['publisher_id']; ?></td>
          <td><?php echo htmlspecialchars($row['publisher_name']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($row['publisher_address']));
              ?></td>
          <td>
            <a href="edit_publisher.php?id=<?php echo $row['publisher_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_publisher.php?id=<?php echo $row['publisher_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this publisher?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <div class="alert alert-info">No publisher data found.</div>
<?php endif; ?>

<?php
mysqli_free_result($result);
require_once '../includes/footer.php';
?>