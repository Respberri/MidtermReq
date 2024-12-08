<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch student and subject details
$student_id = intval($_GET['student_id'] ?? 0);
$subject_id = intval($_GET['subject_id'] ?? 0);
$student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();
$subject = $conn->query("SELECT * FROM subjects WHERE subject_id = $subject_id")->fetch_assoc();

if (!$student || !$subject) {
    echo "Invalid student or subject ID.";
    exit();
}

// Fetch grades for the student, grouped by period (avoiding duplication)
$grades_sql = "
    SELECT g.grade_id, sub.name AS subject_name, g.period, MAX(g.grade) AS grade
    FROM grades g
    INNER JOIN subjects sub ON g.subject_id = sub.subject_id
    WHERE g.student_id = $student_id AND g.subject_id = $subject_id
    GROUP BY g.period
    ORDER BY g.period
";
$grades = $conn->query($grades_sql);

// Handle grade update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_id = intval($_POST['grade_id']);
    $new_grade = floatval($_POST['grade']);
    $update_sql = "UPDATE grades SET grade = ? WHERE grade_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("di", $new_grade, $grade_id);

    if ($stmt->execute()) {
        echo "<p class='message success'>Grade updated successfully!</p>";
        header("Location: view_student_grades.php?student_id=$student_id&subject_id=$subject_id");
        exit();
    } else {
        echo "<p class='message error'>Error updating grade: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?></title>
    <link rel="stylesheet" href="page.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 200px; /* Assuming sidebar width */
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .message {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .update-form {
            display: flex;
            align-items: center;
        }
        .update-form input[type="number"] {
            margin-right: 10px;
        }
        .update-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .update-form button:hover {
            background-color: #45a049;
        }
        .back-btn {
            padding: 10px 20px;
            background-color: #9e1f1f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-btn:hover {
            background-color: #be9191 ;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h1>Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?></h1>
        </header>
        <form method="POST">
            <button type="button" onclick="window.history.back()" class="back-btn">Back</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($grade = $grades->fetch_assoc()): ?>
                    <tr>
                        <td><?= $grade['period'] == 5 ? "Final" : "Period " . htmlspecialchars($grade['period']) ?></td>
                        <td><?= htmlspecialchars($grade['grade']) ?></td>
                        <td>
                            <form method="post" class="update-form">
                                <input type="hidden" name="grade_id" value="<?= $grade['grade_id'] ?>">
                                <input type="number" step="0.01" name="grade" value="<?= $grade['grade'] ?>" required>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
