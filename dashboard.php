<?php
session_start();

// Predefined credentials
$predefinedUsername = "admin";
$predefinedPassword = "password";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['uName'];
    $password = $_POST['pass'];

    // Validate credentials
    if ($username == $predefinedUsername && $password == $predefinedPassword) {
        // Successful login, set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
    } else {
        // Redirect to incorrect credentials page
        header('Location: incorrect.php');
        exit();
    }
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="menu-bar">
        <div class="grp-num">
            <p>Admin Page</p>
        </div>
        <ul>
            <li><a href="#">Admin Page</a></li>
            <li><a href="project.php">Project Description</a></li>
            <li><a href="members.html">Members</a></li>
            <li><a href="login.html">Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="box">
            <h1> Title of Proposed Project </h1>
            <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
             labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
             nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
             esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, 
             sunt in culpa qui officia deserunt mollit anim id est laborum. </p>
        </div>
    </div>
</body>
</html>