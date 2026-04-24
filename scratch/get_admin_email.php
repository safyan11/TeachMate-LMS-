<?php
require_once 'build/inc/db.php';
$res = $conn->query("SELECT email FROM users WHERE role='admin' LIMIT 1");
if($row = $res->fetch_assoc()) {
    echo "Current Admin Email in Database: " . $row['email'] . "\n";
} else {
    echo "No Admin found in database.\n";
}
?>
