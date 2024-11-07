<?php
// Include database connection
include('db_connection.php');

// Define variables and initialize with empty values
$user_id = $password = $role = "";
$user_id_err = $password_err = $role_err = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate user ID
    if (empty(trim($_POST["user_id"]))) {
        $user_id_err = "Please enter a user ID.";
    } else {
        // Prepare a select statement to check if the user ID already exists
        $sql = "SELECT login_id FROM login WHERE user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_user_id);
            $param_user_id = trim($_POST["user_id"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $user_id_err = "This user ID is already taken.";
                } else {
                    $user_id = trim($_POST["user_id"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    }
    
    // Validate role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select a role.";
    } else {
        $role = trim($_POST["role"]);
    }
    
    // Check input errors before inserting in the database
    if (empty($user_id_err) && empty($password_err) && empty($role_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO user_login (user_id, password, role) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_user_id, $param_password, $param_role);
            $param_user_id = $user_id;
            $param_password = $password;
            $param_role = $role;
            if ($stmt->execute()) {
                echo "Signup successful. You can now login.";
            } else {
                echo "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }
    
    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .signup-form {
            width: 300px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .signup-form h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .signup-form label {
            margin-bottom: 10px;
            display: block;
            font-weight: bold;
        }
        .signup-form input[type="text"],
        .signup-form input[type="password"],
        .signup-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .signup-form .error {
            color: red;
            margin-bottom: 10px;
        }
        .signup-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .signup-form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="signup-form">
        <h2>Signup</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="user_id" value="<?php echo $user_id; ?>">
                <span class="error"><?php echo $user_id_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="">Select Role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Academic">Academic</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Treasurer">Treasurer</option>
                    <!-- Add other roles as needed -->
                </select>
                <span class="error"><?php echo $role_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Signup">
            </div>
        </form>
    </div>    
</body>
</html>
