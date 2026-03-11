<?php
require_once __DIR__ . '/Config/constants.php';

$loginUrl = (defined('BASE_URL') ? BASE_URL : '/') . 'View/User/Home/index.php';

header('Location: ' . $loginUrl);
exit;
