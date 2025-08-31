<?php
session_start();
include "config.php";
if ($_SESSION['role'] != 'admin') die("Access denied.");

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $update = $conn->prepare("UPDATE posts SET title=?, description=? WHERE id=?");
    $update->bind_param("ssi", $title, $desc, $id);
    $update->execute();
    header("Location: viewpost.php");
}
?>
<form method="post">
    <input type="text" name="title" value="<?php echo $post['title']; ?>">
    <textarea name="description"><?php echo $post['description']; ?></textarea>
    <button type="submit">Update</button>
</form>