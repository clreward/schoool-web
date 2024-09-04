<?php
// db_connection.php

// Database configuration
$host = 'localhost';         // Database host (usually 'localhost')
$db_username = 'root'; // Your MySQL username
$db_password = ''; // Your MySQL password
$db_name = 'school_management_system'; // Database name

// Create a new MySQLi connection
$conn = new mysqli($host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    // Connection failed, display error message
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to UTF-8 for proper encoding
if (!$conn->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $conn->error);
    exit();
}

// You can now use the $conn variable to interact with the database

?>
