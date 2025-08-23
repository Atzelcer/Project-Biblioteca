<?php
require_once __DIR__ . '/../config/app.php';
session_start();
session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/src/entradas/login.php');
