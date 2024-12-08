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

// Fetch distinct names and emails for dropdown filters
$names_sql = "SELECT DISTINCT name FROM faculty";
$names_result = $conn->query($names_sql);

$emails_sql = "SELECT DISTINCT email FROM faculty";
$emails_result = $conn->query($emails_sql);

// Handle filtering faculty records based on selected name or email
$filter_name = isset($_POST['filter_name']) ? $_POST['filter_name'] : '%';
$filter_email = isset($_POST['filter_email']) ? $_POST['filter_email'] : '%';

// Fetch all faculty records with optional filters
$sql = "SELECT f.faculty_id, f.name, f.email, f.phone, f.age, f.hire_date, u.username 
        FROM faculty f 
        JOIN users u ON f.user_id = u.user_id
        WHERE f.name LIKE ? AND f.email LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $filter_name, $filter_email);
$stmt->execute();
$faculty_records = $stmt->get_result();
$stmt->close();
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

        <!-- Filter Form with Dropdowns -->
        <form method="POST" action="manage_faculty.php">
            <label for="filter_name">Filter by Name:</label>
            <select id="filter_name" name="filter_name">
                <option value="%" <?= $filter_name === '%' ? 'selected' : '' ?>>All</option>
                <?php while ($name = $names_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($name['name']) ?>" <?= $filter_name === $name['name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="filter_email">Filter by Email:</label>
            <select id="filter_email" name="filter_email">
                <option value="%" <?= $filter_email === '%' ? 'selected' : '' ?>>All</option>
                <?php while ($email = $emails_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($email['email']) ?>" <?= $filter_email === $email['email'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($email['email']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn">Apply Filter</button>
        </form>

        <!-- Faculty Table -->
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
                            <!-- View Profile Button -->
                            <a class="btn" href="view_faculty.php?faculty_id=<?= htmlspecialchars($faculty['faculty_id']) ?>">View Profile</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
