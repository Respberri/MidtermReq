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

// Check if student_id parameter is provided
if (isset($_GET['id'])) { // Changed to 'id' to match the link
    $student_id = intval($_GET['id']); // Sanitize input

    // Fetch student details
    $sql = "SELECT * FROM students WHERE student_id = $student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        
        // Handle form submission for updates (if the user is admin)
        if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $updated_name = $_POST['name'];
            $updated_email = $_POST['email'];
            $updated_phone = $_POST['phone'];
            $updated_age = $_POST['age'];
            $updated_address = $_POST['address'];
            $updated_dob = $_POST['date_of_birth'];

            $update_sql = "
                UPDATE students 
                SET name = ?, email = ?, phone = ?, age = ?, address = ?, date_of_birth = ? 
                WHERE student_id = ?
            ";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssssi", $updated_name, $updated_email, $updated_phone, $updated_age, $updated_address, $updated_dob, $student_id);

            if ($stmt->execute()) {
                echo "<p>Student details updated successfully!</p>";
                // Reload the page to reflect changes
                header("Refresh:0");
            } else {
                echo "<p>Error updating student: " . $conn->error . "</p>";
            }
            $stmt->close();
        }

        echo "<h2>Student Profile</h2>";

        // Display student details in either view or edit mode
        if ($is_admin) {
            // If admin, allow for editing
            echo "<form method='POST'>
                    <label>Name:</label><br>
                    <input type='text' name='name' value='" . htmlspecialchars($student['name']) . "' required><br><br>

                    <label>Email:</label><br>
                    <input type='email' name='email' value='" . htmlspecialchars($student['email']) . "' required><br><br>

                    <label>Phone:</label><br>
                    <input type='text' name='phone' value='" . htmlspecialchars($student['phone']) . "' required><br><br>

                    <label>Age:</label><br>
                    <input type='number' name='age' value='" . htmlspecialchars($student['age']) . "' required><br><br>

                    <label>Address:</label><br>
                    <input type='text' name='address' value='" . htmlspecialchars($student['address']) . "' required><br><br>

                    <label>Date of Birth:</label><br>
                    <input type='date' name='date_of_birth' value='" . htmlspecialchars($student['date_of_birth']) . "' required><br><br>

                    <button type='submit'>Save Changes</button>
                  </form>";
        } else {
            // If not admin, just display the student information
            echo "<p><strong>Student ID:</strong> " . $student['student_id'] . "</p>";
            echo "<p><strong>Name:</strong> " . $student['name'] . "</p>";
            echo "<p><strong>Email:</strong> " . $student['email'] . "</p>";
            echo "<p><strong>Phone:</strong> " . $student['phone'] . "</p>";
            echo "<p><strong>Age:</strong> " . $student['age'] . "</p>";
            echo "<p><strong>Address:</strong> " . $student['address'] . "</p>";
            echo "<p><strong>Date of Birth:</strong> " . $student['date_of_birth'] . "</p>";
        }
        
        // Fetch current subjects of the student
        $subjects_sql = "
            SELECT subjects.subject_id, subjects.name, subjects.description
            FROM student_section_subject sss
            INNER JOIN section_subject ss ON sss.section_subject_id = ss.section_subject_id
            INNER JOIN subjects ON ss.subject_id = subjects.subject_id
            WHERE sss.student_id = $student_id";

        $subjects_result = $conn->query($subjects_sql);

        echo "<h3>Current Subjects</h3>";

        if ($subjects_result->num_rows > 0) {
            echo "<ul>";
            while ($subject = $subjects_result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>Subject ID:</strong> " . $subject['subject_id'] . "<br>";
                echo "<strong>Name:</strong> " . $subject['name'] . "<br>";
                echo "<strong>Description:</strong> " . $subject['description'] . "<br>";
                echo "</li><hr>";
            }
            echo "</ul>";
        } else {
            echo "<p>No subjects found for this student.</p>";
        }

    } else {
        echo "<p>No student found with ID $student_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <style>
        /* Just added sum styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        h3 {
            margin-top: 40px;
        }
    </style>
</head>
<body>

</body>
</html>
