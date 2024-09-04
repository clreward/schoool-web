<?php
// Include database connection
include('db_connection.php');

// Retrieve staff_registration_number from the URL
$staff_registration_number = isset($_GET['staff_registration_number']) ? htmlspecialchars($_GET['staff_registration_number']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Next of Kin</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Register Next of Kin</h2>
        <form action="register_next_of_kin_process.php" method="POST">
            <!-- User ID (hidden) -->
            <input type="hidden" name="staff_registration_number" value="<?php echo $staff_registration_number; ?>">

            <!-- First Name -->
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" name="first_name" maxlength="50" required>
            </div>

            <!-- Middle Name -->
            <div class="form-group">
                <label for="middleName">Middle Name</label>
                <input type="text" class="form-control" id="middleName" name="middle_name" maxlength="50">
            </div>

            <!-- Surname -->
            <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" maxlength="50" required>
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>

            <!-- Age -->
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>

            <!-- Address Region -->
            <div class="form-group">
                <label for="addressRegion">Address Region</label>
                <input type="text" class="form-control" id="addressRegion" name="address_region" maxlength="50" required>
            </div>

            <!-- Address Ward -->
            <div class="form-group">
                <label for="addressWard">Address Ward</label>
                <input type="text" class="form-control" id="addressWard" name="address_ward" maxlength="50" required>
            </div>

            <!-- Address Village -->
            <div class="form-group">
                <label for="addressVillage">Address Village</label>
                <input type="text" class="form-control" id="addressVillage" name="address_village" maxlength="50" required>
            </div>

            <!-- Address Street -->
            <div class="form-group">
                <label for="addressStreet">Address Street</label>
                <input type="text" class="form-control" id="addressStreet" name="address_street" maxlength="50">
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" maxlength="15" required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="100">
            </div>

            <!-- Relationship -->
            <div class="form-group">
                <label for="relationship">Relationship</label>
                <input type="text" class="form-control" id="relationship" name="relationship" maxlength="50" required>
            </div>

            <!-- Marital Status -->
            <div class="form-group">
                <label for="maritalStatus">Marital Status</label>
                <input type="text" class="form-control" id="maritalStatus" name="marital_status" maxlength="50">
            </div>

            <!-- NIDA Number -->
            <div class="form-group">
                <label for="nidaNumber">NIDA Number</label>
                <input type="text" class="form-control" id="nidaNumber" name="nida_number" maxlength="20">
            </div>

            <button type="submit" class="btn btn-primary">Register Next of Kin</button>
        </form>
    </div>

    <!-- Include your JavaScript files here -->
    <script src="path/to/bootstrap.js"></script>
</body>
</html>
