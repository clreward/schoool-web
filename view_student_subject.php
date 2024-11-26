<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Subjects</title>
    <style>
        body {
            background-color: rgb(240, 240, 240);
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .assign-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .assign-btn:hover {
            background-color: #45a049;
        }
        .menu {
            margin-bottom: 20px;
        }
        .menu a {
            padding: 10px 20px;
            margin-right: 10px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }
        .menu a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Students Subjects</h1>

    <!-- Menu for form selection -->
    <div class="menu">
        <a href="?form=1">Form 1</a>
        <a href="?form=2">Form 2</a>
        <a href="?form=3">Form 3</a>
        <a href="?form=4">Form 4</a>
        <a href="?form=5">Form 5</a>
        <a href="?form=6">Form 6</a>
    </div>

    <?php
    // Include necessary files and initialize session
    include('db_connection.php');
    session_start();

    // Check if the user is logged in and has the Academic role
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
        header("Location: login.php");
        exit;
    }

    // Check if a form is selected, default to form 1 if not
    $form_level = isset($_GET['form']) ? (int)$_GET['form'] : 1;

    // Query to get all students in the selected form level
    $student_query = "
        SELECT registration_number, first_name, middle_name, surname, gender
        FROM students 
        WHERE form = {$form_level}
    ";

    $student_result = $conn->query($student_query);

    if ($student_result->num_rows > 0) {
        echo "<h2>Form {$form_level} Students</h2>";
        echo "<table>";
        echo "<thead><tr><th>S/N</th><th>Registration Number</th><th>Names</th><th>Gender</th><th>Subjects</th></tr></thead>";
        echo "<tbody>";

        // Fetch each student in the selected form level
        $count = 1;
        while ($student = $student_result->fetch_assoc()) {
            // Get subjects for the current form level
            $subject_query = "
                SELECT sub.subject_name
                FROM subjects sub
                WHERE sub.form = {$form_level}
            ";

            $subject_result = $conn->query($subject_query);

            // Store subjects in an array
            $subjects = [];
            while ($subject = $subject_result->fetch_assoc()) {
                $subjects[] = $subject['subject_name'];
                
                // Check if the subject is already assigned to the student
                $check_query = "SELECT 1 FROM student_subjects WHERE student_id = ? AND subject_id = ?";
                $stmt = $conn->prepare($check_query);
                $stmt->bind_param("si", $student['registration_number'], $subject['subject_id']);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    // Insert subject if not already assigned
                    $insert_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($insert_query);
                    $stmt->bind_param("si", $student['registration_number'], $subject['subject_id']);
                    $stmt->execute();
                }
            }

            // Display student details and subjects
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . htmlspecialchars($student['registration_number']) . "</td>";
            echo "<td>" . htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['surname']) . "</td>";
            echo "<td>" . htmlspecialchars($student['gender']) . "</td>";
            echo "<td>" . (!empty($subjects) ? implode(', ', $subjects) : "No subjects assigned") . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No students found in Form {$form_level}.</p>";
    }

    // Close database connection
    $conn->close();
    ?>

</body>
</html>
