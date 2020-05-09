<html>
 <head>
  <title>PHP Regression</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script>
var pointBackgroundColors = [];
var pointBorderColors = [];
function searchForArray(haystack, needle){
  var i, j, current;
  for(i = 0; i < haystack.length; ++i){
    if(needle.length === haystack[i].length){
      current = haystack[i];
      for(j = 0; j < needle.length && needle[j] === current[j]; ++j);
      if(j === needle.length)
        return i;
    }
  }
  return -1;
}
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
             data: data,
             pointBackgroundColor: pointBackgroundColors,
             pointBorderColor: pointBorderColors
         }]
     },
     options: {
                            scales: {
                            xAxes: [{
                                    type: 'linear'
                            }],
                            yAxes: [{
                                    type: 'linear'
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
  <div style="width:75%;">
      <canvas id="myChart"></canvas>
  </div>
  <?php
    use Phpml\Clustering\KMeans;
    include 'vendor/autoload.php';
    function nrand($mean, $sd){
        $x = mt_rand()/mt_getrandmax();
        $y = mt_rand()/mt_getrandmax();
        return sqrt(-2*log($x))*cos(2*pi()*$y)*$sd + $mean;
    }
    srand(3.1415926535897);
    $N = 1000;
    $m1 = -2;
    $m2 = 5;
    $s1 = 3;
    $s2 = 1;
    $samples = array();
    $data = array();
    for ($i = 0; $i < $N; $i++){
        array_push($samples,array(nrand($m1,$s1),nrand($m1,$s1)));
        array_push($data,array("x"=>$samples[$i][0],"y"=>$samples[$i][1]));
        if ($i % 3 == 0){
            array_push($samples,array(nrand($m2,$s2),nrand($m2,$s2)));
            array_push($data,array("x"=>$samples[$i+1][0],"y"=>$samples[$i+1][1]));
        }
    }
    $clusterer = new KMeans(2);
    $clusters = $clusterer->cluster($samples);
  ?>
  <script>
     var data = <?php echo json_encode($data, JSON_NUMERIC_CHECK); ?>;
     var clusters = <?php echo json_encode($clusters, JSON_NUMERIC_CHECK); ?>;
     window.onload = function() {
         var chart = plotter('myChart','scatter', data, 'Data');
         console.log(data);
         console.log(clusters);
         console.log(clusters[0]);
         console.log(Object.values(chart.data.datasets[0].data[0]))
         console.log(Object.values(chart.data.datasets[0].data[0]) in clusters[0]);
         console.log(Object.values(chart.data.datasets[0].data[0]) in clusters[1]);
         for (i = 0; i < chart.data.datasets[0].data.length; i++) {
             var current = Object.values(chart.data.datasets[0].data[i]);
            if (searchForArray(clusters[0],current) != -1)  {
                pointBackgroundColors.push("#90cd8a");
                pointBorderColors.push("#90cd8a");
            } else {
                pointBackgroundColors.push("#f58368");
                pointBorderColors.push("#f58368");
            }
         }
         chart.update();
     }
 </script>
</body>
</html>
