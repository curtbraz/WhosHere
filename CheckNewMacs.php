<?php

require_once 'dbconfig.php';

// Create MySQL Connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Adds New Unique MAC Addresses to Table
$sql = "CALL AddAssets();";

$result = $conn->query($sql);

$conn->close();
?>

