<?php
    require_once 'controllers/connection.php';
    require_once 'controllers/CustomSessionHandler.php';
    session_start();

    // Check if the user is logged in and has the role of "admin"
    if ($_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
        header("Location: index.php"); // Redirect non-admin users to the login page
        exit();
    }

    validateSession();
    validateUserAgentAndIP();

    $query = "SELECT c.id, c.sender_id, c.title, c.message, c.send_at, u.username, c.attachment, c.recipient_id
                FROM communications c
                JOIN users u ON c.sender_id = u.id
                ORDER BY send_at DESC
                ";
    $result = $db->query($query);
    $db->close();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/style.css">
        <title>Admin Page</title>
    </head>
    <?php
        if(isset($_SESSION["delete_error"])) {
            echo '<div class="alert alert-error">' . $_SESSION["delete_error"] . '</div>';
            unset($_SESSION["delete_error"]);
        }

        if(isset($_SESSION["delete_success"])) {
            echo '<div class="alert alert-success">' . $_SESSION["delete_success"] . '</div>';
            unset($_SESSION["delete_success"]);
        }
    ?>
    <body>
        <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> (ADMIN)!</h2>
            <h3>All Messages</h3>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="message">';
                    echo '<p><strong>Sender:</strong> ' . htmlspecialchars($row['username']) . '</p>';
                    echo '<p><strong>Recipient:</strong> ' . getRecipientName($row['recipient_id']) . '</p>';
                    echo '<p><strong>Title:</strong> ' . $row['title'] . '</p>';
                    echo $row['message'];
                    echo '<p><strong>Sent at:</strong> ' . $row['send_at'] . '</p>';
                    echo '<p><strong>Attachment:</strong> ' . displayAttachment($row['attachment']) . '</p>';
                    echo '<form action="controllers/DeleteController.php" method="POST">';
                    echo '<input type="hidden" name="message_id" value="' . $row['id'] . '">';
                    echo '<button type="submit">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '<hr>';
                }
            } else {
                echo "<p>No messages found.</p>";
            }
            ?>

            <form action="logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        </div>
    </body>
    </html>

    <?php

    function getRecipientName($recipientId) {
        if ($recipientId == 1) {
            return 'Administrator';
        } elseif ($recipientId == 2) {
            return 'Network Manager';
        } elseif ($recipientId == 3) {
            return 'IT Support';
        } elseif ($recipientId == 4) {
            return 'Coworker';
        }
    }

    function displayAttachment($attachment) {
        // Check if there is an attachment
        if (!empty($attachment)) {
            $allowedImageExtensions = ['png', 'jpg', 'jpeg'];
            $extension = pathinfo($attachment, PATHINFO_EXTENSION);

            // Get the original file name without the unique ID prefix
            $originalFileName = preg_replace('/^[^_]+_/', '', basename($attachment));

            // Check if the file extension is allowed as an image
            if (in_array($extension, $allowedImageExtensions)) {
                return '<img src="' . $attachment . '" alt="Attachment" style="max-width:100%; height:auto;">';
            } else {
                return htmlspecialchars($originalFileName);
            }
        } else {
            return 'No Attachment';
        }
    }

    function validateSession(){
        $sessionLifetime = 30 * 60; // Set session lifetime (e.g., 30 * 60 seconds = 30 minutes)

        if (isset($_SESSION['last_access']) && (time() - $_SESSION['last_access']) > $sessionLifetime) {
            session_unset();
            session_destroy();
            header("Location: index.php?timeoutMessage='Session%20Timeout!'");
            exit;
        }

        $_SESSION['last_access'] = time();
    }

    function validateUserAgentAndIP()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        if (!isset($_SESSION['user_agent']) && !isset($_SESSION['ip_address'])) {

            $_SESSION['user_agent'] = $userAgent;
            $_SESSION['ip_address'] = $ipAddress;
        } else {
            if ($_SESSION['user_agent'] !== $userAgent || $_SESSION['ip_address'] !== $ipAddress) {
                session_unset();
                session_destroy();
                header("Location: index.php?timeoutMessage='HIJACKED%20SESSION!'");
                exit;
            }
        }
    }

?>
