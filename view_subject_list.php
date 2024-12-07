<?php
require_once 'db.php';

session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: incorrect.php');
    exit();
}

// Handle deletion of a subject or section subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'], $_POST['type'])) {
    $delete_id = intval($_POST['delete_id']);
    $type = $_POST['type'];

    if ($type === 'subject') {
        $sql = "DELETE FROM subjects WHERE subject_id = ?";
    } elseif ($type === 'section_subject') {
        $sql = "DELETE FROM section_subject WHERE section_subject_id = ?";
    } else {
        echo "<p>Invalid deletion type.</p>";
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<p>Item deleted successfully!</p>";
    } else {
        echo "<p>Error deleting item: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Fetch filter values
$year_level = isset($_GET['year_level']) ? intval($_GET['year_level']) : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : '';
$subject_filter = isset($_GET['subject_filter']) ? intval($_GET['subject_filter']) : '';

// Fetch subjects
$subjects_sql = "SELECT * FROM subjects ORDER BY name ASC";
$subjects_result = $conn->query($subjects_sql);

// Fetch section subjects with filters
$section_subjects_sql = "
    SELECT ss.section_subject_id, sections.year, sections.year_level, sections.section_name, subjects.name AS subject_name
    FROM section_subject ss
    INNER JOIN sections ON ss.section_id = sections.section_id
    INNER JOIN subjects ON ss.subject_id = subjects.subject_id
    WHERE 1=1
";

if ($year_level) {
    $section_subjects_sql .= " AND sections.year_level = " . $conn->real_escape_string($year_level);
}
if ($year) {
    $section_subjects_sql .= " AND sections.year = " . $conn->real_escape_string($year);
}
if ($subject_filter) {
    $section_subjects_sql .= " AND ss.subject_id = " . $conn->real_escape_string($subject_filter);
}

$section_subjects_sql .= " ORDER BY sections.year DESC, sections.year_level ASC, sections.section_name ASC";
$section_subjects_result = $conn->query($section_subjects_sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin: Subjects and Section Subjects</title>
    <script>
        function confirmDelete(event, form) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this item?')) {
                form.submit();
            }
        }
    </script>
</head>
<body>
    <h1>Admin: Subjects and Section Subjects</h1>

    <h2>Filter Section Subjects</h2>
    <form method="get">
        <label for="year_level">Year Level:</label>
        <input type="number" name="year_level" id="year_level" value="<?= htmlspecialchars($year_level) ?>">

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" value="<?= htmlspecialchars($year) ?>">

        <label for="subject_filter">Subject:</label>
        <select name="subject_filter" id="subject_filter">
            <option value="">-- Select Subject --</option>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <option value="<?= $subject['subject_id'] ?>" <?= $subject_filter == $subject['subject_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($subject['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <h2>Subjects</h2>
    <ul>
        <?php $subjects_result->data_seek(0); // Reset the pointer for reuse ?>
        <?php while ($subject = $subjects_result->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($subject['name']) ?></strong>
                <a href="view_subject.php?subject_id=<?= $subject['subject_id'] ?>">View</a>
                <form method="post" style="display:inline;" onsubmit="confirmDelete(event, this)">
                    <input type="hidden" name="delete_id" value="<?= $subject['subject_id'] ?>">
                    <input type="hidden" name="type" value="subject">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <h2>Section Subjects</h2>
    <ul>
        <?php if ($section_subjects_result->num_rows > 0): ?>
            <?php while ($section_subject = $section_subjects_result->fetch_assoc()): ?>
                <li>
                    <strong>Year:</strong> <?= htmlspecialchars($section_subject['year']) ?>,
                    <strong>Year Level:</strong> <?= htmlspecialchars($section_subject['year_level']) ?>,
                    <strong>Section:</strong> <?= htmlspecialchars($section_subject['section_name']) ?>,
                    <strong>Subject:</strong> <?= htmlspecialchars($section_subject['subject_name']) ?>
                    <a href="view_section_subject.php?section_subject_id=<?= $section_subject['section_subject_id'] ?>">View</a>
                    <form method="post" style="display:inline;" onsubmit="confirmDelete(event, this)">
                        <input type="hidden" name="delete_id" value="<?= $section_subject['section_subject_id'] ?>">
                        <input type="hidden" name="type" value="section_subject">
                        <button type="submit">Delete</button>
                    </form>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No section subjects found.</li>
        <?php endif; ?>
    </ul>

</body>
</html>
