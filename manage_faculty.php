<?php
require_once 'db.php';

session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Handle deletion of a faculty record
if (isset($_GET['delete'])) {
    $faculty_id = intval($_GET['delete']);
    $sql = "DELETE FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $faculty_id);

    if ($stmt->execute()) {
        echo "<p>Faculty member deleted successfully!</p>";
    } else {
        echo "<p>Error deleting faculty member: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Fetch all faculty records
$faculty_records = $conn->query("SELECT f.faculty_id, f.name, f.email, f.phone, f.age, f.hire_date, u.username FROM faculty f JOIN users u ON f.user_id = u.user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Faculty</title>
</head>
<body>
    <h2>Manage Faculty</h2>
    <a href="create_faculty.php">Add New Faculty Member</a>
    <br><br>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Faculty ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Hire Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($faculty = $faculty_records->fetch_assoc()): ?>
                <tr>
                    <td><?= $faculty['faculty_id'] ?></td>
                    <td><?= $faculty['username'] ?></td>
                    <td><?= $faculty['name'] ?></td>
                    <td><?= $faculty['email'] ?></td>
                    <td><?= $faculty['phone'] ?></td>
                    <td><?= $faculty['age'] ?></td>
                    <td><?= $faculty['hire_date'] ?></td>
                    <td>
                        <a href="view_faculty.php?faculty_id=<?= $faculty['faculty_id'] ?>">Edit</a> |
                        <a href="manage_faculty.php?delete=<?= $faculty['faculty_id'] ?>" onclick="return confirm('Are you sure you want to delete this faculty member?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
