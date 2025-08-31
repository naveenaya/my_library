<?php
session_start();
include 'config.php';

// Debug: show current logged-in role
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];  // must be 'admin' or 'user'

// Fetch posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Posts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Role: <b><?php echo $role; ?></b></p>
    <a href="logout.php">Logout</a> | 
    <a href="dashboard.php">Dashboard</a>

    <?php if ($role === 'admin') { ?>
        <br><br>
        <a href="addpost.php">➕ Add New Post</a>
    <?php } ?>

    <h3>All Posts</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Content</th>
            <th>Created At</th>
            <?php if ($role === 'admin') { echo "<th>Actions</th>"; } ?>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['content']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <?php if ($role === 'admin') { ?>
                    <td>
                        <a href="editpost.php?id=<?php echo $row['id']; ?>">✏ Edit</a> | 
                        <a href="deletepost.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?');">🗑 Delete</a>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</body>
</html>