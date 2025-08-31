<?php
session_start();
require 'config.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name = trim($_POST['book_name'] ?? '');
    $author_name = trim($_POST['author_name'] ?? '');
    $published_year = (int)($_POST['published_year'] ?? 0);

    if ($book_name === '' || $author_name === '' || $published_year <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        $stmt = $conn->prepare("INSERT INTO books (book_name, author_name, published_year) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $book_name, $author_name, $published_year);
        if ($stmt->execute()) {
            header("Location: viewpost.php");
            exit();
        } else {
            $error = "DB error: " . $stmt->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Book</title><link rel="stylesheet" href="file.css"></head>
<body>
  <div class="container">
    <div class="card">
      <h3>Add New Book</h3>
      <?php if ($error): ?><p class="notice"><?=htmlspecialchars($error)?></p><?php endif; ?>
      <form method="post">
        <div class="form-row"><label>Book Name</label><input type="text" name="book_name" required></div>
        <div class="form-row"><label>Author Name</label><input type="text" name="author_name" required></div>
        <div class="form-row"><label>Published Year</label><input type="number" name="published_year" required></div>
        <button class="btn" type="submit">Save</button>
        <a href="viewpost.php" style="margin-left:12px;">Cancel</a>
      </form>
    </div>
  </div>
</body>
</html>