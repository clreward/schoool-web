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
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        select, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .assign-btn {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .assign-btn:hover {
            background-color: #45a049;
        }
        .home-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .home-btn:hover {
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
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>Assign Subjects to Teachers</h1>
</div>

<!-- Main Content -->
<div class="content">
    <!-- Go Home Button -->
    <a href="academic_dashboard.php" class="home-btn">Go Home</a>

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
                    <form action="assign_subjects.php" method="POST">
                        <input type="hidden" name="teacher_registration_number" value="<?php echo $teacher['staff_registration_number']; ?>">
                        <td>
                            <select name="form" required>
                                <option value="">Select Form</option>
                                <?php
                                // Fetch forms to display in the dropdown
                                $form_query = "SELECT DISTINCT form FROM students";
                                $form_result = $conn->query($form_query);

                                while ($form = $form_result->fetch_assoc()) {
                                    echo '<option value="' . $form['form'] . '">' . $form['form'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <select name="subject_id" required>
                                <option value="">Select Subject</option>
                                <?php
                                // Fetch subjects that are not assigned to the teacher for the selected form
                                $assigned_subject_query = "
                                    SELECT DISTINCT subject_id, subject_name 
                                    FROM subjects 
                                    WHERE subject_id NOT IN (
                                        SELECT subject_id 
                                        FROM teacher_assignments 
                                        WHERE teacher_registration_number = '" . $teacher['staff_registration_number'] . "' 
                                          AND form = '" . $teacher['form'] . "'
                                    )
                                ";

                                $assigned_subject_result = $conn->query($assigned_subject_query);

                                if ($assigned_subject_result) {
                                    while ($subject = $assigned_subject_result->fetch_assoc()) {
                                        echo '<option value="' . $subject['subject_id'] . '">' . $subject['subject_name'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Error fetching subjects</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td><button type="submit" class="assign-btn">Assign</button></td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 All rights reserved. Designed by Clifford.</p>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
