<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <style>
        body {
            background-color: rgb(240, 240, 240);
            font-family: Arial, sans-serif;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content h2, .content h3 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"], .home-btn {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover, .home-btn:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            font-size: 14px;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Manage Subjects</h1>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Go Home Button -->
        <a href="academic_dashboard.php" class="home-btn">Go Home</a>

        <h2>Manage Subjects</h2>

        <!-- Form to Add Subject -->
        <h3>Add Subject</h3>
        <form action="" method="post">
            <label for="subject_name">Subject Name:</label>
            <input type="text" id="subject_name" name="subject_name" required>
            <input type="submit" name="add_subject" value="Add Subject">
        </form>

        <!-- Form to Delete Subject -->
        <h3>Delete Subject</h3>
        <form action="" method="post">
            <label for="subject_id">Select Subject to Delete:</label>
            <select id="subject_id" name="subject_id" required>
                <option value="">--Select Subject--</option>
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "school_management_system";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch subjects for the dropdown
                $sql = "SELECT subject_id, subject_name FROM subjects";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['subject_id'] . "'>" . $row['subject_name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No subjects available</option>";
                }

                $conn->close();
                ?>
            </select>
            <input type="submit" name="delete_subject" value="Delete Subject">
        </form>

        <?php
        // Database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Add Subject logic
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add_subject'])) {
                $subject_name = $conn->real_escape_string($_POST['subject_name']);

                // Array of all forms (1, 2, 3, 4)
                $forms = [1, 2, 3, 4];

                // Insert the subject for each form
                foreach ($forms as $form) {
                    $add_subject_sql = "INSERT INTO subjects (subject_name, form) VALUES ('$subject_name', $form)";
                    if ($conn->query($add_subject_sql) !== TRUE) {
                        echo "<div class='message'>Error adding subject: " . $conn->error . "</div>";
                        break;
                    }
                }

                // If all inserts succeed, show success message
                echo "<div class='message'>Subject added successfully to all forms!</div>";
            }

            // Delete Subject logic
            if (isset($_POST['delete_subject'])) {
                $subject_id = $_POST['subject_id'];

                // Delete the subject from the database
                $delete_subject_sql = "DELETE FROM subjects WHERE subject_id = $subject_id";
                if ($conn->query($delete_subject_sql) === TRUE) {
                    echo "<div class='message'>Subject deleted successfully!</div>";
                } else {
                    echo "<div class='message'>Error deleting subject: " . $conn->error . "</div>";
                }
            }
        }

        $conn->close();
        ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 All rights reserved. Designed by Clifford.</p>
    </div>
</body>
</html>
