<html>
 <head>
  <title>PHP Regression</title>
</head>
<script>
//TODO
// Multiple lines per plot
// ! plot function to modify? 
	function scatterplotter(container,data,title,xlabel,ylabel,lineColor="#0400FF",markerColor="#0400FF") {
	 var chart = new CanvasJS.Chart(container, {
		 title: {
			 text: title
		 },
		 axisY: {
			 title: ylabel
		 },
		 axisX: {
			 title: xlabel
		 },
		 data: [{
			 type: "line",
			 dataPoints: data,
			 lineColor: lineColor,
			 markerColor: markerColor
		 }]
	 });
	 chart.render();
	}
</script>
 <body>
   Samples and Targets:
   <br></br>
   <?php
   use Phpml\Regression\LeastSquares;
   include 'vendor/autoload.php';
   require_once ('deps/jpgraph/src/jpgraph.php');
   require_once ('deps/jpgraph/src/jpgraph_line.php');


   $samples = [[60], [61], [62], [63], [65]];
   $targets = [3.1, 3.6, 3.8, 4, 4.1];
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
   A new least squares object is created and trained on the data
   <br></br>
   <?php
    $regression = new LeastSquares();
    $regression->train($samples, $targets);
   ?>
   Now predict on the original data!
   <br></br>
   <?php
   for ($i = 0; $i < count($targets); $i++){
     echo "Target: ";
     echo $targets[$i];
     echo ", Prediction: ";
     echo $regression->predict($samples[$i]);
     echo "<br></br>";
   }
   ?>$regression->predict($samples[$i]);

   Now Lets Try Plotting!
   <br></br>

	 <?php
	 $dataPoints = array();
	 for ($i = 0; $i < count($targets); $i++){
		 array_push($dataPoints,array("y"=>$targets[$i],"label"=>$samples[$i][0]));
	 }
	 ?>
	 <script>
	 		var data = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
		 	window.onload = function() {
				scatterplotter("chartContainer",data,"PHP Regression","Samples","Targets");
			}
		</script>
	 <div id="chartContainer" style="height: 370px; width: 100%;"></div>
	 <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
 </body>
</html>
