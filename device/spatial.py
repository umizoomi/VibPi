from ctypes import *
import sys, io, json, time, requests
from datetime import date, datetime
from Phidgets.Phidget import Phidget
from Phidgets.PhidgetException import PhidgetErrorCodes, PhidgetException
from Phidgets.Events.Events import SpatialDataEventArgs, AttachEventArgs, DetachEventArgs, ErrorEventArgs
from Phidgets.Devices.Spatial import Spatial, SpatialEventData, TimeSpan

try:
    spatial = Spatial()
except RuntimeError as e:
    print("Runtime Exception: %s" % e.details)
    print("Exiting....")
    exit(1)

class ShakeEvent:
    """This class holds the data when a shake event is triggered"""

    def __init__(self, device):
        dt = date.today()
        self.datadict = {'device': '%s' % (device)}
        self.startTime = '%s-%s-%s %s' % (dt.year, dt.month, dt.day, datetime.now().strftime('%H:%M:%S'))
        self.c = 1

    def AddData(self, x, y, z):
        dt = date.today()
        datap = '%s-%s-%s %s' % (dt.year, dt.month, dt.day, datetime.now().strftime('%H:%M:%S'))
        self.datadict['%s' % self.c] = {}
        self.datadict['%s' % self.c]['ts'] = '%s' % datap
        self.datadict['%s' % self.c]['ms'] = '%s' % datetime.now().strftime('%f')
        self.datadict['%s' % self.c]['x'] = '%s' % x
        self.datadict['%s' % self.c]['y'] = '%s' % y
        self.datadict['%s' % self.c]['z'] = '%s' % z
        LogPrint("Added Data -%s-" % (self.c))
        self.c += 1

    def CloseEvent(self):
        dt = date.today()
        self.datadict['starttime'] = self.startTime
        self.datadict['endtime'] = '%s-%s-%s %s' % (dt.year, dt.month, dt.day, datetime.now().strftime('%H:%M:%S'))
        self.c -= 1
        LogPrint("%s Measures. Sending Data..." % (self.c))
        SendData(self.datadict)
        self.c = 1

def SendData(data):
    r = requests.post('SERVER_POST_URL_HERE', data=json.dumps(data)) #server post url

    if r.status_code != 200:
        LogPrint("Error while saving data (Server response: %s), saving locally instead.." % (r.status_code))
        dt = date.today()
        datap = '%s-%s-%s--%s' % (dt.year, dt.month, dt.day, datetime.now().strftime('%H-%M-%S'))

        with io.open('event_%s.txt' % (datap), 'w', encoding='utf-8') as f:
            f.write(unicode(json.dumps(data, ensure_ascii=False)))

        LogPrint("Data saved locally")

    else:
        LogPrint("Data has successfully been sent. Server response: %s"  % (r.status_code))

def LogPrint(message):
    dt = date.today()
    datap = '%s-%s-%s %s' % (dt.year, dt.month, dt.day, datetime.now().strftime('%H:%M:%S'))
    print("[%s]: %s" % (datap, message))

key = 'DEVICE_KEY_HERE' #DEVICE KEY
startTime = datetime.now().strftime('%H:%M:%S:%f')
eventData = ShakeEvent(key)
eventActive = False
oldpos = {'x': 0.000000, 'y': 0.000000, 'z': 0.000000}
now = time.time()
start = now + 1
eventStartCounter = now
eventStopCounter = now

def DisplayDeviceInfo():
    print("|------------|----------------------------------|--------------|------------|")
    print("|- Attached -|-              Type              -|- Serial No. -|-  Version -|")
    print("|------------|----------------------------------|--------------|------------|")
    print("|- %8s -|- %30s -|- %10d -|- %8d -|" % (spatial.isAttached(), spatial.getDeviceName(), spatial.getSerialNum(), spatial.getDeviceVersion()))
    print("|------------|----------------------------------|--------------|------------|")
    print("Number of Acceleration Axes: %i" % (spatial.getAccelerationAxisCount()))
    print("Number of Gyro Axes: %i" % (spatial.getGyroAxisCount()))
    print("Number of Compass Axes: %i" % (spatial.getCompassAxisCount()))

