<?php
session_start();
require 'config.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: viewpost.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
if (!$book) { header('Location: viewpost.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name = trim($_POST['book_name'] ?? '');
    $author_name = trim($_POST['author_name'] ?? '');
    $published_year = (int)($_POST['published_year'] ?? 0);

    if ($book_name === '' || $author_name === '' || $published_year <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        $u = $conn->prepare("UPDATE books SET book_name=?, author_name=?, published_year=? WHERE id=?");
        $u->bind_param("ssii", $book_name, $author_name, $published_year, $id);
        if ($u->execute()) {
            header("Location: viewpost.php");
            exit();
        } else {
            $error = "DB Error: " . $u->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Book</title><link rel="stylesheet" href="file.css"></head>
<body>
  <div class="container">
    <div class="card">
      <h3>Edit Book</h3>
      <?php if ($error): ?><p class="notice"><?=htmlspecialchars($error)?></p><?php endif; ?>
      <form method="post">
        <div class="form-row"><label>Book Name</label><input type="text" name="book_name" value="<?=htmlspecialchars($book['book_name'])?>" required></div>
        <div class="form-row"><label>Author Name</label><input type="text" name="author_name" value="<?=htmlspecialchars($book['author_name'])?>" required></div>
        <div class="form-row"><label>Published Year</label><input type="number" name="published_year" value="<?=htmlspecialchars($book['published_year'])?>" required></div>
        <button class="btn" type="submit">Update</button>
        <a href="viewpost.php" style="margin-left:12px;">Cancel</a>
      </form>
    </div>
  </div>
</body>
</html>