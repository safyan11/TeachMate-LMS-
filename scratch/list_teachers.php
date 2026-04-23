<?php
require_once 'build/inc/db.php';
$result = $conn->query("SELECT id, name FROM users WHERE role = 'teacher' ORDER BY name");
while($row = $result->fetch_assoc()) {
    echo $row['id'] . ': ' . $row['name'] . "\n";
}
?>
