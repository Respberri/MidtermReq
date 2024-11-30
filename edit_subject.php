<?php
require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: incorrect.php');
    exit();
}

// Fetch subject for editing
$subject_id = $_GET['subject_id'];
$subject = $conn->query("SELECT * FROM subjects WHERE subject_id = $subject_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $credits = $_POST['credits'];

    $sql = "UPDATE subjects 
            SET name = '$name', description = '$description', credits = '$credits' 
            WHERE subject_id = $subject_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Subject updated successfully.'); window.location.href = 'subjects_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link rel="stylesheet" href="main.css"> <!-- Add your external CSS file here -->
    <style>
        /* Main layout and typography */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url("/images/bmsiBG.jpg") no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
            color: #333;
        }

        .container:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: rgb(158, 31, 31); 
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 500;
        }

        label {
            font-size: 16px;
            color: rgb(158, 31, 31);
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 3px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease-in-out;
        }

        input[type="text"]:focus, input[type="number"]:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 14px 20px;
            text-align: center;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease-in-out;
            margin-bottom: 25px;
        }

        button:hover {
            background-color: #27ae60; 
        }

        .cancel-btn {
            background-color: #e74c3c; 
            width: 100%;
            padding: 14px;
            text-align: center;
            font-size: 18px;
            border-radius: 8px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.3s ease-in-out;
        }

        .cancel-btn:hover {
            background-color: #be9191; 
        }

        /* Adding responsive design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }

            h2 {
                font-size: 28px;
            }

            label, input[type="text"], input[type="number"], textarea, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Subject</h2>

        <form method="POST" action="">
            <label for="name">Subject Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($subject['description']); ?></textarea>

            <label for="credits">Credits:</label>
            <input type="number" id="credits" name="credits" value="<?php echo htmlspecialchars($subject['credits']); ?>" required>

            <button type="submit">Update Subject</button>
            <a href="subjects_dashboard.php" class="cancel-btn">Cancel</a>
        </form>
    </div>

</body>
</html>