<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uName']) && isset($_POST['pass'])) {
        $username = $_POST['uName'];
        $password = $_POST['pass'];

        // Predefined credentials
        $predefinedUsername = "admin";
        $predefinedPassword = "password";

        // Validate credentials
        if ($username == $predefinedUsername && $password == $predefinedPassword) {
            // Successful login, set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit();
        } else {
            // Redirect to incorrect credentials page
            header('Location: incorrect.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Left panel: Illustration -->
        <div class="left-panel">
            <img src="/images/bmsi-logo.png" alt="Student Illustration">
        </div>

        <!-- Right panel: Login Form -->
        <div class="right-panel">
            <h1 class="form-title">Login</h1>
            <form action="login.php" method="POST">
                <!-- Username Input -->
                <div class="input-group">
                    <input type="text" name="uName" placeholder="User ID" required>
                </div>

                <!-- Password Input -->
                <div class="input-group">
                    <input type="password" name="pass" placeholder="Password" required>
                </div>

                <!-- Submit Button -->
                <input type="submit" class="btn" value="Login">
            </form>
        </div>
    </div>
</body>
</html>
