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

// Fetch all form levels
$form_levels = range(1, 6); // Form levels 1 to 6

// Check if form is selected
$form_level = isset($_GET['form_level']) ? (int)$_GET['form_level'] : 1; // Default to form 1

// Display the menu (Form 1 to 6)
echo "<ul class='nav nav-pills'>";
foreach ($form_levels as $level) {
    $activeClass = ($form_level == $level) ? 'active' : '';
    echo "<li class='nav-item'>
            <a class='nav-link $activeClass' href='?form_level=$level'>Form $level</a>
          </li>";
}
echo "</ul>";

// Fetch subjects for the selected form level
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
    exit;
}

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

// Loop through subjects for the selected form
while ($row = $result_subjects->fetch_assoc()) {
    $subject_id = $row['subject_id'];
    $subject_name = $row['subject_name'];

    // Check marks status for the subject
    $statuses = [];
    $status_types = ['test_marks', 'midterm_marks', 'final_marks'];

    foreach ($status_types as $status_type) {
        $sql_marks = "
            SELECT COUNT(*) AS count 
            FROM student_results 
            WHERE subject_id = ? 
            AND student_id IN (SELECT registration_number FROM students WHERE form = ?)
            AND $status_type IS NOT NULL
        ";

        $stmt_marks = $conn->prepare($sql_marks);
        $stmt_marks->bind_param("ii", $subject_id, $form_level);
        $stmt_marks->execute();
        $result_marks = $stmt_marks->get_result();
        $row_marks = $result_marks->fetch_assoc();
        $statuses[$status_type] = $row_marks['count'] > 0 ? '✔' : '❌';
    }

    echo "<tr>";
    echo "<td>$subject_name</td>";

    // Display each status (Test, Midterm, Final)
    foreach ($statuses as $status) {
        echo "<td><span class='" . ($status == '✔' ? 'text-success' : 'text-danger') . "'><i class='fas fa-" . ($status == '✔' ? 'check-circle' : 'times-circle') . "'></i> $status</span></td>";
    }

    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

$stmt_subjects->close();

// Close the connection
$conn->close();
?>

<!-- Add some basic Bootstrap styling for buttons and table -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<!-- Add FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
