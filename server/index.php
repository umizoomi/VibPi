<?php
include('db.php');

$events = getAllEvents();

for($i = 0; $i <= count($events)-1; $i++){
    $starttime = explode(" ", $events[$i]['starttime']);
    $endtime = explode(" ", $events[$i]['endtime']);
    echo '<a href="event.php?event='.$events[$i]['id'].'">Event: '.$events[$i]['id'].' Device: '.$events[$i]['device'].'. '.$starttime[0].' '.$starttime[1].' - '.$endtime[1].'</a><br>';
}
?>