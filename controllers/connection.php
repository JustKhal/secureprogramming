<?php
require "/xampp/htdocs/sp/config/database.php";
$db = new mysqli(
    $config["server"],
    $config["username"],
    $config["password"],
    $config["database"]
);
?>
