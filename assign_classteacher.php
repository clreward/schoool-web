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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teacher to Class</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(240, 240, 240);
        }
        .header, .footer {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px auto;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .menu {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .menu .go-home-btn {
            margin-right: auto;
        }
        .menu a {
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }
        .menu a.active {
            background-color: #45a049;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        button:hover {
            background-color: #45a049;
        }
        .go-home-btn {
            display: inline-block;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-left: 10px;
        }
        .go-home-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>Assign Teacher to Class</h1>
</div>

<!-- Content -->
<div class="content">
    <div class="menu">
        <a href="academic_dashboard.php" class="go-home-btn">Go Home</a>
    </div>

    <!-- Form -->
    <form action="" method="POST">
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
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> School Management System. All rights reserved.</p>
</div>

<?php
// Close the database connection
$conn->close();
?>

</body>
</html>
