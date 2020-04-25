<html>
 <head>
  <title>PHP Regression</title>
 </head>
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
   ?>
   Now Lets Try Plotting!
   <br></br>
   <?php
   $ydata = array(11,3,8,12,5,1,9,13,5,7);

  // Create the graph. These two calls are always required
  $graph = new Graph(350,250);
  $graph->SetScale('textlin');

  // Create the linear plot
  $lineplot=new LinePlot($ydata);
  $lineplot->SetColor('blue');

  // Add the plot to the graph
  $graph->Add($lineplot);

  // Display the graph
  $graph->Stroke();
    // $x = [];
    // $y = [];
    // $pred = [];
    // for ($i=0;$i<count($samples);$i++){
    //   array_push($x,$samples[$i][0]);
    //   array_push($y,$targets[$i]);
    //   array_push($pred,$regression->predict($samples[$i]));
    // }
    // $graph = new Graph(350,250);
    // $graph->SetScale('textlin');
    // $data_line=new LinePlot(array(1,2,3,4,5));
    // $data_line->SetColor('blue');
    // //$data_line->SetTickLabels($x);
    // $graph->Add($data_line);
    //
    // //$pred_line=new LinePlot($pred);
    // //$pred_line->SetColor('blue');
    // //$pred_line->SetTickLabels($x);
    // //$graph->Add($pred_line);
    // $graph->Stroke();
   ?>
 </body>
</html>
