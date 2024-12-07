<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if form is submitted
$popup_message = '';
$popup_type = '';  // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_name = $_POST['section_name'];
    $year_level = intval($_POST['year_level']);
    $year = intval($_POST['year']);

    // Insert section into the database
    $sql = "INSERT INTO sections (year_level, year, section_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $year_level, $year, $section_name);

    if ($stmt->execute()) {
        $popup_message = "Section Created Successfully!";
        $popup_type = "success";
    } else {
        $popup_message = "Error creating section: " . $conn->error;
        $popup_type = "error";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Section</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    
    <div class="main-content">
        <header><h1>Create Section</h1></header>
        
        <!-- Popup message -->
        <?php if ($popup_message): ?>
            <div id="popup" class="popup-message <?php echo $popup_type; ?>">
                <p><?php echo $popup_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="section_name">Section Name:</label>
            <input type="text" id="section_name" name="section_name" required><br><br>

            <label for="year_level">Year Level:</label>
            <input type="number" id="year_level" name="year_level" required><br><br>

            <label for="year">Year:</label>
            <input type="number" id="year" name="year" required><br><br>

            <button type="submit">Create Section</button>
        </form>
    </div>

    <script>
        // Function to show the popup (fade-in effect)
        function showPopup() {
            const popup = document.getElementById('popup');
            if (popup) {
                popup.style.animation = 'fadeIn 1s ease-in-out forwards'; // Trigger fade-in animation
            }
        }

        // Function to hide the popup after some time (fade-out effect)
        function closePopup() {
            const popup = document.getElementById('popup');
            if (popup) {
                popup.style.animation = 'fadeOut 1s ease-in-out forwards'; // Trigger fade-out animation
                setTimeout(() => {
                    popup.style.display = 'none'; // Hide the popup after fade-out
                }, 1000); // Match fade-out duration
            }
        }

        // Trigger the popup if there's a message
        <?php if ($popup_message): ?>
            setTimeout(showPopup, 100); // Show popup after the page loads
        <?php endif; ?>
    </script>
</body>
</html>
