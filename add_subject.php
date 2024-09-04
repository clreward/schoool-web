<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Subject Data</title>
</head>
<body>
    <h2>Insert Subject Data</h2>
    <form action="" method="post">
        <label for="subject_name">Subject Name:</label><br>
        <input type="text" id="subject_name" name="subject_name" required><br><br>

        <label for="form">Form Level:</label><br>
        <select id="form" name="form">
            <option value="1">Form 1</option>
            <option value="2">Form 2</option>
            <option value="3">Form 3</option>
            <option value="4">Form 4</option>
            <option value="5">Form 5</option>
            <option value="6">Form 6</option>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "school_management_system";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve form data
        $subject_name = $_POST['subject_name'];
        $form = $_POST['form'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, form) VALUES (?, ?)");
        $stmt->bind_param("ss", $subject_name, $form);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New subject inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
