<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Get the form from the query string (if provided)
$form = isset($_GET['form']) ? (int)$_GET['form'] : 1; // Default to Form 1 if no form is selected

// Fetch subjects for the selected form
$sql_subjects = "
    SELECT s.subject_id, s.subject_name
    FROM subjects s
    JOIN student_subjects ss ON s.subject_id = ss.subject_id
    JOIN students st ON ss.student_id = st.registration_number
    WHERE st.form = ?
";

if ($stmt_subjects = $conn->prepare($sql_subjects)) {
    $stmt_subjects->bind_param("i", $form);
    $stmt_subjects->execute();
    $result_subjects = $stmt_subjects->get_result();

    $subjects = [];
    while ($row = $result_subjects->fetch_assoc()) {
        $subjects[$row['subject_id']] = $row['subject_name'];
    }

    // Prepare the SQL query to fetch student results for the selected form
    $sql_students = "
        SELECT 
            s.registration_number, 
            CONCAT(s.first_name, ' ', s.middle_name, ' ', s.surname) AS names, 
            s.gender
        FROM students s
        WHERE s.form = ?
    ";

    if ($stmt_students = $conn->prepare($sql_students)) {
        $stmt_students->bind_param("i", $form);
        $stmt_students->execute();
        $result_students = $stmt_students->get_result();

        // Display the menu for forms with Bootstrap styling
        echo "<div class='container mt-4'>";
        echo "<h2>Select a Form</h2>";
        echo "<nav><ul class='nav nav-pills'>";
        foreach ([1, 2, 3, 4, 5, 6] as $form_num) {
            $active = $form_num == $form ? 'active' : ''; // Add 'active' class to the selected form
            echo "<li class='nav-item'>
                    <a class='nav-link $active' href='?form=$form_num'>Form $form_num</a>
                  </li>";
        }
        echo "</ul></nav>";

        // Display the table for the selected form
        echo "<h3 class='mt-4'>Form $form</h3>";
        echo "<table class='table table-bordered'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Registration Number</th>";
        echo "<th>Names</th>";
        echo "<th>Gender</th>";

        // Add headers for each subject
        foreach ($subjects as $subject_name) {
            echo "<th colspan='4'>$subject_name</th>";
        }
        echo "</tr>";

        echo "<tr>";
        echo "<th></th>";
        echo "<th></th>";
        echo "<th></th>";

        // Add sub-headers for each subject
        foreach ($subjects as $subject_name) {
            echo "<th>Test</th>";
            echo "<th>Midtest</th>";
            echo "<th>Final</th>";
            echo "<th>Total</th>";
        }
        echo "</tr>";
        echo "</thead>";

        echo "<tbody>";

        // Loop through students
        while ($student = $result_students->fetch_assoc()) {
            $student_id = $student['registration_number'];
            echo "<tr>";
            echo "<td>{$student['registration_number']}</td>";
            echo "<td>{$student['names']}</td>";
            echo "<td>{$student['gender']}</td>";

            // Loop through subjects
            foreach ($subjects as $subject_id => $subject_name) {
                // Fetch results for this student and subject
                $sql_results = "
                    SELECT 
                        test_marks, 
                        midterm_marks, 
                        final_marks, 
                        (COALESCE(test_marks, 0) + COALESCE(midterm_marks, 0) + COALESCE(final_marks, 0)) AS total
                    FROM student_results
                    WHERE student_id = ? AND subject_id = ?
                ";

                if ($stmt_results = $conn->prepare($sql_results)) {
                    $stmt_results->bind_param("si", $student_id, $subject_id);
                    $stmt_results->execute();
                    $result_results = $stmt_results->get_result();
                    $result_data = $result_results->fetch_assoc();

                    // Ensure $result_data is not null and display the results
                    $test_marks = isset($result_data['test_marks']) ? $result_data['test_marks'] : "...";
                    $midterm_marks = isset($result_data['midterm_marks']) ? $result_data['midterm_marks'] : "...";
                    $final_marks = isset($result_data['final_marks']) ? $result_data['final_marks'] : "...";

                    // Calculate total or display "--" if final marks are missing
                    $total = isset($result_data['final_marks']) && $result_data['final_marks'] !== null
                        ? $result_data['total']
                        : "--";

                    echo "<td>{$test_marks}</td>";
                    echo "<td>{$midterm_marks}</td>";
                    echo "<td>{$final_marks}</td>";
                    echo "<td>{$total}</td>";

                    $stmt_results->close();
                } else {
                    echo "<td colspan='4'>Error</td>";
                }
            }
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        $stmt_students->close();
    } else {
        echo "Error preparing students query: " . $conn->error;
    }

    $stmt_subjects->close();
} else {
    echo "Error preparing subjects query: " . $conn->error;
}

// Close the connection
$conn->close();
?>

<!-- Add Bootstrap CSS for styling -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
