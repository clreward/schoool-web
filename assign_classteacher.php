<?php
// Include database connection
include('db_connection.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form and teacher data from the POST request
    $form = $_POST['form'];  // Form selected by the admin
    $teacher_registration_number = $_POST['teacher_registration_number'];  // Teacher registration number
    
    // Validate inputs
    if (empty($form) || empty($teacher_registration_number)) {
        echo "Please select both a form and a teacher.";
        exit();
    }

    // Insert the teacher-class assignment into the teacher_classes table
    $insertQuery = "INSERT INTO teacher_classes (staff_registration_number, form) VALUES (?, ?)";

    // Prepare the insert statement
    $stmt = $conn->prepare($insertQuery);

    // Check if preparation was successful
    if ($stmt === false) {
        echo "Error preparing insert statement: " . $conn->error;
        exit();
    }

    // Bind parameters to the insert statement
    $stmt->bind_param("ss", $teacher_registration_number, $form);

    // Execute the insert query
    if ($stmt->execute()) {
        echo "Teacher successfully assigned to Form " . $form . "!";
    } else {
        echo "Error assigning teacher to form: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Teacher to Class</title>
</head>
<body>

<form action="" method="POST">
    <!-- Form selection dropdown -->
    <label for="form">Select Form:</label>
    <select name="form" id="form" required>
        <option value="">--Select Form--</option>
        <?php
        // Fetch available forms from the students table (or any other table storing forms)
        $formQuery = "SELECT DISTINCT form FROM students";  // Adjust if necessary
        $formResult = $conn->query($formQuery);
        if ($formResult->num_rows > 0) {
            while ($row = $formResult->fetch_assoc()) {
                echo "<option value='" . $row['form'] . "'>Form " . $row['form'] . "</option>";
            }
        }
        ?>
    </select>

    <!-- Teacher selection dropdown -->
    <label for="teacher_registration_number">Select Teacher:</label>
    <select name="teacher_registration_number" id="teacher_registration_number" required>
        <option value="">--Select Teacher--</option>
        <?php
        // Fetch teachers from the staff table
        $teacherQuery = "
            SELECT s.staff_registration_number, s.first_name, s.surname 
            FROM staff s
            INNER JOIN user_login ul ON s.staff_registration_number = ul.staff_registration_number
            WHERE ul.role = 'Teacher'";
        $teacherResult = $conn->query($teacherQuery);
        if ($teacherResult->num_rows > 0) {
            while ($row = $teacherResult->fetch_assoc()) {
                echo "<option value='" . $row['staff_registration_number'] . "'>" . $row['first_name'] . " " . $row['surname'] . "</option>";
            }
        }
        ?>
    </select>

    <button type="submit">Assign Teacher</button>
</form>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
