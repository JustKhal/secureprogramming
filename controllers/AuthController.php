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
            $row = $result->fetch_assoc();
            // Verify the entered password against the hashed password in the database
            if (password_verify($password, $row['password'])) {
                $_SESSION["success_message"] = "Login Success";
                $_SESSION['login'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row["email"];

                if ($_SESSION['role'] === 'admin') {
                    header("Location: ../admin.php");
                } else {
                    header("Location: ../messages.php");
                }
            } else {
                $_SESSION["error_message"] = "Login Failed b";
                header("Location: ../index.php");
            }
        } else {
            $_SESSION["error_message"] = "Login Failed c";
            header("Location: ../index.php");
        }

        $stmt->close();
        $db->close();
    }
?>
