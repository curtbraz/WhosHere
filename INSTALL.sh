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

sed -i -e 's/disable_lua = false/disable_lua = true/g' /usr/share/wireshark/init.lua;

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

sed -i -e "s~IFTTT_WEBHOOK_GOES_HERE~$webhook~g" CheckAlerts.php;

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
mysql -u root -p$MySQLPassword -e "CREATE USER 'whoshere' IDENTIFIED BY '$MySQLPassword';"
mysql -u root -p$MySQLPassword -e "GRANT ALL PRIVILEGES ON *.* TO 'whoshere' WITH GRANT OPTION;"
mysql -u root -p$MySQLPassword -e "FLUSH PRIVILEGES;"

## SETS UP CRON JOBS

echo "Setting up Scheduled Cron Jobs and Autostart Background Services..";

sleep 2;

crontab -l >> cron;

sudo crontab cron;


## INSTALLS SYSTEMD SERVICE

Path=`pwd`

sed -i -e "s~CHANGEME~$Path~g" whoshere.service;

sudo cp whoshere.service /lib/systemd/system/whoshere.service;

sudo systemctl daemon-reload

sudo systemctl enable whoshere.service

sudo systemctl start whoshere.service


## YOU'RE DONE!

echo "Installation Complete!  Visit \"http://localhost/WhosHere\" to Begin!";
