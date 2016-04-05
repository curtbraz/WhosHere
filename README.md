# WhosHere
Notifies You of Chosen People in Your Vicinity via Their Wifi Probe Requests From an Simple Web GUI

#Description
The script is named after my dogs, because they proactively alert me before the doorbell rings.  I used to like to get them going crazy by saying, "Who's Here?!". They cause a lot of noise when someone arrives, whether it's the pizza guy or a friend/family member coming over to visit.  It's not so fun now that I'm a dad because it wakes my sleeping baby up and that's no bueno for me.  I created this script to passively look for cell phone probe requests and alert me if certain visitors are in the area by sending a Slack push notification to my phone.  I typically get the alert before the visitor gets out of their vehicle thanks to a 9 dB antenna connected to a wifi dongle running on my Raspberry Pi.

The management is made simple due to an HTML web table that reads the database and allows you to give friendly nicknames to MAC addresses.  There are also first and last seen timestamps that help you determine who someone is that visits, name them appropriately, and enable a notification checkbox to get real-time alerts next time they get close.  Probe Request tracking is nothing new but I'm not aware of something like this for personal use, especially something this easy to set up, interface with, and forget until you get an alert.

#Requirements
1) A PC or Raspberry Pi running a Debian OS

2) A WiFi dongle or card that supports monitor mode

3) A Dedicated Slack Channel Set Up with Mobile Push Notifications (or another method for alerting you)

#Instructions
1) Clone Repo Locally and run INSTALL.sh.

2) You will be prompted to provide MySQL credentials twice as part of the mysql-server install.

3) Modify "/var/www/html/WhosHere/dbconfig.php" and "run.py" to include your database credentials.

4) Create a Slack Channel, Download the Mobile App, Set up Push Notifications, and Sign up for the API (https://api.slack.com/incoming-webhooks#sending_messages)

5) Edit "/var/www/html/WhosHere/CheckAlerts.php" to include Slack API Incoming Webhook Channel and URL Information

6) When finished launch "run.py". (preferably in Screen)

7) Visit http://[ip]/WhosHere/ in a browser and enjoy!

#Other Thoughts
Performance is great on the Raspberry Pi, but that's because I truncate the log table frequently.  If you'd like to retain this data on more powerful hardware, modify the PurgeLogs() Stored Procedure or comment it out entirely.  The asset table still retains FirstSeen, LastSeen, and total count.

If you have errors, take a look at the "run.py" script and modify the "iw" command to match the wireless lan adapter if it's not wlan0 and phy0 by default.  You may have issues with monitor mode on certain wifi dongles.  

I plan to add a lot more support for querying assets and managing notification types from the Web GUI.  I also want to improve performance and have more options for different OS's, so look for those changes soon!  I'd like to create an app for rooted Android OS's to make this mobile for when you're on the go and possibly centralized between users.  Imagine driving up to the parking lot of a building and knowing if someone was inside or not!  I also think it would be neat to hook into other API's for home automation to do things like turn on lights when you're approaching or interface with the garage door, thermostat, or speakers.  Enjoy and please let me know if you have any questions or feedback!  
