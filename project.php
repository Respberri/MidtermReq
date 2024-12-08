<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Handle filter values
$level_filter = $_GET['level_filter'] ?? '';
$section_filter = $_GET['section_filter'] ?? '';
$subject_filter = $_GET['subject_filter'] ?? '';

// Base query for students with their section and year level
$query = "
    SELECT 
        s.student_id, s.name, s.email, s.phone, s.age, s.address, s.date_of_birth, 
        e.year_level, sec.section_name
    FROM students s
    LEFT JOIN enrollments e ON s.student_id = e.student_id
    LEFT JOIN sections sec ON e.section_id = sec.section_id
    WHERE 1
";

// Apply filters if any
if ($level_filter) {
    $query .= " AND e.year_level = '$level_filter'";
}

if ($section_filter) {
    $query .= " AND sec.section_name = '$section_filter'";
}

if ($subject_filter) {
    $query .= "
        AND s.student_id IN (
            SELECT sss.student_id 
            FROM student_section_subject sss
            INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
            INNER JOIN subjects sub ON ss.subject_id = sub.subject_id
            WHERE sub.name = '$subject_filter'
        )
    ";
}

$result = $conn->query($query);

// Fetch available sections and subjects for filtering
$sections = $conn->query("SELECT section_id, section_name FROM sections");
$subjects = $conn->query("SELECT subject_id, name FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script>
        function filterStudents() {
            const levelFilter = document.getElementById('level_filter').value;
            const sectionFilter = document.getElementById('section_filter').value;
            const subjectFilter = document.getElementById('subject_filter').value;

            // Refresh the page with new query parameters
            let query = "?";
            if (levelFilter) query += "level_filter=" + levelFilter + "&";
            if (sectionFilter) query += "section_filter=" + sectionFilter + "&";
            if (subjectFilter) query += "subject_filter=" + subjectFilter;

            window.location.href = query;
        }
    </script>
</head>
<body>
<?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>Student List</h1>
        </header>

        <main style="margin-top: -10px">
            <!-- Filter Form -->
            <section>
                <label for="level_filter">Filter by Grade Level:</label>
                <select id="level_filter" onchange="filterStudents()">
                    <option value="">All</option>
                    <option value="1" <?php echo $level_filter == "1" ? "selected" : ""; ?>>Grade 1</option>
                    <option value="2" <?php echo $level_filter == "2" ? "selected" : ""; ?>>Grade 2</option>
                    <option value="3" <?php echo $level_filter == "3" ? "selected" : ""; ?>>Grade 3</option>
                    <option value="4" <?php echo $level_filter == "4" ? "selected" : ""; ?>>Grade 4</option>
                    <option value="5" <?php echo $level_filter == "5" ? "selected" : ""; ?>>Grade 5</option>
                    <option value="6" <?php echo $level_filter == "6" ? "selected" : ""; ?>>Grade 6</option>
                </select><br><br>

                <label for="section_filter">Filter by Section:</label>
                <select id="section_filter" onchange="filterStudents()">
                    <option value="">All</option>
                    <?php
                    while ($section = $sections->fetch_assoc()) {
                        echo "<option value='" . $section['section_name'] . "'"
                            . ($section_filter == $section['section_name'] ? " selected" : "") . ">" . $section['section_name'] . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="subject_filter">Filter by Subject:</label>
                <select id="subject_filter" onchange="filterStudents()">
                    <option value="">All</option>
                    <?php
                    while ($subject = $subjects->fetch_assoc()) {
                        echo "<option value='" . $subject['name'] . "'"
                            . ($subject_filter == $subject['name'] ? " selected" : "") . ">" . $subject['name'] . "</option>";
                    }
                    ?>
                </select><br><br>
            </section>
            <!-- Student List -->
            <div class="student-list">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Assigned Subjects</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                                <td><?php echo htmlspecialchars($student['section_name']); ?></td>
                                <td>
                                    <?php
                                    // Fetch assigned subjects for the current student
                                    $subjects_sql = "
                                        SELECT sub.name AS subject_name, f.name AS faculty_name
                                        FROM student_section_subject sss
                                        INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
                                        INNER JOIN subjects sub ON ss.subject_id = sub.subject_id
                                        LEFT JOIN faculty_section_subject fss ON fss.section_subject_id = ss.section_subject_id
                                        LEFT JOIN faculty f ON f.faculty_id = fss.faculty_id
                                        WHERE sss.student_id = {$student['student_id']}
                                    ";
                                    $assigned_subjects = $conn->query($subjects_sql);

                                    if ($assigned_subjects->num_rows > 0) {
                                        echo "<ul>";
                                        while ($subject = $assigned_subjects->fetch_assoc()) {
                                            echo "<li>" . htmlspecialchars($subject['subject_name']);
                                            if (!empty($subject['faculty_name'])) {
                                                echo " (Instructor: " . htmlspecialchars($subject['faculty_name']) . ")";
                                            }
                                            echo "</li>";
                                        }
                                        echo "</ul>";
                                    } else {
                                        echo "No subjects assigned.";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="view_student.php?id=<?php echo $student['student_id']; ?>" class="btn view-btn">View Profile</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
