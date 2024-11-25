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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>
<body>
<div class="sidebar">
    <h2>Welcome, Admin!</h2>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="project.php">Students</a></li>
        <li class="dropdown">
            <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Courses</a>
            <div class="dropdown-content">
                <a href="courses.php">Manage Subjects</a>
                <a href="subjects_dashboard.php">View Subjects</a>
            </div>
        </li>
        <li><a href="members.php">Departments</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<script>
    // Function to toggle the dropdown visibility
    function toggleDropdown(event) {
        const dropdownContent = event.target.nextElementSibling; // Get the dropdown content (div)
        
        // Toggle the 'show' class which controls visibility
        dropdownContent.classList.toggle('show');
        
        // Close the dropdown if clicked anywhere outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                dropdownContent.classList.remove('show');
            }
        });
    }
</script>


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
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['department']); ?></td>
                                <td>
                                    <a href="view_student.php?id=<?php echo $student['student_id']; ?>" class="btn view-btn">View</a>
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
