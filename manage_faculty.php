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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header><h1>Manage Faculty</h1></header>
        <table class="styled-table">
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
                        <td><?= htmlspecialchars($faculty['faculty_id']) ?></td>
                        <td><?= htmlspecialchars($faculty['username']) ?></td>
                        <td><?= htmlspecialchars($faculty['name']) ?></td>
                        <td><?= htmlspecialchars($faculty['email']) ?></td>
                        <td><?= htmlspecialchars($faculty['phone']) ?></td>
                        <td><?= htmlspecialchars($faculty['age']) ?></td>
                        <td><?= htmlspecialchars($faculty['hire_date']) ?></td>
                        <td>
                            <a class="edit-link edit-btn" href="view_faculty.php?faculty_id=<?= htmlspecialchars($faculty['faculty_id']) ?>">Edit</a> |
                            <a class="delete-link delete-btn" href="manage_faculty.php?delete=<?= htmlspecialchars($faculty['faculty_id']) ?>" onclick="return confirm('Are you sure you want to delete this faculty member?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
