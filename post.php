<?php
session_start();
include("config.php");

// Check if logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add Post
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (title, content, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $created_by);
    $stmt->execute();
    header("Location: post.php");
}

// Handle Delete Post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM posts WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: post.php");
}

// Handle Edit Post
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $sql = "UPDATE posts SET title=?, content=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    header("Location: post.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Posts (Admin)</title>
    <link rel="stylesheet" href="file.css">
</head>
<body>
    <h2>Manage Posts - Admin Panel</h2>
    <a href="dashboard.php">⬅ Back to Dashboard</a> | 
    <a href="logout.php">Logout</a>

    <h3>Add New Post</h3>
    <form method="post">
        <input type="text" name="title" placeholder="Post Title" required><br><br>
        <textarea name="content" placeholder="Post Content" required></textarea><br><br>
        <button type="submit" name="add">Add Post</button>
    </form>

    <h3>All Posts</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th><th>Title</th><th>Content</th><th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['content']}</td>
                <td>
                    <form method='post' style='display:inline'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='text' name='title' value='{$row['title']}'>
                        <input type='text' name='content' value='{$row['content']}'>
                        <button type='submit' name='edit'>Edit</button>
                    </form>
                    <a href='post.php?delete={$row['id']}' onclick='return confirm(\"Delete this post?\")'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>