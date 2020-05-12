<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '/home/harvey/bin/dbconfig.php';
include 'vendor/autoload.php';
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Classification\MLPClassifier;
use Phpml\NeuralNetwork\ActivationFunction\PReLU;
use Phpml\NeuralNetwork\ActivationFunction\Sigmoid;
use Phpml\NeuralNetwork\Layer;
use Phpml\NeuralNetwork\Node\Neuron;
use Phpml\ModelManager;
// 38305 rows
// memory for ML (lol)
// still only do 1000
// TODO find a better way?
ini_set('memory_limit','4096M');
$N=10;

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  echo "Connected to $dbname at $host successfully.\n";
  $q_channel_id = "SELECT channel_id FROM YoutubeGB";
  $q_likes = "SELECT likes FROM YoutubeGB";
  $q_dislikes = "SELECT dislikes FROM YoutubeGB";
  $q_trend = "SELECT trending_date FROM YoutubeGB";
  $q_description = "SELECT description FROM YoutubeGB";
  $q_category = "SELECT category_id FROM YoutubeGB";
  $queries = array($q_channel_id,$q_likes,$q_dislikes,$q_trend,$q_description,$q_category);
  $cols = ["channel_id","likes","dislikes","trending_date","description","category_id"];
  } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
  $likes = array();
  $dislikes = array();
  $channel_id = array();
  $trend = array();
  $description = array();
  $category = array();
  $DataSet = array($channel_id,$likes,$dislikes,$trend,$description,$category);
  for ($i=0;$i<count($queries);$i++){
      $q = $pdo->query($queries[$i]);
      $q->setFetchMode(PDO::FETCH_ASSOC);
      for ($j=0; $j<$N;$j++){
          $r = $q->fetch();
          if (in_array($i,[0,1,2])){
              array_push($DataSet[$i],(int)$r[$cols[$i]]);
          }else{
              array_push($DataSet[$i],$r[$cols[$i]]);
          }
      }
  }
  $tokens = $DataSet[4];
  $vectorizer = new TokenCountVectorizer(new WordTokenizer());
  // Build the dictionary.
  $vectorizer->fit($tokens);
  // Transform the provided text samples into a vectorized list.
  $vectorizer->transform($tokens);
  // $tokens is now a tokenised array!
  // e.g [[0=>2, 1=>1, 2=>0],
  //      [0=>5, 1=>0, 2=>1]]
  // for ($i = 0; $i < count($DataSet[4]); $i++){
  //     echo $DataSet[4][$i][0];
  // }
  $tfidf = $tokens;
  // now get the tfidf stats from tokens
  $transformer = new TfIdfTransformer($tfidf);
  $transformer->transform($tfidf);
  $classes = array_unique($DataSet[5]);

  $l1 = new Layer(100, Neuron::class, new PReLU);
  $l2 = new Layer(50, Neuron::class, new PReLU);
  $l3 = new Layer(count($classes), Neuron::class, new Sigmoid);
  $S = 100;
  $mlp = new MLPClassifier(count($tfidf[0]), [$l1, $l2, $l3], $classes,$S,new Sigmoid,0.1);
  echo "Training the Neural Network\n";
  $t = time();
  $mlp->train(
    $samples = $tfidf,
    $targets = $DataSet[5]
  );
  echo "Training with N = ";
  echo $N;
  echo " and $S iterations took: ";
  echo time()-$t;
  echo " seconds\n";

  $filepath = 'model/youtubeNN';
  $modelManager = new ModelManager();
  $modelManager->saveToFile($mlp, $filepath);

  echo $mlp->predict($tfidf[0]);
  echo "\n";
  echo  $DataSet[5][0];
  echo "\n";
  $accuracy = 0.0;
  for ($i = 0; $i < $N; $i++){
    if ($mlp->predict($tfidf[$i]) == $DataSet[5][$i]){
      $accuracy += 1;
    }
  }
  echo "Accuracy = ";
  echo $accuracy / $N;
?>
