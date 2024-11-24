<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch subject for editing
$subject_id = $_GET['subject_id'];
$subject = $conn->query("SELECT * FROM subjects WHERE subject_id = $subject_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $credits = $_POST['credits'];

    $sql = "UPDATE subjects 
            SET name = '$name', description = '$description', credits = '$credits' 
            WHERE subject_id = $subject_id";

    if ($conn->query($sql) === TRUE) {
        echo "Subject updated successfully.";
        header('Location: subjects_dashboard.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
</head>
<body>
    <h2>Edit Subject</h2>
    <form method="POST" action="">
        <label for="name">Subject Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $subject['name']; ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required><?php echo $subject['description']; ?></textarea><br><br>

        <label for="credits">Credits:</label><br>
        <input type="number" id="credits" name="credits" value="<?php echo $subject['credits']; ?>" required><br><br>

        <button type="submit">Update Subject</button>
    </form>
</body>
</html>