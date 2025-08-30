<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); exit;
}
require 'config.php';
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard • My Library</title><link rel="stylesheet" href="file.css"></head>
<body>
<div class="container">
  <div class="header">
    <h2>Dashboard</h2>
    <div>
      <span class="small">Hello, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
      &nbsp;|&nbsp;<a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <ul>
      <li><a href="index.php">Manage Books</a></li>
      <li><a href="viewpost.php">View Posts</a></li>
      <?php if ($role === 'admin'): ?>
        <li><a href="add_post.php">Create Post</a></li>
        <li><a href="add_book.php">Add Book</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
</body>
</html>