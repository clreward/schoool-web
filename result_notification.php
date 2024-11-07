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

// Loop through forms (1 to 6)
for ($form_level = 1; $form_level <= 6; $form_level++) {
    // Fetch all subjects for the current form level
    $sql_subjects = "
        SELECT s.subject_id, s.subject_name
        FROM subjects s
        JOIN student_subjects ss ON s.subject_id = ss.subject_id
        JOIN students st ON ss.student_id = st.registration_number
        WHERE st.form = ?
        GROUP BY s.subject_id, s.subject_name
    ";

    $stmt_subjects = $conn->prepare($sql_subjects);

    if (!$stmt_subjects) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt_subjects->bind_param("i", $form_level);  // 'i' for integer because form is an integer
    $stmt_subjects->execute();
    $result_subjects = $stmt_subjects->get_result();

    if ($result_subjects->num_rows === 0) {
        echo "<p>No subjects found for Form $form_level.</p>";
        continue;  // Skip to next form if no subjects found
    }

    // Display Form Heading
    echo "<h2>Form $form_level Subjects - Result Upload Status</h2>";
    echo "<table class='table'>";
    echo "<thead>
            <tr>
                <th>Subject</th>
                <th>Test Marks Status</th>
                <th>Midterm Marks Status</th>
                <th>Final Marks Status</th>
            </tr>
          </thead>";
    echo "<tbody>";

    // Loop through subjects for the current form
    while ($row = $result_subjects->fetch_assoc()) {
        $subject_id = $row['subject_id'];
        $subject_name = $row['subject_name'];

        // Check if test marks are uploaded
        $sql_test_marks = "
            SELECT COUNT(*) AS test_count 
            FROM student_results 
            WHERE subject_id = ? 
            AND student_id IN (SELECT registration_number FROM students WHERE form = ?)
            AND test_marks IS NOT NULL
        ";

        $stmt_test_marks = $conn->prepare($sql_test_marks);
        $stmt_test_marks->bind_param("ii", $subject_id, $form_level);
        $stmt_test_marks->execute();
        $result_test_marks = $stmt_test_marks->get_result();
        $row_test_marks = $result_test_marks->fetch_assoc();
        $test_count = $row_test_marks['test_count'];

        // Check if midterm marks are uploaded
        $sql_midterm_marks = "
            SELECT COUNT(*) AS midterm_count 
            FROM student_results 
            WHERE subject_id = ? 
            AND student_id IN (SELECT registration_number FROM students WHERE form = ?)
            AND midterm_marks IS NOT NULL
        ";

        $stmt_midterm_marks = $conn->prepare($sql_midterm_marks);
        $stmt_midterm_marks->bind_param("ii", $subject_id, $form_level);
        $stmt_midterm_marks->execute();
        $result_midterm_marks = $stmt_midterm_marks->get_result();
        $row_midterm_marks = $result_midterm_marks->fetch_assoc();
        $midterm_count = $row_midterm_marks['midterm_count'];

        // Check if final marks are uploaded
        $sql_final_marks = "
            SELECT COUNT(*) AS final_count 
            FROM student_results 
            WHERE subject_id = ? 
            AND student_id IN (SELECT registration_number FROM students WHERE form = ?)
            AND final_marks IS NOT NULL
        ";

        $stmt_final_marks = $conn->prepare($sql_final_marks);
        $stmt_final_marks->bind_param("ii", $subject_id, $form_level);
        $stmt_final_marks->execute();
        $result_final_marks = $stmt_final_marks->get_result();
        $row_final_marks = $result_final_marks->fetch_assoc();
        $final_count = $row_final_marks['final_count'];

        // Display subject and status
        echo "<tr>";
        echo "<td>$subject_name</td>";

        // Test marks status
        if ($test_count > 0) {
            echo "<td><span class='text-success'><i class='fas fa-check-circle'></i> ✔</span></td>";
        } else {
            echo "<td><span class='text-danger'><i class='fas fa-times-circle'></i> ❌</span></td>";
        }

        // Midterm marks status
        if ($midterm_count > 0) {
            echo "<td><span class='text-success'><i class='fas fa-check-circle'></i> ✔</span></td>";
        } else {
            echo "<td><span class='text-danger'><i class='fas fa-times-circle'></i> ❌</span></td>";
        }

        // Final marks status
        if ($final_count > 0) {
            echo "<td><span class='text-success'><i class='fas fa-check-circle'></i> ✔</span></td>";
        } else {
            echo "<td><span class='text-danger'><i class='fas fa-times-circle'></i> ❌</span></td>";
        }

        echo "</tr>";

        $stmt_test_marks->close();
        $stmt_midterm_marks->close();
        $stmt_final_marks->close();
    }

    echo "</tbody>";
    echo "</table>";

    $stmt_subjects->close();
}

// Close the connection
$conn->close();
?>

<!-- Add some basic Bootstrap styling for buttons and table -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<!-- Add FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
