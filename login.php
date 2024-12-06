<?php
session_start();
require_once 'db.php';


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
                $_SESSION['user_id'] = $user['user_id']; // Set user_id in session

                // Check if user is a faculty or student and set respective ID
                if ($user['role'] == 'faculty') {
                    // Fetch the faculty_id based on user_id
					error_log("IS_FACULTY");
                    $facultyQuery = "SELECT faculty_id FROM faculty WHERE user_id = ?";
                    $stmt = $conn->prepare($facultyQuery);
                    $stmt->bind_param("i", $user['user_id']); // Bind user_id to the query
                    $stmt->execute();
                    $facultyResult = $stmt->get_result();

                    if ($facultyResult->num_rows > 0) {
                        $faculty = $facultyResult->fetch_assoc();
                        $_SESSION['faculty_id'] = $faculty['faculty_id']; // Set faculty_id in session
						error_log("faculty_id " . $faculty['faculty_id']);
                    }
                } elseif ($user['role'] == 'student') {
                    // Fetch the student_id based on user_id
                    $studentQuery = "SELECT student_id FROM students WHERE user_id = ?";
                    $stmt = $conn->prepare($studentQuery);
                    $stmt->bind_param("i", $user['user_id']); // Bind user_id to the query
                    $stmt->execute();
                    $studentResult = $stmt->get_result();

                    if ($studentResult->num_rows > 0) {
                        $student = $studentResult->fetch_assoc();
                        $_SESSION['student_id'] = $student['student_id']; // Set student_id in session
                    }
                }

                // Redirect to the dashboard after login
                header('Location: dashboard.php');
                exit();
            } else {
                // Incorrect password
                header('Location: incorrect.php');
                exit();
            }
        } else {
            // User not found
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Left panel -->
        <div class="left-panel">
            <img src="/images/bmsi-logo.png" alt="Student Illustration">
            <p>Where  <br><span>ACADEMIC EXCELLENCE</span><br> is a <br><span>TRADITION</span></p>
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
