#!/bin/sh
#
# Shell script to access your database. Requires access to
# mysql_db_info, assumed to be in your /webdev/user/$USER file.
USER=`whoami`
DBNAME=`grep "Database name:" /webdev/user/$USER/mysql_db_info | cut -f 3 -d" "`
DBPASSWD=`grep "DB Password:" /webdev/user/$USER/mysql_db_info | cut -f 3 -d" "`
DBHOST=`grep "DB Host:" /webdev/user/$USER/mysql_db_info | cut -f 3 -d" "`
DBPORT=`grep "DB Port:" /webdev/user/$USER/mysql_db_info | cut -f 3 -d" "`

# Access MySQL
mysql --user="$USER" --password="$DBPASSWD" --host="$DBHOST" --port="$DBPORT" "$DBNAME"
