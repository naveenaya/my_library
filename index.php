<?php
session_start();
if (!isset($_SESSION['username'])) { header('Location: login.php'); exit; }
require 'config.php';

$res = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Books • My Library</title><link rel="stylesheet" href="file.css"></head>
<body>
<div class="container">
  <div class="card">
    <h2>Books</h2>
    <p><a href="dashboard.php">← Dashboard</a> <?php if ($_SESSION['role'] === 'admin') echo '| <a href="add_book.php">Add Book</a>'; ?></p>

    <table>
      <thead><tr><th>#</th><th>Title</th><th>Author</th><th>Year</th><th>Category</th><?php if ($_SESSION['role'] === 'admin') echo '<th>Action</th>'; ?></tr></thead>
      <tbody>
      <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <td><?= htmlspecialchars($r['author']) ?></td>
          <td><?= (int)$r['year'] ?></td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <td>
              <a href="edit_book.php?id=<?= (int)$r['id'] ?>">Edit</a> |
              <a href="delete_book.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Delete book?')">Delete</a>
            </td>
          <?php endif; ?>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>