<?php
session_start();
require_once 'controllers/CustomSessionHandler.php';

session_destroy();

header("Location: index.php?message=Logout%20Success!");
exit;
?>
