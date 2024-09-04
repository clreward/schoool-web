<?php
// Include database connection
include('db_connection.php');

// Retrieve staff registration number from URL
$staff_registration_number = isset($_GET['staff_registration_number']) ? htmlspecialchars($_GET['staff_registration_number']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Role and Password</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Set Role and Password</h2>
        <form action="set_role_password.php" method="POST">
            <!-- Staff Registration Number (hidden field) -->
            <input type="hidden" name="staff_registration_number" value="<?php echo $staff_registration_number; ?>" required>

            <!-- Role -->
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Academic">Academic</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Treasurer">Treasurer</option>
                </select>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Set Role and Password</button>
        </form>
    </div>

    <script src="path/to/bootstrap.js"></script>
</body>
</html>
