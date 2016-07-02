#! /bin/bash

BASE_DIR=$(dirname $0)/..
DUMP_FILE=$BASE_DIR/db.sql

mysqldump -u iseplive -p iseplive --no-data --skip-comments --triggers --routines > $DUMP_FILE
sed -i -e 's/ AUTO_INCREMENT=[0-9]\+//' $DUMP_FILE

