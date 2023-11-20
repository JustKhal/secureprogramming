<?php
session_start();
require "controllers/connection.php";
if ($_SESSION['login'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Send Message</title>
</head>
<body>
    <div class="container">
        <form action="controllers/MessageController.php" method="POST" enctype="multipart/form-data">
            <h2>Compose Message</h2>
            <label for="recipient">Recipient:</label>
            <select id="recipient" name="recipient" required>
                <option value="1">Administrator</option>
                <option value="2">Network Manager</option>
                <option value="3">IT Support</option>
                <option value="4">Coworker</option>
            </select>

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>

            <label for="user_file">Attachment:</label>
            <input type="file" id="user_file" name="user_file">

            <button type="submit">Send Message</button>
        </form>
        <a href="messages.php">Back to Messages</a>
    </div>
</body>
</html>
