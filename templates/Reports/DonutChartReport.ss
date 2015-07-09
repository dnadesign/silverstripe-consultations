<script type="text/javascript">
	var data = $jsData;
	window.addEventListener('load', function() { drawChart('$ReportID', data) }, false);
</script>

<div class="dounught-report">
	<h2>$ReportTitle</h2>
	<canvas id="$ReportID" width="200" height="200"></canvas>
	<div id="legendfor_{$ReportID}" ></div>
</div>
