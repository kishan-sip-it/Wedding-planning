<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: " . (defined('BASE_URL') ? BASE_URL : '/') . "login.php");
    exit();
}
?>