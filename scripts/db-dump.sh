if [ "$#" -lt 1 ]; then
  echo "No file specified"
  exit
fi
mysqldump --lock-tables=false --user=root codecepty-symphonycms-db  > tests/_data/$1.sql 
echo "Dumping SQL to tests/_data/$1.sql"
