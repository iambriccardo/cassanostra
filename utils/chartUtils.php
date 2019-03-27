<?php

function generatePieGraph($title, $subtitle, $containerId, $data)
{
    echo '<script>
CanvasJS.addColorSet("chartColors",
                [

                "#2196F3",
                "#FFC107",
                "#4CAF50",
                "#E91E63",
                "#5E35B1"                
                ]);
 
var chart = new CanvasJS.Chart("' . $containerId . '", {
	animationEnabled: true,
	colorSet: "chartColors",
	title:{
		text: "' . $title . '",
		fontFamily: "BlinkMacSystemFont,Segoe UI,Roboto,sans-serif"
	},
	subtitles: [{
		text: "' . $subtitle . '",
		fontFamily: "BlinkMacSystemFont,Segoe UI,Roboto,sans-serif"
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

function generateSplineGraph($title, $yTitle, $containerId, $data)
{
    echo '<script>
CanvasJS.addColorSet("chartColors",
                [
                "#' . getAccentColor() . '"            
                ]);
var chart = new CanvasJS.Chart("' . $containerId . '", {
	animationEnabled: true,
	colorSet: "chartColors",
	title:{
		text: "' . $title . '",
		fontFamily: "BlinkMacSystemFont,Segoe UI,Roboto,sans-serif"
	},
	axisY: {
		title: "' . $yTitle . '",
		prefix: "€",
		fontFamily: "BlinkMacSystemFont,Segoe UI,Roboto,sans-serif"
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