<?php
if (!isset($_SESSION["CI"])) 
{
    header('Location: ../auth/login.php');
    die();
}