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

if ($form == '1' || $form == '2') {
    $query = "SELECT * FROM subjects WHERE form = '1' OR form = '2'";
} elseif ($form == '3' || $form == '4') {
    $query = "SELECT * FROM subjects WHERE form = '3' OR form = '4'";
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

// Handle form submission to insert subjects
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_subjects = $_POST['subjects'] ?? [];

    // Remove existing subjects for this student
    $delete_query = "DELETE FROM student_subjects WHERE student_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $registration_number); // Adjusted to 's' for varchar type
    if (!$stmt->execute()) {
        die("Error deleting old subjects: " . $stmt->error);
    }

    // Insert new subjects
    $insert_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    foreach ($selected_subjects as $subject_id) {
        $stmt->bind_param("si", $registration_number, $subject_id); // Adjusted to 'i' for integer type
        if (!$stmt->execute()) {
            die("Error inserting new subjects: " . $stmt->error);
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
            <input type="checkbox" name="subjects[]" value="<?php echo htmlspecialchars($subject['subject_id']); ?>" <?php echo in_array($subject['subject_id'], array_column($current_subjects, 'subject_id')) ? 'checked' : ''; ?>>
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