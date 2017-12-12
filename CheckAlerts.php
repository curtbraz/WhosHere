<?php

// For IFTTT Integration, Enter Incoming Webhook Here
$webhook = 'https://maker.ifttt.com/trigger/WhosHere/with/key/IFTTT_WEBHOOK_GOES_HERE';

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ADDS NEW ASSETS NOT PREVIOUSLY SEEN
$sql = "CALL AddAssets();";

$result = $conn->query($sql);


// UPDATES LastSeen FOR ALL ASSETS
$sql = "CALL UpdateAssetsLastSeen();";

$result = $conn->query($sql);


// LOOKS FOR RECENT PROBES FOR ASSETS THAT HAVE A NOTIFICATION FLAG SET
$sql = "CALL NotificationLogic();";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

$LastSeen = date('h:i:s A', strtotime($row["LastSeen"]));

// SEND NOTIFICATION TO SLACK
$cmd = "curl -X POST -H 'Content-type: application/json' --data '{\"value1\" : \"".$row["Nickname"]."\", \"value2\" : \"".$LastSeen."\"}' ".$webhook;

// UPDATES LastSeen FOR THIS ASSET
// Create connection
$conn2 = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

$sql1 = "CALL UpdateSingleAsset('".$row["MAC"]."');";

$result1 = $conn2->query($sql1);

$conn2->close();

exec($cmd);

echo $cmd."\r\n";

}
}

$conn->close();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// TRUNCATES LOG TABLE FOR PERFORMANCE REASONS
$sql = "CALL PurgeLogs();";

$result = $conn->query($sql);

$conn->close();
?>
