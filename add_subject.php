<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
</head>
<body>
    <h2>Manage Subjects</h2>

    <!-- Form to Add Subject -->
    <h3>Add Subject</h3>
    <form action="" method="post">
        <label for="subject_name">Subject Name:</label><br>
        <input type="text" id="subject_name" name="subject_name" required><br><br>
        <input type="submit" name="add_subject" value="Add Subject">
    </form>

    <!-- Form to Delete Subject -->
    <h3>Delete Subject</h3>
    <form action="" method="post">
        <label for="subject_id">Select Subject to Delete:</label><br>
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
        </select><br><br>
        <input type="submit" name="delete_subject" value="Delete Subject">
    </form>

    <?php
    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "school_management_system";

    // Add Subject Logic
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_subject'])) {
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve the subject name
        $subject_name = trim($_POST['subject_name']);

        // Check if the subject already exists
        $check_query = $conn->prepare("SELECT COUNT(*) FROM subjects WHERE subject_name = ?");
        $check_query->bind_param("s", $subject_name);
        $check_query->execute();
        $check_query->bind_result($count);
        $check_query->fetch();
        $check_query->close();

        if ($count > 0) {
            echo "Subject '$subject_name' already exists in the database.<br>";
        } else {
            // Apply the subject to all forms (1 to 6)
            $forms = [1, 2, 3, 4, 5, 6];

            foreach ($forms as $form) {
                $stmt = $conn->prepare("INSERT INTO subjects (subject_name, form) VALUES (?, ?)");
                $stmt->bind_param("si", $subject_name, $form);

                if ($stmt->execute()) {
                    echo "Subject '$subject_name' successfully added to Form $form.<br>";
                } else {
                    echo "Error adding subject '$subject_name' to Form $form: " . $stmt->error . "<br>";
                }

                $stmt->close();
            }
        }

        $conn->close();
    }

    // Delete Subject Logic
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_subject'])) {
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the selected subject ID
        $subject_id = $_POST['subject_id'];

        // Delete the subject
        $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $subject_id);

        if ($stmt->execute()) {
            echo "Subject deleted successfully!<br>";
        } else {
            echo "Error deleting subject: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
