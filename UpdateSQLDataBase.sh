BOLD=$(tput bold)
NORMAL=$(tput sgr0)

cp kaggle/GBvideos.csv ./
echo "Begining Long PreProcessing Step" 
julia-1.1 -O3 PreProcess.jl
echo "${BOLD}Next steps require sudo and MySQL access, CTRL-C if you want to do it yourself${NORMAL}"
echo "${BOLD}Allow 'sudo cp GBvideos.csv /var/lib/mysql-files/'?${NORMAL}"
sudo cp GBvideos.csv /var/lib/mysql-files/
./GenTable.sh --table "YoutubeGB" --csv "GBvideos.csv" --database "Youtube" && cat GenTable.sql
echo "${BOLD}Allow 'mysql -u root -p Youtube < GenTable.sql'?${NORMAL}"
mysql -u root -p Youtube < GenTable.sql
