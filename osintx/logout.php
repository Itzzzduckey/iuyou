<?php
session_start();
require_once __DIR__ . '/includes/keyauth.php';

$keyauth = new KeyAuth();
$keyauth->logout();

header('Location: login.php');
exit;
?>
