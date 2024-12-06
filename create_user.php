<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

$new_user_id = null;

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Consider hashing this in a real-world app
    $role = $_POST['role']; // Role can be 'admin', 'faculty', or 'student'

    // Insert user into the database
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        $user_id  = $stmt->insert_id; // Get the newly created user ID
        echo "<p>User created successfully!</p>";
		
		if ($role === 'student') {
			echo "<a href='create_student.php?user_id=$user_id'>Click here to create a student profile for this user</a>";
        } elseif ($role === 'faculty') {
            echo "<a href='create_faculty.php?user_id=$user_id'>Click here to create a faculty profile for this user</a>";
        }
    } else {
        echo "<p>Error creating user: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
</head>
<body>
    <h2>Create User</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="faculty">Faculty</option>
            <option value="student">Student</option>
        </select><br><br>

        <button type="submit">Create User</button>
    </form>

    <?php if ($new_user_id && isset($_POST['role'])): ?>
        <?php if ($_POST['role'] === 'faculty'): ?>
            <p><a href="create_faculty.php?user_id=<?= $new_user_id ?>">Continue to Create Faculty</a></p>
        <?php elseif ($_POST['role'] === 'student'): ?>
            <p><a href="create_student.php?user_id=<?= $new_user_id ?>">Continue to Create Student</a></p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
