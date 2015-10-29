if [ "$#" -lt 1 ]; then
  echo "No file specified"
  exit
fi

mysqladmin -u root drop codecepty-symphonycms-db -f
mysqladmin -u root create codecepty-symphonycms-db
mysql -u root codecepty-symphonycms-db < tests/_data/$1.sql
