<?php
require_once 'db.php';

session_start();

// Check if the user is logged in and is a faculty member
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'faculty') {
    header('Location: incorrect.php');
    exit();
}

// Get the logged-in faculty_id
$faculty_id = $_SESSION['faculty_id'];

// Fetch section subjects assigned to the faculty from faculty_section_subject table
$sql = "
    SELECT ss.section_subject_id, sections.section_name, subjects.name AS subject_name
    FROM faculty_section_subject fss
    INNER JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
    INNER JOIN sections ON ss.section_id = sections.section_id
    INNER JOIN subjects ON ss.subject_id = subjects.subject_id
    WHERE fss.faculty_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Section Subjects</title>
    <link rel="stylesheet" href="page.css"> <!-- Add your CSS file here for styling -->
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="content-container">
            <header>
                <h2>My Section Subjects</h2>
            </header>

            <?php if ($result->num_rows > 0): ?>
                <!-- Table to display section subjects -->
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Subject Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['section_name']) ?></td>
                                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                <td>
                                    <a href="view_students_and_grades.php?section_subject_id=<?= $row['section_subject_id'] ?>" 
                                       class="btn view-btn">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No section subjects assigned.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
