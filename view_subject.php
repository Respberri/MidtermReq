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
if (isset($_GET['subject_id'])) {
    $subject_id = intval($_GET['subject_id']); // Sanitize input

    // Fetch subject details
    $sql = "SELECT * FROM subjects WHERE subject_id = $subject_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();

        // Handle form submission for updates (if the user is admin)
        if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $updated_name = $_POST['name'];
            $updated_description = $_POST['description'];

            $update_sql = "UPDATE subjects SET name = ?, description = ? WHERE subject_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssi", $updated_name, $updated_description, $subject_id);

            if ($stmt->execute()) {
                echo "<p>Subject details updated successfully!</p>";
                // Reload the page to reflect changes
                header("Refresh:0");
            } else {
                echo "<p>Error updating subject: " . $conn->error . "</p>";
            }
            $stmt->close();
        }

        echo "<h2>Subject Details</h2>";

        // Display subject details in either view or edit mode
        if ($is_admin) {
            // If admin, allow for editing
            echo "<form method='POST'>
                    <label>Name:</label><br>
                    <input type='text' name='name' value='" . htmlspecialchars($subject['name']) . "' required><br><br>

                    <label>Description:</label><br>
                    <textarea name='description' required>" . htmlspecialchars($subject['description']) . "</textarea><br><br>

                    <button type='submit'>Save Changes</button>
                  </form>";
        } else {
            // If not admin, just display the subject information
            echo "<p><strong>Subject ID:</strong> " . $subject['subject_id'] . "</p>";
            echo "<p><strong>Name:</strong> " . $subject['name'] . "</p>";
            echo "<p><strong>Description:</strong> " . $subject['description'] . "</p>";
        }

        // Fetch students enrolled in the subject
        $students_sql = "
            SELECT DISTINCT students.student_id, students.name
            FROM student_section_subject sss
            INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
            INNER JOIN students ON sss.student_id = students.student_id
            WHERE ss.subject_id = $subject_id";

        $students_result = $conn->query($students_sql);

        echo "<h3>Enrolled Students</h3>";

        if ($students_result->num_rows > 0) {
            echo "<ul>";
            while ($student = $students_result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>Student ID:</strong> " . $student['student_id'] . "<br>";
                echo "<strong>Name:</strong> " . $student['name'] . "<br>";
                echo "</li><hr>";
            }
            echo "</ul>";
        } else {
            echo "<p>No students found for this subject.</p>";
        }

    } else {
        echo "<p>No subject found with ID $subject_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

$conn->close();
?>
