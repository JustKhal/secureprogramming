<?php
    session_start();
    require_once 'controllers/connection.php';
    require_once 'CustomSessionHandler.php';

    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['message_id'])) {
            $messageId = $_POST['message_id'];


            // Retrieve the attachment path before deleting the message
            $attachmentPathQuery = "SELECT attachment FROM communications WHERE id = ?";
            $attachmentPathStmt = $db->prepare($attachmentPathQuery);
            $attachmentPathStmt->bind_param("i", $messageId);
            $attachmentPathStmt->execute();
            $attachmentPathStmt->bind_result($attachmentPath);
            $attachmentPathStmt->fetch();
            $attachmentPath = "../" . $attachmentPath;
            $attachmentPathStmt->close();

            // Delete the message from the database
            $deleteMessageQuery = "DELETE FROM communications WHERE id = ?";
            $deleteMessageStmt = $db->prepare($deleteMessageQuery);
            $deleteMessageStmt->bind_param("i", $messageId);

            if ($deleteMessageStmt->execute()) {
                // Delete the attachment file from the server
                if (!empty($attachmentPath) && file_exists($attachmentPath)) {
                    unlink($attachmentPath);
                }

                $_SESSION['delete_success'] = "Message deleted successfully";
            } else {
                $_SESSION['delete_error'] = "Error deleting the message";
            }

            $deleteMessageStmt->close();
            $db->close();
        }
    }

    // Redirect back to the admin page
    header("Location: ../admin.php");
    exit();
?>
