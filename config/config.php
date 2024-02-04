<?php

$hostname = 'localhost'; // usually 'localhost'
$username = 'root';
$password = '';
$database = 'sparklab';

// Create a connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

?>
