<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Sign Up</title>
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
        <form action="signup.php" method="POST">
            <h2>Sign Up</h2>
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="index.php"><button type="button">Log in here</button></a>.</p>
    </div>
</body>
</html>