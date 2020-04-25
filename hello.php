<html>
 <head>
  <title>Hello PHP</title>
 </head>
 <body>
 <?php echo '<p>Hello World</p>'; ?>
 echo of _SERVER['HTTP_USER_AGENT']
 <br></br>
 <?php echo $_SERVER['HTTP_USER_AGENT'];?>
 <br></br>
 seeing if firefox is used:
 <?php
  if (strpos($_SERVER['HTTP_USER_AGENT'],'Firefox') !== FALSE){
    echo 'Detected Firefox!';
  }
 ?>
 </body>
</html>
