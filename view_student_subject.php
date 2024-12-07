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
        .menu {
            margin-bottom: 20px;
            text-align: center;
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
        .header {
            margin-bottom: 20px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
        }
        .header img {
            width: 50px;
            height: auto;
            vertical-align: middle;
        }
        .header p {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        .footer {
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            font-size: 14px;
        }
        h2 {
            text-align: center;
            color: #333; /* Optional: Set a color for better visibility */
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Students Subjects</h1>
        <!-- <p><img src="school_logo.png" alt="School Logo"> Your School Name</p> -->
    </div>

    <!-- Menu for form selection -->
    <div class="menu">
        <a href="academic_dashboard.php" class="home-btn">Go Home</a>
        <a href="?form=1">Form 1</a>
        <a href="?form=2">Form 2</a>
        <a href="?form=3">Form 3</a>
        <a href="?form=4">Form 4</a>
        <a href="?form=5">Form 5</a>
        <a href="?form=6">Form 6</a>
    </div>

    <?php
    include('db_connection.php');
    session_start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
        header("Location: login.php");
        exit;
    }

    $form_level = isset($_GET['form']) ? (int)$_GET['form'] : 1;

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

        $count = 1;
        while ($student = $student_result->fetch_assoc()) {
            $subject_query = "
                SELECT sub.subject_name
                FROM subjects sub
                WHERE sub.form = {$form_level}
            ";

            $subject_result = $conn->query($subject_query);

            $subjects = [];
            while ($subject = $subject_result->fetch_assoc()) {
                $subjects[] = $subject['subject_name'];
            }

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

    $conn->close();
    ?>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 All rights reserved. Designed by Clifford.</p>
    </div>
</body>
</html>
