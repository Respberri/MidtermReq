<?php

// Stop browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
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
            <p>Project Description</p>
        </div>
        <ul>
            <li><a href="dashboard.php">Admin Page</a></li>
            <li><a href="#">Project Description</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="box2">
            <h1> Project Description </h1>
            <p> The implementation of our Student Information System (SIS) greatly overhauls and launches Bepz Multinational School Incorporated (BMSI) into a new light.
                 It addresses the current operational challenges and deficits of the old system — thus, enhancing the overall experience in handling and managing both 
                 staff and student data. These invaluable insights, brought about by detailed reporting, is not only feasible for implementation, but also flexible 
                 in terms of scaling — especially, for security and the protection of private/sensitive information of both parties.
            </p>
        </div>
    </div>
</body>
</html>