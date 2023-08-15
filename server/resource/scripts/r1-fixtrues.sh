#!/bin/bash

# Switch to the specified directory
cd /var/app/r1

# Default database information
DB_HOST=$(grep -oP '(?<=^DB_HOST=).+' .env)
DB_NAME=$(grep -oP '(?<=^DB_NAME=).+' .env)
DB_USER=$(grep -oP '(?<=^DB_USER=).+' .env)
DB_PWD=$(grep -oP '(?<=^DB_PWD=).+' .env)

# Parse command line arguments
while [[ $# -gt 0 ]]
do
  key="$1"
  
  case $key in
    --dbhost)
      DB_HOST="$2"
      shift
      shift
      ;;
    --dbname)
      DB_NAME="$2"
      shift
      shift
      ;;
    --dbuser)
      DB_USER="$2"
      shift
      shift
      ;;
    --dbpwd)
      DB_PWD="$2"
      shift
      shift
      ;;
    *)
      shift
      ;;
  esac
done

# Check if all database information exists
if [[ -z $DB_HOST || -z $DB_NAME || -z $DB_USER || -z $DB_PWD ]]; then
  echo "Missing or empty database configuration. Aborting script."
  exit 1
fi

# Delete the database
deleteDatabase(){
  echo "Start: Delete database $DB_NAME"
  # Use the 'DROP DATABASE' command to drop the specified database
  mysql -h$DB_HOST -u$DB_USER -p$DB_PWD -e "DROP DATABASE IF EXISTS $DB_NAME;"
  echo "Finish: The database $DB_NAME has been successfully deleted."
}

# Set application environment variables
#
# * Parameter: The value of an environment variable
# * Because the.env file is mounted in both the container and the host, you cannot use sed -i to directly modify the source file.
# * The idea here is to write to a temporary file and copy it back
setAppEnv(){
  echo "Start: Set the environment variable: APP_ENV=$1"

  ENV_FILE="/var/app/r1/.env"
  sed "s/APP_ENV=.*/APP_ENV=$1/" "$ENV_FILE" > /tmp/.env.tmp
  cp /tmp/.env.tmp "$ENV_FILE"

  echo "Finish: APP_ENV has been set to '$1' in '.env' file."
}

# Symfony fixtures load
loadData(){
 echo "Start: Load data."

  rm -rf migrations/*

  php bin/console doctrine:database:create > /dev/null
  php bin/console make:migration > /dev/null

  echo yes | php bin/console doctrine:migrations:migrate > /dev/null
  echo yes | php bin/console doctrine:fixtures:load --group=postmanTestData > /dev/null

  echo "Finish: Completion of fixtures load"
}

main(){
  # * Setting the environment variables to the development environment, deleting the database, and loading the test data will run successfully in sequence
  # * Be sure to set APP_ENV to dev before loadData, otherwise you will receive a symfony error.
  setAppEnv "dev" && deleteDatabase && loadData
}

main
