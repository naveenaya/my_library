<?php
session_start();
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard • My Library</title>
  <link rel="stylesheet" href="file.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h2>Dashboard</h2>
        <div class="small">Welcome, <?=htmlspecialchars($username)?> — Role: <?=htmlspecialchars($role)?></div>
      </div>
      <div>
        <a href="viewpost.php">View Books</a> |
        <?php if ($role === 'admin'): ?><a href="addpost.php">Add Book</a> |<?php endif; ?>
        <a href="logout.php">Logout</a>
      </div>
    </div>

    <div class="card">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="viewpost.php">View/Search Books</a></li>
        <?php if ($role === 'admin'): ?>
          <li><a href="addpost.php">Add New Book</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>
</html>