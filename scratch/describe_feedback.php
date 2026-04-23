<?php
require_once 'build/inc/db.php';
$res = $conn->query("DESCRIBE feedback");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
