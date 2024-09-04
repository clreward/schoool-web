<?php
// Include database connection
include('db_connection.php');
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $staff_registration_number = $_POST['staff_registration_number'];
    $password = $_POST['password'];

    // SQL Query to fetch user data
    $query = "SELECT staff_registration_number, role, password FROM user_login WHERE staff_registration_number = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("s", $staff_registration_number);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            // Verify password
            if (password_verify($password, $result['password'])) {
                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $staff_registration_number;
                $_SESSION['role'] = $result['role'];

                // Redirect to dashboard
                header("Location: academic_dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that registration number.";
        }

        // Close statement
        $stmt->close();
    } else {
        $error = "Error: " . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="staffRegistrationNumber">Staff Registration Number</label>
                <input type="text" class="form-control" id="staffRegistrationNumber" name="staff_registration_number" maxlength="20" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
