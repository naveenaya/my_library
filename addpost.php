<?php
session_start();
include "config.php";
if ($_SESSION['role'] != 'admin') die("Access denied.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO posts (title, description, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $desc);
    $stmt->execute();

    header("Location: search.php");
}
?>
<form method="post">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <button type="submit">Save</button>
</form>