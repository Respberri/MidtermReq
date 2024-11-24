<?php
session_start();
require_once 'db.php';


error_log("LOG");
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uName']) && isset($_POST['pass'])) {
        $username = $_POST['uName'];
        $password = $_POST['pass'];

        // Prepare a SQL query to fetch user details
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username); // Bind username to the query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if ($password === $user['password']) {
                // Successful login
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: dashboard.php');
                exit();
            } else {
                // Incorrect password
				error_log("A");

                header('Location: incorrect.php');
                exit();
            }
        } else {
            // User not found
			error_log("LOG");

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
                    <input type="text" name="uName" placeholder="Employee/Student ID" required>
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