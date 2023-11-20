<?php
    session_start();
    require "controllers/connection.php";

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (fullname, username, password, email, role, created_at, updated_at) VALUES (?, ?, ?, ?, 'user', NOW(), NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssss", $fullname, $username, $hashedPassword, $email);

        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Registration successful. You can now log in.";
            header("Location: ../index.php");
        } else {
            $_SESSION["error_message"] = "Registration failed. Please try again.";
            header("Location: ../signup.php");
        }

        $stmt->close();
        $db->close();
    }
?>