VibPi
=====

VibPi. Measuring vibration using cheap electronics, a RaspberryPi and Phidget Spatial 0/0/3 which sends vibration events with acceleration data to a server.

Check out http://www.team-umizoomi.nl/vibpi/ , where this project is located (fake data, shaked the device with my hand :).

![alt tag](http://team-umizoomi.nl/vibpi/VIBPI.png)


The "device" is a computer, in my case I used a raspberry pi, with a Phidget Spatial 0/0/3 running Python. The spatial has an accelerometer measuring up to +- 8g (+- 78.5 m/s squared) sampling data at a maximum rate of 1ms. 
When an acceleration measurement difference above 0.1g is measured, the script will trigger an event storing the following measurements with atleast 0.1g at a sample rate of 32ms. Each time it triggered, the script also holds a timer which after 15 seconds of no measurement with 0.1g+, will stop storing data, convert it to JSON and send it to the server.

The server is just some PHP code receiving posts, processing and showing data. My aim is to create a platform where different users can store their meausured data (vibrations in the house for example) and view it.

=====

To get this project working you need the following:

-Computer running Python.
-webhost/server running PHP 5.x+
-MySQL Database 5.3+

-database/db.sql should be imported in your database.

-in server/config.inc.php you should change the following:

---'DBUSER', '[Database Username]'
---'DBPW', '[Database Password]'
---'DBHOST', '[Database Host]'
---'DBNAME', '[Database Name]'

-obviously, upload the files in the server folder to your webhost/server.

-in device/spatial.py you should change the following:

---server post url to http://[yourhost]/[server folder]/post/index.php on line 47
---device key (which should be in the databse too) on line 67

I'm planning to make this more user-friendly(plug-in-play) in the future with raspberry pi's. 
