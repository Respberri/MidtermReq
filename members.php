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
    <title>Members</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <img class="bmsi-logo" src="/images/bmsi-logo.png" alt="logo of bmsi">
        <h2>Welcome, Admin!</h2>
        <div class="menu">
            <div class="item"><a href="dashboard.php"><i class="fa-solid fa-house"></i>Home</a></div>
            <div class="item"><a href="#"><i class="fa-solid fa-graduation-cap"></i>Students</a></div>
            <div class="item"><a href="#"><i class="fa-solid fa-book"></i>Subjects</a></div>
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
            <h1>Developers</h1>
        </header>
        <div class="member-row">
            <div class="member-col">
                <img class="img-pic" src="/images/pic_rjc.jpg" alt="picture of Cruz">
                <h3>Ralph Jaisell S. Cruz</h3>
                <p>Backend Developer</p>
            </div>
            <div class="member-col">
                <img class="img-pic" src="/images/pic_msn.png" alt="picture of Nagamany">
                <h3>Marc Steven G. Nagamany</h3>
                <p>System Analyst</p>
            </div>  
            <div class="member-col">
                <img class="img-pic" src="/images/pic_lvs.jpg" alt="picture of Salenga">
                <h3>Lhyon Victor S. Salenga</h3>
                <p>Backend Developer</p>
            </div>
            <div class="member-col">
                <img class="img-pic" src="/images/pic_ktav.jpg" alt="picture of Valdecanas">
                <h3>Kurt Timothy Aston C. Valdecañas</h3>
                <p>Frontend Developer</p>
            </div>  
        

        </div>
    </div>

    <!-- jquery for sub-menu toggle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.sub-btn').click(function(){
                $(this).next('.sub-menu').slideToggle();
                $(this).find('.drpdown').toggleClass('rotate');
            })
        })
    </script>

</body>
</html>