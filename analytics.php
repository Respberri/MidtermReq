<?php
require_once 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch Faculty-to-Student Ratio by Section
$sql = "SELECT s.section_name, COUNT(e.student_id) AS total_students, 
               COUNT(DISTINCT f.faculty_id) AS total_faculty
        FROM sections s
        LEFT JOIN enrollments e ON s.section_id = e.section_id
        LEFT JOIN faculty_section_subject fss ON s.section_id = fss.section_subject_id
        LEFT JOIN faculty f ON f.faculty_id = fss.faculty_id
        GROUP BY s.section_id";
$faculty_student_ratios = $conn->query($sql);

// Fetch Grade Distribution by Subject
$sql = "SELECT sub.name AS subject_name, 
               CASE 
                   WHEN g.grade >= 90 THEN 'A'
                   WHEN g.grade >= 80 THEN 'B'
                   WHEN g.grade >= 70 THEN 'C'
                   WHEN g.grade >= 60 THEN 'D'
                   ELSE 'F'
               END AS grade_category,
               COUNT(g.grade) AS count
        FROM grades g
        JOIN subjects sub ON g.subject_id = sub.subject_id
        GROUP BY g.subject_id, grade_category";
$grade_distribution = $conn->query($sql);

// Fetch Top-Performing Students by Average Grade
$sql = "SELECT st.name AS student_name, AVG(g.grade) AS average_grade
        FROM students st
        JOIN grades g ON st.student_id = g.student_id
        GROUP BY st.student_id
        ORDER BY average_grade DESC
        LIMIT 10";
$top_students = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analytics</title>
    <link rel="stylesheet" href="page.css">
    <script src="/js/chart.js"></script> <!-- Include Chart.js -->
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="content-container">
            <header>
                <h1>Data Analytics Dashboard</h1>
            </header>

            <!-- Faculty-to-Student Ratio -->
            <section class="analytics-section">
                <h2>Faculty-to-Student Ratio by Section</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Total Students</th>
                            <th>Total Faculty</th>
                            <th>Student-to-Faculty Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $faculty_student_ratios->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['section_name']) ?></td>
                                <td><?= $row['total_students'] ?></td>
                                <td><?= $row['total_faculty'] ?></td>
                                <td><?= $row['total_faculty'] > 0 ? round($row['total_students'] / $row['total_faculty'], 2) : 'N/A' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Grade Distribution -->
            <section class="analytics-section">
                <h2>Grade Distribution by Subject</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade Category</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $grade_distribution->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                <td><?= htmlspecialchars($row['grade_category']) ?></td>
                                <td><?= $row['count'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Bar Chart for Top-Performing Students -->
            <section class="analytics-section">
                <h2>Top 10 Performing Students</h2>
                <canvas id="performanceChart" width="400" height="200"></canvas>
                <script>
                    var studentNames = [];
                    var averageGrades = [];

                    <?php while ($row = $top_students->fetch_assoc()): ?>
                        studentNames.push("<?= $row['student_name'] ?>");
                        averageGrades.push(<?= round($row['average_grade'], 2) ?>);
                    <?php endwhile; ?>

                    var ctx = document.getElementById('performanceChart').getContext('2d');
                    var performanceChart = new Chart(ctx, {
                        type: 'bar', 
                        data: {
                            labels: studentNames, // Student names on the X-axis
                            datasets: [{
                                label: 'Average Grade',
                                data: averageGrades, // Average grades on the Y-axis
                                backgroundColor: '#4CAF50', 
                                borderColor: '#388E3C', 
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    min: 75,
                                    max: 100, 
                                    title: {
                                        display: true,
                                        text: 'Average Grade (%)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Student Name'
                                    },
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: 10 // Adjust if you have too many student names
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return 'Grade: ' + tooltipItem.raw + '%'; // Show grade percentage in tooltip
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            </section>
        </div>
    </div>
</body>
</html>
