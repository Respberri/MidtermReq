<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch student and subject details
$student_id = intval($_GET['student_id'] ?? 0);
$subject_id = intval($_GET['subject_id'] ?? 0);
$student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();
$subject = $conn->query("SELECT * FROM subjects WHERE subject_id = $subject_id")->fetch_assoc();

if (!$student || !$subject) {
    echo "Invalid student or subject ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_id = intval($_POST['grade_id'] ?? 0); // Default to 0 if not set
    $new_grade = floatval($_POST['grade']);

    // Validate the grade
    if ($new_grade < 0 || $new_grade > 100) {
        echo "<p class='message error'>Invalid grade. Please enter a value between 0 and 100.</p>";
        exit();
    }

    if ($grade_id > 0) {
        // Update the specific period's grade
        $update_sql = "UPDATE grades SET grade = ? WHERE grade_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("di", $new_grade, $grade_id);
    } else {
        // Insert a new grade if grade_id is not set
        $insert_sql = "INSERT INTO grades (student_id, subject_id, period, grade) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);

        // Determine the period based on the form submission context
		error_log("Period . " . $_POST['period']);
        $period = intval($_POST['period'] ?? 0); // Ensure the period is provided
        if ($period < 1 || $period > 4) {
            echo "<p class='message error'>Invalid period. Please provide a valid period (1-4).</p>";
            exit();
        }

        $stmt->bind_param("iiid", $student_id, $subject_id, $period, $new_grade);
    }

    if ($stmt->execute()) {
        // Recalculate the final grade for the student
        $grades_sql = "
            SELECT grade 
            FROM grades 
            WHERE student_id = ? AND subject_id = ? AND period BETWEEN 1 AND 4
        ";
        $calc_stmt = $conn->prepare($grades_sql);
        $calc_stmt->bind_param("ii", $student_id, $subject_id);
        $calc_stmt->execute();
        $result = $calc_stmt->get_result();

        $total = 0;
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $total += $row['grade'];
            $count++;
        }

        $final_grade = $count > 0 ? round($total / $count, 2) : null;

        // Update or insert the final grade
        if ($final_grade !== null) {
            $final_grade_sql = "
                INSERT INTO grades (student_id, subject_id, period, grade)
                VALUES (?, ?, 5, ?)
                ON DUPLICATE KEY UPDATE grade = VALUES(grade)
            ";
            $final_stmt = $conn->prepare($final_grade_sql);
            $final_stmt->bind_param("iid", $student_id, $subject_id, $final_grade);
            $final_stmt->execute();
        }

        echo "<p class='message success'>Grade updated successfully, and final grade recalculated!</p>";
        header("Location: view_student_grades.php?student_id=$student_id&subject_id=$subject_id");
        exit();
    } else {
        echo "<p class='message error'>Error updating grade: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Fetch grades for the student, grouped by period (avoiding duplication)
$grades_sql = "
    SELECT g.grade_id, sub.name AS subject_name, g.period, MAX(g.grade) AS grade
    FROM grades g
    INNER JOIN subjects sub ON g.subject_id = sub.subject_id
    WHERE g.student_id = $student_id AND g.subject_id = $subject_id
    GROUP BY g.period
    ORDER BY g.period
";
$grades = $conn->query($grades_sql);

// Initialize data for final grade calculation
$period_grades = [];
$total_grade = 0;
$period_count = 0;

while ($grade_row = $grades->fetch_assoc()) {
    $period = intval($grade_row['period']);
    $grade = floatval($grade_row['grade']);
    
    if ($period >= 1 && $period <= 4) { // Include only regular periods for average calculation
        $total_grade += $grade;
        $period_count++;
    }
    
    $period_grades[$period] = $grade_row; // Store grade details for rendering
}

// Calculate the final grade (average of periods 1-4)
$final_grade = $period_count > 0 ? round($total_grade / $period_count, 2) : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?></title>
    <link rel="stylesheet" href="page.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 200px; /* Assuming sidebar width */
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .message {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .update-form {
            display: flex;
            align-items: center;
        }
        .update-form input[type="number"] {
            margin-right: 10px;
        }
        .update-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .update-form button:hover {
            background-color: #45a049;
        }
        .back-btn {
            padding: 10px 20px;
            background-color: #9e1f1f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-btn:hover {
            background-color: #be9191 ;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h1>Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?></h1>
        </header>
        <form method="POST">
            <button type="button" onclick="window.history.back()" class="back-btn">Back</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
				<?php for ($period = 1; $period <= 5; $period++): ?>
					<tr>
						<td><?= $period == 5 ? "Final" : "Period " . htmlspecialchars($period) ?></td>
						<td>
							<?php
							if ($period < 5): // Display grades for periods 1-4
								$grade = $period_grades[$period]['grade'] ?? null;
								echo htmlspecialchars($grade ?? 'N/A');
							else: // Display the calculated final grade for period 5
								echo htmlspecialchars($final_grade ?? 'N/A');
							endif;
							?>
						</td>
						<td>
							<?php if ($period < 5): // Allow updates only for periods 1-4 ?>
								<form method="post" class="update-form">
									<input type="hidden" name="grade_id" value="<?= htmlspecialchars($period_grades[$period]['grade_id'] ?? '') ?>">
									<input type="hidden" name="period" value=<?=htmlspecialchars($period)?>>
									<input type="number" step="0.01" name="grade" value="<?= htmlspecialchars($grade ?? '') ?>" required>
									<button type="submit">Update</button>
								</form>
							<?php else: // No actions for the calculated final grade ?>
								<span></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endfor; ?>
			</tbody>
        </table>
    </div>
</body>
</html>
