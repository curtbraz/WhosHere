#!/bin/bash

## WELCOME LANGUAGE

echo "Installing.. You will be prompted to create a database password if setting up MySQL for this first time.";

sleep 5;


## UPDATES AND INSTALLS REQUIRED PACKAGES FROM DEBIAN BASED DISTROS

sudo apt-get update;

sudo apt-get install apache2 php5 mysql-client mysql-server-5.5 php5-mysqlnd python-mysqldb tshark mysql-server -y;



## SETS UP MONITORING WLAN INTERFACE

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

PhyDev="`iw dev |grep "$SelectedInterface" -B 1 |grep phy | sed 's/#//g'`"

PhyDevMonitorCheck="`iw "$PhyDev" info |grep monitor | wc -l`"

if [ $PhyDevMonitorCheck -eq 0 ]                                   
then
        echo "Error! "$SelectedInterface" does not support monitor mode.  Is it possible the proper drivers are not installed for this Distribution or your wireless card simply does not support monitor mode?"
        exit 1                
fi


sed -i -e s/PHY_DEVICE_HERE/$PhyDev/g run.py;




## CONFIGURE CREDENTIALS

echo "Please enter your MySQL password..";

read MySQLPassword;

sed -i -e s/PASSWORD_GOES_HERE/$MySQLPassword/g run.py;

sed -i -e s/PASSWORD_GOES_HERE/$MySQLPassword/g dbconfig.py;

echo "This script uses the Slack chat application to send alerts.  You must register a free team with https://slack.com/create if you don't have one already.  Then, once signed in, you'll want to go to https://slack.com/apps/A0F7XDUAZ-incoming-webhooks in order to setup "Incoming Webhooks" and get your URL.  Once you have that please enter the information below.  You may also want to install the mobile app and configure push notifications for real-time alerting.";

echo "What is your Slack URL? (Should look like https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX)"

read SlackURL

echo "What is your Slack Channel? (Default is #general)" 

read SlackChannel

sed -i -e s/URL_GOES_HERE/$SlackURL/g CheckAlerts.php;

sed -i -e s/CHANNEL_GOES_HERE/$SlackChannel/g CheckAlerts.php;

## SETS UP APACHE AND COPIES PHP WEB FILES

sudo service apache2 restart;

sudo mkdir /var/www/html/WhosHere/ && sudo cp *.php /var/www/html/WhosHere/;


## MODIFIES MYSQL CONFIGURATION SETTINGS FOR LOWER PERFORMANCE MACHINES (Raspberry Pi)

sudo cp my.cnf /etc/mysql/ && sudo service mysql restart;


## IMPORTS MySQL SCHEMA AND STORED PROCEDURES

mysql -u root -p $MySQLPassword < MySQLSchema.sql;


## SETS UP CRON JOBS

sudo crontab cron;


## YOU'RE DONE!

echo "Installation Complete.  Execute \"sudo python run.py\" in Screen and Visit \"http://localhost/WhosHere\" to Begin!";
