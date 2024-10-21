<?php
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
            <p>Admin Page</p>
        </div>
        <ul>
            <li><a href="#">Admin Page</a></li>
            <li><a href="project.php">Project Description</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="box">
            <h1> Student Information System For Bepz Multinational School Incorporated  </h1>
            <p>We are Group V from BSCS-SD3B. Our goal is to create a functional SIS for a local school in Mariveles.
                This is due to the school constantly facing challenges in managing their student inventory, and 
                keeping track with its student records — leading to data inconsistencies and data anomalies towards student evaluations. </p>
            <p>Globally, the growth of education technology sectors constantly prove necessary towards the longevity and foundational upkeep of maintaining
                 an efficient and effective school system. In response to such demand, the Philippines utilized countless software that is designed to cater
                  towards attaining this goal for all educational institutions; in relation, greatly overhauling the outdated school systems, with a more
                   refined and cost-effective alternative. 
            </p>
        </div>
        
    </div>
</body>
</html>