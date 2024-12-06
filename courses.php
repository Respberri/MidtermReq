<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_subject'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $credits = $_POST['credits'];

        $sql = "INSERT INTO subjects (name, description, credits) VALUES ('$name', '$description', '$credits')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Subject created successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['assign_subject'])) {
        $student_id = $_POST['student_id'];
        $subject_id = $_POST['subject_id'];
        $enrollment_date = date('Y-m-d');

        $sql = "INSERT INTO student_subjects (student_id, subject_id, enrollment_date) 
                VALUES ($student_id, $subject_id, '$enrollment_date')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Subject assigned to student successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
        }
    }
}

// Fetch students and subjects for dropdowns
$students = $conn->query("SELECT student_id, name FROM students");
$subjects = $conn->query("SELECT subject_id, name FROM subjects");

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
    <title>Subjects</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
    integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                <h1>Subjects</h1>
            </header>
            <main>
                <div class="stats">
                    <div class="stat">
                        <img src="/images/student.png" alt="Students">
                        <h2>Students</h2>
                    </div>
                    <div class="stat <?php echo setActiveStat('courses'); ?>">
                        <img src="/images/course.png" alt="Courses">
                            <h2>Subjects</h2>
                    </div>
                    <div class="stat">
                        <img src="/images/department.png" alt="Departments">
                        <h2>Departments</h2>
                    </div>
                </div>
            </main>
        </div>

        <div class="main-content">
            <header>
                <h1>Manage Subjects</h1>
            </header>
            <main>
                <section>
                    <form method="POST" action="">
                        <h2>Create a New Subject</h2>
                        <input type="hidden" name="create_subject" value="1">
                        <label for="name">Subject Name:</label><br>
                        <input type="text" id="name" name="name" required><br><br>

                        <label for="description">Description:</label><br>
                        <textarea id="description" name="description" required></textarea><br><br>

                        <label for="credits">Credits:</label><br>
                        <input type="number" id="credits" name="credits" required><br><br>

                        <button type="submit">Create Subject</button>
                    </form>
                </section>
                <section>
                    <form method="POST" action="">
                        <h2>Assign Subject to Student</h2>
                        <input type="hidden" name="assign_subject" value="1">
                        <label for="student_id">Select Student:</label><br>
                        <select id="student_id" name="student_id" required>
                            <option value="">--Select Student--</option>
                            <?php while ($row = $students->fetch_assoc()) { ?>
                                <option value="<?php echo $row['student_id']; ?>">
                                    <?php echo $row['name']; ?>
                                </option>
                            <?php } ?>
                        </select><br><br>

                        <label for="subject_id">Select Subject:</label><br>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">--Select Subject--</option>
                            <?php while ($row = $subjects->fetch_assoc()) { ?>
                                <option value="<?php echo $row['subject_id']; ?>">
                                    <?php echo $row['name']; ?>
                                </option>
                            <?php } ?>
                        </select><br><br>

                        <button type="submit">Assign Subject</button>
                    </form>
                </section>
            </main>
        </div>
    </div>

    <!-- jquery for sub-menu toggle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.sub-btn').click(function(){
                $(this).next('.sub-menu').slideToggle();
                $(this).find('.dropdown').toggleClass('rotate');
            })
        })
    </script>
</body>
</html>
