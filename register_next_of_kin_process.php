<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $staff_registration_number = $_POST['staff_registration_number'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $surname = $_POST['surname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $address_region = $_POST['address_region'];
    $address_ward = $_POST['address_ward'];
    $address_village = $_POST['address_village'];
    $address_street = $_POST['address_street'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $relationship = $_POST['relationship'];
    $marital_status = $_POST['marital_status'];
    $nida_number = $_POST['nida_number'];

    // SQL Query to insert next of kin data
    $query = "INSERT INTO next_of_kin (
                staff_registration_number, first_name, middle_name, surname, gender, age, address_region, address_ward,
                address_village, address_street, phone, email, relationship, marital_status, nida_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param(
            "sssssssssssssss",
            $staff_registration_number, $first_name, $middle_name, $surname, $gender, $age, $address_region, $address_ward,
            $address_village, $address_street, $phone, $email, $relationship, $marital_status, $nida_number
        );

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            // echo "Next of Kin information has been registered successfully.";
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
