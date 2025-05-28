<?php
require_once '../config.php';
require_once '../includes/header.php';

$sql = "SELECT b.book_id, b.book_title, ba.author_name, p.publisher_name, b.publication_year, b.isbn
        FROM books b
        JOIN authors ba ON b.author_id = ba.author_id
        JOIN publishers p ON b.publisher_id = p.publisher_id
        ORDER BY b.book_title ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mt-4 mb-3">Book Management</h2>
<a href="add_book.php" class="btn btn-success mb-3">Add New Book</a>

<?php if (mysqli_num_rows($result) > 0): ?>
  <table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Book Title</th>
        <th>Author</th>
        <th>Publisher</th>
        <th>Year</th>
        <th>ISBN</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['book_id']; ?></td>
          <td><?php echo htmlspecialchars($row['book_title']); ?></td>
          <td><?php echo htmlspecialchars($row['author_name']); ?></td>
          <td><?php echo htmlspecialchars($row['publisher_name']); ?></td>
          <td><?php echo $row['publication_year']; ?></td>
          <td><?php echo htmlspecialchars($row['isbn']); ?></td>
          <td>
            <a href="edit_book.php?id=<?php echo $row['book_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_book.php?id=<?php echo $row['book_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <div class="alert alert-info">No book data found.</div>
<?php endif; ?>

<?php
mysqli_free_result($result);
require_once '../includes/footer.php';
?>