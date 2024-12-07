<?php
require_once 'db.php';
session_start();

// Check if the user is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

// Get student_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['student_id'];

// Get announcements for the student's enrolled section subjects
$sql = "SELECT a.title, a.message, a.date_posted, s.section_name, sub.name AS subject_name
        FROM announcements a
        JOIN faculty_section_subject fss ON a.faculty_section_subject_id = fss.faculty_section_subject_id
        JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
        JOIN sections s ON ss.section_id = s.section_id
        JOIN subjects sub ON ss.subject_id = sub.subject_id
        JOIN student_section_subject sss ON ss.section_subject_id = sss.section_subject_id
        WHERE sss.student_id = ?
        ORDER BY a.date_posted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$announcements = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Announcements</title>
</head>
<body>
    <h1>Announcements</h1>
    <?php if ($announcements->num_rows > 0): ?>
        <?php while ($row = $announcements->fetch_assoc()): ?>
            <div style="border: 1px solid #000; margin-bottom: 10px; padding: 10px;">
                <h2><?= $row['title'] ?></h2>
                <p><?= $row['message'] ?></p>
                <small>
                    Section: <?= $row['section_name'] ?> - <?= $row['subject_name'] ?><br>
                    Date: <?= $row['date_posted'] ?>
                </small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No announcements found.</p>
    <?php endif; ?>
</body>
</html>
