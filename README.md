# HelloMySQL-PHP
Example Machine Learning with MySQL via PHP with Apache2 

If you did want to run this yourself you should install PHP-ML using composer as in
their guide and set up MySQL to look for files in /var/lib/mysql-files/ and
have apache2 look for html in /var/www/html/ if you want to just run the scripts
without tinkering. Also store a dbconfig.php
```php
<?php
    $host = 'localhost';
    $dbname = 'Youtube';
    $username = 'root';
    $password = 'yourpassword';
?>
```
in you home/bin/. Then when ready run
```bash
./UpdateSQLDataBase.sh && ./deploy
```
deploy will attempt to open localhost in firefox so change that if you want.


- Using [Youtube trending data from Kaggle](https://www.kaggle.com/datasnaek/youtube-new) 
- Forming a MySQL database with a bash generated script
- Doing some basic MLP classification from tokenised descriptions and some other simple attributes to
  detemine the category and some other things
- Using [PHP-ML](https://php-ml.readthedocs.io/en/latest/) to do the machine learning because why not learn some more PHP
