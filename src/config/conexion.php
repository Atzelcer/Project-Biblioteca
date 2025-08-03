<?php
$con = new mysqli("localhost", "root", "", "biblioteca_bd");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>