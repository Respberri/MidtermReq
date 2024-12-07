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

// Check if faculty_id parameter is provided
if (isset($_GET['faculty_id'])) {
    $faculty_id = intval($_GET['faculty_id']); // Sanitize input

    // Fetch faculty details
    $sql = "SELECT * FROM faculty WHERE faculty_id = $faculty_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $faculty = $result->fetch_assoc();

        // Handle form submission for updates (if the user is admin)
        if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $updated_name = $_POST['name'];
            $updated_email = $_POST['email'];
            $updated_phone = $_POST['phone'];
            $updated_age = $_POST['age'];
            $updated_hire_date = $_POST['hire_date'];

            $update_sql = "
                UPDATE faculty 
                SET name = ?, email = ?, phone = ?, age = ?, hire_date = ? 
                WHERE faculty_id = ?
            ";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssssi", $updated_name, $updated_email, $updated_phone, $updated_age, $updated_hire_date, $faculty_id);

            if ($stmt->execute()) {
                echo "<p>Faculty details updated successfully!</p>";
                // Reload the page to reflect changes
                header("Refresh:0");
            } else {
                echo "<p>Error updating faculty: " . $conn->error . "</p>";
            }
            $stmt->close();
        }

        echo "<h2>Faculty Profile</h2>";

        // Display faculty details in either view or edit mode
        if ($is_admin) {
            // If admin, allow for editing
            echo "<form method='POST'>
                    <label>Name:</label><br>
                    <input type='text' name='name' value='" . htmlspecialchars($faculty['name']) . "' required><br><br>

                    <label>Email:</label><br>
                    <input type='email' name='email' value='" . htmlspecialchars($faculty['email']) . "' required><br><br>

                    <label>Phone:</label><br>
                    <input type='text' name='phone' value='" . htmlspecialchars($faculty['phone']) . "' required><br><br>

                    <label>Age:</label><br>
                    <input type='number' name='age' value='" . htmlspecialchars($faculty['age']) . "' required><br><br>

                    <label>Hire Date:</label><br>
                    <input type='date' name='hire_date' value='" . htmlspecialchars($faculty['hire_date']) . "' required><br><br>

                    <button type='submit'>Save Changes</button>
                  </form>";
        } else {
            // If not admin, just display the faculty information
            echo "<p><strong>Faculty ID:</strong> " . $faculty['faculty_id'] . "</p>";
            echo "<p><strong>Name:</strong> " . $faculty['name'] . "</p>";
            echo "<p><strong>Email:</strong> " . $faculty['email'] . "</p>";
            echo "<p><strong>Phone:</strong> " . $faculty['phone'] . "</p>";
            echo "<p><strong>Age:</strong> " . $faculty['age'] . "</p>";
            echo "<p><strong>Hire Date:</strong> " . $faculty['hire_date'] . "</p>";
        }

        // Fetch section-subject assignments for the faculty
        $section_subjects_sql = "
            SELECT ss.section_id, sections.section_name AS section_name, subjects.name AS subject_name, ss.subject_id 
            FROM faculty_section_subject fss
            INNER JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
            INNER JOIN sections ON ss.section_id = sections.section_id
            INNER JOIN subjects ON ss.subject_id = subjects.subject_id
            WHERE fss.faculty_id = $faculty_id
        ";

        $section_subjects_result = $conn->query($section_subjects_sql);

        echo "<h3>Assigned Section-Subjects</h3>";

        if ($section_subjects_result->num_rows > 0) {
            echo "<ul>";
            while ($assignment = $section_subjects_result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>Section Name:</strong> " . htmlspecialchars($assignment['section_name']) . "<br>";
                echo "<strong>Subject Name:</strong> " . htmlspecialchars($assignment['subject_name']) . "<br>";
                echo "</li><hr>";
            }
            echo "</ul>";
        } else {
            echo "<p>No section-subject assignments found for this faculty.</p>";
        }

    } else {
        echo "<p>No faculty member found with ID $faculty_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

$conn->close();
?>