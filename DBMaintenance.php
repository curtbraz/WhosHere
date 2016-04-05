<?php

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// PURGES LOG TABLE FOR DATA OLDER THAN 7 DAYS - SET UP IN CRON TO RUN DAILY
$sql = "CALL PurgeLogs();";

$result = $conn->query($sql);


$conn->close();
?>

