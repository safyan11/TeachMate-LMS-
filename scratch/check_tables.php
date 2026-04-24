<?php
require_once 'build/inc/db.php';

echo "--- USERS ---\n";
$res = $conn->query("DESCRIBE users");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- COURSES ---\n";
$res = $conn->query("DESCRIBE courses");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- ASSIGNMENTS ---\n";
$res = $conn->query("DESCRIBE assignments");
while($row = $res->fetch_assoc()) print_r($row);

echo "\n--- FEEDBACK ---\n";
$res = $conn->query("DESCRIBE feedback");
while($row = $res->fetch_assoc()) print_r($row);
?>
