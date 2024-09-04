<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $staff_registration_number = $_POST['staff_registration_number'];
    $primary_level = $_POST['primary_level'];
    $secondary_level = $_POST['secondary_level'];
    $advanced_level = $_POST['advanced_level'];
    $other_level = $_POST['other_level'];

    // Initialize file variables
    $secondary_level_file_blob = null;
    $advanced_level_file_blob = null;
    $other_level_file_blob = null;

    // Handle file uploads
    if (isset($_FILES['secondary_level_file']) && $_FILES['secondary_level_file']['error'] == 0) {
        $secondary_level_file_blob = file_get_contents($_FILES['secondary_level_file']['tmp_name']);
    }

    if (isset($_FILES['advanced_level_file']) && $_FILES['advanced_level_file']['error'] == 0) {
        $advanced_level_file_blob = file_get_contents($_FILES['advanced_level_file']['tmp_name']);
    }

    if (isset($_FILES['other_level_file']) && $_FILES['other_level_file']['error'] == 0) {
        $other_level_file_blob = file_get_contents($_FILES['other_level_file']['tmp_name']);
    }

    // SQL Query to insert staff education data
    $query = "INSERT INTO staff_education (
                staff_registration_number, primary_level, secondary_level, advanced_level, other_level,
                secondary_level_file, advanced_level_file, other_level_file
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param(
            "ssssssss",
            $staff_registration_number, $primary_level, $secondary_level, $advanced_level, $other_level,
            $secondary_level_file_blob, $advanced_level_file_blob, $other_level_file_blob
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to the "Next of Kin" registration page after successful insertion
            header("Location: register_next_of_kin.php?staff_registration_number=" . $staff_registration_number);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
