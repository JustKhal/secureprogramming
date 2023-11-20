<?php
    require "connection.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recipient = $_POST['recipient'];
        $validRec = [1, 2, 3, 4];
        if(!in_array($recipient, $validRec)){
            echo "Invalid Recipient Value!";
            header("Location: send.php");
            exit;
        }
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

        if (isset($_FILES['user_file'])) {
            $unique = uniqid() . "_";
            $attachment = $_FILES['user_file'];
            $attachment_name = $attachment['name'];
            $attachment_tmp_name = $attachment['tmp_name'];
            $unique_attach_name = $unique . $attachment_name;
            $upload_path = "../uploads/" . $unique_attach_name;
            $attachment_dir = "uploads/" . $unique_attach_name;
        } else {
            $attachment_name = null;
            $attachment_tmp_name = null;
            $upload_path = null;
            $attachment_dir = null;
        }

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }


        session_start();
        $user_id = $_SESSION['user_id'];

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
                    echo "File Uploaded Successfully!";
                } else {
                    echo "Error uploading the file.";
                }
            }
            header("Location: ../messages.php");
        } else {
            echo "Error: " . $db->error;
        }

        $stmt->close();
        $db->close();
    }
?>