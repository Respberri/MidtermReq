<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM subjects WHERE subject_id = $delete_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Subject deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting subject: " . $conn->error . "');</script>";
    }
}

// Fetch subjects
$subjects = $conn->query("SELECT subject_id, name, description, credits FROM subjects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subjects</title>
</head>
<body>
    <h2>Subjects</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Credits</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $subjects->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['subject_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['credits']; ?></td>
                    <td>
                        <a href="edit_subject.php?subject_id=<?php echo $row['subject_id']; ?>">Edit</a>
                        |
                        <a href="?delete_id=<?php echo $row['subject_id']; ?>" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <br>
    <a href="create_subject.php">Add New Subject</a>
</body>
</html>