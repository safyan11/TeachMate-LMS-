<?php
require_once 'build/inc/db.php';
$new_email = 'safimughal.com@gmail.com';
if($conn->query("UPDATE users SET email='$new_email' WHERE role='admin'")) {
    echo "Admin email updated to $new_email successfully.\n";
} else {
    echo "Error updating admin email: " . $conn->error . "\n";
}
?>
