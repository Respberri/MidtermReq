<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch student grades
$student_id = intval($_GET['student_id'] ?? 0);
$student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();

if (!$student) {
    echo "Invalid student ID.";
    exit();
}

$grades_sql = "
    SELECT g.grade_id, sub.name AS subject_name, g.period, g.grade
    FROM grades g
    INNER JOIN subjects sub ON g.subject_id = sub.subject_id
    WHERE g.student_id = $student_id
    ORDER BY sub.name, g.period
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
        echo "<p>Grade updated successfully!</p>";
        header("Refresh:0");
    } else {
        echo "<p>Error updating grade: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Grades for <?= $student['name'] ?></title>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <h2>Grades for <?= $student['name'] ?></h2>
    <table border="1">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Period</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($grade = $grades->fetch_assoc()): ?>
                <tr>
                    <td><?= $grade['subject_name'] ?></td>
                    <td>
                        <?= $grade['period'] == 5 ? "Final" : "Period " . $grade['period'] ?>
                    </td>
                    <td><?= $grade['grade'] ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="grade_id" value="<?= $grade['grade_id'] ?>">
                            <input type="number" step="0.01" name="grade" value="<?= $grade['grade'] ?>" required>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
