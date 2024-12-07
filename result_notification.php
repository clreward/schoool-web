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

// Define form levels (1 to 6)
$form_levels = range(1, 6);

// Get the selected form level or default to Form 1
$form_level = isset($_GET['form_level']) ? (int)$_GET['form_level'] : 1;

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

$stmt_subjects->bind_param("i", $form_level);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();

// Fetch the status for test, midterm, and final marks for each subject
function fetch_marks_status($conn, $subject_id, $form_level, $mark_type)
{
    $sql_marks = "
        SELECT COUNT(*) AS count 
        FROM student_results 
        WHERE subject_id = ? 
        AND student_id IN (SELECT registration_number FROM students WHERE form = ?)
        AND $mark_type IS NOT NULL
    ";

    $stmt_marks = $conn->prepare($sql_marks);
    $stmt_marks->bind_param("ii", $subject_id, $form_level);
    $stmt_marks->execute();
    $result_marks = $stmt_marks->get_result();
    $row_marks = $result_marks->fetch_assoc();

    return $row_marks['count'] > 0 ? '✔' : '❌';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form <?php echo $form_level; ?> Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(240, 240, 240);
        }
        .header, .footer {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px auto;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .menu {
            display: flex; /* Align buttons in a row */
            justify-content: center; /* Center the form buttons */
            margin-bottom: 20px;
        }
        .menu .go-home-btn {
            margin-right: auto; /* Push the "Go Home" button to the left */
        }
        .menu a {
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #4CAF50; /* Green button background */
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }
        .menu a.active {
            background-color: #45a049; /* Darker green for active button */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-icon {
            font-weight: bold;
        }
        .status-success {
            color: green;
        }
        .status-failure {
            color: red;
        }
        /* Center the notification */
        .notification {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            color: #333;
        }
        /* Center the title */
        h2 {
            text-align: center;
            font-size: 28px;
            color: #333;
        }
        /* Green Go Home Button */
        .go-home-btn {
            display: inline-block; /* Align in a row */
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-left: 10px;
        }
        .go-home-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>Result Notification</h1>
</div>

<div class="content">
    <h2>Form <?php echo $form_level; ?> Subjects</h2>

    <div class="menu">
        <!-- Go Home Button on the left -->
        <a href="academic_dashboard.php" class="go-home-btn">Go Home</a>
        
        <!-- Form Buttons centered -->
        <?php foreach ($form_levels as $level): ?>
            <a href="?form_level=<?php echo $level; ?>" class="<?php echo $level === $form_level ? 'active' : ''; ?>">
                Form <?php echo $level; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Notification Section -->
    <div class="notification">
        <?php if ($result_subjects->num_rows == 0): ?>
            <p>No subjects found for Form <?php echo $form_level; ?>.</p>
        <?php endif; ?>
    </div>

    <?php if ($result_subjects->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Test Marks Status</th>
                    <th>Midterm Marks Status</th>
                    <th>Final Marks Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($subject = $result_subjects->fetch_assoc()): ?>
                    <?php
                    $subject_id = $subject['subject_id'];
                    $test_status = fetch_marks_status($conn, $subject_id, $form_level, 'test_marks');
                    $midterm_status = fetch_marks_status($conn, $subject_id, $form_level, 'midterm_marks');
                    $final_status = fetch_marks_status($conn, $subject_id, $form_level, 'final_marks');
                    ?>
                    <tr>
                        <td><?php echo $subject['subject_name']; ?></td>
                        <td class="status-icon <?php echo $test_status === '✔' ? 'status-success' : 'status-failure'; ?>">
                            <?php echo $test_status; ?>
                        </td>
                        <td class="status-icon <?php echo $midterm_status === '✔' ? 'status-success' : 'status-failure'; ?>">
                            <?php echo $midterm_status; ?>
                        </td>
                        <td class="status-icon <?php echo $final_status === '✔' ? 'status-success' : 'status-failure'; ?>">
                            <?php echo $final_status; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> School Management System. All rights reserved.</p>
</div>

<?php
$stmt_subjects->close();
$conn->close();
?>

</body>
</html>
