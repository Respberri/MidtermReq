<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_name = $_POST['section_name'];
    $year_level = intval($_POST['year_level']);
    $year = intval($_POST['year']);

    // Insert section into the database
    $sql = "INSERT INTO sections (year_level, year, section_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $year_level, $year, $section_name);

    if ($stmt->execute()) {
        echo "<p>Section created successfully!</p>";
    } else {
        echo "<p>Error creating section: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Section</title>
</head>
<body>
    <h2>Create Section</h2>
    <form method="post" action="">
        <label for="section_name">Section Name:</label>
        <input type="text" id="section_name" name="section_name" required><br><br>

        <label for="year_level">Year Level:</label>
        <input type="number" id="year_level" name="year_level" required><br><br>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" required><br><br>

        <button type="submit">Create Section</button>
    </form>
</body>
</html>
