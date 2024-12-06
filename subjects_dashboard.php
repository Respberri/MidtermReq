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
<div class="sidebar">
        <img class="bmsi-logo" src="/images/bmsi-logo.png" alt="logo of bmsi">
        <h2>Welcome, Admin!</h2>
        <div class="menu">
            <div class="item"><a href="dashboard.php"><i class="fa-solid fa-house"></i>Home</a></div>
            <div class="item"><a href="project.php"><i class="fa-solid fa-graduation-cap"></i>Students</a></div>
            <div class="item dropdown">
            <a href="#" class="dropbtn" onclick="toggleDropdown(event)">
        <i class="fa-solid fa-book"></i>Subjects
    </a>
    <div class="dropdown-content">
        <a href="courses.php">Manage Subjects</a>
        <a href="subjects_dashboard.php">View Subjects</a>
    </div>
</div>

            <div class="item"><a class="sub-btn"><i class="fa-solid fa-circle-info"></i>More
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
            <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <a href="mission_vision.php" class="sub-item">Mission & Vision</a>
                <a href="members.php" class="sub-item">Developers</a>
            </div>
        </div>
            <div class="item"><a href="logout.php"><i class="fa-solid fa-circle-left"></i>Logout</a></div>
        </div>
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

    <div class="content-container">
        <div class="main-content">
            <header>
                <h1>Courses</h1>
            </header>
            <main>
                <div class="stats">
                    <div class="stat">
                        <img src="/images/student.png" alt="Students">
                        <h2>Students</h2>
                    </div>
                    <div class="stat <?php echo setActiveStat('subjects_dashboard'); ?>">
                        <img src="/images/course.png" alt="Courses">
                            <h2>Courses</h2>
                    </div>
                    <div class="stat">
                        <img src="/images/department.png" alt="Departments">
                        <h2>Departments</h2>
                    </div>
                </div>

                <section>
                    <h2>Subjects</h2>
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
                </section>
            </main>
        </div>
    </div>

    <script>
        // Function to toggle the dropdown visibility
        function toggleDropdown(event) {
            const dropdownContent = event.target.nextElementSibling; // Get the dropdown content (div)
            dropdownContent.classList.toggle('show');
            
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    dropdownContent.classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>
