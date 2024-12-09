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
    <link rel="stylesheet" href="/font-awesome/css/all.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-contentbg">
        <header>
            <h1>Developers</h1>
        </header>
        <div class="wrapper">
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_rjc.jpg" alt="picture of Cruz">
                </div>
                    <p class="name">Ralph Jaisell S.<br>Cruz</p>
                    <p class="role">Back-End Developer</p>
                    <p class="dev-info">Course: BS Computer Science Major in Software Development <br><br> School: Bataan Peninsula State University <br><br> Year Level: Third Year </p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_msn.png" alt="picture of Nagamany">
                </div>
                    <p class="name">Marc Steven G. Nagamany</p>
                    <p class="role">Front-End Developer</p>
                    <p class="dev-info">Course: BS Computer Science Major in Software Development <br><br> School: Bataan Peninsula State University <br><br> Year Level: Third Year </p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_lvs.jpg" alt="picture of Salenga">
                </div>
                    <p class="name">Lhyon Victor S. Salenga</p>
                    <p class="role">Back-End Developer</p>
                    <p class="dev-info">Course: BS Computer Science Major in Software Development <br><br> School: Bataan Peninsula State University <br><br> Year Level: Third Year </p>
            </div>
            <div class="card">
                <div class="card-image">
                    <img src="/images/pic_ktav.jpg" alt="picture of Valdecanas">
                </div>
                    <p class="name">Kurt Timothy Aston C. Valdecañas</p>
                    <p class="role">System Analyst</p>
                    <p class="dev-info">Course: BS Computer Science Major in Software Development <br><br> School: Bataan Peninsula State University <br><br> Year Level: Third Year </p>
            </div>
        </div>
    </div>
</body>
</html>