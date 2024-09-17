<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['media_id']) || !isset($_POST['comment'])) {
    header("Location: gallery.php");
    exit();
}

$conn = new mysqli("localhost:3306", "root", "", "beesbiz");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$media_id = intval($_POST['media_id']);
$comment = $conn->real_escape_string($_POST['comment']);

$stmt = $conn->prepare("INSERT INTO comments (user_id, media_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $media_id, $comment);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: gallery.php");
exit();
?>
