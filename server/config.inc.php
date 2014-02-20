<?php

DEFINE ('DBUSER', '');
DEFINE ('DBPW', '');
DEFINE ('DBHOST', '');
DEFINE ('DBNAME', '');

if ($dbc = mysql_connect(DBHOST, DBUSER, DBPW)){
    if (!mysql_select_db(DBNAME)) {
        trigger_error("Can't find database '".$DBNAME."', y u no fix? <br />MySQL Error: " . mysql_error());
        exit();
    }
} else{
    trigger_error("Can't connect to MySQL!<br />MySQL Error: " . mysql_error());
    exit();
}
?>