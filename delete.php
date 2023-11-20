<?php
session_start();

if ($_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message_id'])) {
        $messageId = $_POST['message_id'];
        require "controllers/connection.php";

        $query = "DELETE FROM communications WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $messageId);

        if ($stmt->execute()) {
            $_SESSION['delete_success'] = "Message deleted successfully";
        } else {
            $_SESSION['delete_error'] = "Error deleting the message";
        }

        $stmt->close();
        $db->close();
    }
}

header("Location: admin.php");
exit();
?>
