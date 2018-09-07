#!/bin/bash

## WELCOME LANGUAGE

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

echo "Initiating Install..";

sleep 2;


## UPDATES AND INSTALLS REQUIRED PACKAGES FROM DEBIAN BASED DISTROS

echo "Updating Repositories..";

sleep 2;

sudo apt-get update;

echo "Installing Required Packages.. You Will Have to Create a Database Password if Installing MySQL For the First Time..";

sleep 2;

sudo apt-get install apache2 php mysql-client mysql-server php-mysqlnd python-mysqldb tshark mysql-server python3-pymysql -y;



## SETS UP MONITORING WLAN INTERFACE

echo "Setting Up wlan Interface..  If you Haven't Already, Plug in Your WiFi Adapter Now and Hit Enter to Continue: ";

read Wait;

Interfaces=`iw dev |grep "Interface" |cut -d " " -f 2 |grep -v "mon0"`
InterfaceCount=`iw dev |grep "Interface" |grep -v "mon0" | wc -l`

if [ $InterfaceCount -gt 1 ]
then
        echo "Which interface would you like to set up? The following wlan interfaces were detected: "$Interfaces
        read SelectedInterface
fi

if [ $InterfaceCount -eq 1 ]
then
        SelectedInterface=`iw dev |grep "Interface" |cut -d " " -f 2 |grep -v "mon0"`
fi

if [ $InterfaceCount -eq 0 ]
then
        echo "No wireless adapters were detected!  Please ensure the wireless adapter is set up and re-run the Install script."
        exit 1
fi

if [ `iw dev |grep "Interface" |cut -d " " -f 2 |grep "mon0" | wc -l` -gt 0 ]
then
MonInterface=`iw dev |grep "Interface" |cut -d " " -f 2 |grep "mon0"`
iw dev $MonInterface del
fi

echo "Using Interface "$SelectedInterface"..";

sleep 2;

PhyDev="`iw dev |grep "$SelectedInterface" -B 1 |grep phy | sed 's/#//g'`"

PhyDevMonitorCheck="`iw "$PhyDev" info |grep monitor | wc -l`"

if [ $PhyDevMonitorCheck -eq 0 ]
then
        echo "Error! "$SelectedInterface" does not support monitor mode.  Is it possible the proper drivers are not installed for this Distribution or your wireless card simply does not support monitor mode?"
        exit 1
fi


sed -i -e 's/PHY_DEVICE_HERE/'$PhyDev'/g' run.py;




## CONFIGURE CREDENTIALS

echo "Please enter your MySQL password..";

read MySQLPassword;

sed -i -e 's/PASSWORD_GOES_HERE/'$MySQLPassword'/g' run.py;

sed -i -e 's/PASSWORD_GOES_HERE/'$MySQLPassword'/g' dbconfig.php;

echo "This script uses IFTTT.com (If Then Then That) Incoming Webhooks for Slack/SMS/etc Notifications/Automation";

echo "What is your IFTTT.com Webhook URL? (Should look like https://maker.ifttt.com/trigger/WhosHere/with/key/IFTTT_WEBHOOK_GOES_HERE)"

read webhook

sed -i -e 's/IFTTT_WEBHOOK_GOES_HERE/'$webhook'/g' CheckAlerts.php;

## SETS UP APACHE AND COPIES PHP WEB FILES

echo "Setting Up the Web Server..";

sleep 2;

sudo service apache2 restart;

DIRECTORY='/var/www/html/'

if [ -d "$DIRECTORY" ]; then

mkdir /var/www/html/WhosHere/;

cp *.php /var/www/html/WhosHere/ && cp whoshere-logo.png /var/www/html/WhosHere/;

fi

if [ ! -d "$DIRECTORY" ]; then

mkdir /var/www/WhosHere/;

cp *.php /var/www/WhosHere/ && cp whoshere-logo.png /var/www/WhosHere/;

sed -i -e 's/html\///g' cron;

fi

## MODIFIES MYSQL CONFIGURATION SETTINGS FOR LOWER PERFORMANCE MACHINES (Raspberry Pi)

echo "Configuring MySQL Server and WhosHere Database..";

sleep 2;

sudo cp my.cnf /etc/mysql/ && sudo service mysql restart;


## IMPORTS MySQL SCHEMA AND STORED PROCEDURES

mysql -u root -p$MySQLPassword -h localhost < MySQLSchema.sql;
mysql -u root -p$MySQLPassword -e "CREATE USER 'whoshere' IDENTIFIED BY '".$MySQLPassword."';"
mysql -u root -p$MySQLPassword -e "GRANT ALL PRIVILEGES ON *.* TO 'whoshere' WITH GRANT OPTION;"
mysql -u root -p$MySQLPassword -e "FLUSH PRIVILEGES;"

## SETS UP CRON JOBS

echo "Setting up Scheduled Cron Jobs and Autostart Background Services..";

sleep 2;

crontab -l >> cron;

sudo crontab cron;


## INSTALLS SYSTEMD SERVICE

Path='`pwd`'

sed -i -e 's/CHANGEME/'$Path'/g' whoshere.service;

sudo cp whoshere.service /lib/systemd/system/whoshere.service;

sudo systemctl daemon-reload

sudo systemctl enable whoshere.service

sudo systemctl start whoshere.service


