<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom styles */
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background-color: #495057;
            border-radius: 5px;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4>School Name</h4>
                <p>Admin Dashboard</p>
                <img src="path_to_profile_photo.jpg" alt="Profile Photo" class="img-fluid rounded-circle mb-3">
                <p>Administrator Name</p>
                <p>Registration Number</p>
                <hr>
                <a href="register_student.html">Register Student</a>
                <a href="register_parent.html">Register Parent</a>
                <a href="upload_staff.html">Register Staff</a>
                <a href="#nextOfKin">Register Next of Kin</a>
                <a href="#staffEducation">Staff Education Info</a>
                <a href="view_student.php">View Students</a>
                <a href="view_staff.php">View Staff</a>
                <a href="#logout">Logout</a>
            </div>
            <div class="col-md-10 main-content">
                <h3>Welcome, Administrator</h3>
                <div id="dashboard-content">
                    <!-- Content for each functionality will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
