<?php
// Predefined credentials
$predefinedUsername = "admin";
$predefinedPassword = "password";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['uName'];
    $password = $_POST['pass'];

    // Validate credentials
    if ($username == $predefinedUsername && $password == $predefinedPassword) {
        // Successful login, continue to dashboard
    } else {
        // Redirect back to login page
        header(header: 'Location: incorrect.php');
        exit();
    }
} else {
    // Redirect if accessed directly without login
    header(header: 'Location: login.html');
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
            <li><a href="#">Project Description</a></li>
            <li><a href="members.html">Members</a></li>
            <li><a href="login.html">Logout</a></li>
        </ul>
    </div>

</body>
</html>