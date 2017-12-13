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
<b><i>Modifying Parameters Below Apply Globally</i></b>
<br><br>
<TABLE class="gridtable">
<TR><TH>Name</TH><TH>Value</TH><TH>Update</TH></TR>

<?php

if(isset($_POST["Name"])){$Name = $_POST["Name"];}
if(isset($_POST["Value"])){$Value = $_POST["Value"];}

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



if(isset($_REQUEST["Value"])){

$sql2 = "UPDATE config SET Value = '$Value' WHERE Name = '$Name';";

$result2 = $conn->query($sql2);

}

$sql = "SELECT * FROM config;";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

//var_dump($row);

$Name = $row["Name"];
$Value = $row["Value"];

$Value=htmlspecialchars($Value);
$Name=htmlspecialchars($Name);

?>
<TR><FORM METHOD="POST" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>"><TR><TD ALIGN="CENTER"><INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php echo $Name; ?>"><?php echo $Name; ?></TD><TD><INPUT TYPE="TEXT" NAME="Value" VALUE="<?php echo $Value; ?>"></TD><TD ALIGN="CENTER"><INPUT TYPE="SUBMIT" VALUE="Save"></TD></FORM></TR>

<?php
    }
}

$conn->close();
?>

</TABLE>
<br><br>
</CENTER>
</BODY>
</HTML>
