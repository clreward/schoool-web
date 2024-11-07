<?php
// Include necessary files and initialize session
include('db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch user details and role
$user_id = $_SESSION['user_id'];
$query = "
    SELECT 
        staff.first_name, 
        staff.middle_name, 
        staff.surname, 
        staff.staff_registration_number, 
        staff.image_data, 
        staff.image_name,  /* Assuming image_name holds the image filename or description */
        user_login.role 
    FROM staff 
    JOIN user_login 
    ON staff.staff_registration_number = user_login.staff_registration_number 
    WHERE staff.staff_registration_number = ?";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Failed to prepare the SQL statement: " . $conn->error);
}
$stmt->bind_param("s", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user === null) {
    die("Failed to fetch user details or no user found.");
}

$role = $user['role']; // Store the role for later use
$image_data = $user['image_data']; // Image stored in the database as BLOB
$image_name = $user['image_name']; // Image name

// Convert BLOB image data to base64
$base64_image = base64_encode($image_data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($role); ?> Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .header img {
            width: 50px;
            vertical-align: middle;
        }
        .header h1 {
            display: inline;
            margin: 0;
            vertical-align: middle;
        }
        .sidebar {
            width: 200px;
            position: fixed;
            top: 60px;
            left: 0;
            background-color: #f8f8f8;
            padding: 15px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            text-decoration: none;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #4CAF50;
            color: white;
            padding-left: 10px;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .user-info {
            margin-bottom: 20px;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
        }
        .user-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 10px;
        }
        .user-info h2 {
            display: inline;
            margin: 0;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="school_logo.png" alt="School Logo">
        <h1>School Name</h1>
    </div>

    <div class="sidebar">
        <h3>Menu</h3>
        <?php if ($role == 'Administrator') { ?>
            <a href="view_students.php">View Students</a>
            <a href="register_students.php">Register Students</a>
            <a href="register_staff.php">Register Staff</a>
            <a href="upload_profile_photo.php">Upload Staff Profile Photo</a>
            <a href="upload_student_profile_photo.php">Upload Student Profile Photo</a>
            <!-- Add more Administrator options -->
        <?php } elseif ($role == 'Academic') { ?>
            <a href="view_student_subject.php">View Students</a>
            <a href="##">Register Students**</a>
            <a href="view_students.php">Assign Subjects</a>
            <a href="add_subject.php">Add subject</a>
            <a href="academic_view_teachers.php">View Teachers</a>
            <a href="academic_view_teacher_assignments.php">View Teachers Assignments</a>
            <a href="result_notification.php">Result Notification</a>
            <!-- <a href="view_results.php">View Uploaded Results</a> -->
            <a href="view_form_results.php">View Results</a>
            <a href="#">Upload Results</a>
            <a href="#">View Permitted Students</a>
        <?php } elseif ($role == 'Teacher') { ?>
            <a href="teacher_view_assignments.php">View Assigned Classes</a>
            <a href="teacher_add_marks.php">Add Marks</a>
            <a href="edit_marks.php">Edit Marks</a>
            <!-- Add more Teacher options -->
        <?php } elseif ($role == 'Treasurer') { ?>
            <a href="view_payments.php">View Payments</a>
            <a href="print_receipt.php">Print Receipt</a>
            <!-- Add more Treasurer options -->
        <?php } elseif ($role == 'Student') { ?>
            <a href="view_results.php">View Results</a>
            <a href="view_payments.php">View Payment Information</a>
            <a href="send_message.php">Send Message</a>
            <!-- Add more Student options -->
        <?php } ?>
        
        <a href="login.php">Logout</a>
    </div>

    <div class="content">
        <div class="user-info">
            <!-- Display the profile photo from the base64-encoded image data -->
            <img src="data:image/jpeg;base64,<?php echo $base64_image; ?>" alt="<?php echo $image_name; ?>">
            <h2><?php echo $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['surname']; ?></h2>
            <p>Registration Number: <?php echo $user['staff_registration_number']; ?></p>
            <p>Role: <?php echo ucfirst($role); ?></p>
        </div>

        <p>Use the menu on the left to navigate through the system functionalities.</p>
    </div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
