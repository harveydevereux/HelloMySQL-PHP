<html>
 <head>
  <title>PHP and MySQL: Youtube DataSet</title>
</head>
<script src="https://cdn.plot.ly/plotly-latest.min.js" charset="utf-8"></script>
<body>
  <?php
  require_once 'dbconfig.php';
  include 'vendor/autoload.php';
  use Phpml\FeatureExtraction\TokenCountVectorizer;
  use Phpml\Tokenization\WordTokenizer;
  use Phpml\Classification\MLPClassifier;
  use Phpml\NeuralNetwork\ActivationFunction\PReLU;
  use Phpml\NeuralNetwork\ActivationFunction\Sigmoid;
  use Phpml\NeuralNetwork\Layer;
  use Phpml\NeuralNetwork\Node\Neuron;
  // 38305 rows
  // memory for ML (lol)
  // still only do 1000
  // TODO find a better way?
  ini_set('memory_limit','4096M');
  $N=1000;

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Connected to $dbname at $host successfully.";
    $sql = "SELECT * FROM YoutubeGB WHERE channel_title='Numberphile'";
    $q_channel_id = "SELECT channel_id FROM YoutubeGB";
    $q_likes = "SELECT likes FROM YoutubeGB";
    $q_dislikes = "SELECT dislikes FROM YoutubeGB";
    $q_trend = "SELECT trending_date FROM YoutubeGB";
    $q_description = "SELECT description FROM YoutubeGB";
    $queries = array($q_channel_id,$q_likes,$q_dislikes,$q_trend,$q_description);
    $cols = ["channel_id","likes","dislikes","trending_date","description"];
    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
  } catch (PDOException $pe) {
      die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
  ?>
  <br></br>
  Let's try a SQL query to find the Numberphile videos (if any!)
  <br></br>
<div id="container">
  <table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>Title</th>
            <th>Channel ID</th>
            <th>View</th>
            <th>Likes</th>
            <th>Dislikes</th>
            <th>Comments</th>
            <th>Trending Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($r = $q->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
                <td><?php echo htmlspecialchars($r['channel_id']); ?></td>
                <td><?php echo htmlspecialchars($r['views']); ?></td>
                <td><?php echo htmlspecialchars($r['likes']); ?></td>
                <td><?php echo htmlspecialchars($r['dislikes']); ?></td>
                <td><?php echo htmlspecialchars($r['comment_count']); ?></td>
                <td><?php echo htmlspecialchars($r['trending_date']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php
    set_time_limit(120);
    $likes = array();
    $dislikes = array();
    $channel_id = array();
    $trend = array();
    $description = array();
    $DataSet = array($channel_id,$likes,$dislikes,$trend,$description);
    for ($i=0;$i<count($queries);$i++){
        $q = $pdo->query($queries[$i]);
        $q->setFetchMode(PDO::FETCH_ASSOC);
        // why did the while loop not work here???
        for ($j=0; $j<$N;$j++){
            $r = $q->fetch();
            if ($i<count($queries)-1){
                array_push($DataSet[$i],(int)$r[$cols[$i]]);
            }else{
                array_push($DataSet[$i],$r[$cols[$i]]);
            }
        }
    }
    echo "Next we can tokenise the descriptions and store the token frequencies using:";
?>
    <code>
       <pre>&lt;?php $vectorizer = new TokenCountVectorizer(new WordTokenizer()); ?&gt;</pre>
   </code>
   <code>
       <pre>&lt;?php $vectorizer->fit($corpus); ?&gt;</pre>
    </code>
    <code>
        <pre>&lt;?php $vectorizer->transform($corpus); ?&gt;</pre>
     </code>
<?php
    // echo $DataSet[4][0];
    // echo "<br></br>";
    // echo $DataSet[4][$N-1];
    $vectorizer = new TokenCountVectorizer(new WordTokenizer());
    // Build the dictionary.
    $vectorizer->fit($DataSet[4]);
    // Transform the provided text samples into a vectorized list.
    $vectorizer->transform($DataSet[4]);
    // $DataSet[4] is now a tokenised array!
    // e.g [[0=>2, 1=>1, 2=>0],
    //      [0=>5, 1=>0, 2=>1]]
    // for ($i = 0; $i < count($DataSet[4]); $i++){
    //     echo $DataSet[4][$i][0];
    // }
    echo "Lets see some example tokens!";
    echo "<br></br>";
    echo "Number of tokens: ";
    echo count($DataSet[4][0]);
    echo "<br></br>";
    $zeros = 0;
    echo "[";
    for ($i = 0; $i < count($DataSet[4][0]); $i++){
        if ($zeros == 0){
            echo $i;
            echo "=>";
            echo $DataSet[4][0][$i];
            echo ", ";
            if ($DataSet[4][0][$i] == 0){
                $zeros=1;
                echo "...";
            }
        } else{
            if ($DataSet[4][0][$i]!=0){
                echo $DataSet[4][0][$i];
                echo ", ";
                $zeros=0;
            }
        }
        if ($i % 50 == 0){
            echo "";
        }
    }
    echo "]";
?>
</body>
</html>
