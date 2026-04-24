<?php
require_once 'build/inc/db.php';
$sql = "ALTER TABLE users MODIFY COLUMN verify_status ENUM('pending', 'approved', 'ban') DEFAULT 'pending'";
if ($conn->query($sql)) {
    echo "Users table updated successfully\n";
    // Also update existing students to approved for safety, except maybe some test ones
    // Actually let's just make sure all current ones are approved so I don't break the user's testing
    $conn->query("UPDATE users SET verify_status = 'approved' WHERE verify_status IS NULL OR verify_status = ''");
} else {
    echo "Error updating table: " . $conn->error . "\n";
}
?>
