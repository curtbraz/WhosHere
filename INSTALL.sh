#!/bin/bash

## UPDATES AND INSTALLS REQUIRED PACKAGES FROM DEBIAN BASED DISTROS

sudo apt-get update;

sudo apt-get install apache2 php5 mysql-client mysql-server-5.5 php5-mysqlnd python-mysqldb tshark mysql-server -y;


## SETS UP APACHE AND COPIES PHP WEB FILES

sudo service apache2 restart;

sudo mkdir /var/www/html/WhosHere/ && sudo cp *.php /var/www/html/WhosHere/;


## MODIFIES MYSQL CONFIGURATION SETTINGS FOR LOWER PERFORMANCE MACHINES (Raspberry Pi)

sudo cp my.cnf /etc/mysql/ && sudo service mysql restart;


## IMPORTS MySQL SCHEMA AND STORED PROCEDURES

mysql -u root -p < MySQLSchema.sql;


## SETS UP CRON JOBS

sudo crontab cron;


## YOU'RE DONE!

echo "Installation Complete.  Execute \"sudo python run.py\" in Screen and Visit \"http://localhost/WhosHere\" to Begin!";
