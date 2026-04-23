<?php
session_start();
session_unset();
session_destroy();
header("Location: ../login.php"); // Or wherever your login page is
exit();
?>