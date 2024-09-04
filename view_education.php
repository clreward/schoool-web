<?php
// Include database connection
include('db_connection.php');

// Retrieve staff_registration_number from URL
$staff_registration_number = isset($_GET['staff_registration_number']) ? htmlspecialchars($_GET['staff_registration_number']) : '';

if (!$staff_registration_number) {
    echo "No staff registration number provided.";
    exit();
}

// SQL query to fetch education information
$query = "SELECT * FROM staff_education WHERE staff_registration_number = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $staff_registration_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $education = $result->fetch_assoc();
    } else {
        echo "No education information found for this staff.";
    }

    $stmt->close();
} else {
    echo "Error preparing query: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Education Information</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Education Information</h2>
        <?php if (isset($education)): ?>
            <table class="table table-bordered">
                <tr>
                    <th>Primary Level</th>
                    <td><?php echo htmlspecialchars($education['primary_level']); ?></td>
                </tr>
                <tr>
                    <th>Secondary Level</th>
                    <td><?php echo htmlspecialchars($education['secondary_level']); ?></td>
                </tr>
                <tr>
                    <th>Advanced Level</th>
                    <td><?php echo htmlspecialchars($education['advanced_level']); ?></td>
                </tr>
                <tr>
                    <th>Other Level</th>
                    <td><?php echo htmlspecialchars($education['other_level']); ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p>No education information available.</p>
        <?php endif; ?>
        <a href="view_staff.php" class="btn btn-primary">Back to Staff List</a>
    </div>

    <script src="path/to/bootstrap.js"></script>
</body>
</html>
