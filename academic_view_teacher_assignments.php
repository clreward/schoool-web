<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Academic
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Academic') {
    header("Location: login.php");
    exit;
}

// Fetch teacher assignments
$query = "
    SELECT 
        staff.staff_registration_number, 
        staff.first_name, 
        staff.middle_name, 
        staff.surname, 
        staff.gender, 
        teacher_assignments.form,
        subjects.subject_name
    FROM staff
    JOIN teacher_assignments ON staff.staff_registration_number = teacher_assignments.teacher_registration_number
    JOIN subjects ON teacher_assignments.subject_id = subjects.subject_id
    ORDER BY staff.staff_registration_number, teacher_assignments.form, subjects.subject_name";

// Execute the query
$result = $conn->query($query);

if ($result === false) {
    die("Failed to fetch teacher assignments: " . $conn->error);
}

// Fetch distinct forms for the columns
$forms_query = "SELECT DISTINCT form FROM teacher_assignments ORDER BY form";
$forms_result = $conn->query($forms_query);

if ($forms_result === false) {
    die("Failed to fetch forms: " . $conn->error);
}

$forms = [];
while ($form_row = $forms_result->fetch_assoc()) {
    $forms[] = $form_row['form'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher Assignments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
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
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-column {
            width: 150px;
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
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>View Teacher Assignments</h1>
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
                <?php
                // Generate form columns
                foreach ($forms as $form) {
                    echo "<th class='form-column'>Form $form</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $current_teacher = null;
            $form_subjects = [];
            
            while ($row = $result->fetch_assoc()) {
                if ($current_teacher !== $row['staff_registration_number']) {
                    // Output previous teacher's data
                    if ($current_teacher !== null) {
                        echo "<tr>
                            <td>$current_teacher</td>
                            <td>{$current_name}</td>
                            <td>{$current_gender}</td>";
                        // Output subjects for each form
                        foreach ($forms as $form) {
                            echo "<td>" . implode(', ', $form_subjects[$form] ?? []) . "</td>";
                        }
                        echo "</tr>";
                    }
                    // Reset for the new teacher
                    $current_teacher = $row['staff_registration_number'];
                    $current_name = "{$row['first_name']} {$row['middle_name']} {$row['surname']}";
                    $current_gender = $row['gender'];
                    $form_subjects = [];
                }
                // Collect form and subject
                $form_subjects[$row['form']][] = $row['subject_name'];
            }
            // Output last teacher's data
            if ($current_teacher !== null) {
                echo "<tr>
                    <td>$current_teacher</td>
                    <td>{$current_name}</td>
                    <td>{$current_gender}</td>";
                // Output subjects for each form
                foreach ($forms as $form) {
                    echo "<td>" . implode(', ', $form_subjects[$form] ?? []) . "</td>";
                }
                echo "</tr>";
            }
            ?>
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
