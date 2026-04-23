<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['assignment_id']) || !isset($_FILES['submission_file'])) {
    header('Location: assignments.php?status=error');
    exit;
}

$assignment_id = intval($_POST['assignment_id']);
$file = $_FILES['submission_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: assignments.php?status=error');
    exit;
}

$allowed_types = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/zip',
    'application/x-rar-compressed',
    'text/plain',
    'image/jpeg',
    'image/png'
];

if (!in_array($file['type'], $allowed_types)) {
    header('Location: assignments.php?status=error');
    exit;
}

$upload_dir = __DIR__ . '/uploads/submissions/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = $student_id . '_' . $assignment_id . '_' . time() . '.' . $ext;
$target_file = $upload_dir . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $target_file)) {
    header('Location: assignments.php?status=error');
    exit;
}

require_once "../inc/db.php";

$filesize = $file['size'];
$filename_sql = $conn->real_escape_string($new_filename);

// Check if submission exists
$sql_check = "SELECT id FROM submissions WHERE assignment_id = $assignment_id AND student_id = $student_id";
$result = $conn->query($sql_check);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id'];
    $sql_update = "UPDATE submissions SET filename='$filename_sql', filesize=$filesize, submitted_at=NOW(), status='pending' WHERE id=$id";
    $conn->query($sql_update);
} else {
    $sql_insert = "INSERT INTO submissions (assignment_id, student_id, filename, filesize, submitted_at) VALUES ($assignment_id, $student_id, '$filename_sql', $filesize, NOW())";
    if ($conn->query($sql_insert)) {
        // Initialize grading record for teacher visibility
        $sql_grade = "INSERT INTO grading (assignment_id, student_id, total_marks, obtained_marks, percentage, grade, status) VALUES (?, ?, 100, 0, 0, '', 'Pending') ON DUPLICATE KEY UPDATE status='Pending'";
        $stmt_g = $conn->prepare($sql_grade);
        $stmt_g->bind_param("ii", $assignment_id, $student_id);
        $stmt_g->execute();
        $stmt_g->close();
    }
}

header('Location: assignment.php?status=success');
exit;
