<?php

DEFINE ('DBUSER', '');
DEFINE ('DBPW', '');
DEFINE ('DBHOST', '');
DEFINE ('DBNAME', '');

if ($dbc = mysql_connect(DBHOST, DBUSER, DBPW)) {
    if (!mysql_select_db(DBNAME)) {
        trigger_error("Can't find database, y u no fix? <br />MySQL Error: " . mysql_error());
        exit();
    } else{
        //echo "connection works";
    }
} else {
    echo "swag";
    trigger_error("Can't connect to MySQL!<br />MySQL Error: " . mysql_error());
    exit();
}

?>