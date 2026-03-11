<?php
require "../config/database.php";
require "../models/User.php";

$db = (new Database())->connect();
$user = new User($db);

$token = $_GET['token'];

if ($user->verify($token)) {
    echo "EMAIL VERIFIED! You can now login.";
} else {
    echo "INVALID TOKEN!";
}
?>
