<?php
if (!isset($_SESSION["CI"])) 
{
    header('Location: ../views/auth/login.php');
    die();
}