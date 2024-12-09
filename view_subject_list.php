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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="page.css">
    <style>
        .subject-table th:nth-child(1) {
            width: 30%; 
        }
        .subject-table th:nth-child(2) {
            width: 30%;
        }
        .subject-table th:nth-child(3) {
            width: 30%; 
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content-container">
        <div class="main-content">
            <header>
                <h1>Subjects and Section Subjects</h1>
            </header>
            <main>
                <h2>Filter Section Subjects</h2>
                <form method="get">
                    <label for="year_level">Grade Level:</label><br>
                    <select name="year_level" id="year_level">
                        <option value="">-- Select Grade Level --</option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= $year_level == $i ? 'selected' : '' ?>>Grade <?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="year">Year:</label><br>
                    <select name="year" id="year">
                        <option value="">-- Select Year --</option>
                        <?php for ($y = 2020; $y <= 2030; $y++): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="subject_filter">Subject:</label><br>
                    <select name="subject_filter" id="subject_filter">
                        <option value="">-- Select Subject --</option>
                        <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                            <option value="<?= $subject['subject_id'] ?>" <?= $subject_filter == $subject['subject_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($subject['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit" style="margin-top: 10px">Filter</button>
                </form>
                <br>
                <header>
                    <h1>Subjects</h1>
                </header>
                <table class="subject-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $subjects_result->data_seek(0); // Reset the pointer for reuse ?>
                        <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($subject['subject_id']) ?></td>
                                <td><?= htmlspecialchars($subject['name']) ?></td>
                                <td>
								
                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this subject?');" style="all:unset">
                                        <a href="view_subject.php?subject_id=<?= $subject['subject_id'] ?>" class="btn">View</a>
										|
										<input type="hidden" name="delete_id" value="<?= $subject['subject_id'] ?>">
                                        <input type="hidden" name="type" value="subject">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <br>
                <header>
                    <h1>Section Subjects</h1>
                </header>
                <table class="section-subject-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($section_subjects_result->num_rows > 0): ?>
                            <?php while ($section_subject = $section_subjects_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($section_subject['year']) ?></td>
                                    <td><?= htmlspecialchars($section_subject['year_level']) ?></td>
                                    <td><?= htmlspecialchars($section_subject['section_name']) ?></td>
                                    <td><?= htmlspecialchars($section_subject['subject_name']) ?></td>
                                    <td>
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this section subject?');" style="all:unset">
                                            <input type="hidden" name="delete_id" value="<?= $section_subject['section_subject_id'] ?>">
                                            <input type="hidden" name="type" value="section_subject">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No section subjects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</body>
</html>
