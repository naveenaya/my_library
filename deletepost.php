<?php
session_start();
include 'config.php';

// Only admin can delete
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: viewpost.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "DELETE FROM posts WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: viewpost.php");
    exit();
} else {
    echo "Error deleting post: " . $conn->error;
}
?>