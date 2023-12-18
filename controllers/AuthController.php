    <?php
    require_once 'connection.php';
    require_once 'CustomSessionHandler.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        $usernameOrEmail = $_POST['usernameOrEmail'];
        $password = $_POST['password'];

        // Check if the entered value is an email or a username
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            // User entered an email
            $query = "SELECT * FROM users WHERE email=?";
        } else {
            // User entered a username
            $query = "SELECT * FROM users WHERE username=?";
        }

        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify the entered password against the hashed password in the database
            if ($user && check_login_attempts($user) && password_verify($password, $user['password'])) {
                reset_failed_login($db, $user);
                $_SESSION["success_message"] = "Login Success";
                $_SESSION['login'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user["email"];

                if ($_SESSION['role'] === 'admin') {
                    header("Location: ../admin.php");
                } else {
                    header("Location: ../messages.php");
                }
            } else {
                if ($user) {
                    update_failed_login($db, $user);
                    if($user["failed_attempts"] + 1 == 3){
                        $_SESSION["error_message"] = "Account is locked for 5 minutes. Please try again later.";
                        header("Location: ../index.php");
                        exit();
                    }
                }
                $_SESSION["error_message"] = "Login Failed";
                header("Location: ../index.php");
            }
        } else {
            $_SESSION["error_message"] = "Login Failed";
            header("Location: ../index.php");
        }

        $stmt->close();
        $db->close();
    }

    // Check the user's login attempts
    function check_login_attempts($user) {
        $current_time = time();
        $last_failed_login_time = strtotime($user['last_failed_login']);
        if ($user['failed_attempts'] >= 3) {
            if ($current_time - $last_failed_login_time < 300) {
                $_SESSION["error_message"] = "Account is locked. Please wait.";
                header("Location: ../index.php");
                exit;
            }
            return false;
        }
        return true;
    }

    // Update the failed attempts of the user if the user failed to login
    function update_failed_login($db, $user) {
        $query = "UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $current_time = date('Y-m-d H:i:s', time());
        $stmt->bind_param("si", $current_time, $user['id']);
        $stmt->execute();
    }

    // Reset the failed attempts if the user able to login
    function reset_failed_login($db, $user) {
        $query = "UPDATE users SET failed_attempts = 0 WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
    }

?>
