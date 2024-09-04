<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $registration_number = $_POST['registration_number'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $address_region = $_POST['address_region'];
    $address_district = $_POST['address_district'];
    $address_ward = $_POST['address_ward'];
    $address_village = $_POST['address_village'];
    $address_street = $_POST['address_street'];
    $alleges = $_POST['alleges'];
    $disorder = $_POST['disorder'];
    $disorder_details = $_POST['disorder_details'];
    $form = $_POST['form'];

    // Handling file upload
    $profile_photo = $_FILES['profile_photo']['tmp_name'];
    $profile_photo_blob = file_get_contents($profile_photo);

    // SQL Query to insert student data
    $query = "INSERT INTO students (
                registration_number, first_name, middle_name, surname, gender, age, 
                address_region, address_district, address_ward, address_village, address_street, 
                alleges, disorder, disorder_details, form, profile_photo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param(
            "sssssiissssiiisb",
            $registration_number, $first_name, $middle_name, $surname, $gender, $age,
            $address_region, $address_district, $address_ward, $address_village, $address_street,
            $alleges, $disorder, $disorder_details, $form, $profile_photo_blob
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to parent registration form with the registration_number as a GET parameter
            header("Location: register_parent.php?registration_number=" . urlencode($registration_number));
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
