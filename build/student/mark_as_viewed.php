<?php
session_start();
require_once "../inc/db.php";

if (isset($_SESSION['user_id']) && isset($_POST['assignment_id'])) {
    $student_id = intval($_SESSION['user_id']);
    $assignment_id = intval($_POST['assignment_id']);
    
    $stmt = $conn->prepare("INSERT IGNORE INTO assignment_views (student_id, assignment_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $assignment_id);
    $stmt->execute();
    $stmt->close();
    echo "success";
} else {
    echo "error";
}
?>
