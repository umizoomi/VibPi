<?php

include('db.php');

addDevice(stripslashes($_POST['devicename']), stripslashes($_POST['devicelocation']));
?>