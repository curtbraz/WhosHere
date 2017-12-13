<HTML>
  <HEAD>
    <TITLE>Who's Here?!</TITLE>
    <META http-equiv="refresh" content="120">

<style type="text/css">
table.gridtable {
        font-family: verdana,arial,sans-serif;
        font-size:10px;
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
}
table.gridtable th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
}
table.gridtable td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
}
</style>

  </HEAD>
<BODY>
<CENTER>
<img src="whoshere-logo.png"><br>
<b><i>SSID Requests Listed Below for This Asset</i></b>
<br><br>
<TABLE class="gridtable">
<TR><TH>SSID</TH></TR>

<?php

if(isset($_REQUEST["MAC"])){$MAC = $_REQUEST["MAC"];}

$MAC=htmlspecialchars($MAC);

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$MAC = mysqli_real_escape_string($conn, $MAC);

$sql = "SELECT * FROM SSIDs WHERE MAC = '$MAC';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

//var_dump($row);

$SSID = $row["SSID"];

$SSID=htmlspecialchars($SSID);

if($SSID != ""){
?>

<TR><TD><?php echo $SSID; ?></TD></TR>

<?php
}

    }
}

$conn->close();
?>

</TABLE>
<br><br>
</CENTER>
</BODY>
</HTML>
