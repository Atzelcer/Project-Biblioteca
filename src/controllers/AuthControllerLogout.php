<?php
session_start();
session_destroy();
header("Location: ../../public/images/login.php");
exit;
?>
