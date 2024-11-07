<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from form
    $teacher_registration_number = $_POST['teacher_registration_number'];
    $form = $_POST['form'];
    $subject_id = $_POST['subject_id'];

    // Prepare the SQL statement
    $query = "INSERT INTO teacher_assignments (teacher_registration_number, form, subject_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("ssi", $teacher_registration_number, $form, $subject_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "Teacher assigned successfully.";
        header("Location: academic_view_teachers.php"); // Redirect to the teachers page after assignment
        exit;
    } else {
        echo "Error assigning teacher: " . $stmt->error;
    }
    
    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
