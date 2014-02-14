<?php
include('config.inc.php');
$con = mysql_connect(DBHOST, DBUSER, DBPW);

function rand_md5($length) {
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

function getDeviceInfo($key){
    $sql = "SELECT * FROM devices WHERE `key` = '".$key."'";
    $device = mysql_query($sql) or die (mysql_error());
    
    if (mysql_num_rows($device) <= "0"){
        $sql = "SELECT * FROM devices WHERE `id` = '".$key."'";
        $device = mysql_query($sql) or die (mysql_error());
        if (mysql_num_rows($device) <= "0"){
            return "No device found";   
        }
    }
    while($row = mysql_fetch_assoc($device)){
        $info = array(
            "id" => $row['id'],
            "key" => $row['key'],
            "name" => $row['name'],
            "location" => $row['location']
        );
        return $info;
    }
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
    $sql = "INSERT INTO `measures` (`event`, `timestamp`, `ms`, `x`, `y`, `z`) VALUES $data";
    
    if (!mysql_query($sql)){
        die('Error: ' . mysql_error($con));
    }
    
    return true;
}

function addEvent($data){
    $sql = "INSERT INTO `events` (`device`, `starttime`, `endtime`) VALUES $data";
    
    if (!mysql_query($sql)){
        die('Error: ' . mysql_error($con));
    }
    
    return true;
}
?>