<?php
session_start();
include 'config.php';

// Number of posts per page
$limit = 5; 

// Get current page or set default
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate offset
$offset = ($page - 1) * $limit;

// Handle search
$search = "";
$where = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE title LIKE '%$search%' OR description LIKE '%$search%'";
}

// Count total posts
$countSql = "SELECT COUNT(*) AS total FROM posts $where";
$countResult = $conn->query($countSql);
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Fetch posts with search + pagination
$sql = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Posts</title>
    <link rel="stylesheet" href="file.css">
</head>
<body>
    <h2>📚 Posts</h2>

    <!-- Search Form -->
    <form method="get" action="viewpost.php">
        <input type="text" name="search" placeholder="🔍 Search posts..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
    <br>

    <?php if ($result->num_rows > 0) { ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <th>Actions</th>
                <?php } ?>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <td>
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>">✏ Edit</a> | 
                            <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>

        <!-- Pagination -->
        <div style="margin-top:15px;">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">⬅ Prev</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                   style="<?php if($i==$page) echo 'font-weight:bold;'; ?>">
                   <?php echo $i; ?>
                </a>
            <?php } ?>

            <?php if ($page < $totalPages) { ?>
                <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">Next ➡</a>
            <?php } ?>
        </div>

    <?php } else { ?>
        <p>No posts found.</p>
    <?php } ?>
</body>
</html>