def SpatialAttached(e):
    attached = e.device
    LogPrint("Spatial %i Attached!" % (attached.getSerialNum()))

def SpatialDetached(e):
    detached = e.device
    LogPrint("Spatial %i Detached!" % (detached.getSerialNum()))

def SpatialError(e):
    try:
        source = e.device
        LogPrint("Spatial %i: Phidget Error %i: %s" % (source.getSerialNum(), e.eCode, e.description))
    except PhidgetException as e:
        LogPrint("Phidget Exception %i: %s" % (e.code, e.details))

def SpatialData(e):
    source = e.device
    global eventStartCounter
    global eventStopCounter
    global key
    global eventData
    global eventActive
    global oldpos
    global start
    
    for index, spatialData in enumerate(e.spatialData):
        
        x = float(spatialData.Acceleration[0])
        y = float(spatialData.Acceleration[1])
        z = float(spatialData.Acceleration[2])
        
        distanceX = abs(oldpos['x']-x)
        distanceY = abs(oldpos['y']-y)
        distanceZ = abs(oldpos['z']-z)
        
        #print("Acceleration> x: %6f  y: %6f  z: %6f" % (x, y, z))
        #print("AccelerationOffset> x: %6f  y: %6f  z: %6f" % (distanceX, distanceY, distanceZ))
        if time.time() < start:
            print('Waiting for timer to exceed...')

        elif eventActive == False and any([distanceX >= 0.1, distanceY >= 0.1, distanceZ >= 0.1]):
            LogPrint("SHAKE DETECTED, CREATING EVENT...")
            eventData = ShakeEvent(key)
            eventData.AddData(x, y, z)
            eventActive = True
            eventStartCounter = time.time()
            eventStopCounter = eventStartCounter + 15

        elif eventActive == True and any([distanceX >= 0.1, distanceY >= 0.1, distanceZ >= 0.1]):
            eventStartCounter = time.time()
            eventStopCounter = eventStartCounter + 15
            eventData.AddData(x, y, z)
        
        elif eventActive == True and any([distanceX < 0.1, distanceY < 0.1, distanceZ < 0.1]):
            if time.time() < eventStopCounter:
                continue
                
            elif time.time() >= eventStopCounter:
                LogPrint("SHAKE EVENT ENDED, CLOSING EVENT...")
                eventData.CloseEvent()
                eventData = ShakeEvent(key)
                eventActive = False
            else:
                eventData.AddData(x, y, z)
            
        oldpos['x'] = x
        oldpos['y'] = y
        oldpos['z'] = z

try:
    spatial.setOnAttachHandler(SpatialAttached)
    spatial.setOnDetachHandler(SpatialDetached)
    spatial.setOnErrorhandler(SpatialError)
    spatial.setOnSpatialDataHandler(SpatialData)
except PhidgetException as e:
    LogPrint("Phidget Exception %i: %s" % (e.code, e.details))
    LogPrint("Exiting....")
    exit(1)

LogPrint("Opening phidget object....")

try:
    spatial.openPhidget()
except PhidgetException as e:
    LogPrint("Phidget Exception %i: %s" % (e.code, e.details))
    LogPrint("Exiting....")
    exit(1)

LogPrint("Waiting for attach....")

try:
    spatial.waitForAttach(10000)
except PhidgetException as e:
    LogPrint("Phidget Exception %i: %s" % (e.code, e.details))
    try:
        spatial.closePhidget()
    except PhidgetException as e:
        LogPrint("Phidget Exception %i: %s" % (e.code, e.details))
        LogPrint("Exiting....")
        exit(1)
    LogPrint("Exiting....")
    exit(1)
else:
    spatial.setDataRate(32)
    DisplayDeviceInfo()

LogPrint("Press Enter to quit....")

chr = sys.stdin.read(69)

LogPrint("Closing...")

try:
    spatial.closePhidget()
except PhidgetException as e:
    LogPrint("Phidget Exception %i: %s" % (e.code, e.details))
    LogPrint("Exiting....")
    exit(1)

LogPrint("Done.")
exit(0)	