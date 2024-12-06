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

    <div class="main-contentbg">
        <header>
            <h1>Developers</h1>
        </header>
        <div class="wrapper">
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_rjc.jpg" alt="picture of Cruz">
                </div>
                    <p class="name">Ralph Jaisell S. Cruz</p>
                    <p class="role">Back-End Developer</p>
                    <p class="dev-info">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias, dolorem ab quas fugiat pariatur eveniet commodi quos magni est error
                    repudiandae qui sint reiciendis, iusto quibusdam nobis minus fuga praesentium!</p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_msn.png" alt="picture of Nagamany">
                </div>
                    <p class="name">Marc Steven G. Nagamany</p>
                    <p class="role">Front-End Developer</p>
                    <p class="dev-info">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias, dolorem ab quas fugiat pariatur eveniet commodi quos magni est error
                    repudiandae qui sint reiciendis, iusto quibusdam nobis minus fuga praesentium!</p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_lvs.jpg" alt="picture of Salenga">
                </div>
                    <p class="name">Lhyon Victor S. Salenga</p>
                    <p class="role">Back-End Developer</p>
                    <p class="dev-info">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias, dolorem ab quas fugiat pariatur eveniet commodi quos magni est error
                    repudiandae qui sint reiciendis, iusto quibusdam nobis minus fuga praesentium!</p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_ktav.jpg" alt="picture of Valdecanas">
                </div>
                    <p class="name">Kurt Timothy Aston C. Valdecañas</p>
                    <p class="role">System Analyst</p>
                    <p class="dev-info">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias, dolorem ab quas fugiat pariatur eveniet commodi quos magni est error
                    repudiandae qui sint reiciendis, iusto quibusdam nobis minus fuga praesentium!</p>
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