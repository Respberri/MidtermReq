<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $sql = "INSERT INTO subjects (name, description) VALUES ('$name', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "Subject created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Subject</title>
</head>
<body>
    <?php include 'sidebar.php' ?>
    <h2>Create a New Subject</h2>
    <form method="POST" action="">
        <label for="name">Subject Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <button type="submit">Create Subject</button>
    </form>
</body>
</html>
