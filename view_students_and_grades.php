<?php
require_once 'db.php';

session_start();

// Check if the user is logged in and is a faculty member
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'faculty') {
    header('Location: incorrect.php');
    exit();
}

// Get section_subject_id from query parameter
$section_subject_id = isset($_GET['section_subject_id']) ? intval($_GET['section_subject_id']) : null;

if (!$section_subject_id) {
    echo "<p>Invalid Section Subject ID.</p>";
    exit();
}

// Fetch section subject details
$sql = "
    SELECT ss.subject_id, sections.section_name AS section_name, subjects.name AS subject_name
    FROM section_subject ss
    INNER JOIN sections ON ss.section_id = sections.section_id
    INNER JOIN subjects ON ss.subject_id = subjects.subject_id
    WHERE ss.section_subject_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $section_subject_id);
$stmt->execute();
$section_subject = $stmt->get_result()->fetch_assoc();

if (!$section_subject) {
    echo "<p>Section Subject not found.</p>";
    exit();
}

// Fetch students enrolled in the section subject
$sql_students = "
    SELECT sss.student_id, students.name AS student_name
    FROM student_section_subject sss
    INNER JOIN students ON sss.student_id = students.student_id
    WHERE sss.section_subject_id = ?
";
$stmt = $conn->prepare($sql_students);
$stmt->bind_param("i", $section_subject_id);
$stmt->execute();
$students_result = $stmt->get_result();

// Handle grade submission for each period
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['grades'] as $student_id => $grades) {
        foreach ($grades as $period => $grade) {
            if ($grade === '' || $grade === null) continue; // Skip empty grades

            $update_sql = "
                INSERT INTO grades (student_id, subject_id, period, grade)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE grade = VALUES(grade)";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iiid", $student_id, $section_subject['subject_id'], $period, $grade);
            $update_stmt->execute();
        }
    }
    echo "<p>Grades updated successfully!</p>";
    header("Refresh:0"); // Refresh the page to show updated grades
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Students and Grades</title>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <h2>Students for Section: <?= htmlspecialchars($section_subject['section_name']) ?>, Subject: <?= htmlspecialchars($section_subject['subject_name']) ?></h2>
    
    <form method="post">
        <table border="1">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Period 1</th>
                    <th>Period 2</th>
                    <th>Period 3</th>
                    <th>Period 4</th>
                    <th>Final Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students_result->num_rows > 0): ?>
                    <?php while ($student = $students_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['student_name']) ?></td>
                            <?php
                            // Fetch grades for the student for all periods
                            $grades_query = "
                                SELECT period, grade 
                                FROM grades 
                                WHERE student_id = ? AND subject_id = ? 
                                ORDER BY period ASC
                            ";
                            $grades_stmt = $conn->prepare($grades_query);
                            $grades_stmt->bind_param("ii", $student['student_id'], $section_subject['subject_id']);
                            $grades_stmt->execute();
                            $grades_result = $grades_stmt->get_result();
                            $student_grades = [];
                            while ($grade_row = $grades_result->fetch_assoc()) {
                                $student_grades[$grade_row['period']] = $grade_row['grade'];
                            }
                            ?>
                            <?php for ($period = 1; $period <= 5; $period++): ?>
                                <td>
                                    <input type="number" 
                                           name="grades[<?= $student['student_id'] ?>][<?= $period ?>]" 
                                           value="<?= isset($student_grades[$period]) ? htmlspecialchars($student_grades[$period]) : '' ?>" 
                                           step="0.01" min="0" max="100">
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No students found in this section subject.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="submit">Save Grades</button>
    </form>
</body>
</html>
