<?php

$hostname = 'localhost'; 
$username = 'sparklab_portal';
$password = 'sparklab_portal';
$database = 'sparklab_portal';

// Create a connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>
