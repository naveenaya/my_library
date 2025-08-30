<?php
session_start();
include 'config.php';

// Allow only admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $sql = "INSERT INTO posts (title, content, created_at) VALUES ('$title', '$content', NOW())";
    if ($conn->query($sql) === TRUE) {
        header("Location: viewpost.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
</head>
<body>
    <h1>➕ Add New Post</h1>
    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="5" cols="50" required></textarea><br><br>
        <button type="submit">Add Post</button>
    </form>
    <p><a href="viewpost.php">⬅ Back to Posts</a></p>
</body>
</html>