## YOU'RE DONE!

echo "Installation Complete!  Visit \"http://localhost/WhosHere\" to Begin!";
root@oldpi:/home/pi/WhosHere# cd /var/www/html/
root@oldpi:/var/www/html# cd WhosHere/
root@oldpi:/var/www/html/WhosHere# ls
assetinfo.php  CheckAlerts.php  configpage.php  dbconfig.php  DBMaintenance.php  index.php  whoshere-logo.png
root@oldpi:/var/www/html/WhosHere# nano index.php
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere#
root@oldpi:/var/www/html/WhosHere# cat index.php
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
<b><i>You Can Modify Names and Enable Notifications for Assets Below:</i></b>
<p align="center">
<a href="configpage.php" TARGET="_BLANK">Options</a>
</p>

<?php
$output = shell_exec('systemctl status whoshere.service |grep "active (running)" | wc -l');
if($output == 0)
{
echo "<font color=red><b>The Collector Service is NOT Running!</b></font><br>(run \"<b>sudo systemctl start whoshere.service</b>\" or \"<b>python run.py</b>\" if that fails)<br><br>";
}
else
{echo "(<font color=green>The Collector Service is Running</font>)<br><br>";}
?>
<TABLE class="gridtable">
<TR><TH>Name</TH><TH>Times Seen</TH><TH>First Seen</TH><TH>Last Seen</TH><TH>Strength</TH><TH colspan="2">Notify Treshold</TH><TH>Update</TH></TR>

<?php

if(isset($_POST["asset"])){$asset = $_POST["asset"];}else{$asset = "";}
if(isset($_POST["Nickname"])){$Nickname = $_POST["Nickname"];}else{$Nickname = "";}
if(isset($_POST["DBTreshold"])){$DBTreshold = $_POST["DBTreshold"];}else{$DBTreshold = "";}
if(isset($_POST["Notify"])){$Notify = 1;}else{$Notify = 0;}
if(isset($_POST["ShowMore"])){$ShowMore = $_POST["ShowMore"];}else{$ShowMore = "No";}

$ShowMore=htmlspecialchars($ShowMore);

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Nickname = mysqli_real_escape_string($conn, $Nickname);
$DBTreshold = mysqli_real_escape_string($conn, $DBTreshold);
$Notify = mysqli_real_escape_string($conn, $Notify);
$asset = mysqli_real_escape_string($conn, $asset);

if(isset($asset)){
$sql = "CALL UpdateAssets('$Nickname','$Notify','$asset','$DBTreshold');";

$result = $conn->query($sql);
}

if($ShowMore == "Yes"){
$sql = "CALL ViewAllAssets();";
} else {
$sql = "CALL QuickViewAssets();";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

//var_dump($row);

$Nickname = $row["Nickname"];
$DBTreshold = $row["DBTreshold"];
$Notify = $row["Notify"];
$MAC = $row["MAC"];
$TimesSeen = $row["TimesSeen"];
$FirstSeen = $row["FirstSeen"];
$LastSeen = $row["LastSeen"];
$SignalStrength = $row["SignalStrength"];
$SSIDs = $row["SSIDs"];
if($FirstSeen == "0000-00-00 00:00:00"){$FirstSeen = "";}

$FirstSeen = date('m/d/Y h:i:s A', strtotime($FirstSeen));
$LastSeen = date('m/d/Y h:i:s A', strtotime($LastSeen));

$Nickname=htmlspecialchars($Nickname);
$DBTreshold=htmlspecialchars($DBTreshold);
$Notify=htmlspecialchars($Notify);
$MAC=htmlspecialchars($MAC);
$TimesSeen=htmlspecialchars($TimesSeen);
$FirstSeen=htmlspecialchars($FirstSeen);
$LastSeen=htmlspecialchars($LastSeen);

?>
<TR><FORM METHOD="POST" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>"><INPUT TYPE="hidden" NAME="asset" VALUE="<?php echo $MAC; ?>"><TD><INPUT TYPE="TEXT" NAME="Nickname" VALUE="<?php echo $Nickname; ?>"></TD><TD ALIGN="CENTER"><?php if($SSIDs >= 1){?><A HREF="assetinfo.php?MAC=<?php echo $MAC; ?>" TARGET="_BLANK"><?php echo $TimesSeen; ?></a><?php } else { echo $TimesSeen; }?></TD><TD><?php echo $FirstSeen; ?></TD><TD><?php echo $LastSeen; ?></TD><TD ALIGN="CENTER"><?php echo $SignalStrength; ?></TD><TD ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="Notify" <?php if($Notify == 1){echo "checked";}else{echo "unchecked";}?>></TD><TD><INPUT TYPE="TEXT" NAME="DBTreshold" VALUE="<?php echo $DBTreshold; ?>" SIZE="3"></TD><TD ALIGN="CENTER"><INPUT TYPE="SUBMIT" VALUE="Save"></TD></FORM></TR>

<?php
    }
}

$conn->close();
?>

</TABLE>
<br><br>
<?php if($ShowMore == "No"){?><FORM METHOD="POST" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>"><INPUT TYPE="HIDDEN" NAME="ShowMore" VALUE="Yes"><INPUT TYPE="SUBMIT" VALUE="Show All"></FORM><?php } ?>
</CENTER>
</BODY>
</HTML>
