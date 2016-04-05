## CHANGE DATABASE CONNECTION INFORMATION BELOW! 

#Server Connection to MySQL:

import MySQLdb
conn = MySQLdb.connect(host= "localhost",
                  user="USERNAME_GOES_HERE",
                  passwd="PASSWORD_GOES_HERE",
                  db="WhosHere")
x = conn.cursor()


## SETS UP YOUR WLAN FOR MONITOR MODE.  YOU MAY NEED TO CHANGE "phy0" TO THE OUTPUT OF "iw list" FOR YOUR DEVICE THAT SUPPORTS MONITOR MODE!! 

import subprocess
MonMode = "sudo iw phy phy0 interface add mon0 type monitor && ifconfig mon0 up"
import subprocess
process = subprocess.Popen(MonMode.split(), stdout=subprocess.PIPE)
output = process.communicate()[0]


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
       x.execute("""INSERT INTO log(MAC) VALUES (%s)""",(MAC))
       conn.commit()
    except:
       conn.rollback()

conn.close()

