<?php
    session_start();
    require "connection.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recipient = $_POST['recipient'];
        $validRec = [1, 2, 3, 4];

        // Check Recipient Value
        if (!in_array($recipient, $validRec)) {
            $_SESSION['error_message'] = "Invalid Recipient Value!";
            header("Location: ../send.php");
            exit;
        }

        // check title
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        if (empty($title) || strlen($title) > 32) {
            $_SESSION['error_message'] = "Invalid Title! Please provide a non-empty title with a maximum length of 32 characters.";
            header("Location: ../send.php");
            exit;
        }

        // Check message
        $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
        if (empty($message) || strlen($message) > 255 || str_word_count($message) < 5) {
            $_SESSION['error_message'] = "Invalid Message! Please provide a non-empty message with a maximum length of 255 characters and at least 5 words.";
            header("Location: ../send.php");
            exit;
        }

        // Check for swear words in the message
        $swearWords = ['fuck', 'bitch', 'shit', 'motherfucker'];
        foreach ($swearWords as $word) {
            if (stripos($message, $word) !== false) {
                $_SESSION['error_message'] = "Invalid Message! Please avoid using inappropriate language.";
                header("Location: ../send.php");
                exit;
            }
        }

        if (isset($_FILES['user_file'])) {
            $attachment = $_FILES['user_file'];
            $attachment_name = $attachment['name'];
            $attachment_tmp_name = $attachment['tmp_name'];
            $allowedExtensions = ['png', 'jpg', 'jpeg'];

            $extension = pathinfo($attachment_name, PATHINFO_EXTENSION);

            // check file extension
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $_SESSION['error_message'] = "Invalid Attachment Format! Allowed formats: PNG, JPG, JPEG.";
                header("Location: ../send.php");
                exit;
            }

            // Check file size
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
            if (preg_match('/[\/\\\\]+/', $randomizedFilename) || substr_count($randomizedFilename, '.') > 1) {
                $_SESSION['error_message'] = "Invalid characters in the filename! Please avoid using /, \\, and more than one dot in the filename (except for the dot in the extension).";
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

        $user_id = $_SESSION['user_id'];

        $send_at = date("Y-m-d H:i:s");

        $recipient_id = null;

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
