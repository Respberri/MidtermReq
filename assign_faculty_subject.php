<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available faculty members
$faculty = $conn->query("SELECT faculty_id, name FROM faculty");

// Fetch available section-subject mappings
$section_subjects = $conn->query("
    SELECT ss.section_subject_id, s.section_name, sub.name AS subject_name
    FROM section_subject ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.subject_id
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = intval($_POST['faculty_id']);
    $section_subject_id = intval($_POST['section_subject_id']);

    // Insert faculty-section-subject mapping
    $sql = "INSERT INTO faculty_section_subject (faculty_id, section_subject_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $faculty_id, $section_subject_id);

    if ($stmt->execute()) {
        echo "<p>Faculty assigned successfully!</p>";
    } else {
        echo "<p>Error assigning faculty: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Faculty to Section-Subject</title>
</head>
<body>
    <h2>Assign Faculty to Section-Subject</h2>
    <form method="post" action="">
        <label for="faculty_id">Select Faculty:</label>
        <select id="faculty_id" name="faculty_id" required>
            <?php while ($fac = $faculty->fetch_assoc()): ?>
                <option value="<?= $fac['faculty_id'] ?>"><?= $fac['name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="section_subject_id">Select Section-Subject:</label>
        <select id="section_subject_id" name="section_subject_id" required>
            <?php while ($ss = $section_subjects->fetch_assoc()): ?>
                <option value="<?= $ss['section_subject_id'] ?>">
                    <?= $ss['section_name'] ?> - <?= $ss['subject_name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Assign Faculty</button>
    </form>
</body>
</html>
