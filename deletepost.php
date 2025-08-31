<?php
session_start();
include "config.php";
if ($_SESSION['role'] != 'admin') die("Access denied.");

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: searchpost.php");