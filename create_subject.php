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
        $success_message = "Subject Created Successfully!";
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

        <!-- Display Success/Error Popup Message -->
        <?php if (isset($success_message)): ?>
            <div class="popup-message" id="popup-message">
                <?= $success_message ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="popup-message error" id="popup-message">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Subject Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <button type="submit">Create Subject</button>
        </form>
    </div>

    <!-- JavaScript to fade out the popup message -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popupMessage = document.getElementById('popup-message');
            if (popupMessage) {
                // Fade-out effect after 3 seconds
                setTimeout(function() {
                    popupMessage.style.animation = 'fadeOut 1s ease-in-out forwards';
                }, 3000); // Wait 3 seconds before starting fade-out
            }
        });
    </script>
</body>
</html>
