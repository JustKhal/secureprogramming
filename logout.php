<?php
require_once 'controllers/CustomSessionHandler.php';
session_start();

session_destroy();

header("Location: index.php?message=Logout%20Success!");
exit;
?>
