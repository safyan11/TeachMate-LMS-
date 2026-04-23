<?php
require_once "../inc/db.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['grading_id'])) {
    header('Location: grading.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$grading_id = intval($_POST['grading_id']);
$total_marks_input = intval($_POST['total_marks']);

// Fetch grading + assignment info, ensure teacher owns the assignment
$sql = "SELECT a.uploaded_by
        FROM grading g
        JOIN assignments a ON g.assignment_id = a.id
        WHERE g.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $grading_id);
$stmt->execute();
$grading_info = $stmt->get_result()->fetch_assoc();

if (!$grading_info || $grading_info['uploaded_by'] != $teacher_id) {
    // Unauthorized or invalid grading record
    header('Location: grading.php');
    exit;
}

// Validate inputs
$total_marks = max(1, $total_marks_input);  // at least 1
$obtained_marks = max(0, min(intval($_POST['obtained_marks']), $total_marks));
// Validate obtained_marks range
if ($obtained_marks < 0) $obtained_marks = 0;
if ($obtained_marks > $total_marks) $obtained_marks = $total_marks;

// Calculate percentage
$percentage = ($total_marks > 0) ? round(($obtained_marks / $total_marks) * 100, 2) : 0;

// Grade logic
if ($percentage >= 90) $grade = 'A+';
elseif ($percentage >= 80) $grade = 'A';
elseif ($percentage >= 70) $grade = 'B';
elseif ($percentage >= 60) $grade = 'C';
elseif ($percentage >= 50) $grade = 'D';
else $grade = 'F';

$status = ($percentage >= 50) ? 'Passed' : 'Failed';

// Update grading table
$updateSql = "UPDATE grading SET total_marks=?, obtained_marks=?, percentage=?, grade=?, status=?, graded_at=NOW() WHERE id=?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("iidssi", $total_marks, $obtained_marks, $percentage, $grade, $status, $grading_id);
$updateStmt->execute();
$updateStmt->close();

header('Location: grading.php?success=1');
exit;
?>
