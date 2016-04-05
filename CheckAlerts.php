<?php

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// UPDATES LastSeen FOR All Assets
$sql = "CALL UpdateAssetsLastSeen()";

$result = $conn->query($sql);

// LOOKS FOR RECENT PROBES FOR ASSETS THAT HAVE A NOTIFICATION FLAG SET
$sql = "CALL NotificationLogic();";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

//echo $row["Nickname"]." was just seen at ".$row["LastSeen"].".";

// SEND NOTIFICATION TO SLACK
$cmd = "curl -X POST --data-urlencode 'payload={\"channel\": \"#general\", \"username\": \"WhosHere\", \"text\": \"".$row["Nickname"]." was just seen at ".$row["LastSeen"].".\", \"icon_emoji\": \":bell:\"}' https://hooks.slack.com/services/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";

exec($cmd);

}
}

// TRUNCATES LOG TABLE FOR PERFORMANCE REASONS
$sql = "CALL PurgeLogs();";

$result = $conn->query($sql);

$conn->close();
?>

