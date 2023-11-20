<?php
    session_start();
    require "connection.php";

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE username=? AND password=?;";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $db->close();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION["success_message"] = "Login Success";
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row["email"];

            if($_SESSION['role'] === 'admin'){
                header("Location: ../admin.php");
            }
            else {
                header("Location: ../messages.php");
            }
        }
        else {
            $_SESSION["error_message"] = "Login Failed";

            header("Location: ../index.php");
        }

    }
