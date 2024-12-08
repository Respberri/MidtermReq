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
$sql = "SELECT a.title, a.message, a.date_posted, s.section_name, sub.name AS subject_name, f.name AS faculty_name
        FROM announcements a
        JOIN faculty_section_subject fss ON a.faculty_section_subject_id = fss.faculty_section_subject_id
        JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
        JOIN sections s ON ss.section_id = s.section_id
        JOIN subjects sub ON ss.subject_id = sub.subject_id
        JOIN faculty f ON f.faculty_id = fss.faculty_id
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
    <link rel="stylesheet" href="main.css">
    <style>
        .announcement-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .announcement-container h2 {
            margin-top: 0;
            color: #9e1f1f;
        }
        .announcement-container p {
            margin: 10px 0;
            color: #555;
        }
        .announcement-container small {
            display: block;
            margin-top: 10px;
            color: #999;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content-container">
    <div class="main-content">
        <header>
            <h1>Announcements</h1>
        </header>
        <?php if ($announcements->num_rows > 0): ?>
            <?php while ($row = $announcements->fetch_assoc()): ?>
                <div class="announcement-container">
                    <h2><?= htmlspecialchars($row['title']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                    <small>
                        Section: <?= htmlspecialchars($row['section_name']) ?> - <?= htmlspecialchars($row['subject_name']) ?><br>
                        Date: <?= htmlspecialchars($row['date_posted']) ?><br>
                        Announced by: <?= htmlspecialchars($row['faculty_name']) ?>
                    </small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No announcements found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
