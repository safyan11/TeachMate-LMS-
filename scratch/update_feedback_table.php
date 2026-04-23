<?php
require_once 'build/inc/db.php';
$sql = "ALTER TABLE feedback ADD COLUMN category VARCHAR(50) DEFAULT 'General' AFTER course_id, ADD COLUMN teacher_id INT NULL AFTER category";
if ($conn->query($sql)) {
    echo "Table updated successfully\n";
} else {
    echo "Error updating table: " . $conn->error . "\n";
}
?>
