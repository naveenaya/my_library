<?php
session_start();
include "config.php";

// Pagination setup
$limit = 5; // posts per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? $_GET['search'] : "";
$searchQuery = $search ? "WHERE title LIKE ?" : "";

// Count total
$countSql = "SELECT COUNT(*) AS total FROM posts $searchQuery";
$stmt = $conn->prepare($countSql);

if ($search) {
    $like = "%$search%";
    $stmt->bind_param("s", $like);
}
$stmt->execute();
$countResult = $stmt->get_result()->fetch_assoc();
$total = $countResult['total'];
$pages = ceil($total / $limit);

// Fetch posts
$sql = "SELECT * FROM posts $searchQuery ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if ($search) {
    $stmt->bind_param("sii", $like, $start, $limit);
} else {
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h2>📚 Library Posts</h2>

<form method="get" action="">
    <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo substr($row['description'], 0, 100) . "..."; ?></p>
        <a href="view_post.php?id=<?php echo $row['id']; ?>">Read More</a>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            | <a href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a>
            | <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
           class="<?php echo $i == $page ? 'active' : ''; ?>">
           <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

<?php if ($_SESSION['role'] == 'admin'): ?>
    <a href="add_post.php">➕ Add New Book</a>
<?php endif; ?>

<a href="logout.php">Logout</a>
</body>
</html>
