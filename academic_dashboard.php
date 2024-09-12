<?php
// Include necessary files and initialize session.
include('db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, middle_name, surname, staff_registration_number,  profile_photo FROM staff WHERE staff_registration_number = ?";

$stmt = $conn->prepare($query);

// Check if preparation was successful
if ($stmt === false) {
    die("Failed to prepare the SQL statement: " . $conn->error);
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user === null) {
    die("Failed to fetch user details or no user found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Dashboard</title>
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
            width: 50px;
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
        <a href="view_students.php">View Students</a>
        <a href="view_student_subject.php">View Students_subjects</a>
        <a href="register_students.php">Register Students</a>
        <a href="add_subjects.php">Assign Subjects</a>
        <a href="add_subject.php">Add subject</a>
        <a href="view_teachers.php">View Teachers</a>
        <a href="assign_classes.php">Assign Classes & Subjects</a>
        <a href="view_results.php">View Uploaded Results</a>
        <a href="upload_results.php">Upload Results</a>
        <a href="view_permitted_students.php">View Permitted Students</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="user-info">
            <img src="<?php echo $user['profile_photo']; ?>" alt="Profile Photo">
            <h2><?php echo $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['surname']; ?></h2>
            <p>Registration Number: <?php echo $user['staff_registration_number']; ?></p>
            <!-- <p>Role: <?php echo ucfirst($user['role']); ?></p> -->
        </div>

        <!-- <h2>Welcome to your dashboard, <?php echo ucfirst($user['role']); ?>!</h2> -->
        <p>Use the menu on the left to navigate through the system functionalities.</p>
    </div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
