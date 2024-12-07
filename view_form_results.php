<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form from the query string (if provided)
$form = isset($_GET['form']) ? (int)$_GET['form'] : 1; // Default to Form 1 if no form is selected

// Fetch subjects for the selected form
$sql_subjects = "
    SELECT s.subject_id, s.subject_name
    FROM subjects s
    JOIN student_subjects ss ON s.subject_id = ss.subject_id
    JOIN students st ON ss.student_id = st.registration_number
    WHERE st.form = ?
";

if ($stmt_subjects = $conn->prepare($sql_subjects)) {
    $stmt_subjects->bind_param("i", $form);
    $stmt_subjects->execute();
    $result_subjects = $stmt_subjects->get_result();

    $subjects = [];
    while ($row = $result_subjects->fetch_assoc()) {
        $subjects[$row['subject_id']] = $row['subject_name'];
    }

    // Prepare the SQL query to fetch student results for the selected form
    $sql_students = "
        SELECT 
            s.registration_number, 
            CONCAT(s.first_name, ' ', s.middle_name, ' ', s.surname) AS names, 
            s.gender
        FROM students s
        WHERE s.form = ?
    ";

    if ($stmt_students = $conn->prepare($sql_students)) {
        $stmt_students->bind_param("i", $form);
        $stmt_students->execute();
        $result_students = $stmt_students->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            background-color: white;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .menu {
            margin: 20px auto;
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
        .footer {
            margin-top: 20px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Student Results</h1>
    </div>

    <!-- Menu for form selection and Go Home button -->
    <div class="menu">
        <a href="academic_dashboard.php" class="btn">Go Home</a>
        <?php foreach ([1, 2, 3, 4, 5, 6] as $form_num): ?>
            <a href="?form=<?php echo $form_num; ?>" class="<?php echo $form_num == $form ? 'btn btn-outline-light' : ''; ?>">Form <?php echo $form_num; ?></a>
        <?php endforeach; ?>
    </div>

    <h3 class="text-center">Form <?php echo $form; ?> Students</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Registration Number</th>
                <th>Names</th>
                <th>Gender</th>
                <?php foreach ($subjects as $subject_name): ?>
                    <th colspan="4"><?php echo $subject_name; ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <?php foreach ($subjects as $subject_name): ?>
                    <th>Test</th>
                    <th>Midtest</th>
                    <th>Final</th>
                    <th>Total</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $result_students->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                    <td><?php echo htmlspecialchars($student['names']); ?></td>
                    <td><?php echo htmlspecialchars($student['gender']); ?></td>
                    <?php foreach ($subjects as $subject_id => $subject_name): ?>
                        <?php
                        // Fetch student results for this subject
                        $sql_results = "
                            SELECT 
                                test_marks, 
                                midterm_marks, 
                                final_marks, 
                                (COALESCE(test_marks, 0) + COALESCE(midterm_marks, 0) + COALESCE(final_marks, 0)) AS total
                            FROM student_results
                            WHERE student_id = ? AND subject_id = ? 
                        ";

                        $stmt_results = $conn->prepare($sql_results);
                        $stmt_results->bind_param("si", $student['registration_number'], $subject_id);
                        $stmt_results->execute();
                        $result_results = $stmt_results->get_result();
                        $result_data = $result_results->fetch_assoc();
                        ?>
                        <td><?php echo isset($result_data['test_marks']) ? $result_data['test_marks'] : '...'; ?></td>
                        <td><?php echo isset($result_data['midterm_marks']) ? $result_data['midterm_marks'] : '...'; ?></td>
                        <td><?php echo isset($result_data['final_marks']) ? $result_data['final_marks'] : '...'; ?></td>
                        <td><?php echo isset($result_data['total']) ? $result_data['total'] : '--'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; 2024 School Management System | Designed by Clifford</p>
    </div>

</body>
</html>

<?php
        $stmt_students->close();
    } else {
        echo "Error preparing students query: " . $conn->error;
    }

    $stmt_subjects->close();
} else {
    echo "Error preparing subjects query: " . $conn->error;
}

// Close the connection
$conn->close();
?>
