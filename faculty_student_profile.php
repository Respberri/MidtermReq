<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Get the faculty ID from the session
$faculty_id = $_SESSION['faculty_id'];

// Check if student_id parameter is provided
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);

    // Fetch student details
    $sql = "SELECT * FROM students WHERE student_id = $student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();

        // Fetch current subjects of the student along with section details, filtered by faculty's sections
        $subjects_sql = "
            SELECT subjects.subject_id, subjects.name, subjects.description, ss.section_id, sections.section_name
            FROM student_section_subject sss
            INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
            INNER JOIN subjects ON ss.subject_id = subjects.subject_id
            INNER JOIN sections ON ss.section_id = sections.section_id
            INNER JOIN faculty_section_subject fss ON fss.section_subject_id = ss.section_subject_id
            WHERE sss.student_id = $student_id AND fss.faculty_id = $faculty_id";
        $subjects_result = $conn->query($subjects_sql);
    } else {
        echo "<p>No student found with ID $student_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Profile</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>Student Profile</h1>
        </header>
        <?php if (isset($student)): ?>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($student['age']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['date_of_birth']) ?></p>

            <header><h1>Current Subjects</h1></header>
            <?php if ($subjects_result->num_rows > 0): ?>
                <div class="accordion">
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <div class="accordion-item">
                            <button class="accordion-btn"><?= htmlspecialchars($subject['name']) ?></button>
                            <div class="accordion-content">
                                <p><strong>Subject ID:</strong> <?= htmlspecialchars($subject['subject_id']) ?></p>
                                <p><strong>Description:</strong> <?= htmlspecialchars($subject['description']) ?></p>
                                <p><strong>Section ID:</strong> <?= htmlspecialchars($subject['section_id']) ?></p>
                                <p><strong>Section Name:</strong> <?= htmlspecialchars($subject['section_name']) ?></p>
                                <a href="view_student_grades.php?student_id=<?= $student_id ?>&subject_id=<?= $subject['subject_id'] ?>" class="view-grades-btn">View Grades</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No subjects found for this student.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Invalid student ID or no student found.</p>
        <?php endif; ?>
        
        <!-- JavaScript for accordion functionality -->
        <script>
            document.querySelectorAll('.accordion-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;

                    // Toggle accordion content visibility with smooth transition
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                        content.classList.remove('open');
                    } else {
                        content.style.display = 'block';
                        content.classList.add('open');
                    }

                    // Toggle active state for the button (for visual feedback)
                    button.classList.toggle('active');
                });
            });
        </script>
    </div>
</body>
</html>
