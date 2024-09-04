<?php
// Include necessary files and initialize session
include('db_connection.php');
session_start();

// Check if the user is logged in and has the Academic role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Fetch students and their subjects from the students table
$form_levels = range(1, 6);
foreach ($form_levels as $form_level) {
    $query = "
        SELECT s.registration_number, s.first_name, s.middle_name, s.surname, s.gender, 
               GROUP_CONCAT(sub.subject_name SEPARATOR ', ') AS subjects
        FROM students s
        LEFT JOIN student_subjects ss ON s.registration_number = ss.student_id
        LEFT JOIN subjects sub ON ss.subject_id = sub.subject_id
        WHERE s.form = '{$form_level}'
        GROUP BY s.registration_number, s.first_name, s.middle_name, s.surname, s.gender
    ";
    $result = $conn->query($query);

    if (!$result) {
        die("Error: " . $conn->error);
    }

    $students = $result->fetch_all(MYSQLI_ASSOC);
    echo "<h2>Form {$form_level} Students</h2>";
    if (count($students) > 0) {
        $count = 1;
        echo "<table border='1'>";
        echo "<thead><tr><th>S/N</th><th>Registration Number</th><th>First Name</th><th>Middle Name</th><th>Surname</th><th>Gender</th><th>Subjects</th></tr></thead>";
        echo "<tbody>";
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . htmlspecialchars($student['registration_number']) . "</td>";
            echo "<td>" . htmlspecialchars($student['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($student['middle_name']) . "</td>";
            echo "<td>" . htmlspecialchars($student['surname']) . "</td>";
            echo "<td>" . htmlspecialchars($student['gender']) . "</td>";
            echo "<td>" . htmlspecialchars($student['subjects']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No students found in Form {$form_level}.</p>";
    }
}

// Close database connection
$conn->close();
?>
