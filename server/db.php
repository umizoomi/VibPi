<?php
include('config.inc.php');
$con = mysql_connect(DBHOST, DBUSER, DBPW);

function rand_md5($length){
    $max = ceil($length / 32);
    $random = '';
    
    for ($i = 0; $i < $max; $i ++) {
        $random .= md5(microtime(true).mt_rand(10000,90000));
    }
    
    return substr($random, 0, $length);
}

function checkDuplicateKey($key){
    $dupesql = "SELECT * FROM devices WHERE `key` = '".$key."'";
    $duperaw = mysql_query($dupesql) or die('SQL error :' . mysql_error());
    
    if (mysql_num_rows($duperaw) > "0"){
        return true;
    } else{
        return false;
    }
}

function getNextEventID(){
    $sqlresult = mysql_query("SHOW TABLE STATUS LIKE 'events'") or die('SQL error :' . mysql_error());
    $data = mysql_fetch_assoc($sqlresult);
    
    return $data['Auto_increment'];
}

function getDeviceFromKey($key){
    $sql = "SELECT * FROM devices WHERE `key` = '".$key."'";
    $data = mysql_query($sql) or die (mysql_error());
    
    if (mysql_num_rows($data) <= "0"){
        $device = array(
            'id'        => 'Device not found with key '.$key.';( ',
            'key'       => 'Device not found with key '.$key.';( ',
            'name'      => 'Device not found with key '.$key.';( ',
            'location'  => 'Device not found with key '.$key.';( ');
            
        return $device;
    }
    
    while($row = mysql_fetch_assoc($data)){
        $device = array(
            'id'        => $row['id'],
            'key'       => $row['key'],
            'name'      => $row['name'],
            'location'  => $row['location']);
            
        return $device;
    }
}

function getDeviceFromId($id){
    $sql = "SELECT * FROM devices WHERE `id` = '".$id."'";
    $data = mysql_query($sql) or die (mysql_error());
    
    if (mysql_num_rows($data) <= "0"){
        $device = array(
            'id'        => 'Device not found with id '.$id.';( ',
            'key'       => 'Device not found with id '.$id.';( ',
            'name'      => 'Device not found with id '.$id.';( ',
            'location'  => 'Device not found with id '.$id.';( ');
            
        return $device;
    }
    
    while($row = mysql_fetch_assoc($data)){
        $device = array(
            'id'        => $row['id'],
            'key'       => $row['key'],
            'name'      => $row['name'],
            'location'  => $row['location']);
            
        return $device;
    }
}

function getAllDevices(){
    $sql = "SELECT * FROM devices";
    $data = mysql_query($sql) or die(mysql_error());
    $devices = array();
    
    while($row = mysql_fetch_assoc($device)){
        $device = array(
            'id'        => $row['id'],
            'key'       => $row['key'],
            'name'      => $row['name'],
            'location'  => $row['location']);
        
        array_push($devices, $device);
    }
    
    return $devices;
}

function getAllEvents(){
    $sql = "SELECT * FROM events ORDER BY starttime DESC";
    $data = mysql_query($sql) or die(mysql_error());
    $events = array();
    
    while($row = mysql_fetch_assoc($data)){
        $event = array(
            'id'        => $row['id'], 
            'device'    => $row['device'], 
            'starttime' => $row['starttime'], 
            'endtime'   => $row['endtime']);
            
        array_push($events, $event);
    }
    
    return $events;
}

function getEvent($id){
    $sql = "SELECT * FROM events WHERE `id` = '".$id."'";
    $data = mysql_query($sql) or die(mysql_error());
    $event =  array();
    
    while($row = mysql_fetch_assoc($data)){
        $event = array(
            'id'        => $row['id'], 
            'device'    => $row['device'], 
            'starttime' => $row['starttime'], 
            'endtime'   => $row['endtime']);
    }
    
    return $event;
}

function getEventMeasures($id){
    $sql = "SELECT * FROM measures WHERE `event` = '".$id."' ORDER BY timestamp, ms";
    $data = mysql_query($sql) or die(mysql_error());
    $measures = array();
    
    while($row = mysql_fetch_assoc($data)){
        $measure = array(
            'id'        => $row['id'],  
            'timestamp' => $row['timestamp'],  
            'ms'        => $row['ms'],  
            'x'         => $row['x'],  
            'y'         => $row['y'],  
            'z'         => $row['z']);
            
        array_push($measures, $measure);
    }
    
    return $measures;
}

function addDevice($name, $location){
    $key = rand_md5(18);
    
    while(checkDuplicateKey($key) == true){
        $key = rand_md5(18);
    }
    
    $sql = "INSERT INTO devices VALUES (0,'".$key."','".mysql_real_escape_string($name)."','".mysql_real_escape_string($location)."')";
    
    if (!mysql_query($sql)){
        die('Error: ' . mysql_error($con));
    }
    
    echo "Device added :D! Key: " . $key . ' . Dis key be important.';
}

function addMeasure($data){
    $sql = "INSERT INTO `measures` (`event`, `timestamp`, `ms`, `x`, `y`, `z`) VALUES ".$data;
    
    if (!mysql_query($sql)){
        die('Error: ' . mysql_error());
    }
    
    return true;
}

function addEvent($data){
    $sql = "INSERT INTO `events` (`device`, `starttime`, `endtime`) VALUES ".$data;
    
    if (!mysql_query($sql)){
        die('Error: ' . mysql_error());
    }
    
    return true;
}
?>