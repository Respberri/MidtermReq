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
<html>
<head>
    <title>Data Analytics</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Data Analytics Dashboard</h1>

    <!-- Faculty-to-Student Ratio -->
    <h2>Faculty-to-Student Ratio by Section</h2>
    <table>
        <tr>
            <th>Section Name</th>
            <th>Total Students</th>
            <th>Total Faculty</th>
            <th>Student-to-Faculty Ratio</th>
        </tr>
        <?php while ($row = $faculty_student_ratios->fetch_assoc()): ?>
            <tr>
                <td><?= $row['section_name'] ?></td>
                <td><?= $row['total_students'] ?></td>
                <td><?= $row['total_faculty'] ?></td>
                <td><?= $row['total_faculty'] > 0 ? round($row['total_students'] / $row['total_faculty'], 2) : 'N/A' ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Grade Distribution -->
    <h2>Grade Distribution by Subject</h2>
    <table>
        <tr>
            <th>Subject</th>
            <th>Grade Category</th>
            <th>Count</th>
        </tr>
        <?php while ($row = $grade_distribution->fetch_assoc()): ?>
            <tr>
                <td><?= $row['subject_name'] ?></td>
                <td><?= $row['grade_category'] ?></td>
                <td><?= $row['count'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Top-Performing Students -->
    <h2>Top 10 Performing Students</h2>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Average Grade</th>
        </tr>
        <?php while ($row = $top_students->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_name'] ?></td>
                <td><?= round($row['average_grade'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
