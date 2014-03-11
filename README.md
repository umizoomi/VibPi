VibPi
=====

VibPi. Measuring vibration using cheap electronics, a RaspberryPi and Phidget Spatial 0/0/3 which sends vibration events with acceleration data to a server.

Check out http://www.team-umizoomi.nl/vibpi/ , where this project is located (fake data, shaked the device with my hand :).

![alt tag](http://team-umizoomi.nl/vibpi/VIBPI.png)


The "device" is a computer, in my case I used a raspberry pi, with a Phidget Spatial 0/0/3 running Python. The spatial has an accelerometer measuring up to +- 8g (+- 78.5 m/s squared) sampling data at a maximum rate of 1ms. 
When an acceleration measurement difference above 0.1g is measured, the script will trigger an event storing the following measurements with atleast 0.1g at a sample rate of 32ms. Each time it triggered, the script also holds a timer which after 15 seconds of no measurement with 0.1g+, will stop storing data, convert it to JSON and send it to the server.

The server is just some PHP code receiving posts, processing and showing data. My aim is to create a platform where different users can store their meausured data (vibrations in the house for example) and view it.

## To get this project working you need the following hardware: #
=====

* Computer running Python.
* Phidget Spatial 0/0/3
* webhost/server running PHP 5.x+
* webhost/server running MySQL Database 5.3+ 

## Follow these steps to setup the server #
=====

1. database/db.sql should be imported in your database.

2. in server/config.inc.php you should change the following:

    * 'DBUSER', '[Database Username]'
    * 'DBPW', '[Database Password]'
    * 'DBHOST', '[Database Host]'
    * 'DBNAME', '[Database Name]'

3. obviously, upload the contents in /server/ to your server.

## Follow these steps to setup the device #
=====

1. Install [Python 2.7.x](http://www.python.org/download/), if you haven't already.
2. Install the [Phidget Library and Driver](http://www.phidgets.com/docs/Language_-_Python#Libraries_and_Drivers) for your device's systeem.
3. Install [Python Requests](http://docs.python-requests.org/en/latest/user/install/#install)
4. Put the contents of /device/ anywhere you like on your computer.
5. Change the following in device/spatial.py:

    * server post url to http://[yourhost]/[server folder]/post/index.php on line 47
    * device key (which should be in the databse too) on line 67
6. Connect your Phidget and run the script

    * 