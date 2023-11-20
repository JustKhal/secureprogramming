<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Login</title>
</head>
<?php
    if(isset($_SESSION['error_message'])) {
?>
        <div class="alert alert-error">
            <?= $_SESSION['error_message']; ?>
        </div>

<?php
        unset($_SESSION['error_message']);
    }
?>

<?php
    if(isset($_SESSION['success_message'])) {
?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message']; ?>
        </div>

<?php
        unset($_SESSION['success_message']);
    }
?>
<body>
    <div class="container">
        <form action="controllers/AuthController.php" method="POST">
            <h2>Login</h2>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login" value="login">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php"><button type="button">Sign Up</button></a></p>
    </div>
</body>
</html>
