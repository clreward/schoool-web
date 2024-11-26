<?php
include('db_connection.php');
session_start();

// Check if the user is logged in as Teacher
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit;
}

// Check if staff_registration_number is set in the session
if (!isset($_SESSION['user_id'])) {
    die("Staff registration number not found in session.");
}

// Get the teacher's registration number from session
$teacher_registration_number = $_SESSION['user_id'];

// Query to check if the teacher is assigned to any class
$query = "
    SELECT form 
    FROM teacher_classes
    WHERE staff_registration_number = ? 
";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Failed to prepare the SQL statement: " . $conn->error);
}
$stmt->bind_param("s", $teacher_registration_number);
$stmt->execute();
$result = $stmt->get_result();

// If the teacher is not assigned to any class
if ($result->num_rows == 0) {
    echo "<h3>You are not assigned to any class.</h3>";
} else {
    // If the teacher is assigned to a class, fetch the class details
    $row = $result->fetch_assoc();
    $assigned_class = $row['form'];

    // Display welcome message
    echo "<h3>Welcome, Class Teacher of Form $assigned_class</h3>";
    
    // Display the buttons horizontally
    echo "<div class='button-container'>
            <button class='view-btn' onclick='viewStudents()'>View Students</button>
            <button class='view-btn'>Character Assessment</button>
            <button class='view-btn'>Permission</button>
          </div>";

    // Query to fetch students for the assigned class
    $student_query = "
        SELECT registration_number, first_name, surname 
        FROM students
        WHERE form = ? 
    ";

    $stmt2 = $conn->prepare($student_query);
    if ($stmt2 === false) {
        die("Failed to prepare the SQL statement: " . $conn->error);
    }
    $stmt2->bind_param("s", $assigned_class);
    $stmt2->execute();
    $student_result = $stmt2->get_result();

    // We won't display students immediately, only show on button click
    echo "<div id='student-list' style='display:none;'>";
    if ($student_result->num_rows > 0) {
        echo "<h3>Students in Class $assigned_class</h3>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Registration Number</th>
                        <th>First Name</th>
                        <th>Surname</th>
                    </tr>
                </thead>
                <tbody>";

        // Display the students
        while ($student_row = $student_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $student_row['registration_number'] . "</td>
                    <td>" . $student_row['first_name'] . "</td>
                    <td>" . $student_row['surname'] . "</td>
                </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No students found for this class.</p>";
    }
    echo "</div>";

    // Close the second statement
    $stmt2->close();
}

// Close the first statement and database connection
$stmt->close();
$conn->close();
?>

<script>
// JavaScript to handle the "View Students" button functionality
function viewStudents() {
    // Toggle visibility of the student list
    var studentList = document.getElementById('student-list');
    if (studentList.style.display === 'none') {
        studentList.style.display = 'block';
    } else {
        studentList.style.display = 'none';
    }
}
</script>

<style>
/* Style for the buttons container to arrange them horizontally */
.button-container {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.view-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin: 0 10px;
}

.view-btn:hover {
    background-color: #45a049;
}

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

#student-list {
    margin-top: 20px;
}
</style>
