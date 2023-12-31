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
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/style.css">
        <title>Send Message</title>
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

<?php
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