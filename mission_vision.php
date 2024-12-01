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
    <title>Mission & Vision</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
</head>

<body>
<div class="sidebar">
        <img class="bmsi-logo" src="/images/bmsi-logo.png" alt="logo of bmsi">
        <h2>Welcome, Admin!</h2>
        <div class="menu">
            <div class="item"><a href="dashboard.php"><i class="fa-solid fa-house"></i>Home</a></div>
            <div class="item"><a href=""><i class="fa-solid fa-graduation-cap"></i>Students</a></div>
            <div class="item"><a href="courses.php"><i class="fa-solid fa-book"></i>Subjects</a></div>
            <div class="item"><a class="sub-btn"><i class="fa-solid fa-circle-info"></i>More
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
            <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <a href="mission_vision.php" class="sub-item">Mission & Vision</a>
                <a href="members.php" class="sub-item">Developers</a>
            </div>
        </div>
            <div class="item"><a href="logout.php"><i class="fa-solid fa-circle-left"></i>Logout</a></div>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Mission and Vision</h1>
        </header>
        <div class="card-row">
            <div class="card-col">
                <h3>Mission</h3>
                <p>The mission of BEPZ Multinational School is to pursue mastery of the established curriculum for all students through learning focused instruction,
                    continuous assessments, supportive interventions and extended learning opportunities. Furthermore, BMS aims to pursue excellence in academic knowledge,
                    skills and behavior for each student resulting in measured improvement against local, national and world-class standards.
                    As a distinct institution, BMS is likewise committed to produce quality graduates who have high esteem for self-respect, love of country,
                    preservation and conservation of environment for the glorification of their creator.</p>
            </div>
            <div class="card-col">
                <h3>Vision</h3>
                <p>BEPZ Multinational School, Inc is a learning community committed to educating all students. BMSi believes that it has an obligation to provide meaningful
                    experiences, which will enable each student to develop his or her potential skills, values, and attitude necessary to make a worthwhile contribution to society.
                    Our societal system shares the responsibility of preparing students academically, emotionally, physically, and socially in developing skills and values necessary
                    to equip the youth to become authentic channels of faith and zealous bearers of truth.</p>
            </div>
        </div>
    </div>

    <!-- jquery for sub-menu toggle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.sub-btn').click(function(){
                $(this).next('.sub-menu').slideToggle();
                $(this).find('.dropdown').toggleClass('rotate');
            })
        })
    </script>
    
</body>
</html>