<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $enrollment_date = date('Y-m-d');

    $sql = "INSERT INTO student_subjects (student_id, subject_id, enrollment_date) 
            VALUES ($student_id, $subject_id, '$enrollment_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Subject assigned to student successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch students and subjects for dropdowns
$students = $conn->query("SELECT student_id, name FROM students");
$subjects = $conn->query("SELECT subject_id, name FROM subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Subject</title>
</head>
<body>
    <h2>Assign Subject to Student</h2>
    <form method="POST" action="">
        <label for="student_id">Select Student:</label><br>
        <select id="student_id" name="student_id" required>
            <option value="">--Select Student--</option>
            <?php while ($row = $students->fetch_assoc()) { ?>
                <option value="<?php echo $row['student_id']; ?>">
                    <?php echo $row['name']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label for="subject_id">Select Subject:</label><br>
        <select id="subject_id" name="subject_id" required>
            <option value="">--Select Subject--</option>
            <?php while ($row = $subjects->fetch_assoc()) { ?>
                <option value="<?php echo $row['subject_id']; ?>">
                    <?php echo $row['name']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <button type="submit">Assign Subject</button>
    </form>
</body>
</html>
