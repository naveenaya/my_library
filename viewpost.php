<?php
session_start();
require 'config.php';
if (!isset($_SESSION['username'])) { header('Location: login.php'); exit; }

$role = $_SESSION['role'] ?? 'user';
$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];
$types = '';

if ($search !== '') {
    $where = "WHERE book_name LIKE ? OR author_name LIKE ? OR published_year LIKE ?";
    $like = "%$search%";
    $params = [$like, $like, $like];
    $types = 'sss';
}

// Count total
$countSql = "SELECT COUNT(*) AS c FROM books $where";
$stmt = $conn->prepare($countSql);
if ($where !== '') { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['c'];
$totalPages = max(1, (int)ceil($total / $limit));
$stmt->close();

// Fetch rows
$sql = "SELECT * FROM books $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($where === '') {
    $stmt->bind_param('ii', $limit, $offset);
} else {
    $bindTypes .= "ii"; 
$params[] = $limit;
$params[] = $offset;

$stmt->bind_param($bindTypes, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Books • My Library</title>
  <link rel="stylesheet" href="file.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>Library Books</h2>
      <div>
        <span class="small">Logged in as <?=htmlspecialchars($_SESSION['username'])?> (<?=htmlspecialchars($role)?>)</span>
      </div>
    </div>

    <div class="card">
      <form method="get" style="display:flex; gap:8px; align-items:center;">
        <input type="text" name="search" placeholder="Search by book / author / year" value="<?=htmlspecialchars($search)?>">
        <button class="btn" type="submit">Search</button>
        <?php if ($role === 'admin'): ?>
          <a class="btn" href="addpost.php" style="margin-left:auto">➕ Add Book</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="card">
      <table class="table">
        <thead>
          <tr><th>#</th><th>Book Name</th><th>Author</th><th>Year</th><?php if ($role==='admin') echo '<th>Actions</th>'; ?></tr>
        </thead>
        <tbody>
        <?php if ($res->num_rows === 0): ?>
          <tr><td colspan="<?= $role==='admin' ? 5 : 4 ?>">No books found.</td></tr>
        <?php else: ?>
          <?php while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td><?= htmlspecialchars($r['book_name']) ?></td>
              <td><?= htmlspecialchars($r['author_name']) ?></td>
              <td><?= htmlspecialchars($r['published_year']) ?></td>
              <?php if ($role==='admin'): ?>
                <td>
                  <a href="editpost.php?id=<?= (int)$r['id'] ?>">Edit</a> |
                  <a href="deletepost.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Delete this book?')">Delete</a>
                </td>
              <?php endif; ?>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="pager">
        <?php if ($page > 1): ?>
          <a href="?page=<?=$page-1?>&search=<?=urlencode($search)?>">Prev</a>
        <?php endif; ?>

        <?php for ($i=1;$i<= $totalPages;$i++): ?>
          <a class="<?= $i === $page ? 'active' : '' ?>" href="?page=<?=$i?>&search=<?=urlencode($search)?>"><?=$i?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?page=<?=$page+1?>&search=<?=urlencode($search)?>">Next</a>
        <?php endif; ?>
      </div>
    </div>

    <div style="text-align:center; margin-top:14px;">
      <a href="dashboard.php">← Dashboard</a> | <a href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>