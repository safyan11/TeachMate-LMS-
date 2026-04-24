<?php
require_once 'build/inc/db.php';
$sql = "CREATE TABLE IF NOT EXISTS assignment_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (student_id, assignment_id)
)";
if ($conn->query($sql)) {
    echo "Table created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>
