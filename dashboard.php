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
            <h1> Student Information System For Bepz Multinational School Incorporated  </h1>
            <p>We are Group V from BSCS-SD3B. Our goal is to create a functional SIS for a local school in Mariveles.
                This is due to the school constantly facing challenges in managing their student inventory, and 
                keeping track with its student records — leading to data inconsistencies and data anomalies towards student evaluations. </p>
        </div>
        
    </div>
</body>
</html>