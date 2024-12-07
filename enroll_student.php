
<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available students
$students = $conn->query("SELECT student_id, name FROM students");

// Fetch available sections
$sections = $conn->query("SELECT section_id, year_level, section_name FROM sections");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id']);
    $enrollment_year = intval(date("Y"));
    $year_level = intval($_POST['year_level']);
    $section_id = intval($_POST['section_id']);

    // Insert enrollment record
    $sql = "INSERT INTO enrollments (student_id, enrollment_year, year_level, section_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $enrollment_year, $year_level, $section_id);

    if ($stmt->execute()) {
        echo "<p>Student enrolled successfully!</p>";
    } else {
        echo "<p>Error enrolling student: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll Student</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function filterSections() {
            const yearLevelFilter = document.getElementById('year_level_filter').value;

            // Update the hidden year_level input
            document.getElementById('year_level').value = yearLevelFilter;

            const options = document.querySelectorAll('.section-option');
            options.forEach(option => {
                const yearLevel = option.getAttribute('data-year-level');
                if (yearLevelFilter === '' || yearLevel === yearLevelFilter) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>Enroll Student</h1>
        </header>
    <form method="post" action="">
        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id" required>
            <?php while ($student = $students->fetch_assoc()): ?>
                <option value="<?= $student['student_id'] ?>"><?= $student['name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="year_level">Select Grade Level:</label>
        <select id="year_level_filter" onchange="filterSections()" required>
            <option value="">All</option>
            <?php 
            $unique_year_levels = $conn->query("SELECT DISTINCT year_level FROM sections ORDER BY year_level");
            while ($level = $unique_year_levels->fetch_assoc()): ?>
                <option value="<?= $level['year_level'] ?>">Year <?= $level['year_level'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- Hidden year_level field -->
        <input type="hidden" id="year_level" name="year_level" value="">

        <label for="section_id">Select Section:</label>
        <select id="section_id" name="section_id" required>
            <?php while ($section = $sections->fetch_assoc()): ?>
                <option 
                    value="<?= $section['section_id'] ?>" 
                    class="section-option" 
                    data-year-level="<?= $section['year_level'] ?>"
                >
                    <?= "Year " . $section['year_level'] . " - " . $section['section_name'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Enroll Student</button>
    </form>
    </div>
</body>
</html>
