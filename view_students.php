<?php
// Include necessary files and initialize session
include('db_connection.php');
session_start();

// Check if the user is logged in and has the Academic role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Fetch student details from the single students table
$query = "SELECT * FROM students";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error: " . $conn->error);
}

// Fetch students by form level
$students_by_form = [];
while ($row = $result->fetch_assoc()) {
    $form = $row['form'];
    if (!isset($students_by_form[$form])) {
        $students_by_form[$form] = [];
    }
    $students_by_form[$form][] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <style>
        /* Add styles for the table */
        table {
            margin-bottom: 20px;
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    <h1>Student List</h1>

    <?php foreach (range(1, 6) as $form_level): ?>
        <h2>Form <?php echo $form_level; ?> Students</h2>
        <?php if (isset($students_by_form[$form_level]) && count($students_by_form[$form_level]) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Registration Number</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Surname</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students_by_form[$form_level] as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['surname']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><a href="manage_subjects.php?registration_number=<?php echo htmlspecialchars($row['registration_number']); ?>">Manage Subjects</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students found in Form <?php echo $form_level; ?>.</p>
        <?php endif; ?>
    <?php endforeach; ?>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
