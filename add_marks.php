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
$subject_name = isset($_GET['subject']) ? $_GET['subject'] : '';

// Fetch the subject_id using the subject name
$query = "SELECT subject_id FROM subjects WHERE subject_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $subject_name);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $subject_id = $row['subject_id'];
} else {
    die("Invalid subject name.");
}

// Fetch students in the selected form
$query = "SELECT registration_number, first_name, middle_name, surname FROM students WHERE form = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $form);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Failed to fetch students: " . $conn->error);
}

// Initialize status variables
$success = true;
$errors = [];

// Handle form submission for test, midterm, or final marks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks_type = $_POST['marks_type'];
    $marks_column = ($marks_type === 'test') ? 'test_marks' :
                    (($marks_type === 'midterm') ? 'midterm_marks' : 'final_marks');

    $student_ids = $_POST['student_ids'];
    $marks = $_POST['marks'];

    for ($i = 0; $i < count($student_ids); $i++) {
        $student_id = $student_ids[$i];
        $mark = $marks[$i];

        // Check if the student already has an entry for the subject in the student_results table
        $check_query = "SELECT result_id FROM student_results WHERE student_id = ? AND subject_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("si", $student_id, $subject_id);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check->num_rows > 0) {
            // If exists, update the marks
            $update_query = "UPDATE student_results SET $marks_column = ? WHERE student_id = ? AND subject_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("dsi", $mark, $student_id, $subject_id);
        } else {
            // Otherwise, insert a new row
            $insert_query = "INSERT INTO student_results (student_id, subject_id, $marks_column) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sid", $student_id, $subject_id, $mark);
        }

        if (!$stmt->execute()) {
            $success = false;
            $errors[] = "Error for student $student_id: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Marks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Enter Marks for Form <?php echo htmlspecialchars($form); ?> - Subject: <?php echo htmlspecialchars($subject_name); ?></h2>

        <form method="POST" action="">
            <div class="form-group">
                <label for="marks_type">Select Marks Type:</label>
                <select class="form-control" id="marks_type" name="marks_type" required>
                    <option value="test">Test Marks</option>
                    <option value="midterm">Midterm Marks</option>
                    <option value="final">Final Marks</option>
                </select>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Registration Number</th>
                        <th>Name</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($student = $result->fetch_assoc()) {
                            $student_id = htmlspecialchars($student['registration_number']);
                            $student_name = htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['surname']);
                            echo "<tr>";
                            echo "<td><input type='hidden' name='student_ids[]' value='$student_id' />$student_id</td>";
                            echo "<td>$student_name</td>";
                            echo "<td><input type='number' name='marks[]' class='form-control' required min='0' max='100'></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No students found for this form.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Submit Marks</button>
        </form>

        <?php
        // Display success or error message
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($success) {
                echo "<div class='alert alert-success mt-3'>Marks updated successfully for all students.</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>";
                foreach ($errors as $error) {
                    echo "$error<br>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>