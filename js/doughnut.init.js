function drawChart(id, data) {
	var ctx = document.getElementById(id).getContext("2d");
	console.log(ctx);
	var donutChart = new Chart(ctx).Doughnut(data);

	var helpers = Chart.helpers;
    var legendHolder = document.getElementById('legendfor_'+id);
        legendHolder.innerHTML = donutChart.generateLegend();
}
