# WhosHere
Notifies You of Chosen People in Your Vicinity via Their Wifi Probe Requests From an Simple Web GUI

# Description
The script is named after my dogs, because they proactively alert me before the doorbell rings.  I used to like to get them going crazy by saying, "Who's Here?!". They cause a lot of noise when someone arrives, whether it's the pizza guy or a friend/family member coming over to visit.  It's not so fun now that I'm a dad because it wakes my sleeping baby up and that's no bueno for me.  I created this script to passively look for cell phone probe requests and alert me if certain visitors are in the area by sending an IFTTT SMS/Android push notification to my phone.  I typically get the alert before the visitor gets out of their vehicle thanks to a 9 dB antenna connected to a wifi dongle running on my Raspberry Pi.

The management is made simple due to an HTML web table that reads the database and allows you to give friendly nicknames to MAC addresses.  There are also first and last seen timestamps that help you determine who someone is that visits, name them appropriately, and enable a notification checkbox to get real-time alerts next time they get close.  Probe Request tracking is nothing new but I'm not aware of something like this for personal use, especially something this easy to set up, interface with, and forget until you get an alert.

# Requirements
1) A PC or Raspberry Pi running a Debian OS

2) A WiFi dongle or card that supports monitor mode


# Instructions
1) Install WiFi card/adapter

2) Clone Repo Locally (git clone https://github.com/curtbraz/WhosHere) 

3) Create an IFTTT Incoming Webhook Named "WhosHere" for SMS/Slack/Push Notifications!

4) chmod +x and run INSTALL.sh

5) Visit http://[ip]/WhosHere/ in a browser and enjoy!


# Other Thoughts
Performance is great on my Raspberry Pi 3.  I haven't tested the install script on Ubuntu yet, but it should work just the same.  The new install script will set everything up for you, including configs and credentials.

If you have errors, you may have a wireless adapter installed that does not support monitor mode or you simply may not have the correct drivers installed.  

I'd like to create an app for rooted mobile OS's to make this mobile for when you're on the go, all reporting to a centralized database where users can share information about MAC addresses.  Imagine driving up to the parking lot of a building and knowing if and who was inside!  I also think it would be neat to hook into other IFTTT applets for home automation to do things like turn on lights when you're approaching or interface with my garage door script, thermostat, or speakers.  Enjoy and please let me know if you have any questions or feedback!  


<p align="center"><img align="center" width="500" alt="whoshere-screenshot" src="https://cloud.githubusercontent.com/assets/17833760/14305192/e6076f6a-fb87-11e5-95c7-29b2404f10aa.jpg"></p>


<p align="center"><img align="center" width="600" alt="whoshere-screenshot" src="https://cloud.githubusercontent.com/assets/17833760/14305169/b0b43668-fb87-11e5-9231-a81e2d2828a6.png"></p>


<p align="center"><img align="center" width="300" alt="whoshere-screenshot" src="https://cloud.githubusercontent.com/assets/17833760/14305217/1fe3105e-fb88-11e5-920c-3fd7e1a5c001.png"></p>


<p align="center"><img align="center" width="300" alt="whoshere-screenshot" src="https://i.imgur.com/r6kQzKP.png"></p>
