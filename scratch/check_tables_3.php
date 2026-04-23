<?php
require_once 'build/inc/db.php';
$tables = ['attendance', 'submissions', 'assignments', 'online_classes'];
foreach ($tables as $t) {
    $res = $conn->query("SHOW TABLES LIKE '$t'");
    echo "$t: " . ($res->num_rows > 0 ? 'exists' : 'missing') . "\n";
}
?>
