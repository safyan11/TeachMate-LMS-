<?php
require_once 'build/inc/db.php';
$res = $conn->query("SELECT * FROM categories");
while($row = $res->fetch_assoc()) {
    echo $row['id'] . ": " . $row['name'] . "\n";
}
?>
