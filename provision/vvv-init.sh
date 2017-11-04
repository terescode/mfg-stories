#!/usr/bin/env bash
# Provision WordPress Stable

# Make a database, if we don't already have one
echo -e "\nCreating database 'wp_mfgstories' (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS manufacturingstories"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON manufacturingstories.* TO wp@localhost IDENTIFIED BY 'wp';"
echo -e "\n DB operations done.\n\n"

# Nginx Logs
mkdir -p ${VVV_PATH_TO_SITE}/log
touch ${VVV_PATH_TO_SITE}/log/error.log
touch ${VVV_PATH_TO_SITE}/log/access.log

# Install and configure the latest stable version of WordPress
if [[ ! -d "${VVV_PATH_TO_SITE}/public_html/index.php" ]]; then

  echo "Downloading WordPress Stable, see http://wordpress.org/..."
  cd ${VVV_PATH_TO_SITE}
  curl -L -O "https://wordpress.org/latest.tar.gz"
  echo "Unpacking WordPress Stable to public_html..."
  noroot tar -xvf latest.tar.gz
  cp -R wordpress/* public_html
  rm -rf wordpress
  rm latest.tar.gz
  cd ${VVV_PATH_TO_SITE}/public_html

  echo "Configuring WordPress Stable..."
  noroot wp core config --dbname=manufacturingstories --dbuser=wp --dbpass=wp --quiet --extra-php <<PHP
define( 'WP_DEBUG', true );
PHP

  echo "Importing production DB copy..."
  mysql -u root --password=root wp_mfgstories < `ls ${VVV_PATH_TO_SITE}/provision/manufacturingstories*.sql`

else

  echo "Updating WordPress Stable..."
  cd ${VVV_PATH_TO_SITE}/public_html
  noroot wp core update

fi

echo "Running npm install..."
cd ${VVV_PATH_TO_SITE}
npm install

