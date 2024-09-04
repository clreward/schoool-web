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
    <title>Register Staff Education</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Register Staff Education</h2>
        <form action="register_education_process.php" method="POST" enctype="multipart/form-data">
            <!-- Staff ID (hidden) -->
            <input type="hidden" name="staff_registration_number" value="<?php echo htmlspecialchars($staff_registration_number); ?>">

            <!-- Primary Level -->
            <div class="form-group">
                <label for="primaryLevel">Primary Level</label>
                <input type="text" class="form-control" id="primaryLevel" name="primary_level" maxlength="100">
            </div>

            <!-- Secondary Level -->
            <div class="form-group">
                <label for="secondaryLevel">Secondary Level</label>
                <input type="text" class="form-control" id="secondaryLevel" name="secondary_level" maxlength="100">
            </div>

            <!-- Secondary Level File -->
            <div class="form-group">
                <label for="secondaryLevelFile">Secondary Level File</label>
                <input type="file" class="form-control" id="secondaryLevelFile" name="secondary_level_file">
            </div>

            <!-- Advanced Level -->
            <div class="form-group">
                <label for="advancedLevel">Advanced Level</label>
                <input type="text" class="form-control" id="advancedLevel" name="advanced_level" maxlength="100">
            </div>

            <!-- Advanced Level File -->
            <div class="form-group">
                <label for="advancedLevelFile">Advanced Level File</label>
                <input type="file" class="form-control" id="advancedLevelFile" name="advanced_level_file">
            </div>

            <!-- Other Level -->
            <div class="form-group">
                <label for="otherLevel">Other Level</label>
                <textarea class="form-control" id="otherLevel" name="other_level"></textarea>
            </div>

            <!-- Other Level File -->
            <div class="form-group">
                <label for="otherLevelFile">Other Level File</label>
                <input type="file" class="form-control" id="otherLevelFile" name="other_level_file">
            </div>

            <button type="submit" class="btn btn-primary">Register Education</button>
        </form>
    </div>

    <!-- Include your JavaScript files here -->
    <script src="path/to/bootstrap.js"></script>
</body>
</html>
