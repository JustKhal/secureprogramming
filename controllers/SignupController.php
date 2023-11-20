<?php
    session_start();
    require "connection.php";

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Check if the username is already taken
        $checkUsernameQuery = "SELECT id FROM users WHERE username = ?";
        $checkUsernameStmt = $db->prepare($checkUsernameQuery);
        $checkUsernameStmt->bind_param("s", $username);
        $checkUsernameStmt->execute();
        $checkUsernameResult = $checkUsernameStmt->get_result();

        // Check if the email is already used
        $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
        $checkEmailStmt = $db->prepare($checkEmailQuery);
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailResult = $checkEmailStmt->get_result();

        // If username or email already exists, redirect to signup with an error message
        if ($checkUsernameResult->num_rows > 0 ) {
            $_SESSION["error_message"] = "Username is already taken. Please choose a different one.";
            header("Location: ../signup.php");
            exit();
        }

        if ($checkEmailResult->num_rows > 0) {
            $_SESSION["error_message"] = "Email is already taken. Please choose a different one.";
            header("Location: ../signup.php");
            exit();
        }

        // Validate password strength
        if (!isPasswordStrong($password)) {
            $_SESSION["error_message"] = "Password must be at least 8 characters long, contain at least one capital letter, one number, and one special character.";
            header("Location: ../signup.php");
            exit();
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO users (fullname, username, password, email, role, created_at, updated_at) VALUES (?, ?, ?, ?, 'user', NOW(), NOW())";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bind_param("ssss", $fullname, $username, $hashedPassword, $email);

        if ($insertStmt->execute()) {
            $_SESSION["success_message"] = "Registration successful. You can now log in.";
            header("Location: ../index.php");
        } else {
            $_SESSION["error_message"] = "Registration failed. Please try again.";
            header("Location: ../signup.php");
        }

        $insertStmt->close();
        $checkUsernameStmt->close();
        $checkEmailStmt->close();
        $db->close();

        function isPasswordStrong($password) {
            // Minimum length of 8 characters
            $length = strlen($password) >= 8;
            // At least one capital letter
            $capital = preg_match('/[A-Z]/', $password);
            // At least one number
            $number = preg_match('/\d/', $password);
            // At least one special character
            $specialChar = preg_match('/[^A-Za-z0-9]/', $password);

            return $length && $capital && $number && $specialChar;
        }
    }
?>
