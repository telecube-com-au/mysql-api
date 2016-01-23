#!/bin/sh

#
# This script will install Nginx with self signed certs and run it on port 443
# It is expected that you have configured you firewall to allow access to the api on port 443 (https)
# Only supports Ubuntu version(s) 14.04
# It will install the required packages and configure the server
# 

# Make sure only root can run this script
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi
#

# test the os and version
. /etc/os-release
if [ "$NAME" != "Ubuntu" ] || [ "$VERSION_ID" != "14.04" ]; then
    echo "Sorry, this script only supports Ubuntu version 14.04"
    echo "You are on: $NAME $VERSION_ID"
    exit 1
fi

# make sure aptitude is installed
apt-get -y install aptitude

# get the nginx sources and gpg key
if [ ! -f /etc/apt/sources.list.d/nginx-stable.list ]; then
	echo "deb http://ppa.launchpad.net/nginx/stable/ubuntu $(lsb_release -sc) main" > /etc/apt/sources.list.d/nginx-stable.list 
	apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C 
fi

apt-get -y update

x=0
while true ; do

	if debconf-apt-progress -- aptitude -y install nginx php5 php5-fpm php5-mysql php5-curl php5-cli git rsync iptables
		then 
			echo "done .."
			break
		else 
			echo "oops, trying again in a few seconds .."
			sleep 3
	fi
	
	x=$((x+1))
	if ["$x" = 30] ; then 
		echo "\n\n## ERROR! ##\nFailed to install some critical packages!\n## ## ##\n"
		break 
	fi	
done

# create certs folder
if [ ! -d /var/www/certs ]; then
	mkdir -p /var/www/certs
fi

# create self signed ssl certificate
if [ ! -f /var/www/certs/nginx.crt ]; then
	openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout /var/www/certs/nginx.key -out /var/www/certs/nginx.crt -subj "/C=AU/ST=Victoria/L=Melbourne/O=Telecube Pty Ltd/OU=IT Department/CN=telecube.com.au"
fi

# Find the line, cgi.fix_pathinfo=1, and change the 1 to 0.
sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php5/fpm/php.ini

# Start php5-fpm
service php5-fpm restart

# create a new config file
mv /etc/nginx/sites-available/default /etc/nginx/sites-available/default_BAK_$(date "+%Y-%m-%d-%H:%M:%S")

echo "# Default server configuration" > /etc/nginx/sites-available/default
echo "#" >> /etc/nginx/sites-available/default
echo "server {" >> /etc/nginx/sites-available/default
echo "        # configure ssl" >> /etc/nginx/sites-available/default
echo "        listen 443 ssl default_server;" >> /etc/nginx/sites-available/default
echo "        listen [::]:443 ssl default_server;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # turn off gzip" >> /etc/nginx/sites-available/default
echo "        gzip off;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # path to the certs" >> /etc/nginx/sites-available/default
echo "        ssl_certificate /var/www/certs/nginx.crt;" >> /etc/nginx/sites-available/default
echo "        ssl_certificate_key /var/www/certs/nginx.key;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # doc root" >> /etc/nginx/sites-available/default
echo "        root /var/www/html;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # add .php" >> /etc/nginx/sites-available/default
echo "        index index.php;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # server name" >> /etc/nginx/sites-available/default
echo "        server_name _;" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        	location ~ /(auto|classes|functions|daemons|includes) {" >> /etc/nginx/sites-available/default
echo "        		deny all;" >> /etc/nginx/sites-available/default
echo "        	}" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        	location ~ /init.php {" >> /etc/nginx/sites-available/default
echo "        		deny all;" >> /etc/nginx/sites-available/default
echo "        	}" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # main rule" >> /etc/nginx/sites-available/default
echo "        location / {" >> /etc/nginx/sites-available/default
echo "                try_files \$uri \$uri/ =404;" >> /etc/nginx/sites-available/default
echo "        }" >> /etc/nginx/sites-available/default
echo "" >> /etc/nginx/sites-available/default
echo "        # main rule" >> /etc/nginx/sites-available/default
echo "        location ~ \.php$ {" >> /etc/nginx/sites-available/default
echo "                include snippets/fastcgi-php.conf;" >> /etc/nginx/sites-available/default
echo "                fastcgi_pass unix:/var/run/php5-fpm.sock;" >> /etc/nginx/sites-available/default
echo "        }" >> /etc/nginx/sites-available/default
echo "}" >> /etc/nginx/sites-available/default

service nginx restart


# write the password to the config file in /opt so the control panel has access to the db
echo "<?php" > /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php

echo "// ensure this definition exists before running the script." >> /opt/mysql_api_config.inc.php
echo "if(!defined('MAIN_INCLUDED'))" >> /opt/mysql_api_config.inc.php
echo "	exit(\"Not allowed here!\");" >> /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php
echo "// the master db for write/read" >> /opt/mysql_api_config.inc.php
echo "\$master_db_host = \"\";" >> /opt/mysql_api_config.inc.php
echo "\$master_db_port = \"\";" >> /opt/mysql_api_config.inc.php
echo "\$master_db_user = \"\";" >> /opt/mysql_api_config.inc.php
echo "\$master_db_pass = \"\";" >> /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php
echo "// readonly slaves" >> /opt/mysql_api_config.inc.php
echo "\$db_slaves = array(" >> /opt/mysql_api_config.inc.php
echo "		// readonly slave 1" >> /opt/mysql_api_config.inc.php
echo "		array(" >> /opt/mysql_api_config.inc.php
echo "				\"db_host\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_port\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_user\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_pass\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "			)," >> /opt/mysql_api_config.inc.php
echo "		// readonly slave 2" >> /opt/mysql_api_config.inc.php
echo "		array(" >> /opt/mysql_api_config.inc.php
echo "				\"db_host\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_port\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_user\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "				\"db_pass\" => \"\"," >> /opt/mysql_api_config.inc.php
echo "			)," >> /opt/mysql_api_config.inc.php
echo "		// etc .." >> /opt/mysql_api_config.inc.php
echo "	);" >> /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php
echo "// set strong keys here - a good key generator can be found at: https://www.grc.com/passwords.htm" >> /opt/mysql_api_config.inc.php
echo "\$apikey 			= \"\";" >> /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php
echo "// http basic auth settings" >> /opt/mysql_api_config.inc.php
echo "\$http_auth_enable 	= true; // enable|disable http auth by setting true|false" >> /opt/mysql_api_config.inc.php
echo "\$http_auth_realm 	= \"MySQL API\";" >> /opt/mysql_api_config.inc.php
echo "\$http_auth_user 	= \"\";" >> /opt/mysql_api_config.inc.php
echo "\$http_auth_pass 	= \"\";" >> /opt/mysql_api_config.inc.php
echo "" >> /opt/mysql_api_config.inc.php

# check if the repo has been checked out and clone it if it hasn't
if [ -d /opt/mysql-api ]; then
	cd /opt/mysql-api
	git pull
else
	cd /opt
	git clone https://github.com/telecube/mysql-api.git
fi

rsync -av --delete --exclude '.git*' /opt/mysql-api/html/ /var/www/html/


echo "\n\nDone!"
echo "#########################################"
echo "Your api will be available at the following address(es)"
echo ""
HOST_IP=$(ifconfig | awk -F':' '/inet addr/&&!/127.0.0.1/{split($2,_," ");print _[1]}')
arr=$(echo $HOST_IP | tr " " "\n")
for x in $arr
do
    echo "https://$x/"
done
echo ""
echo "Remember to set passwords and detail in /opt/mysql_api_config.php"
echo "#########################################"



