<?php
session_start();
require 'config.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}
header('Location: viewpost.php');
exit();