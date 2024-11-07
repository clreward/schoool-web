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
    $address_district = $_POST['address_district'];
    $address_ward = $_POST['address_ward'];
    $address_village = $_POST['address_village'];
    $address_street = $_POST['address_street'];
    $alleges = isset($_POST['alleges']) ? (int)$_POST['alleges'] : 0;
    $disorder = isset($_POST['disorder']) ? (int)$_POST['disorder'] : 0;
    $disorder_details = $_POST['disorder_details'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $marital_status = $_POST['marital_status'];
    $nida_number = $_POST['nida_number'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Start transaction
    $conn->begin_transaction();

    try {
        // SQL Query to insert staff data
        $query_staff = "INSERT INTO staff (
                        staff_registration_number, first_name, middle_name, surname, gender, age, 
                        address_region, address_district, address_ward, address_village, address_street, 
                        alleges, disorder, disorder_details, phone, email, marital_status, nida_number
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare statement for staff
        if ($stmt_staff = $conn->prepare($query_staff)) {
            $stmt_staff->bind_param(
                "sssssssssssiisissi",
                $staff_registration_number, $first_name, $middle_name, $surname, $gender, $age,
                $address_region, $address_district, $address_ward, $address_village, $address_street,
                $alleges, $disorder, $disorder_details, $phone, $email, $marital_status, $nida_number
            );

            if (!$stmt_staff->execute()) {
                throw new Exception("Error inserting into staff table: " . $stmt_staff->error);
            }
        } else {
            throw new Exception("Error preparing staff statement: " . $conn->error);
        }

        // SQL Query to insert user login data
        $query_login = "INSERT INTO user_login (staff_registration_number, password, role) VALUES (?, ?, ?)";

        // Prepare statement for user login
        if ($stmt_login = $conn->prepare($query_login)) {
            $stmt_login->bind_param("sss", $staff_registration_number, $password, $role);

            if (!$stmt_login->execute()) {
                throw new Exception("Error inserting into user_login table: " . $stmt_login->error);
            }
        } else {
            throw new Exception("Error preparing user_login statement: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();

        // Redirect to success page
        header("Location: register_staff_education.php?staff_registration_number=" . urlencode($staff_registration_number));
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close statements
    if ($stmt_staff) {
        $stmt_staff->close();
    }
    if ($stmt_login) {
        $stmt_login->close();
    }

    // Close the database connection
    $conn->close();
}
?>
