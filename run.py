#Server Connection to MySQL:

import MySQLdb
conn = MySQLdb.connect(host= "localhost",
                  user="root",
                  passwd="PASSWORD_GOES_HERE",
                  db="WhosHere")
x = conn.cursor()


## SETS UP YOUR WLAN FOR MONITOR MODE.  YOU MAY NEED TO CHANGE TO THE OUTPUT OF "iw list" FOR YOUR DEVICE THAT SUPPORTS MONITOR MODE!! 

import subprocess
MonMode1 = subprocess.Popen(['sudo','iw','phy','PHY_DEVICE_HERE','interface','add','mon0','type','monitor'],stdout=subprocess.PIPE)
MonMode2 = subprocess.Popen(['sudo','ifconfig','mon0','up'],stdout=subprocess.PIPE)

## CALLS TSHARK AND FILTERS FOR PROBE REQUESTS

proc = subprocess.Popen(['/usr/bin/tshark','-i','mon0','-Y','wlan.fc.type_subtype eq 4','-f','type mgt','-l'],stdout=subprocess.PIPE)
#while True:
#  line = proc.stdout.readline()
for line in iter(proc.stdout.readline, ""):
  if "Probe Request" in line:
   if "da:a1:19" not in line:
    linepostart=line.index(".") + 8
    lineposend=line.index(" ->")
    MAC=line[linepostart:lineposend]
#    print MAC

## DUMPS MAC ADDRESSES FOR DEVICES INTO MySQL TABLE IN REAL-TIME VIA STDOUT

    try:
       x.execute("""CALL InsertMac(%s)""",(MAC))
       conn.commit()
    except:
       conn.rollback()

conn.close()

