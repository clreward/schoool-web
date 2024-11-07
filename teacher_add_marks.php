<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Teacher
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit;
}

// Check if staff_registration_number is set in the session
if (!isset($_SESSION['user_id'])) {
    die("Staff registration number not found in session.");
}

// Get the teacher's registration number from session
$teacher_registration_number = $_SESSION['user_id'];

// Fetch teacher's assigned forms and subjects
$query = "
    SELECT 
        teacher_assignments.form, 
        subjects.subject_name
    FROM teacher_assignments
    JOIN subjects ON teacher_assignments.subject_id = subjects.subject_id
    WHERE teacher_assignments.teacher_registration_number = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Failed to prepare the SQL statement: " . $conn->error);
}
$stmt->bind_param("s", $teacher_registration_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Failed to fetch assignments: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assigned Forms and Subjects</title>
    <style>
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
        .view-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .view-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Assigned Forms and Subjects</h1>

<table>
    <thead>
        <tr>
            <th>Form</th>
            <th>Subject</th>
            <th>View Students</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch and display results
        $assignments = [];
        while ($row = $result->fetch_assoc()) {
            $assignments[$row['form']][] = $row['subject_name'];
        }

        foreach ($assignments as $form => $subjects) {
            foreach ($subjects as $subject) {
                echo "<tr>
                    <td>$form</td>
                    <td>$subject</td>
                    <td><button class='view-btn' onclick='viewStudents(\"$form\", \"$subject\")'>Add Marks</button></td>
                </tr>";
            }
        }
        ?>
    </tbody>
</table>

<script>
function viewStudents(form, subject) {
    // Redirect to the students page with form and subject as query parameters
    window.location.href = `add_marks.php?form=${encodeURIComponent(form)}&subject=${encodeURIComponent(subject)}`;
}
</script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
