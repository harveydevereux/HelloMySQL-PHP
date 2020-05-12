<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '/home/harvey/bin/dbconfig.php';
include 'vendor/autoload.php';
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\ModelManager;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\Metric\ClassificationReport;
// 38305 rows
// memory for ML (lol)
// still only do 1000
// TODO find a better way?
ini_set('memory_limit','6114M');
$N=3000;

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
  //$classes = array_unique($DataSet[5]);

  $classifier = new SVC(
    Kernel::LINEAR, // $kernel
    1.0,            // $cost
    3,              // $degree
    null,           // $gamma
    0.0,            // $coef0
    0.001,          // $tolerance
    1,            // $cacheSize
    true,           // $shrinking
    false           // $probabilityEstimates, set to true
  );
  echo "Training the SVC\n";
  $S = 2500;
  $train_x = array_slice($tfidf,0,$S);
  $train_y = array_slice($DataSet[5],0,$S);

  $test_x = array_slice($tfidf,$S+1);
  $test_y = array_slice($DataSet[5],$S+1);

  $t = time();
  $classifier->train($train_x,$train_y);
  echo "Training with N = ";
  echo $S;
  echo " took: ";
  echo time()-$t;
  echo " seconds\n";

  // $filepath = 'model/youtubeSVC';
  // $modelManager = new ModelManager();
  // $modelManager->saveToFile($classifier, $filepath);

  $pred = $classifier->predict($test_x);
  $correct = 0.0;
  for($i = 0; $i < count($test_x); $i++){
    if ($pred[$i] == $test_y[$i]){
      $correct += 1;
    }
  }
  echo "\n";
  echo "% Correct: ";
  echo 100*($correct / $N);
  echo "\n";

?>
