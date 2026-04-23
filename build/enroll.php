<?php
session_start();
require_once "inc/db.php";

if (!isset($_SESSION['user_id'])) {
    // If not logged in, send to login
    header("Location: login.php?msg=Please login to enroll in courses.");
    exit();
}

if ($_SESSION['user_role'] !== 'student') {
    // Only students can enroll
    header("Location: index.php?msg=Only students can enroll in courses.");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$student_id = intval($_SESSION['user_id']);
$course_id = intval($_GET['id']);

// Check if already enrolled
$check = $conn->query("SELECT id FROM course_enrollments WHERE student_id = $student_id AND course_id = $course_id");

if ($check->num_rows === 0) {
    // Perform Enrollment
    $sql = "INSERT INTO course_enrollments (student_id, course_id, progress_percentage) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $student_id, $course_id);
    
    if ($stmt->execute()) {
        $msg = "Successfully enrolled!";
    } else {
        $msg = "Enrollment failed.";
    }
    $stmt->close();
}

// Redirect to the Student Playlist for this course
header("Location: student/playlist.php?id=$course_id");
exit();
?>
