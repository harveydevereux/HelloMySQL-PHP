<html>
 <head>
  <title>PHP and MySQL: Youtube DataSet</title>
</head>
<script src="https://cdn.plot.ly/plotly-latest.min.js" charset="utf-8"></script>
<body>
  <?php
  require_once 'dbconfig.php';
  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Connected to $dbname at $host successfully.";
    $sql = "SELECT * FROM YoutubeGB WHERE channel_title='Numberphile'";
    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
  } catch (PDOException $pe) {
      die("Could not connect to the database $dbname :" . $pe->getMessage());
  }
  $r = $q->fetch();
  ?>
<div id="container">
  <table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>Title</th>
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
</body>
</html>
