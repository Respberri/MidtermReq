<?php
require_once 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if the logged-in user is a student
if ($_SESSION['role'] !== 'student') {
    header('Location: incorrect.php');
    exit();
}

// Get the logged-in student's user_id
$user_id = $_SESSION['user_id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();

    // Fetch current subjects of the student along with section details
    $student_id = $student['student_id'];
    $subjects_sql = "
        SELECT subjects.subject_id, subjects.name, subjects.description, ss.section_id, sections.section_name
        FROM student_section_subject sss
        INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
        INNER JOIN subjects ON ss.subject_id = subjects.subject_id
        INNER JOIN sections ON ss.section_id = sections.section_id
        WHERE sss.student_id = ?";
    $subjects_stmt = $conn->prepare($subjects_sql);
    $subjects_stmt->bind_param("i", $student_id);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
} else {
    echo "<p>No student found with user ID $user_id.</p>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="page.css">
    <style>
        .accordion .accordion-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .accordion .accordion-btn {
            background-color: #912c2c;
            color: white;
            padding: 10px;
            border: none;
            text-align: left;
            width: 100%;
            cursor: pointer;
        }
        .accordion .accordion-btn.active, .accordion .accordion-btn:hover {
            background-color: #be9191;
        }
        .accordion .accordion-content {
            display: none;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .accordion .accordion-content.open {
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>My Profile</h1>
        </header>
        <?php if (isset($student)): ?>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($student['age']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['date_of_birth']) ?></p>

            <header><h1>Enrolled Subjects</h1></header>
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
                                <a href="student_grade_personal.php?student_id=<?= $student_id ?>&subject_id=<?= $subject['subject_id'] ?>" class="view-grades-btn">View Grades</a>
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
