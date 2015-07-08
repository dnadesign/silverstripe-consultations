<%-- Include JS --%>
<script type="text/javascript" src="consultations/js/chart.js"></script>

<script type="text/javascript">
	var data = $JsData;

	window.onload = function () {
		var ctx = document.getElementById("$ReportID").getContext("2d");
		var donutChart = new Chart(ctx).Doughnut(data);

		var helpers = Chart.helpers;
        var legendHolder = document.getElementById('legendfor_{$ReportID}')
            legendHolder.innerHTML = donutChart.generateLegend();
	}

</script>

<h2>$ReportTitle</h2>
<canvas id="$ReportID" width="200" height="200"></canvas>
<div id="legendfor_{$ReportID}" ><div>
