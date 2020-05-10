cp ../Downloads/youtube/GBvideos.csv ../Downloads/youtube/USvideos.csv ./
echo "Begining Minor PreProcessing Step" 
julia-1.1 PreProcess.jl
sudo cp USvideos.csv GBvideos.csv /var/lib/mysql-files/
./GenTable.sh --table "YoutubeGB" --csv "GBvideos.csv" --database "Youtube" && cat GenTable.sql
mysql -u root -p Youtube < GenTable.sql
