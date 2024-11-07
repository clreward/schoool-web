<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $staff_registration_number = $_POST['staff_registration_number'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL Query to insert into user_login
    $query = "INSERT INTO user_login (staff_registration_number, role, password) VALUES (?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("sss", $staff_registration_number, $role, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to view staff page or another page after successful insertion
            header("Location: view_staff.php");
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
