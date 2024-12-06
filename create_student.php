<?php
require_once 'db.php';

session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Handle student creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']); // User ID of the student (from user table)
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = intval($_POST['age']);
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];

    // Insert student into the database
    $sql = "INSERT INTO students (user_id, name, email, phone, age, address, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississs", $user_id, $name, $email, $phone, $age, $address, $date_of_birth);

    if ($stmt->execute()) {
        echo "<p>Student created successfully!</p>";
    } else {
        echo "<p>Error creating student: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Fetch available users (for dropdown)
$users = $conn->query("SELECT user_id, username FROM users WHERE role = 'student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Student</title>
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
    <h2>Create Student</h2>
    <form method="post" action="">
        <label for="user_search">Search for User (Student):</label>
        <input type="text" id="user_search" onkeyup="filterUserDropdown()" placeholder="Search for a user..."><br><br>
        
        <label for="user_id">Assign User ID:</label>
        <select id="user_id" name="user_id" required>
            <option value="">Select User</option>
            <?php while ($user = $users->fetch_assoc()): ?>
                <option value="<?= $user['user_id'] ?>"><?= $user['username'] ?></option>
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

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required><br><br>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required><br><br>

        <button type="submit">Create Student</button>
    </form>
</body>
</html>
