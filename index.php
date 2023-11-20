<?php
session_start();
require "controllers/connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <form action="controllers/AuthController.php" method="POST">
            <h2>Login</h2>
            <?php
                if (isset($_SESSION["error_message"])) {
                    echo '<p class="error-message">' . $_SESSION["error_message"] . '</p>';
                    unset($_SESSION["error_message"]);
                }
                if (isset($_GET['message'])) {
                    $message = $_GET['message'];
                    echo '<div class="success-message">' . htmlspecialchars($message) . '</div>';
                    unset($message);
                }
            ?>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login" value="login">Login</button>
        </form>
    </div>
</body>
</html>
