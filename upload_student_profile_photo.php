<!DOCTYPE html>
<html>
<head>
    <title>Image Upload and Display</title>
</head>
<body>

<h2>Upload Image</h2>
<form action="" method="post" enctype="multipart/form-data">
    Student Registration Number:
    <input type="text" name="registration_number" id="registration_number" required>
    <br><br>
    Select image to upload:
    <input type="file" name="image" id="image" required>
    <br><br>
    <input type="submit" value="Upload Image" name="submit">
</form>

<?php
// Include the database connection
include('db_connection.php'); 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["submit"])) {
    // Get the staff registration number from the form
    $registration_number = $_POST["registration_number"];

    // Check if the file is uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // File details
        $image_name = $_FILES["image"]["name"];
        $image_data = file_get_contents($_FILES["image"]["tmp_name"]);

        // Prepare the SQL statement for updating the staff image
        $stmt = $conn->prepare("UPDATE students SET image_data = ?, image_name = ? WHERE registration_number = ?");

        if ($stmt) {
            // Bind parameters (s for string, b for blob)
            $stmt->bind_param("sss", $image_data, $image_name, $registration_number);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Image uploaded and saved to the database for staff registration number: $registration_number.";
            } else {
                echo "Error executing the query: " . $stmt->error;
            }

            // Close the statement


            // Close the statement
            $stmt->close();
        } else {
            echo "Failed to prepare the statement: " . $conn->error;
        }
    } else {
        echo "No image file uploaded or there was an error with the upload.";
    }
}

// Close the database connection
$conn->close();
?>

</body>
</html>
