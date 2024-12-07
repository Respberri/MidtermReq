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
$subjects = $conn->query("SELECT subject_id, name, description FROM subjects");

function setActiveStat($pageName) {
    $currentFile = basename($_SERVER['PHP_SELF'], ".php");
    return $currentFile === $pageName ? 'active-stat' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects Dashboard</title>
    <link rel="stylesheet" href="main.css">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php' ?>
    
    <div class="content-container">
        <div class="main-content">
            <main>
                    <header>
                    <h1>Subjects</h1>
                    </header>
                    <table class="subject-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $subjects->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['subject_id']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td>
                                        <a href="edit_subject.php?subject_id=<?php echo $row['subject_id']; ?>" class="edit-btn">Edit</a>
                                        |
                                        <a href="?delete_id=<?php echo $row['subject_id']; ?>" onclick="return confirm('Are you sure you want to delete this subject?');" class="delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <br>
                    <a href="courses.php" class="add-new-subject-btn">Add New Subject</a>
            </main>
        </div>
    </div>
</body>
</html>
