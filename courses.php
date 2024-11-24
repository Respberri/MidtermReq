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
    <title>Dashboard</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="sidebar">
        <h2>Welcome, Admin!</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="project.php">Students</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="members.php">Departments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content-container">
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
            </header>
            <main>
                <div class="stats">
                    <div class="stat">
                        <img src="/images/student.png" alt="Students">
                        <h2>Students</h2>
                    </div>
                    <div class="stat <?php echo setActiveStat('courses'); ?>">
                        <img src="/images/course.png" alt="Courses">
                            <h2>Courses</h2>
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
                <h1>Manage Courses</h1>
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
</body>
</html>
