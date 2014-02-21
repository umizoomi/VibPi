<html>
<head>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/globalize/0.1.1/globalize.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.0.0/knockout-min.js"></script>
    <script type="text/javascript" src="http://cdn3.devexpress.com/jslib/13.2.7/js/dx.chartjs.js"></script>
    
</head>
<body>
<?php
include('db.php');

$eventID = $_GET["event"];
$event = getEvent($eventID);
$eventStartTime = explode(" ", $event['starttime']);
$eventEndTime = explode(" ", $event['endtime']);
$device = getDeviceFromId($event['device']);
$measures = getEventMeasures($eventID);
$dataSource= "";
$title = "Location: ".$device['location'].". Date: ".$eventStartTime[0].". Event start/end time: ".$eventStartTime[1]." - ".$eventEndTime[1];

for($i = 0; $i <= count($measures)-1; $i++){
    $dataSource .= "{measure: ".($i+1).", x: ".$measures[$i]['x'].", y: ".$measures[$i]['y'].", z: ".$measures[$i]['z'].", tag: 'Measure. DateTime: ".$measures[$i]['timestamp'].":".$measures[$i]['ms'].". Acceleration: x: ".$measures[$i]['x']." y: ".$measures[$i]['y']." z: ".$measures[$i]['z']."'}";
    
    if ($i <= count($measures)-2){
        $dataSource .=",";
    }
}

?>
<h1>Back to <a href="index.php">overview</a></h1>
<div id="chartContainer" style="max-width:100%;height: 500px;"></div>
<p id="tag" style="text-align: center; font-family: 'Segoe UI Light', 'Helvetica Neue Light', 'Segoe UI', 'Helvetica Neue', 'Trebuchet MS', Verdana; font-weight: 200;font-size: 28px; cursor: default;"></p>
<script>
var zoomingData = [
    <?php echo $dataSource; ?>
];
var series = [{
        argumentField: "measure",
        valueField: "x",
        name: "x"
    }, {
        argumentField: "measure",
        valueField: "y",
        name: "y"
    }, {
        argumentField: "measure",
        valueField: "z",
        name: "z"
    }];

var model = {};
model.chartOptions = {
    argumentAxis: {
       minValueMargin: 0,
       maxValueMargin: 0
    },
    dataSource: zoomingData,
    series: series,
    title: '<?php echo $title; ?>',
    legend:{
        horizontalAlignment: 'center',
        verticalAlignment: 'bottom'
    },
    commonSeriesSettings: {
        tagField: 'tag',
        point: {
            hoverMode: 'allArgumentPoints'
        }
    },
    pointHoverChanged: function(hoveredPoint){
        if(hoveredPoint.isHovered()){
            $('#tag').html(hoveredPoint.tag);
        } else{
            $('#tag').html('');
        }
    }
};

model.rangeOptions = {
    size: {
        height: 120
    },
    margin: {
        left: 10
    },
    dataSource: zoomingData,
    chart: {
        series: series
    },
    behavior: {
        callSelectedRangeChanged: "onMovingComplete"
    },
    selectedRangeChanged: function (e) {
        var zoomedChart = $("#chartContainer #zoomedChart").dxChart("instance");
        zoomedChart.zoomArgument(e.startValue, e.endValue);
    }
};

var html = [
    '<div id="zoomedChart" data-bind="dxChart: chartOptions" style="height: 335px;margin: 0 0 15px"></div>',
    '<div data-bind="dxRangeSelector: rangeOptions" style="height: 80px"></div>'
].join('');

$("#chartContainer").append(html);
ko.applyBindings(model, $("#chartContainer")[0]);

</script>
</body>
</html>