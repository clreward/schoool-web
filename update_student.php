<?php
// Include database connection
include('db_connection.php');

// Get the registration_number from the URL
$registration_number = isset($_GET['registration_number']) ? $_GET['registration_number'] : '';

if ($registration_number && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Fetch the student details based on the registration_number
    $query = "SELECT * FROM students WHERE registration_number = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $registration_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    // $first_name = $_POST['first_name'];
    // $middle_name = $_POST['middle_name'];
    // $surname = $_POST['surname'];
    // $gender = $_POST['gender'];
    // $age = $_POST['age'];

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
    
    // Add additional fields as necessary

    // Update the student information
    $query = "UPDATE students SET 
                first_name = ?, 
                middle_name = ?, 
                surname = ?, 
                gender = ?, 
                age = ? ,
                address_region = ?,
                address_district = ?,
                address_ward = ?,
                address_village = ?,
                address_street = ?,
                alleges = ?,
                disorder = ?,
                disorder_details = ?,
                form = ?
              WHERE registration_number = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssssssssssssii",
                // $first_name, $middle_name, $surname, $gender, $age,$registration_number);
                $first_name, $middle_name, $surname, $gender, $age, $address_region, $address_district, $address_ward, $address_village, $address_street, $alleges, $disorder, $disorder_details, $form, $registration_number);
        
        if ($stmt->execute()) {
            echo "Student information has been updated successfully.";
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

    // Redirect back to the student list after updating
    header("Location: view_student.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Student Information</title>
</head>
<body>
    <h2>Update Student Information</h2>
    <form action="update_student.php?registration_number=<?php echo htmlspecialchars($registration_number); ?>" method="POST">
        
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required><br>
         
        <label for="middle_name">Middle Name:</label>
        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>"><br>
        
        <label for="surname">Surname:</label>
        <input type="text" name="surname" value="<?php echo htmlspecialchars($student['surname']); ?>" required><br>

        <label for="gender">Gender:</label>
        <input type="text" name="gender" value="<?php echo htmlspecialchars($student['gender']); ?>"><br>
        
        <label for="age">dob:</label>
        <input type="text" name="age" value="<?php echo htmlspecialchars($student['age']); ?>"><br>
        <!-- more update-->
        
        <label for="address_region">Region:</label>
        <input type="text" name="address_region" value="<?php echo htmlspecialchars($student['address_region']); ?>"><br>

        <label for="address_district">District:</label>
        <input type="text" name="address_district" value="<?php echo htmlspecialchars($student['address_district']); ?>"><br>

        <label for="address_ward">Ward:</label>
        <input type="text" name="address_ward" value="<?php echo htmlspecialchars($student['address_ward']); ?>"><br>

        <label for="address_village">Village:</label>
        <input type="text" name="address_village" value="<?php echo htmlspecialchars($student['address_village']); ?>"><br>

        <label for="address_street">Street:</label>
        <input type="text" name="address_street" value="<?php echo htmlspecialchars($student['address_street']); ?>"><br>

        <label for="alleges">Alleges:</label>
        <input type="datextte" name="alleges" value="<?php echo htmlspecialchars($student['alleges']); ?>"><br>

        <label for="disorder">Disorder:</label>
        <input type="text" name="disorder" value="<?php echo htmlspecialchars($student['disorder']); ?>"><br>

        <label for="disorder_details">Disorder details:</label>
        <input type="text" name="disorder_details" value="<?php echo htmlspecialchars($student['disorder_details']); ?>"><br>

        <label for="form">Form:</label>
        <input type="text" name="form" value="<?php echo htmlspecialchars($student['form']); ?>"><br>
        
        <!--Add additional fields here as needed -->
        
        <input type="submit" value="Update">
    </form>
    
    <a href="view_student.php">Back to Student List</a>
</body>
</html>
