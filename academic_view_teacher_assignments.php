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
        th {
            background-color: #f2f2f2;
        }
        .form-column {
            width: 150px; /* Adjust width as needed */
        }
    </style>
</head>
<body>

<h1>Teacher Assignments</h1>

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

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
