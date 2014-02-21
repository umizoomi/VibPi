<?php

    include('../db.php');
    
    $data = file_get_contents("php://input");
    $json = json_decode($data, true);
    $device;
    $starttime;
    $endtime;
    $eventid = getNextEventID();
    $measurequery;
    $amount = count($json);
	
	foreach($json as $key => $value){
		if($key == "device"){
			$device = $value;
		} else if($key == "starttime"){
		    $starttime = $value;
		} else if($key == "endtime"){
		    $endtime = $value;
		} else{
		    $measurequery .= "('--','".$value['ts']."','".$value['ms']."','".$value['x']."','".$value['y']."','".$value['z']."'), ";
		}
	}
    $device = getDeviceFromKey($device);
    $eventquery = "('".$device['id']."','".$starttime."','".$endtime."')";
    if (addEvent($eventquery) != true){
        header('Fuck I have an error.', true, 500);
    }
    
    $measurequery = str_replace('--', $eventid, $measurequery);
    $measurequery = chop($measurequery, ", ");
    
    if(addMeasure($measurequery) != true){
        header('Fuck I have an error.', true, 500);
    } else{
        header(':', true, 200);
    }
?>
