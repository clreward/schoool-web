<?php
// Include database connection
include('db_connection.php');

// Define an array to hold the forms
$forms = [1, 2, 3, 4, 5, 6]; // Add all the form levels you need

?>

<!DOCTYPE html>
<html>
<head>
    <title>Students Information</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Students Information</h2>
    
    <?php foreach ($forms as $form): ?>
        <?php
        // Fetch all student records for the current form
        $query = "SELECT student_id, registration_number, first_name, middle_name, surname, gender, form FROM students WHERE form = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $form);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>
        
        <h3>Form <?php echo $form; ?> Students</h3>
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <!-- <th>Student ID</th> -->
                    <th>Registration Number</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Surname</th>
                    <th>Gender</th>
                    <th>Form</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $count = 1; // Initialize counter for each form ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <!-- <td><?php echo $row['student_id']; ?></td> -->
                            <td><?php echo $row['registration_number']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['middle_name']; ?></td>
                            <td><?php echo $row['surname']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['form']; ?></td>
                            <td>
                                <a href="view_student_details.php?registration_number=<?php echo $row['registration_number']; ?>">View More</a>
                                <a href="view_parent.php?registration_number=<?php echo urlencode(htmlspecialchars($row['registration_number'])); ?>" class="btn btn-secondary">View Parental info</a>
                                <a href="update_student.php?registration_number=<?php echo $row['registration_number']; ?>">Update</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No students found for Form <?php echo $form; ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
    
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
