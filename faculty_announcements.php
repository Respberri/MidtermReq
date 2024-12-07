<?php
require_once 'db.php';
session_start();

// Check if the user is a faculty member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit();
}

// Get faculty_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT faculty_id FROM faculty WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();
$faculty_id = $faculty['faculty_id'];

// Get assigned section subjects
$sql = "SELECT fss.faculty_section_subject_id, ss.section_id, s.section_name, sub.name AS subject_name
        FROM faculty_section_subject fss
        JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
        JOIN sections s ON ss.section_id = s.section_id
        JOIN subjects sub ON ss.subject_id = sub.subject_id
        WHERE fss.faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$section_subjects = $stmt->get_result();

$message = "";

// Handle form submission for new announcements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_section_subject_id = $_POST['faculty_section_subject_id'];
    $title = $_POST['title'];
    $message = $_POST['message'];

    $sql = "INSERT INTO announcements (faculty_section_subject_id, title, message, date_posted)
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $faculty_section_subject_id, $title, $message);

    if ($stmt->execute()) {
        $message = "Announcement posted successfully!";
    } else {
        $message = "Failed to post announcement.";
    }
}

// Fetch announcements made by the faculty
$sql = "SELECT a.title, a.message, a.date_posted, s.section_name, sub.name AS subject_name
        FROM announcements a
        JOIN faculty_section_subject fss ON a.faculty_section_subject_id = fss.faculty_section_subject_id
        JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
        JOIN sections s ON ss.section_id = s.section_id
        JOIN subjects sub ON ss.subject_id = sub.subject_id
        WHERE fss.faculty_id = ?
        ORDER BY a.date_posted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$past_announcements = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Announcements</title>
</head>
<body>
    <h1>Create Announcement</h1>
    <form method="POST">
        <label for="faculty_section_subject_id">Select Section Subject:</label>
        <select name="faculty_section_subject_id" required>
            <?php while ($row = $section_subjects->fetch_assoc()): ?>
                <option value="<?= $row['faculty_section_subject_id'] ?>">
                    <?= $row['section_name'] ?> - <?= $row['subject_name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        
        <label for="title">Title:</label>
        <input type="text" name="title" required><br><br>
        
        <label for="message">Message:</label>
        <textarea name="message" required></textarea><br><br>
        
        <button type="submit">Post Announcement</button>
    </form>

    <?= isset($message) ? "<p>$message</p>" : '' ?>

    <h1>Your Announcements</h1>
    <?php if ($past_announcements->num_rows > 0): ?>
        <?php while ($row = $past_announcements->fetch_assoc()): ?>
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
        <p>You have not made any announcements yet.</p>
    <?php endif; ?>
</body>
</html>
