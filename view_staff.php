<?php
// Include database connection
include('db_connection.php');

// Fetch all staff records
$query = "SELECT staff_id, staff_registration_number, first_name, middle_name, surname, gender, phone, email FROM staff";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Information</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
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
    <h2>Staff Information</h2>
    <table>
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Registration Number</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Surname</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['staff_id']; ?></td>
                        <td><?php echo $row['staff_registration_number']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['middle_name']; ?></td>
                        <td><?php echo $row['surname']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <!-- Buttons for more details, update, and delete -->
                            <a href="view_staff_details.php?staff_id=<?php echo $row['staff_id']; ?>">View More</a>
                            <a href="view_education.php?staff_registration_number=<?php echo urlencode(htmlspecialchars($row['staff_registration_number'])); ?>" class="btn btn-secondary">View Education</a>

                            <a href="update_staff.php?staff_id=<?php echo $row['staff_id']; ?>">Update</a>
                            <a href="delete_staff.php?staff_id=<?php echo $row['staff_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No staff information found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
