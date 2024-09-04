<?php
// Include database connection
include('db_connection.php');

// Retrieve registration_number from URL
$registration_number = isset($_GET['registration_number']) ? htmlspecialchars($_GET['registration_number']) : '';

if (!$registration_number) {
    echo "No student registration number provided.";
    exit();
}

// SQL query to fetch parent information
$query = "SELECT * FROM student_parents WHERE student_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $registration_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $parent = $result->fetch_assoc();
    } else {
        echo "No parent information found for this student.";
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
    <title>Student parent Information</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>parent Information</h2>
        <?php if (isset($parent)): ?>
            <table class="table table-bordered">
            <p><strong>Parent ID:</strong> <?php echo htmlspecialchars($parent['parent_id']); ?></p>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($parent['first_name']); ?></p>
        <p><strong>Middle Name:</strong> <?php echo htmlspecialchars($parent['middle_name']); ?></p>
        <p><strong>Surname:</strong> <?php echo htmlspecialchars($parent['surname']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($parent['gender']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($parent['age']); ?></p>
        <p><strong>Region:</strong> <?php echo htmlspecialchars($parent['address_region']); ?></p>
        <p><strong>District:</strong> <?php echo htmlspecialchars($parent['address_district']); ?></p>
        <p><strong>Ward:</strong> <?php echo htmlspecialchars($parent['address_ward']); ?></p>
        <p><strong>Village:</strong> <?php echo htmlspecialchars($parent['address_village']); ?></p>
        <p><strong>Street:</strong> <?php echo htmlspecialchars($parent['address_street']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($parent['phone']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($parent['email']); ?></p>
        <p><strong>Relationship:</strong> <?php echo htmlspecialchars($parent['relationship']); ?></p>
        <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($parent['marital_status']); ?></p>
        <p><strong>NIDA Number:</strong> <?php echo htmlspecialchars($parent['nida_number']); ?></p>
    <?php else: ?>
        <p>No parent information found for this student.</p>
    <?php endif; ?>
        <a href="view_student.php" class="btn btn-primary">Back to Student List</a>
    </div>

    <script src="path/to/bootstrap.js"></script>
</body>
</html>
