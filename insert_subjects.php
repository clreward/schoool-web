<?php
// Include necessary files and initialize session
include('db_connection.php');
session_start();

// Check if the user is logged in and has the Academic role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Fetch form level
$form = $_POST['form'] ?? '';

// Define subjects based on form level
$subjects = [];
if ($form == '1' || $form == '2') {
    $subjects = ['Mathematics', 'Physics', 'Chemistry', 'Biology', 'Geography', 'History', 'Civics', 'English', 'Kiswahili'];
} elseif ($form == '3' || $form == '4') {
    $subjects = ['Mathematics', 'Physics', 'Chemistry', 'Biology', 'Geography', 'History', 'Civics', 'English', 'Kiswahili'];
} elseif ($form == '5' || $form == '6') {
    $combination = ['CBG' => ['Biology', 'Chemistry', 'Geography', 'Mathematics'],
                    'CBA' => ['Biology', 'Chemistry', 'Agriculture', 'Mathematics'],
                    'PCB' => ['Physics', 'Chemistry', 'Biology', 'Mathematics'],
                    'PCM' => ['Physics', 'Chemistry', 'Mathematics'],
                    'PGM' => ['Physics', 'Geography', 'Mathematics'],
                    'HKL' => ['History', 'Kiswahili', 'Literature'],
                    'HGK' => ['History', 'Geography', 'Kiswahili']];
    // You need to handle this separately
    exit;
}

// Insert subjects into the `subjects` table
$insert_query = "INSERT INTO subjects (subject_name, form) VALUES (?, ?)";
$stmt = $conn->prepare($insert_query);

foreach ($subjects as $subject) {
    $stmt->bind_param("ss", $subject, $form);
    $stmt->execute();
}

// Redirect to the student list or manage subjects page
header("Location: view_students.php");
exit;

// Close database connection
$conn->close();
?>
