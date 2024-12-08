<?php
require_once 'db.php';
session_start();

// Ensure the user is logged in and has appropriate permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: incorrect.php');
    exit();
}

// Function to update the student ranking table
function updateStudentRankings($conn) {
    // Clear the existing rankings
    $conn->query("TRUNCATE TABLE student_ranking");

    // Calculate average grades for each student
    $sql = "SELECT st.student_id, st.name, e.year_level, 
                   AVG(g.grade) AS average_grade
            FROM students st
            JOIN grades g ON st.student_id = g.student_id
            JOIN enrollments e ON st.student_id = e.student_id
            GROUP BY st.student_id, e.year_level
            ORDER BY e.year_level, AVG(g.grade) DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rankings = [];
        while ($row = $result->fetch_assoc()) {
            $year_level = $row['year_level'];
            if (!isset($rankings[$year_level])) {
                $rankings[$year_level] = 1; // Start ranking for each year level at 1
            }

            // Insert ranking into the student_ranking table
            $stmt = $conn->prepare("INSERT INTO student_ranking (student_id, year_level, ranking_period, rank, average_grade)
                                    VALUES (?, ?, ?, ?, ?)");
            $ranking_period = 1; // Assume a fixed ranking period for simplicity
            $stmt->bind_param(
                "iiiii",
                $row['student_id'],
                $year_level,
                $ranking_period,
                $rankings[$year_level],
                $row['average_grade']
            );
            $stmt->execute();

            $rankings[$year_level]++;
        }
    }
}

// Handle ranking update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rankings'])) {
    updateStudentRankings($conn);
    $message = "Student rankings have been updated successfully.";
}

// Fetch current rankings
$sql = "SELECT sr.rank, st.name, sr.year_level, sr.average_grade
        FROM student_ranking sr
        JOIN students st ON sr.student_id = st.student_id
        ORDER BY sr.year_level, sr.rank ASC";
$current_rankings = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Rankings</title>
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
        .message {
            margin-bottom: 10px;
            color: green;
        }
    </style>
</head>
<body>
    <h1>Student Rankings</h1>

    <?php if (isset($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" name="update_rankings">Update Rankings</button>
    </form>

    <h2>Current Rankings</h2>
    <table>
        <tr>
            <th>Rank</th>
            <th>Student Name</th>
            <th>Year Level</th>
            <th>Average Grade</th>
        </tr>
        <?php while ($row = $current_rankings->fetch_assoc()): ?>
            <tr>
                <td><?= $row['rank'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['year_level'] ?></td>
                <td><?= round($row['average_grade'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
