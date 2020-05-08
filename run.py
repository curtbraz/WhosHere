#Server Connection to MySQL:

import pymysql.cursors
import re

conn = pymysql.connect(host= "localhost",
                  user="whoshere",
                  passwd="PASSWORD_GOES_HERE",
                  db="WhosHere")
x = conn.cursor()


## SETS UP YOUR WLAN FOR MONITOR MODE.  YOU MAY NEED TO CHANGE TO THE OUTPUT OF "iw list" FOR YOUR DEVICE THAT SUPPORTS MONITOR MODE!!

import subprocess
MonMode1 = subprocess.Popen(['sudo','iw','phy','phy1','interface','add','mon0','type','monitor'],stdout=subprocess.PIPE)
MonMode2 = subprocess.Popen(['sudo','ifconfig','mon0','up'],stdout=subprocess.PIPE)

## CALLS TSHARK AND FILTERS FOR PROBE REQUESTS
## /usr/bin/tshark -i mon0 -Y 'wlan.fc.type_subtype eq 4' -l
## /usr/bin/tshark -l -i mon0 -Y 'wlan.fc.type_subtype eq 4' -T fields -e wlan.sa -e radiotap.dbm_antsignal -e wlan_mgt.ssid
proc = subprocess.Popen(['/usr/bin/tshark','-l','-i','mon0','-Y','wlan.fc.type_subtype eq 4','-T','fields','-e','wlan.sa_resolved','-e','radiotap.dbm_antsignal','-e','wlan.ssid'],stdout=subprocess.PIPE)
#proc = subprocess.Popen(['/usr/bin/tshark','-i','mon0','-Y','wlan.fc.type_subtype eq 4','-l'],stdout=subprocess.PIPE)
#while True:
#  line = proc.stdout.readline()
for line in iter(proc.stdout.readline, ""):


#  if "Probe Request" in line:
  if "da:a1:19" not in line:
    ##linepostart=line.index(".") + 10
    #lineposend=line.index("\x09")
    #MAC=line[0:lineposend]

    line = line.rstrip("\n")
    values = line.split("\t")

    MAC=values[0]
    AP=values[2]
    DB=values[1]

    print values

    #print line
    #print MAC

## DUMPS MAC ADDRESSES FOR DEVICES INTO MySQL TABLE IN REAL-TIME VIA STDOUT

    try:
       x.execute("""CALL InsertMac(%s,%s,%s)""",(MAC,DB,AP))
       conn.commit()
    except:
       conn.rollback()

conn.close()
