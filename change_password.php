<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

$message = "";

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        $message = "<p style='color:red;'>New password and confirm password do not match.</p>";
    } else {
        // Fetch current password from database
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
		
        if (!$user || $current_password != $user['password']) {
            $message = "<p style='color:red;'>Current password is incorrect.</p>";
        } else {
            // Update the password in the database
            $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_password, $user_id);

            if ($update_stmt->execute()) {
                $message = "<p style='color:green;'>Password changed successfully!</p>";
            } else {
                $message = "<p style='color:red;'>Error updating password. Please try again later.</p>";
            }

            $update_stmt->close();
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content-container">
<div class="main-content">
    <header>
    <h1>Change Password</h1>
    </header>
    <?= $message ?>
    <form method="post" action="">
        <label for="current_password">Current Password:</label><br>
        <input type="password" id="current_password" name="current_password" required><br><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>

        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <button type="submit">Change Password</button>
    </form>
    </div>
    </div>
</body>
</html>
