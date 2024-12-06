<?php
require_once 'db.php';

session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Pre-selected user_id from URL (if provided)
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

// Handle faculty creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']); // User ID of the faculty (from user table)
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = intval($_POST['age']);
    $hire_date = $_POST['hire_date'];

    // Insert faculty into the database
    $sql = "INSERT INTO faculty (user_id, name, email, phone, age, hire_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississ", $user_id, $name, $email, $phone, $age, $hire_date);

    if ($stmt->execute()) {
        echo "<p>Faculty created successfully!</p>";
    } else {
        echo "<p>Error creating faculty: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Fetch available users (for dropdown)
$users = $conn->query("SELECT user_id, username FROM users WHERE role = 'faculty'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Faculty</title>
    <script>
        function filterUserDropdown() {
            const input = document.getElementById('user_search');
            const filter = input.value.toLowerCase();
            const options = document.querySelectorAll('#user_id option');

            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(filter)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <h2>Create Faculty Member</h2>
    <form method="post" action="">
        <label for="user_search">Search for User (Faculty):</label>
        <input type="text" id="user_search" onkeyup="filterUserDropdown()" placeholder="Search for a user..."><br><br>
        
        <label for="user_id">Assign User ID:</label>
        <select id="user_id" name="user_id" required>
            <option value="">Select User</option>
            <?php while ($user = $users->fetch_assoc()): ?>
                <option value="<?= $user['user_id'] ?>" 
						<?= $user['user_id'] == $selected_user_id ? 'selected' : '' ?>>
					<?= $user['username'] ?>
				</option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required><br><br>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required><br><br>

        <label for="hire_date">Hire Date:</label>
        <input type="date" id="hire_date" name="hire_date" required><br><br>

        <button type="submit">Create Faculty</button>
    </form>
</body>
</html>
