<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if the logged-in user is an admin
$is_admin = $_SESSION['role'] === 'admin';

// Check if subject_id parameter is provided
$subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <?php
        if ($subject_id) {
            // Fetch subject details
            $sql = "SELECT * FROM subjects WHERE subject_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $subject_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $subject = $result->fetch_assoc();

                // Handle form submission for updates (if the user is admin)
                if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $updated_name = $_POST['name'];
                    $updated_description = $_POST['description'];

                    $update_sql = "UPDATE subjects SET name = ?, description = ? WHERE subject_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ssi", $updated_name, $updated_description, $subject_id);

                    if ($update_stmt->execute()) {
                        echo "<p>Subject details updated successfully!</p>";
                        // Reload the page to reflect changes
                        header("Refresh:0");
                    } else {
                        echo "<p>Error updating subject: " . $conn->error . "</p>";
                    }
                    $update_stmt->close();
                }

                echo "<header><h1>Subject Details</h1></header>";

                // Display subject details in either view or edit mode
                if ($is_admin) {
                    // If admin, allow for editing
                    echo "<form method='POST'>
                            <label>Name:</label><br>
                            <input type='text' name='name' value='" . htmlspecialchars($subject['name']) . "' required><br><br>

                            <label>Description:</label><br>
                            <textarea name='description' required>" . htmlspecialchars($subject['description']) . "</textarea><br><br>

                            <button type='submit'>Save Changes</button>
                            <button type='button' onclick='window.history.back()' class='btn'>Back</button>
                          </form>";
                } else {
                    // If not admin, just display the subject information
                    echo "<p><strong>Subject ID:</strong> " . $subject['subject_id'] . "</p>";
                    echo "<p><strong>Name:</strong> " . $subject['name'] . "</p>";
                    echo "<p><strong>Description:</strong> " . $subject['description'] . "</p>";
                }

                // Fetch students enrolled in the subject
                $students_sql = "
                    SELECT DISTINCT students.student_id, students.name, sections.year_level
                    FROM student_section_subject sss
                    INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
                    INNER JOIN students ON sss.student_id = students.student_id
                    INNER JOIN sections ON ss.section_id = sections.section_id
                    WHERE ss.subject_id = ?";
                $students_stmt = $conn->prepare($students_sql);
                $students_stmt->bind_param("i", $subject_id);
                $students_stmt->execute();
                $students_result = $students_stmt->get_result();

                echo "<header><h1>Enrolled Students</h1></header>";

                if ($students_result->num_rows > 0) {
                    echo "<ul>";
                    while ($student = $students_result->fetch_assoc()) {
                        echo "<li>";
                        echo "<strong>Student ID:</strong> " . htmlspecialchars($student['student_id']) . "<br>";
                        echo "<strong>Name:</strong> " . htmlspecialchars($student['name']) . "<br>";
                        echo "<strong>Grade Level:</strong> " . htmlspecialchars($student['year_level']) . "<br>";
                        echo "</li><hr>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No students found for this subject.</p>";
                }
                $students_stmt->close();

            } else {
                echo "<p>No subject found with ID $subject_id.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Invalid Usage.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
