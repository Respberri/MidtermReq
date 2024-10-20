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
    <link rel="stylesheet" href="members.css">
</head>
<body>
    <div class="menu-bar">
        <div class="grp-num">
            <p>Developers</p>
        </div>
        <ul>
            <li><a href="dashboard.php">Admin Page</a></li>
            <li><a href="project.php">Project Description</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

<div class="team-section">
    <div class="container">
        <div class="row">
            <div class="title">
                <h1>---- Meet The Team ----</h1>
            </div>
        </div>
        <div class="team-card">
            <div class="card">
                <div class="image-section">
                    <img src="/images/pic_rjc.jpg" alt="">
                </div>
                <div class="content">
                    <h3>Cruz, Ralph Jaisell S.</h3>
                    <h4>Developer</h4>
                    <p>Address: Orani</p>
                    <p>Age: 21</p>
                    <p>Hobbies: Reading and Watching</p>
                    <p>Role: Applied Back-end revisions</p>
                </div>
            </div>
            <div class="card">
                <div class="image-section">
                    <img src="/images/pic_msn.png" alt="">
                </div>
                <div class="content">
                    <h3>Nagamany, Marc Steven G.</h3>
                    <h4>Developer</h4>
                    <p>Address: Mariveles</p>
                    <p>Age: 21</p>
                    <p>Hobbies: Music and Playing sports</p>
                    <p>Role: Applied Front-end Development</p>
                </div>
            </div>
            <div class="card">
                <div class="image-section">
                    <img src="/images/pic_lvs.jpg" alt="">
                </div>
                <div class="content">
                    <h3>Salenga, Lhyon Victor S.</h3>
                    <h4>Developer</h4>
                    <p>Address: Orani</p>
                    <p>Age: 20</p>
                    <p>Hobbies: Gaming and Watching</p>
                    <p>Role: Applied Web Development</p>
                </div>
            </div>
            <div class="card">
                <div class="image-section">
                    <img src="/images/pic_ktav.jpg" alt="">
                </div>
                <div class="content">
                    <h3>Valdecañas, Kurt Timothy Aston C.</h3>
                    <h4>Developer</h4>
                    <p>Address: Balanga City                          
                        </p>
                    <p>Age: 20</p>
                    <p>Hobbies: Music and Illustration</p>
                    <p>Role: Applied UI/UX revisions</p>
                </div>
            </div>
        </div>
    </div>
</div>    
</body>
</html>