<?php
// Include necessary files and initialize session
include('db_connection.php');
session_start();

// Check if the user is logged in and has the Academic role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Get student registration number from URL
if (!isset($_GET['registration_number']) || empty($_GET['registration_number'])) {
    die("Registration number is missing.");
}
$registration_number = $_GET['registration_number'];

// Fetch student details
$query = "SELECT form FROM students WHERE registration_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $registration_number); // Adjusted to 's' for varchar type
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

$form = $student['form'];

// Fetch subjects based on form level
$available_subjects = [];

if ($form == '1') {
    $query = "SELECT * FROM subjects WHERE form = '1'";
} elseif ($form == '2') {
    $query = "SELECT * FROM subjects WHERE form = '2'";
} elseif ($form == '3') {
    $query = "SELECT * FROM subjects WHERE form = '3'";
} elseif ($form == '4') {
    $query = "SELECT * FROM subjects WHERE form = '4'";
} elseif ($form == '5' || $form == '6') {
    $query = "SELECT * FROM subjects WHERE form = '5' OR form = '6'";
} else {
    die("Invalid form level.");
}

$result = $conn->query($query);
if ($result) {
    $available_subjects = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Error fetching subjects: " . $conn->error);
}

// Handle form submission to insert/update subjects
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_subjects = $_POST['subjects'] ?? [];

    // Remove subjects that were unchecked (delete them from student_subjects)
    if (!empty($selected_subjects)) {
        // Remove subjects that are not selected by checking 'subject_id' in student_subjects
        $delete_query = "DELETE FROM student_subjects WHERE student_id = ? AND subject_id NOT IN (" . implode(',', array_fill(0, count($selected_subjects), '?')) . ")";
        $stmt = $conn->prepare($delete_query);
        $params = array_merge([$registration_number], $selected_subjects);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params); // Adjusting bind_param for multiple subject IDs
        if (!$stmt->execute()) {
            die("Error deleting unchecked subjects: " . $stmt->error);
        }

        // Insert newly selected subjects
        foreach ($selected_subjects as $subject_id) {
            // Check if the subject already exists for the student
            $check_query = "SELECT 1 FROM student_subjects WHERE student_id = ? AND subject_id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("si", $registration_number, $subject_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                // Insert subject if not already assigned
                $insert_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("si", $registration_number, $subject_id);
                if (!$stmt->execute()) {
                    die("Error inserting new subjects: " . $stmt->error);
                }
            }
        }
    } else {
        // If no subjects are selected, clear all subjects
        $delete_query = "DELETE FROM student_subjects WHERE student_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("s", $registration_number);
        if (!$stmt->execute()) {
            die("Error deleting all subjects for student: " . $stmt->error);
        }
    }

    // Redirect to view_student.php after updating subjects
    header("Location: view_students.php");
    exit;
}

// Fetch and display current subjects for this student
$query = "SELECT * FROM student_subjects WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $registration_number); // Adjusted to 's' for varchar type
$stmt->execute();
$current_subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
</head>
<body>

    <h1>Manage Subjects for Student Registration Number: <?php echo htmlspecialchars($registration_number); ?></h1>

    <form method="POST" action="">
        <h2>Select Subjects</h2>
        <?php foreach ($available_subjects as $subject): ?>
            <input type="checkbox" name="subjects[]" value="<?php echo htmlspecialchars($subject['subject_id']); ?>" 
                <?php echo in_array($subject['subject_id'], array_column($current_subjects, 'subject_id')) ? 'checked' : ''; ?>>
            <?php echo htmlspecialchars($subject['subject_name']); ?><br>
        <?php endforeach; ?>
        <input type="submit" value="Update Subjects">
    </form>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
