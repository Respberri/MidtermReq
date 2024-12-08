<?php include 'db.php'; ?>

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch student enrollment data grouped by year level
$studentData = [];
$labels = [];
$query = "SELECT year_level, COUNT(*) as student_count 
          FROM enrollments 
          GROUP BY year_level 
          ORDER BY year_level ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = 'Grade ' . $row['year_level'];
        $studentData[] = $row['student_count'];
    }
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
        // Fetch PHP data passed to JavaScript
        var labels = <?php echo json_encode($labels); ?>;
        var studentData = <?php echo json_encode($studentData); ?>;

        var ctx = document.getElementById('studentChart').getContext('2d');
        var studentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Enrolled Students',
                    data: studentData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
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
