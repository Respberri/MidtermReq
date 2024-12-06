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

        // Fetch sections assigned to the faculty
        $sections_sql = "
            SELECT sections.section_id, sections.name 
            FROM faculty_section 
            INNER JOIN sections ON faculty_section.section_id = sections.section_id 
            WHERE faculty_section.faculty_id = $faculty_id";

        $sections_result = $conn->query($sections_sql);

        echo "<h3>Assigned Sections</h3>";

        if ($sections_result->num_rows > 0) {
            echo "<ul>";
            while ($section = $sections_result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>Section ID:</strong> " . $section['section_id'] . "<br>";
                echo "<strong>Name:</strong> " . $section['name'] . "<br>";
                echo "</li><hr>";
            }
            echo "</ul>";
        } else {
            echo "<p>No sections assigned to this faculty.</p>";
        }

    } else {
        echo "<p>No faculty member found with ID $faculty_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

$conn->close();
?>

</body>
</html>
