<?php
    require_once 'controllers/connection.php';
    require_once 'controllers/CustomSessionHandler.php';
    session_start();

    if ($_SESSION['login'] !== true) {
        header("Location: index.php");
        exit();
    }

    validateSession();
    validateUserAgentAndIP();

    $query = "SELECT sender_id, title, message, send_at, attachment, recipient_id
                                -- (CASE recipient_id
                                --     WHEN 1 THEN 'Administrator'
                                --     WHEN 2 THEN 'Network Manager'
                                --     WHEN 3 THEN 'IT Support'
                                --     WHEN 4 THEN 'Coworker'
                                --     ELSE 'Unknown'
                                --     END) AS recipient_role
                            FROM communications
                            WHERE sender_id = ?
                            ORDER BY send_at DESC
                            ";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/style.css">
        <title>Messages</title>
    </head>
    <?php
        if(isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error_message'] .'</div>';
            unset($_SESSION['error_message']);
        }

        if(isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
    ?>

    <body>
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
            <form action="send.php" method="get">
                <button type="submit">Compose</button>
            </form>

            <h3>Your Messages</h3>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="message">';
                    echo '<p><strong>Recipient:</strong> ' . getRecipientName($row['recipient_id']) . '</p>';
                    echo '<p><strong>Title:</strong> ' . $row['title'] . '</p>';
                    echo $row['message'];
                    echo '<p><strong>Sent at:</strong> ' . $row['send_at'] . '</p>';
                    echo '<p><strong>Attachment:</strong> ' . displayAttachment($row['attachment']) . '</p>';
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
    $stmt->close();
    $db->close();

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

            // Check if the file extension is allowed as an image
            if (in_array($extension, $allowedImageExtensions)) {
                return '<img src="' . $attachment . '" alt="Attachment" style="max-width:100%; height:auto;">';
            } else {
                return "No Attachment";
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
