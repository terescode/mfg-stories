#!/usr/bin/env bash
# Provision WordPress Stable

WPDBNAME=manufacturingstories
WPTABLEPREFIX=wp_crfqu5gtkb_
WPDIR=public_html
WPSITEURL=http://local.mfgstories.com

# Nginx Logs
mkdir -p ${VVV_PATH_TO_SITE}/log
touch ${VVV_PATH_TO_SITE}/log/error.log
touch ${VVV_PATH_TO_SITE}/log/access.log

# Install and configure the latest stable version of WordPress
if [[ ! -f "${VVV_PATH_TO_SITE}/$WPDIR/index.php" ]]; then

  # Make a database, if we don't already have one
  echo -e "\nCreating database '$WPDBNAME' (if it's not already there)"
  mysql -u root --password=root -e "DROP DATABASE $WPDBNAME;"
  mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS $WPDBNAME;"
  mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON $WPDBNAME.* TO wp@localhost IDENTIFIED BY 'wp';"
  echo -e "\n DB operations done.\n\n"
  
  echo "Downloading WordPress Stable, see http://wordpress.org/..."
  cd ${VVV_PATH_TO_SITE}
  curl -L -O "https://wordpress.org/latest.tar.gz"
  echo "Unpacking WordPress Stable to public_html..."
  noroot tar -xvf latest.tar.gz
  cp -R wordpress/* public_html
  rm -rf wordpress
  rm latest.tar.gz
  cd ${VVV_PATH_TO_SITE}/$WPDIR

  echo "Configuring WordPress Stable..."
  noroot wp core config --dbname=$WPDBNAME --dbuser=wp --dbpass=wp --quiet --extra-php <<PHP
define( 'WP_DEBUG', true );
\$table_prefix = '$WPTABLEPREFIX';
PHP

  echo "Importing production DB copy..."
  mysql -u root --password=root $WPDBNAME < `ls ${VVV_PATH_TO_SITE}/provision/$WPDBNAME*.sql`

  echo "Updating site URLs..."
  noroot wp option update home "$WPSITEURL"
  noroot wp option update siteurl "$WPSITEURL"
  
else

  echo "Updating WordPress Stable..."
  cd ${VVV_PATH_TO_SITE}/$WPDIR
  noroot wp core update

fi

echo "Running npm install..."
cd ${VVV_PATH_TO_SITE}
noroot npm install

if [[ ! -f "${VVV_PATH_TO_SITE}/composer.phar" ]]; then
  echo "Downloading composer locally..."
  noroot node ./setup.js
fi

echo "Running composer install..."
noroot grunt composer:run:install

cd ${VVV_PATH_TO_SITE}/$WPDIR
noroot wp plugin install ${VVV_PATH_TO_SITE}/assets/plugins/blastcaster.zip --activate
noroot wp theme install ${VVV_PATH_TO_SITE}/assets/themes/wpex-noir.zip --activate


