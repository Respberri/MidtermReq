<?php
require_once 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Check if the logged-in user is a student
if ($_SESSION['role'] !== 'student') {
    header('Location: incorrect.php');
    exit();
}

// Get the logged-in student's user_id
$user_id = $_SESSION['user_id'];

// Fetch student details
$student_sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "Invalid student ID.";
    exit();
}

$student_id = $student['student_id'];
$subject_id = intval($_GET['subject_id'] ?? 0);
$subject_sql = "SELECT * FROM subjects WHERE subject_id = ?";
$stmt = $conn->prepare($subject_sql);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject_result = $stmt->get_result();
$subject = $subject_result->fetch_assoc();
$stmt->close();

if (!$subject) {
    echo "Invalid subject ID.";
    exit();
}

// Fetch grades for the student
$grades_sql = "
    SELECT g.grade_id, g.period, MAX(g.grade) AS grade
    FROM grades g
    WHERE g.student_id = ? AND g.subject_id = ?
    GROUP BY g.period
    ORDER BY g.period
";
$stmt = $conn->prepare($grades_sql);
$stmt->bind_param("ii", $student_id, $subject_id);
$stmt->execute();
$grades = $stmt->get_result();
$stmt->close();

// Initialize variables for calculating the final grade
$total_grade = 0;
$period_count = 0;
$period_grades = [];

// Process grades for periods 1-4
while ($grade = $grades->fetch_assoc()) {
    $period = intval($grade['period']);
    $period_grades[$period] = floatval($grade['grade']);

    if ($period >= 1 && $period <= 4) {
        $total_grade += $grade['grade'];
        $period_count++;
    }
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
        .back-btn, .pdf-btn {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-btn {
            background-color: #9e1f1f;
        }
        .back-btn:hover {
            background-color: #be9191;
        }
        .pdf-btn {
            background-color: #007bff;
        }
        .pdf-btn:hover {
            background-color: #0056b3;
        }
    </style>
    <!-- Include jsPDF and jsPDF AutoTable -->
    <script src="/js/jspdf.umd.min.js"></script>
    <script src="/js/jspdf.plugin.autotable.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <header>
            <h1>Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?></h1>
        </header>
        <form>
            <button type="button" onclick="window.history.back()" class="back-btn">Back</button>
            <button type="button" onclick="generatePDF()" class="pdf-btn">Download PDF</button>
        </form>

        <table id="gradesTable">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
			<?php for ($period = 1; $period <= 5; $period++): ?>
				<tr>
					<td><?= $period == 5 ? "Final" : "Period " . htmlspecialchars($period) ?></td>
					<td>
						<?php
						if ($period < 5) {
							echo htmlspecialchars($period_grades[$period] ?? 'N/A');
						} else {
							echo htmlspecialchars($final_grade ?? 'N/A'); // Dynamically calculated final grade
						}
						?>
					</td>
				</tr>
			<?php endfor; ?>
</tbody>

        </table>
    </div>

    <script>
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text('Grades for <?= htmlspecialchars($student['name']) ?> in <?= htmlspecialchars($subject['name']) ?>', 14, 22);

            const gradesTable = document.getElementById('gradesTable');
            doc.autoTable({
                html: gradesTable,
                startY: 30,
                theme: 'striped',
                headStyles: { fillColor: [22, 160, 133] },
                styles: { halign: 'center' },
                head: [['Period', 'Grade']],
                body: Array.from(gradesTable.getElementsByTagName('tbody')[0].rows).map(row => Array.from(row.cells).map(cell => cell.textContent))
            });

            doc.save('grades.pdf');
        }
    </script>
</body>
</html>
