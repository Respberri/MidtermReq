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

    $sql = "INSERT INTO subjects (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $description);

    if ($stmt->execute()) {
        $success_message = "Subject created successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subject</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    <div class="main-content">
        <header>
            <h1>Create a New Subject</h1>
        </header>
        <?php if (isset($success_message)): ?>
            <p><?= $success_message ?></p>
        <?php elseif (isset($error_message)): ?>
            <p><?= $error_message ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="name">Subject Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <button type="submit">Create Subject</button>
        </form>
    </div>
</body>
</html>
