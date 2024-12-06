<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available sections
$sections = $conn->query("SELECT section_id, section_name FROM sections");

// Fetch available subjects
$subjects = $conn->query("SELECT subject_id, name FROM subjects");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_id = intval($_POST['section_id']);
    $selected_subjects = $_POST['subject_ids'] ?? []; // Array of selected subject IDs

    foreach ($selected_subjects as $subject_id) {
        $subject_id = intval($subject_id);

        // Insert section-subject mapping
        $sql = "INSERT INTO section_subject (section_id, subject_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $section_id, $subject_id);

        if (!$stmt->execute()) {
            echo "<p>Error assigning subject ID $subject_id: " . $conn->error . "</p>";
        }
        $stmt->close();
    }

    echo "<p>Subjects assigned to the section successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Section-Subject</title>
</head>
<body>
    <h2>Assign Subjects to Section</h2>
    <form method="post" action="">
        <label for="section_id">Select Section:</label>
        <select id="section_id" name="section_id" required>
            <?php while ($section = $sections->fetch_assoc()): ?>
                <option value="<?= $section['section_id'] ?>"><?= $section['section_name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Select Subjects:</label><br>
        <?php while ($subject = $subjects->fetch_assoc()): ?>
            <input type="checkbox" name="subject_ids[]" value="<?= $subject['subject_id'] ?>">
            <?= $subject['name'] ?><br>
        <?php endwhile; ?><br>

        <button type="submit">Assign Subjects</button>
    </form>
</body>
</html>
