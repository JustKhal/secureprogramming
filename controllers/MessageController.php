<?php
    session_start();
    require "connection.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recipient = $_POST['recipient'];
        $validRec = [1, 2, 3, 4];
        if (!in_array($recipient, $validRec)) {
            $_SESSION['error_message'] = "Invalid Recipient Value!";
            header("Location: ../send.php");
            exit;
        }

        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        if (empty($title) || strlen($title) > 32) {
            $_SESSION['error_message'] = "Invalid Title! Please provide a non-empty title with a maximum length of 32 characters.";
            header("Location: ../send.php");
            exit;
        }

        $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

        if (isset($_FILES['user_file'])) {
            $attachment = $_FILES['user_file'];
            $attachment_name = $attachment['name'];
            $attachment_tmp_name = $attachment['tmp_name'];
            $allowedExtensions = ['pdf', 'png', 'jpeg', 'docx', 'xlsx', 'mp4', 'zip', '7z', 'txt', 'rar', 'pptx'];

            $extension = pathinfo($attachment_name, PATHINFO_EXTENSION);

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $_SESSION['error_message'] = "Invalid Attachment Format! Allowed formats: PDF, PNG, JPEG, DOCX, XLSX, MP4, ZIP, 7Z, TXT, RAR, PPTX.";
                header("Location: ../send.php");
                exit;
            }

            $maxFileSize = 10 * 1024 * 1024;
            if ($attachment['size'] > $maxFileSize || $attachment['size'] <= 0) {
                $_SESSION['error_message'] = "Invalid Attachment Size! Must be greater than 0 bytes and less than 10 MB.";
                header("Location: ../send.php");
                exit;
            }

            // Check filename
            $unique = uniqid();
            $randomizedFilename = $unique . "_" . basename($attachment_name);
            $upload_path = "../uploads/" . $randomizedFilename;
            $attachment_dir = "uploads/" . $randomizedFilename;

            // Check for special characters in the filename
            if (preg_match('/[\/\\\\.]/', $randomizedFilename)) {
                $_SESSION['error_message'] = "Invalid characters in the filename! Please avoid using ., /, \\ in the filename.";
                header("Location: ../send.php");
                exit;
            }
        } else {
            $attachment_name = null;
            $attachment_tmp_name = null;
            $upload_path = null;
            $attachment_dir = null;
        }

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        $user_id = $_SESSION['user_id']; // No need to call session_start() again

        $send_at = date("Y-m-d H:i:s");

        $recipient_id = null;

        if ($recipient == 1) {
            $recipient_id = 1; // Administrator
        } elseif ($recipient == 2) {
            $recipient_id = 2; // Network Manager
        } elseif ($recipient == 3) {
            $recipient_id = 3; // IT Support
        } elseif ($recipient == 4) {
            $recipient_id = 4; // Coworker
        }

        $query = "INSERT INTO communications (sender_id, recipient_id, title, message, send_at, attachment) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($query);
        $stmt->bind_param("iissss", $user_id, $recipient, $title, $message, $send_at, $attachment_dir);

        if ($stmt->execute()) {
            if ($attachment_name !== null && !empty($attachment_tmp_name)) {
                if (move_uploaded_file($attachment_tmp_name, $upload_path)) {
                    $_SESSION['success_message'] = "Message sent successfully!";
                } else {
                    $_SESSION['error_message'] = "Error uploading the file.";
                    header("Location: ../send.php");
                    exit;
                }
            } else {
                $_SESSION['success_message'] = "Message sent successfully!";
            }
            header("Location: ../messages.php");
        } else {
            $_SESSION['error_message'] = "Error: " . $db->error;
            header("Location: ../send.php");
            exit;
        }

        $stmt->close();
        $db->close();
    }
?>
