<?php
require_once __DIR__ . '/../../Config/constants.php';
require_once __DIR__ . '/../../Config/session.php';

if (!isLoggedIn()) {
    // Redirect to login page if not authenticated
    header("Location: ../../Auth/User/login.php");
    exit;
}
?>
