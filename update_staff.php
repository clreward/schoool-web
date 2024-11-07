<?php
// Include database connection
include('db_connection.php');

// Get the $staff_registration_number = isset($_GET['staff_registration_number']) ? $_GET['staff_registration_number'] : '';
//  from the URL
$staff_registration_number = isset($_GET['staff_registration_number']) ? $_GET['staff_registration_number'] : '';

if ($staff_registration_number && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Fetch the staff details based on the staff_registration_number
    $query = "SELECT * FROM staff WHERE staff_registration_number = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $staff_registration_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    // Add additional fields as necessary

    // Update the staff information
    $query = "UPDATE staff SET 
                first_name = ?, 
                middle_name = ?, 
                surname = ?, 
                phone = ?, 
                email = ? 
                -- Add additional fields here 
              WHERE staff_registration_number = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssssi", 
                           $first_name, $middle_name, $surname, $phone, $email, $staff_registration_number);
        
        if ($stmt->execute()) {
            echo "Staff information has been updated successfully.";
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

    // Redirect back to the staff list after updating
    header("Location: view_staff.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Staff Information</title>
</head>
<body>
    <h2>Update Staff Information</h2>
    <form action="update_staff.php?staff_registration_number=<?php echo htmlspecialchars($staff_registration_number); ?>" method="POST">
        
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($staff['first_name']); ?>" required><br>
        
        <label for="middle_name">Middle Name:</label>
        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($staff['middle_name']); ?>"><br>
        
        <label for="surname">Surname:</label>
        <input type="text" name="surname" value="<?php echo htmlspecialchars($staff['surname']); ?>" required><br>
        
        
        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($staff['phone']); ?>"><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>"><br>
        
        <!-- Add additional fields here as needed -->
        
        <input type="submit" value="Update">
    </form>
    
    <a href="view_staff.php">Back to Staff List</a>
</body>
</html>
