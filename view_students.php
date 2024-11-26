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

// Fetch students from the database based on the selected form level
$query = "SELECT * FROM students WHERE form = {$form_level}";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Error: " . $conn->error);
}

// Fetch students by form level
$students_by_form = [];
while ($row = $result->fetch_assoc()) {
    $students_by_form[] = $row;
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

    <h1>Student List</h1>

    <!-- Menu for form selection -->
    <div class="menu">
        <a href="?form=1">Form 1</a>
        <a href="?form=2">Form 2</a>
        <a href="?form=3">Form 3</a>
        <a href="?form=4">Form 4</a>
        <a href="?form=5">Form 5</a>
        <a href="?form=6">Form 6</a>
    </div>

    <h2>Form <?php echo $form_level; ?> Students</h2>

    <?php if (count($students_by_form) > 0): ?>
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
                <?php foreach ($students_by_form as $row): ?>
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

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
