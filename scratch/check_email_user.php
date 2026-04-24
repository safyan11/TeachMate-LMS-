<?php
require_once 'build/inc/db.php';
$email = 'safimughal.com@gmail.com';
$res = $conn->query("SELECT id, name, role FROM users WHERE email='$email'");
if($row = $res->fetch_assoc()) {
    echo "Found user with this email: ID=" . $row['id'] . ", Name=" . $row['name'] . ", Role=" . $row['role'] . "\n";
} else {
    echo "No user found with this email.\n";
}
?>
