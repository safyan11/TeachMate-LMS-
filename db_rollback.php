<?php
$conn = new mysqli('localhost', 'root', '', 'student_portal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($conn->query("RENAME TABLE assignment_submissions TO submissions")) {
    echo "Table renamed successfully back to 'submissions'.\n";
} else {
    echo "Error renaming table: " . $conn->error . "\n";
}

$conn->close();
?>
