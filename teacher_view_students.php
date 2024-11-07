<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Teacher
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit;
}

// Get form and subject from query parameters
$form = $_GET['form'] ?? '';
$subject = $_GET['subject'] ?? '';

// Validate parameters
if (empty($form) || empty($subject)) {
    die("Invalid request.");
}

// Fetch students for the given form and subject
$query = "
    SELECT 
        students.registration_number,
        students.first_name,
        students.middle_name,
        students.surname,
        students.gender
    FROM students
    JOIN student_subjects ON students.registration_number = student_subjects.student_id
    JOIN subjects ON student_subjects.subject_id = subjects.subject_id
    WHERE students.form = ? AND subjects.subject_name = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Failed to prepare the SQL statement: " . $conn->error);
}

$stmt->bind_param("ss", $form, $subject);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Failed to fetch students: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
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
    </style>
</head>
<body>

<h1>Students in Form <?php echo htmlspecialchars($form); ?> for Subject: <?php echo htmlspecialchars($subject); ?></h1>

<table>
    <thead>
        <tr>
            <th>Registration Number</th>
            <th>Name</th>
            <th>gender</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['surname']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>

            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
