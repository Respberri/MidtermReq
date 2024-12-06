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
</body>
</html>