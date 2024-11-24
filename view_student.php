<?php
// Usage:
// header("Location: view_student.php?student_id=$student_id");


require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
</head>
<body>

<?php
// Check if student_id parameter is provided
if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']); // Sanitize input

    // Fetch student details
    $sql = "SELECT * FROM students WHERE student_id = $student_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();

        echo "<h2>Student Profile</h2>";
        echo "<p><strong>Student ID:</strong> " . $student['student_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . $student['name'] . "</p>";
        echo "<p><strong>Email:</strong> " . $student['email'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $student['phone'] . "</p>";
        echo "<p><strong>Age:</strong> " . $student['age'] . "</p>";
        echo "<p><strong>Address:</strong> " . $student['address'] . "</p>";
        echo "<p><strong>Date of Birth:</strong> " . $student['date_of_birth'] . "</p>";
        echo "<p><strong>Enrollment Date:</strong> " . $student['enrollment_date'] . "</p>";
		
		// Fetch current subjects of the student
        $subjects_sql = "
            SELECT subjects.subject_id, subjects.name, subjects.description, subjects.credits
            FROM student_subjects
            INNER JOIN subjects ON student_subjects.subject_id = subjects.subject_id
            WHERE student_subjects.student_id = $student_id";

        $subjects_result = $conn->query($subjects_sql);

        echo "<h3>Current Subjects</h3>";

        if ($subjects_result->num_rows > 0) {
            echo "<ul>";
            while ($subject = $subjects_result->fetch_assoc()) {
                echo "<li>";
                echo "<strong>Subject ID:</strong> " . $subject['subject_id'] . "<br>";
                echo "<strong>Name:</strong> " . $subject['name'] . "<br>";
                echo "<strong>Description:</strong> " . $subject['description'] . "<br>";
                echo "<strong>Credits:</strong> " . $subject['credits'] . "<br>";
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
</body>