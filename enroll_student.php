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
$enrollment_success = false;
$enrollment_error = false;

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
        $enrollment_success = true;
    } else {
        $enrollment_error = $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        // Function to display the success or error popup
        function showPopup(message, type) {
            let popup = document.createElement('div');
            popup.classList.add('popup-message');
            if (type === 'error') {
                popup.classList.add('error'); // Apply error styles
            }
            popup.innerHTML = message;
            document.body.appendChild(popup);

            // Fade-out the popup after 3 seconds
            setTimeout(() => {
                popup.style.animation = 'fadeOut 1s ease-in-out forwards';
                setTimeout(() => {
                    popup.remove();
                }, 1000); // Allow the fade-out animation to finish before removing
            }, 3000);
        }
    </script>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>Enroll Student</h1>
        </header>
        
        <?php
        // Show popup on success or error
        if ($enrollment_success) {
            echo "<script>showPopup('Student Enrolled Successfully!', 'success');</script>";
        } elseif ($enrollment_error) {
            echo "<script>showPopup('Error enrolling student: $enrollment_error', 'error');</script>";
        }
        ?>

        <form method="post" action="">
            <label for="student_id">Select Student:</label>
            <select id="student_id" name="student_id" required>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <option value="<?= $student['student_id'] ?>"><?= $student['name'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <label for="year_level">Select Year Level:</label>
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
