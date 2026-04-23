<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['class_id'])) {
    header('Location: onlineclass.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$class_id = intval($_POST['class_id']);

require_once "../inc/db.php";

// Ensure the class belongs to this teacher before deleting
$sql = "DELETE FROM online_classes WHERE id = $class_id AND teacher_id = $teacher_id";
$conn->query($sql);

header('Location: onlineclass.php');
exit;
