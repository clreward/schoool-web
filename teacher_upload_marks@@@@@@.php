<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Teacher
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit;
}

// Get the selected form and subject from query parameters
$form = isset($_GET['form']) ? $_GET['form'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';

// Fetch students in the selected form
$query = "SELECT registration_number, first_name, middle_name, surname FROM students WHERE form = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $form);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if ($result === false) {
    die("Failed to fetch students: " . $conn->error);
}

// Handle form submission for adding marks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['student_id'] as $key => $student_id) {
        $subject_id = $_POST['subject_id'][$key];
        $test_marks = $_POST['test_marks'][$key];
        $midterm_marks = $_POST['midterm_marks'][$key];
        $final_marks = $_POST['final_marks'][$key];

        // Calculate total_marks, average_marks, and grade
        $total_marks = $test_marks + $midterm_marks + $final_marks;
        $average_marks = $total_marks / 3;

        $grade = ($average_marks >= 80) ? 'A' :
                 (($average_marks >= 70) ? 'B' :
                 (($average_marks >= 60) ? 'C' : 'D'));

        $gpa = ($grade === 'A') ? 4.0 :
               (($grade === 'B') ? 3.0 :
               (($grade === 'C') ? 2.0 : 1.0));

        $division = ($average_marks >= 60) ? 'First' : 'Second';

        // Insert or update marks into the `student_results` table
        $insert_query = "INSERT INTO student_results 
                        (student_id, subject_id, test_marks, midterm_marks, final_marks, grade, gpa, total_marks, average_marks, division) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        test_marks = VALUES(test_marks), 
                        midterm_marks = VALUES(midterm_marks),
                        final_marks = VALUES(final_marks),
                        grade = VALUES(grade),
                        gpa = VALUES(gpa),
                        total_marks = VALUES(total_marks),
                        average_marks = VALUES(average_marks),
                        division = VALUES(division)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("siddiddsss", $student_id, $subject_id, $test_marks, $midterm_marks, $final_marks, $grade, $gpa, $total_marks, $average_marks, $division);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            echo "<div class='alert alert-danger'>Failed to add marks for student: $student_id</div>";
        }
    }

    echo "<div class='alert alert-success'>Marks uploaded successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Marks for Form <?php echo htmlspecialchars($form); ?> - Subject: <?php echo htmlspecialchars($subject); ?></title>
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
        .submit-btn {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h1>Upload Marks for Form <?php echo htmlspecialchars($form); ?> - Subject: <?php echo htmlspecialchars($subject); ?></h1>

<form action="teacher_upload_marks.php?form=<?php echo urlencode($form); ?>&subject=<?php echo urlencode($subject); ?>" method="POST">
    <table>
        <thead>
            <tr>
                <th>Registration Number</th>
                <th>Name</th>
                <th>Test Marks</th>
                <th>Midterm Marks</th>
                <th>Final Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['surname']); ?></td>
                    <td><input type="number" name="test_marks[]" step="0.01" required></td>
                    <td><input type="number" name="midterm_marks[]" step="0.01" required></td>
                    <td><input type="number" name="final_marks[]" step="0.01" required></td>
                    <input type="hidden" name="student_id[]" value="<?php echo htmlspecialchars($row['registration_number']); ?>">
                    <input type="hidden" name="subject_id[]" value="<?php echo htmlspecialchars($subject); ?>"> <!-- Assuming subject_id is passed here -->
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="submit-btn">
        <button type="submit">Upload Marks</button>
    </div>
</form>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
