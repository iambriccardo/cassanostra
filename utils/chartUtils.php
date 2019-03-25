<?php

function generatePieGraph($title, $subtitle, $containerId, $data)
{
    echo '<script>
 
var chart = new CanvasJS.Chart("' . $containerId . '", {
	animationEnabled: true,
	title:{
		text: "' . $title .'"
	},
	subtitles: [{
		text: "' . $subtitle .'"
	}],
	data: [{
		type: "pie",
		showInLegend: "true",
		legendText: "{label}",
		indexLabelFontSize: 16,
		indexLabel: "{label} - #percent%",
		dataPoints: ' . json_encode($data, JSON_NUMERIC_CHECK) . '
	}]
});
chart.render();
 
</script>';
}

function generateSplineGraph($title, $yTitle, $containerId, $data) {
    echo '<script>
var chart = new CanvasJS.Chart("' . $containerId . '", {
	animationEnabled: true,
	title:{
		text: "' . $title .'"
	},
	axisY: {
		title: "' . $yTitle .'",
		prefix: "€"
	},
	data: [{
		type: "spline",
		markerSize: 5,
		yValueFormatString: "€#,###.##",
		xValueType: "dateTime",
		dataPoints: ' . json_encode($data, JSON_NUMERIC_CHECK) . '
	}]
});
 
chart.render();
</script>';
}