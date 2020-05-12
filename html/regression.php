<html>
 <head>
  <title>PHP Regression</title>
</head>
<script>
	function plotter(container,type,data,seriesLabel,
									 backgroundColor='rgba(255,0,0,1.0)',
									 borderColor='rgb(255, 0,0)',
									 fill=false) {
	 var chart = new Chart(document.getElementById(container).getContext('2d'), {
		 type: type,
		 data: {
			 datasets: [{
				 label: seriesLabel,
				 backgroundColor: backgroundColor,
				 borderColor: borderColor,
				 fill: fill,
				 data: data
			 }]
		 },
		 options: {
							 	scales: {
							  	xAxes: [{
										type: 'linear'
							    // 	ticks: {
			            //     max: Math.max(datax),
			            //     min: Math.min(datax),
			            //     stepSize: (Math.max(datax)-Math.min(datax))/datax.length
							    //   }
							    }]
							  }
							}
	 });
	 return chart;
	}
	function addData(chart, data, label,
									 backgroundColor='rgba(255,0,0,1.0)',
									 borderColor='rgb(255, 0,0)',
								 	 fill=false) {
    //chart.data.labels.push(label);
		var D = {label: label,
			backgroundColor: backgroundColor,
			borderColor: borderColor,
			fill: fill,
			data: data};
    chart.data.datasets.push(D);
    chart.update();
}
</script>
 <body>
   Samples and Targets:
   <br></br>
   <?php
	 ini_set('display_errors', 1);
	 ini_set('display_startup_errors', 1);
	 error_reporting(E_ALL);
   use Phpml\Regression\LeastSquares;
   include 'vendor/autoload.php';


   $samples = [[60], [60.5], [61], [62], [62.5], [63], [65], [66]];
   $targets = [3.1, 3.4, 3.6, 3.8, 3.9, 4, 4.1, 4.4];
   for ($i = 0; $i < count($samples); $i++){
     echo $samples[$i][0];
     if ($i < count($samples)-1){
       echo ", ";
    }
    else{
      echo "<br></br>";
    }
   }
   for ($i = 0; $i < count($targets); $i++){
     echo $targets[$i];
     if ($i < count($targets)-1){
       echo ", ";
    }
    else{
      echo "<br></br>";
    }
   }
   ?>
   A new least squares object is created and trained on the data:
	 <code>
		<pre>&lt;?php $regression = new LeastSquares(); ?&gt;</pre>
	</code>
	<code>
		<pre>&lt;?php $regression->train($samples, $targets); ?&gt;</pre>
	 </code>
   <?php
    $regression = new LeastSquares();
    $regression->train($samples, $targets);
   ?>
   Now predict on the original data!
   <br></br>
   <?php
   $predictions = array();
   for ($i = 0; $i < count($targets); $i++){
     echo "Target: ";
     echo $targets[$i];
     echo ", Prediction: ";
		 array_push($predictions,$regression->predict($samples[$i]));
		 echo $predictions[$i];
     echo "<br></br>";
   }
   ?>
	 The fitted coefficients and intercept are:
	 <br></br>
	 <?php
	 	$c = $regression->getCoefficients();
		for ($i = 0; $i < count($c); $i++){
			echo $c[$i];
		}
		echo "<br></br>";
		echo $regression->getIntercept();
	 ?>
	 <br></br>
   Now Lets Try Plotting!
   <br></br>

	 <?php
	 $dataPoints = array();
	 $dataPointsPredictions = array();
	 for ($i = 0; $i < count($targets); $i++){
		 array_push($dataPoints,array("y"=>$targets[$i],"x"=>$samples[$i][0]));
		 array_push($dataPointsPredictions,array("y"=>$predictions[$i],"x"=>$samples[$i][0]));
	 }
	 ?>
	 <script>
	 		var data = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
			var dataPred = <?php echo json_encode($dataPointsPredictions, JSON_NUMERIC_CHECK); ?>;
		 	window.onload = function() {
				var chart = plotter('myChart','line',data,'Ground Truth');
				addData(chart,dataPred,"Predictions",borderColor='rgb(0,0,255)',backgroundColor='rgb(0,0,255)');
				console.log(chart);
			}
		</script>
	 <div style="width:75%;">
	 	<canvas id="myChart"></canvas>
   </div>
	 <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
 </body>
</html>
