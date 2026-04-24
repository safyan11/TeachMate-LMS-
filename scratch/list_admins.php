<?php
require_once 'build/inc/db.php';
$res = $conn->query("SELECT id, name, email FROM users WHERE role='admin'");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . "\n";
}
?>
