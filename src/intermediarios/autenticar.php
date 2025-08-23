<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: /public/login.php');
    exit;
}
