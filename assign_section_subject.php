<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch available sections
$sections = $conn->query("SELECT section_id, section_name FROM sections");

// Fetch available subjects
$subjects = $conn->query("SELECT subject_id, name FROM subjects");

// Handle form submission
$popup_message = '';
$popup_type = '';  // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_id = intval($_POST['section_id']);
    $selected_subjects = $_POST['subject_ids'] ?? []; // Array of selected subject IDs

    foreach ($selected_subjects as $subject_id) {
        $subject_id = intval($subject_id);

        // Insert section-subject mapping
        $sql = "INSERT INTO section_subject (section_id, subject_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $section_id, $subject_id);

        if (!$stmt->execute()) {
            $popup_message = "Error assigning subject ID $subject_id: " . $conn->error;
            $popup_type = "error";
            break; // Stop after the first error
        }
        $stmt->close();
    }

    if ($popup_message === '') {
        $popup_message = "Subjects Assigned Successfully!";
        $popup_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Section-Subject</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'sidebar.php' ?>
    
    <div class="main-content">
        <header>
        <h1>Assign Subjects to Section</h1>
        </header>
        <!-- Popup message -->
        <?php if ($popup_message): ?>
            <div id="popup" class="popup-message <?php echo $popup_type; ?>">
                <p><?php echo $popup_message; ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <label for="section_id">Select Section:</label>
            <select id="section_id" name="section_id" required>
                <?php while ($section = $sections->fetch_assoc()): ?>
                    <option value="<?= $section['section_id'] ?>"><?= $section['section_name'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <label>Select Subjects:</label><br>
            <?php while ($subject = $subjects->fetch_assoc()): ?>
                <input type="checkbox" name="subject_ids[]" value="<?= $subject['subject_id'] ?>">
                <?= $subject['name'] ?><br>
            <?php endwhile; ?><br>

            <button type="submit">Assign Subjects</button>
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
