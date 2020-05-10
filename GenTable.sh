#!/bin/sh
# SQL stuff
MYSQL_ARGS="--defaults-file=/etc/mysql/debian.cnf"
DB="mbctest"
DELIM=","
NEWLINE="\n"
# Bash stuff
BOLD=$(tput bold)
NORMAL=$(tput sgr0)

TABLE=""
CSV=""
DATABASE="DEFAULT"

while :; do
  case $1 in
    -h|--help) echo "usage: $0 [-h] [--table name of table to create]
                               [--csv name of csv to read (must be in /var/lib/mysql-files)]
                               [--database name of database to create]"; exit ;;
    --table)
        if [ "$2" ]; then
          TABLE=$2
          shift
        fi ;;
    --csv)
        if [ "$2" ]; then
          CSV="/var/lib/mysql-files/$2"
          shift
        fi ;;
    --database)
        if [ "$2" ]; then
          DATABASE=$2
          shift
        fi ;;
    --)
        shift
        break
        ;;
    -?*)
        printf 'WARN: Unknown option (ignored): %s\n' "$1" >&2
        ;;
    *)
        break
        ;;
  esac
  shift
done

echo "Creating table: " $TABLE
echo "Reading .csv: " $CSV
echo "Will create database: " $DATABASE

echo "${BOLD}Need sudo permission to read $CSV${NORMAL}"
FIELDS=$(sudo head -1 "$CSV" | sed -e 's/'$DELIM'/` varchar(1024),\n`/g' -e 's/\r//g')
FIELDS='`'"$FIELDS"'` varchar(8192)'

echo "USE $DATABASE;
DROP TABLE IF EXISTS $TABLE;
CREATE TABLE $TABLE ($FIELDS);

LOAD DATA INFILE '$CSV' INTO TABLE $TABLE
FIELDS TERMINATED BY '$DELIM'
LINES TERMINATED BY '$NEWLINE'
IGNORE 1 LINES
;" > GenTable.sql

echo "${BOLD}Outputed to './GenTable.sql'${NORMAL}"
#LINES TERMINATED BY "\'/\n\'"
