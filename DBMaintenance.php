<?php

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// OPTIMIZES MyISAM log TABLE
$sql = "OPTIMIZE TABLE log;";

$result = $conn->query($sql);

$conn->close();



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// OPTIMIZES MyISAM assets TABLE
$sql = "OPTIMIZE TABLE assets;";

$result = $conn->query($sql);

$conn->close();    

?>

