<?php
include('db_connection.php');

// Check if form is selected
if (isset($_GET['form'])) {
    $form = $_GET['form'];

    // Prepare and bind the SQL statement to avoid SQL injection
    $subject_query = "
        SELECT DISTINCT subject_id, subject_name 
        FROM subjects 
        WHERE subject_id NOT IN (
            SELECT subject_id 
            FROM teacher_assignments 
            WHERE form = ?  -- Use placeholder for form
        )";

    // Prepare the statement
    if ($stmt = $conn->prepare($subject_query)) {
        // Bind the form parameter to the statement
        $stmt->bind_param("s", $form);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if any subjects were returned
        if ($result->num_rows > 0) {
            echo '<option value="">Select Subject</option>';
            while ($subject = $result->fetch_assoc()) {
                echo '<option value="' . $subject['subject_id'] . '">' . $subject['subject_name'] . '</option>';
            }
        } else {
            echo '<option value="">No subjects available</option>';
        }

        // Close the statement
        $stmt->close();
    } else {
        echo '<option value="">Error preparing the query</option>';
    }
} else {
    echo '<option value="">Select Form First</option>';
}

$conn->close();
?>
