<?php
require_once 'build/inc/db.php';

$tables = ['notifications', 'chat_messages'];
echo "Checking additional tables...\n";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "Table '$table' exists.\n";
    } else {
        echo "Table '$table' DOES NOT exist.\n";
    }
}
?>
