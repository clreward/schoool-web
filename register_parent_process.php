<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $student_id = $_POST['student_id'];
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
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $relationship = $_POST['relationship'];
    $marital_status = $_POST['marital_status'];
    $nida_number = $_POST['nida_number'];

    // SQL Query to insert parent data
    $query = "INSERT INTO student_parents (
                student_id, first_name, middle_name, surname, gender, age, 
                address_region, address_district, address_ward, address_village, address_street,
                phone, email, relationship, marital_status, nida_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param(
            "issssisssssisssi",
            $student_id, $first_name, $middle_name, $surname, $gender, $age,
            $address_region, $address_district, $address_ward, $address_village, $address_street,
            $phone, $email, $relationship, $marital_status, $nida_number
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to a success page or show a success message
            echo "Parent registered successfully.";
            // Or redirect to a different page if needed
            // header("Location: success_page.php");
            // exit();
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
