<?php include 'db.php'; ?>

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
    <link rel="stylesheet" href="/font-awesome/css/all.css">
    <script src="/js/chart.js"></script>
</head>

<body>
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header>
            <h1>Student Chart</h1>
        </header>
        <main>
            <div class="chart-container">
                <canvas id="studentChart"></canvas>
            </div>
        </main>
    </div>

    <script>
        var ctx = document.getElementById('studentChart').getContext('2d');
        var studentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
                datasets: [{
                    label: 'Enrolled Students',
                    data: [180, 150, 100, 130,80, 68, 50], 
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
