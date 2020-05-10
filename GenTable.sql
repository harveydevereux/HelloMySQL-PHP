CREATE DATABASE Youtube;
USE Youtube;
DROP TABLE IF EXISTS YoutubeGB;
CREATE TABLE YoutubeGB (`video_id` varchar(1024),
`trending_date` varchar(1024),
`title` varchar(1024),
`channel_title` varchar(1024),
`category_id` varchar(1024),
`publish_time` varchar(1024),
`tags` varchar(1024),
`views` varchar(1024),
`likes` varchar(1024),
`dislikes` varchar(1024),
`comment_count` varchar(1024),
`thumbnail_link` varchar(1024),
`comments_disabled` varchar(1024),
`ratings_disabled` varchar(1024),
`video_error_or_removed` varchar(1024),
`description` varchar(8192));

LOAD DATA INFILE '/var/lib/mysql-files/GBvideos.csv' INTO TABLE YoutubeGB
FIELDS TERMINATED BY ','
LINES TERMINATED BY '
'
IGNORE 1 LINES
;
