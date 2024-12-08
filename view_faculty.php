<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if the logged-in user is an admin
$is_admin = $_SESSION['role'] === 'admin';

// Check if faculty_id parameter is provided
if (isset($_GET['faculty_id'])) {
    $faculty_id = intval($_GET['faculty_id']); // Sanitize input

    // Fetch faculty details
    $sql = "SELECT * FROM faculty WHERE faculty_id = $faculty_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $faculty = $result->fetch_assoc();

        // Handle form submission for updates (if the user is admin)
        if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $updated_name = $_POST['name'];
            $updated_email = $_POST['email'];
            $updated_phone = $_POST['phone'];
            $updated_age = $_POST['age'];
            $updated_hire_date = $_POST['hire_date'];

            $update_sql = "
                UPDATE faculty 
                SET name = ?, email = ?, phone = ?, age = ?, hire_date = ? 
                WHERE faculty_id = ?
            ";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssssi", $updated_name, $updated_email, $updated_phone, $updated_age, $updated_hire_date, $faculty_id);

            if ($stmt->execute()) {
                echo "<p>Faculty details updated successfully!</p>";
                // Reload the page to reflect changes
                header("Refresh:0");
            } else {
                echo "<p>Error updating faculty: " . $conn->error . "</p>";
            }
            $stmt->close();
        }

        // Fetch section-subject assignments for the faculty
        $section_subjects_sql = "
            SELECT ss.section_id, sections.section_name AS section_name, subjects.name AS subject_name, ss.subject_id 
            FROM faculty_section_subject fss
            INNER JOIN section_subject ss ON fss.section_subject_id = ss.section_subject_id
            INNER JOIN sections ON ss.section_id = sections.section_id
            INNER JOIN subjects ON ss.subject_id = subjects.subject_id
            WHERE fss.faculty_id = $faculty_id
        ";

        $section_subjects_result = $conn->query($section_subjects_sql);
    } else {
        echo "<p>No faculty member found with ID $faculty_id.</p>";
    }
} else {
    echo "<p>Invalid Usage.</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profile</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header><h1>Faculty Profile</h1></header>

        <?php if (isset($faculty)): ?>
            <?php if ($is_admin): ?>
                <!-- Admin can edit the faculty details -->
                <form method="POST">
                    <label>Name:</label><br>
                    <input type="text" name="name" value="<?= htmlspecialchars($faculty['name']) ?>" required><br><br>

                    <label>Email:</label><br>
                    <input type="email" name="email" value="<?= htmlspecialchars($faculty['email']) ?>" required><br><br>

                    <label>Phone:</label><br>
                    <input type="text" name="phone" value="<?= htmlspecialchars($faculty['phone']) ?>" required><br><br>

                    <label>Age:</label><br>
                    <input type="number" name="age" value="<?= htmlspecialchars($faculty['age']) ?>" required><br><br>

                    <label>Hire Date:</label><br>
                    <input type="date" name="hire_date" value="<?= htmlspecialchars($faculty['hire_date']) ?>" required><br><br>

                    <button type="submit" class="btn">Save Changes</button>
                    <a href="manage_faculty.php" class="btn">Back</a>
                </form>
            <?php else: ?>
                <!-- Display faculty details for non-admin users -->
                <p><strong>Faculty ID:</strong> <?= htmlspecialchars($faculty['faculty_id']) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($faculty['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($faculty['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($faculty['phone']) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($faculty['age']) ?></p>
                <p><strong>Hire Date:</strong> <?= htmlspecialchars($faculty['hire_date']) ?></p>
            <?php endif; ?>

            <!-- Fetch section-subject assignments for the faculty -->
            <Header style="margin-top: 20px"><h1>Assigned Section-Subjects</h1></Header>
            <?php if ($section_subjects_result->num_rows > 0): ?>
                <ul>
                    <?php while ($assignment = $section_subjects_result->fetch_assoc()): ?>
                        <li>
                            <strong>Section Name:</strong> <?= htmlspecialchars($assignment['section_name']) ?><br><br>
                            <strong>Subject Name:</strong> <?= htmlspecialchars($assignment['subject_name']) ?><br>
                        </li>
                        <hr>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No section-subject assignments found for this faculty.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Ensure the connection is closed after all queries are done
$conn->close();
?>
