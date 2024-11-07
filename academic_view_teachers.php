<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Academic
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Fetch teacher data
$query = "
    SELECT 
        staff.staff_registration_number, 
        staff.first_name, 
        staff.middle_name, 
        staff.surname, 
        staff.gender 
    FROM staff 
    JOIN user_login 
    ON staff.staff_registration_number = user_login.staff_registration_number 
    WHERE user_login.role = 'Teacher'";

// Execute the query
$result = $conn->query($query);

if ($result === false) {
    die("Failed to fetch teacher data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Subjects to Teachers</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .assign-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .assign-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Assign Subjects to Teachers</h1>

<table>
    <thead>
        <tr>
            <th>Registration Number</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Assign Form</th>
            <th>Assign Subject</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($teacher = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $teacher['staff_registration_number']; ?></td>
                <td><?php echo $teacher['first_name'] . ' ' . $teacher['middle_name'] . ' ' . $teacher['surname']; ?></td>
                <td><?php echo $teacher['gender']; ?></td>
                <!-- <td> -->
                    <form action="assign_subjects.php" method="POST">
                        <input type="hidden" name="teacher_registration_number" value="<?php echo $teacher['staff_registration_number']; ?>">
                        <td><select name="form" required>
                            <option value="">Select Form</option>
                            <?php
                            // Fetch forms to display in the dropdown
                            $form_query = "SELECT DISTINCT form FROM students";
                            $form_result = $conn->query($form_query);

                            while ($form = $form_result->fetch_assoc()) {
                                echo '<option value="' . $form['form'] . '">' . $form['form'] . '</option>';
                            }
                            ?>
                        </select></td>
                        <td><select name="subject_id" required>
                            <option value="">Select Subject</option>
                            <?php
                            // Fetch subjects to display in the dropdown
                            $subject_query = "SELECT subject_id, subject_name FROM subjects";
                            $subject_result = $conn->query($subject_query);

                            while ($subject = $subject_result->fetch_assoc()) {
                                echo '<option value="' . $subject['subject_id'] . '">' . $subject['subject_name'] . '</option>';
                            }
                            ?>
                        </select></td>
                        <td><button type="submit" class="assign-btn">Assign</button></td>
                    </form>
                <!-- </td> -->
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
