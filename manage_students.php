<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available year levels for filtering
$year_levels = $conn->query("SELECT DISTINCT year_level FROM sections ORDER BY year_level");

// Fetch all students
$students_sql = "
    SELECT 
        s.student_id, s.name, s.email, s.phone, s.age, s.address, s.date_of_birth, 
        e.year_level, sec.section_name
    FROM students s
    LEFT JOIN enrollments e ON s.student_id = e.student_id
    LEFT JOIN sections sec ON e.section_id = sec.section_id
    ORDER BY s.name
";
$students = $conn->query($students_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <script>
        function filterStudents() {
            const yearLevelFilter = document.getElementById('year_level_filter').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                const yearLevel = row.getAttribute('data-year-level').toLowerCase();
                if (yearLevelFilter === '' || yearLevel === yearLevelFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="content-container">
    <div class="main-content">
    <header>
    <h1>Manage Students and Their Assigned Subjects</h1>
    </header>
    <label for="year_level_filter">Filter by Year Level:</label>
    <select id="year_level_filter" onchange="filterStudents()">
        <option value="">All</option>
        <?php while ($level = $year_levels->fetch_assoc()): ?>
            <option value="<?= $level['year_level'] ?>">Year <?= $level['year_level'] ?></option>
        <?php endwhile; ?>
    </select><br><br>
    
    <div class="student-list">
    <table border="1">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Address</th>
                <th>Date of Birth</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Assigned Subjects</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $students->fetch_assoc()): ?>
                <tr class="student-row" data-year-level="<?= $student['year_level'] ?>">
                    <td><?= $student['student_id'] ?></td>
                    <td><?= $student['name'] ?></td>
                    <td><?= $student['email'] ?></td>
                    <td><?= $student['phone'] ?></td>
                    <td><?= $student['age'] ?></td>
                    <td><?= $student['address'] ?></td>
                    <td><?= $student['date_of_birth'] ?></td>
                    <td><?= $student['year_level'] ?></td>
                    <td><?= $student['section_name'] ?></td>
                    <td>
                        <?php
                        // Fetch assigned subjects
                        $subjects_sql = "
                            SELECT 
                                ss.section_subject_id, sub.name AS subject_name, 
                                f.name AS faculty_name
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
                                echo "<li>" . $subject['subject_name'];
                                if (!empty($subject['faculty_name'])) {
                                    echo " (Instructor: " . $subject['faculty_name'] . ")";
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
                        <a href="view_student.php?student_id=<?= $student['student_id'] ?>" class="edit-btn">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    </div>
    </div>
</body>
</html>
