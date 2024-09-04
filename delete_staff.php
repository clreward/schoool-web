<?php
// Include database connection
include('db_connection.php');

// Get the staff_id from the URL
$staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : '';

if ($staff_id) {
    // Prepare the SQL query to delete the staff record
    $query = "DELETE FROM staff WHERE staff_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $staff_id);

        if ($stmt->execute()) {
            echo "Staff record has been deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

    // Redirect back to the staff list after deleting
    header("Location: view_staff.php");
    exit();
} else {
    echo "Invalid staff ID.";
}
?>
