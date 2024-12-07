<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch students data from the database
$query = "SELECT * FROM students"; 
$result = $conn->query($query);

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
    <title>Students</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

</head>
<body>
<?php include 'sidebar.php' ?>

    <div class="main-content">
        <header>
            <h1>Students</h1>
        </header>
        <main>
            <div class="stats">
                <div class="stat <?php echo setActiveStat('project'); ?>">
                    <img src="/images/student.png" alt="Students">
                        <h2>Students</h2>
                </div>
                <div class="stat">
                    <img src="/images/course.png" alt="Courses">
                    <h2>Courses</h2>
                </div>
                <div class="stat">
                    <img src="/images/department.png" alt="Departments">
                    <h2>Departments</h2>
                </div>
            </div>
            
            <!-- Student List -->
            <div class="student-list">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td>
                                    <a href="view_student.php?id=<?php echo $student['student_id']; ?>" class="btn view-btn">View Profile</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
