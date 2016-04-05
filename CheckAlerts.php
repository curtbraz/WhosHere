<?php

// EDIT YOUR SLACK INCOMING WEBHOOK API SETTINGS HERE.
$slackchannel = '#general';
$slackurl = 'https://hooks.slack.com/services/T000000/00000000/000000000000';

require_once 'dbconfig.php';

// Create MySQL Connection
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

// SEND NOTIFICATION TO SLACK.  
$cmd = "curl -X POST --data-urlencode 'payload={\"channel\": \"".$slackchannel."\", \"username\": \"WhosHere\", \"text\": \"".$row["Nickname"]." was just seen at ".$row["LastSeen"].".\", \"icon_emoji\": \":bell:\"}' ".$slackurl;

exec($cmd);

}
}


$conn->close();
?>

