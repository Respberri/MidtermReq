<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available students with enrollment year and year level
$students_sql = "
    SELECT students.student_id, students.name, enrollments.enrollment_year, enrollments.year_level
    FROM students
    JOIN enrollments ON students.student_id = enrollments.student_id";
$students_result = $conn->query($students_sql);

// Fetch available sections and subjects for filtering
$sections = $conn->query("SELECT section_id, section_name FROM sections");
$subjects = $conn->query("SELECT subject_id, name FROM subjects");

// Fetch available section-subject mappings
$section_subjects = $conn->query("
    SELECT ss.section_subject_id, s.section_name, sub.name AS subject_name
    FROM section_subject ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.subject_id
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_students = $_POST['student_ids'] ?? []; // Array of selected student IDs
    $selected_section_subjects = $_POST['section_subject_ids'] ?? []; // Array of selected section-subject IDs

    foreach ($selected_students as $student_id) {
        foreach ($selected_section_subjects as $section_subject_id) {
            $student_id = intval($student_id);
            $section_subject_id = intval($section_subject_id);

            // Insert student-section-subject mapping
            $sql = "INSERT INTO student_section_subject (student_id, section_subject_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $student_id, $section_subject_id);

            if (!$stmt->execute()) {
                echo "<p>Error assigning section-subject ID $section_subject_id to student $student_id: " . $conn->error . "</p>";
            }
            $stmt->close();
        }
    }

    echo "<p>Students assigned to section-subjects successfully!</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Students to Section-Subjects</title>
    <script>
        function filterSectionSubjects() {
            const sectionFilter = document.getElementById('section_filter').value.toLowerCase();
            const subjectFilter = document.getElementById('subject_filter').value.toLowerCase();

            const checkboxes = document.querySelectorAll('.section-subject-checkbox');
            checkboxes.forEach(checkbox => {
                const sectionName = checkbox.getAttribute('data-section').toLowerCase();
                const subjectName = checkbox.getAttribute('data-subject').toLowerCase();

                if (sectionName.includes(sectionFilter) && subjectName.includes(subjectFilter)) {
                    checkbox.parentElement.style.display = '';
                } else {
                    checkbox.parentElement.style.display = 'none';
                }
            });
        }

        function filterStudents() {
            const yearFilter = document.getElementById('year_filter').value;
            const levelFilter = document.getElementById('level_filter').value;

            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                const studentYear = checkbox.getAttribute('data-year');
                const studentLevel = checkbox.getAttribute('data-level');

                if ((yearFilter === "" || studentYear === yearFilter) &&
                    (levelFilter === "" || studentLevel === levelFilter)) {
                    checkbox.parentElement.style.display = '';
                } else {
                    checkbox.parentElement.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <h2>Assign Students to Section-Subjects</h2>

    <form method="post" action="">
        <!-- Filter Students -->
        <h3>Filter Students</h3>
        <label for="year_filter">Filter by Enrollment Year:</label>
        <select id="year_filter" onchange="filterStudents()">
            <option value="">All</option>
            <?php
            // Fetch unique enrollment years for the filter
            $years = $conn->query("SELECT DISTINCT enrollment_year FROM enrollments");
            while ($year = $years->fetch_assoc()) {
                echo "<option value='" . $year['enrollment_year'] . "'>" . $year['enrollment_year'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="level_filter">Filter by Year Level:</label>
        <select id="level_filter" onchange="filterStudents()">
            <option value="">All</option>
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
        </select><br><br>

        <h3>Select Students:</h3>
        <?php while ($student = $students_result->fetch_assoc()): ?>
            <div class="student-checkbox" data-year="<?= $student['enrollment_year'] ?>" data-level="<?= $student['year_level'] ?>">
                <input type="checkbox" name="student_ids[]" value="<?= $student['student_id'] ?>">
                <?= $student['name'] ?> (Year: <?= $student['enrollment_year'] ?>, Level: <?= $student['year_level'] ?>)
            </div>
        <?php endwhile; ?><br>

        <!-- Filter Section-Subjects -->
        <h3>Filter Section-Subjects</h3>
        <label for="section_filter">Filter by Section:</label>
        <input type="text" id="section_filter" onkeyup="filterSectionSubjects()" placeholder="Enter section name"><br><br>

        <label for="subject_filter">Filter by Subject:</label>
        <input type="text" id="subject_filter" onkeyup="filterSectionSubjects()" placeholder="Enter subject name"><br><br>

        <h3>Select Section-Subjects:</h3>
        <?php while ($ss = $section_subjects->fetch_assoc()): ?>
            <div>
                <input 
                    type="checkbox" 
                    class="section-subject-checkbox" 
                    name="section_subject_ids[]" 
                    value="<?= $ss['section_subject_id'] ?>"
                    data-section="<?= $ss['section_name'] ?>"
                    data-subject="<?= $ss['subject_name'] ?>"
                >
                <?= $ss['section_name'] ?> - <?= $ss['subject_name'] ?>
            </div>
        <?php endwhile; ?><br>

        <button type="submit">Assign Students</button>
    </form>
</body>
</html>
