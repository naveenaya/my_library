<?php
session_start();
include 'config.php';

// Only admin can edit
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: viewpost.php");
    exit();
}

$id = intval($_GET['id']);

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $sql = "UPDATE posts SET title='$title', content='$content' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: viewpost.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch post details
$sql = "SELECT * FROM posts WHERE id=$id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "Post not found!";
    exit();
}
$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
    <h1>✏ Edit Post</h1>
    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="5" cols="50" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>
        <button type="submit">Update Post</button>
    </form>
    <p><a href="viewpost.php">⬅ Back to Posts</a></p>
</body>
</html>