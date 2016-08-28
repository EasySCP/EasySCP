#!/bin/sh

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

# File Completely Rewrote by Thomas Wilbur
Auswahl=""
OS=""
VERSION="1.0.0 Alpha"
clear
# Looks Better
echo '___________                      ____________________________';
echo '\_   _____/____    _________.__./   _____/\_   ___ \______   \';
echo ' |    __)_\__  \  /  ___<   |  |\_____  \ /    \  \/|     ___/';
echo ' |        \/ __ \_\___ \ \___  |/        \\     \___|    |    ';
echo '/_______  (____  /____  >/ ____/_______  / \______  /____|    ';
echo '        \/     \/     \/ \/            \/         \/          ';
echo "You will be installing EasySCP $VERSION on your computer"
echo "Checking if you are root..."

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root trying to run as root" 1>&2
   if [ -f easyscp-setup.sh ]; then
    sudo sh easyscp-setup.sh
    else
    echo "Cannot find easyscp-setup.sh exiting"
    fi
   exit 1
   
fi
echo "Detecting Distro..."
[ -x "/usr/bin/apt-get" ]          && _OSTYPE=1
[ -x "/usr/bin/yum" ]          && _OSTYPE=0
if [ $_OSTYPE -eq 1 ]; then
      Auswahl="Ubuntu"
else
if [ $_OSTYPE -eq 0 ]; then
      Auswahl="CentOS"
      else
      echo "Your operating system is unsupported!"
      exit 1
fi
fi
echo "We will be installing easySCP $VERSION on $Auswahl"
	case "$Auswahl" in
		CentOS)
			

			echo "Build the Software"
			cd $(dirname $0)"/"
			make -f Makefile.$Auswahl install > /dev/null

			echo "Copy required files to your system"
			cp -fLpR /tmp/easyscp/* / > /dev/null

			if [ ! -f /etc/easyscp/EasySCP_Config.xml ]; then
				cp /etc/easyscp/tpl/EasySCP_Config.xml /etc/easyscp/EasySCP_Config.xml
			fi

			while :
			do
				read -p "Secure your mysql installation [Y/N]?" MySQL
				case "$MySQL" in
					[JjYy])
						#echo "ja"
						mysql_secure_installation
						break
						;;
					[Nn])
						#echo "nein"
						break
						;;
					*)
						echo "Wrong selection"

						;;
				esac
			done

			echo "Clean the temporary folders"
			# rm -fR /tmp/easyscp/

			echo "Prepare system config"

			TIMEZONE=`timedatectl | gawk -F'[: ]+' ' $2 ~ /Time/ {print $4}'`

			echo $TIMEZONE > /etc/timezone
			TIMEZONE=$(cat /etc/timezone | sed "s/\//\\\\\//g")
			sed -i".bak" "s/^\;date\.timezone.*$/date\.timezone = \"${TIMEZONE}\" /g" /etc/php.ini
			# TIMEZONE=$((cat /etc/sysconfig/clock) | sed 's/^[^"]*"//' | sed  's/".*//')
			# sed -i".bak" "s/^\;date\.timezone.*$/date\.timezone = \"${TIMEZONE}\" /g" /etc/php.ini

			#touch /var/log/rkhunter.log
			#chmod 644 /var/log/rkhunter.log

			chmod 0700 -R /var/www/easyscp/daemon/

			chmod 0777 /var/www/setup/theme/templates_c/
			chmod 0777 /var/www/setup/config.xml

			chcon --reference /etc/httpd/conf.d /etc/httpd/vhost.d
			if [ ! -f /etc/httpd/conf.d/vhost.conf ]; then
				echo "Include vhost.d/*.conf" >>/etc/httpd/conf.d/vhost.conf
   			fi
			chcon --reference /etc/httpd/conf.d/README /etc/httpd/conf.d/vhost.conf

			while :
			do
				read -p "Configure iptables [y/n]? (if unsure, select yes)" IPTables
				case "$IPTables" in
					[JjYy])
						#echo "ja"
						iptables -D INPUT -j REJECT --reject-with icmp-host-prohibited
						iptables -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
						iptables -A INPUT -j REJECT --reject-with icmp-host-prohibited
						break
						;;
					[Nn])
						#echo "nein"
						break
						;;
					*)
						echo "Wrong selection"

						;;
				esac
			done

			service httpd restart
			# /etc/init.d/httpd restart

			echo "Starting EasySCP Controller"
			/etc/init.d/easyscp_control start > /dev/null

			echo "Starting EasySCP Daemon"
			/etc/init.d/easyscp_daemon start > /dev/null

			echo ""
			echo "To finish Setup, please enter 'http://YOUR_DOMAIN/setup' into your Browser"

			break
			;;
		Debian|Ubuntu)
			

			echo "Build the Software"
			cd $(dirname $0)"/"
			make -f Makefile.$Auswahl install > /dev/null

			echo "Copy required files to your system"
			cp -pR /tmp/easyscp/* / > /dev/null

			if [ ! -f /etc/easyscp/EasySCP_Config.xml ]; then
				cp -p /etc/easyscp/tpl/EasySCP_Config.xml /etc/easyscp/EasySCP_Config.xml
			fi

			while :
			do
				read -p "Secure your mysql installation [Y/N]?" MySQL
				case "$MySQL" in
					[JjYy])
						#echo "ja"
						mysql_secure_installation
						break
						;;
					[Nn])
						#echo "nein"
						break
						;;
					*)
						echo "Wrong selection"

						;;
				esac
			done

			echo "Clean the temporary folders"
			rm -fR /tmp/easyscp/

			echo "Prepare system config"

			touch /var/log/rkhunter.log
			chmod 644 /var/log/rkhunter.log

			chmod 0700 -R /var/www/easyscp/daemon/

			chmod 0777 /var/www/setup/theme/templates_c/
			chmod 0777 /var/www/setup/config.xml

			# Checking for bind9 and remove them if needed
			echo "Checking for Bind9"
			if [ -f /etc/init.d/bind9 ]; then
				yes|apt-get remove bind9
			fi

			# Remove all old apache vhost/site configs
			rm /etc/apache2/sites-enabled/* > /dev/null
			# a2dissite default > /dev/null
			a2ensite easyscp-setup.conf > /dev/null
			/etc/init.d/apache2 restart

			if [ -f /lib/systemd/system/easyscp_control.service ]; then
				chmod 0644 /lib/systemd/system/easyscp_control.service
			fi

			echo "Starting EasySCP Controller"
			/etc/init.d/easyscp_control start > /dev/null

			if [ -f /lib/systemd/system/easyscp_daemon.service ]; then
				chmod 0644 /lib/systemd/system/easyscp_daemon.service
			fi

			echo "Starting EasySCP Daemon"
			/etc/init.d/easyscp_daemon start > /dev/null

			echo ""
			echo "To finish Setup, please enter 'http://YOUR_DOMAIN/setup' into your Browser"

			break
			;;
		Oracle)
			echo "Using Oracle Linux. Please wait."

			echo "Build the Software"
			cd $(dirname $0)"/"
			make -f Makefile.$Auswahl install > /dev/null

			echo "Copy required files to your system"
			cp -RLf /tmp/easyscp/* / > /dev/null

			if [ ! -f /etc/easyscp/EasySCP_Config.xml ]; then
				cp /etc/easyscp/tpl/EasySCP_Config.xml /etc/easyscp/EasySCP_Config.xml
			fi

			while :
			do
				read -p "Secure your mysql installation [Y/N]?" MySQL
				case "$MySQL" in
					[JjYy])
						#echo "ja"
						mysql_secure_installation
						break
						;;
					[Nn])
						#echo "nein"
						break
						;;
					*)
						echo "Wrong selection"

						;;
				esac
			done

			echo "Clean the temporary folders"
			# rm -fR /tmp/easyscp/

			echo "Prepare system config"

			TIMEZONE=`cat /etc/sysconfig/clock | sed "s/^[^\"]*\"//" | sed  "s/\".*//"`
			echo $TIMEZONE > /etc/timezone
			TIMEZONE=$(cat /etc/timezone | sed "s/\//\\\\\//g")
			sed -i".bak" "s/^\;date\.timezone.*$/date\.timezone = \"${TIMEZONE}\" /g" /etc/php.ini
			# TIMEZONE=$((cat /etc/sysconfig/clock) | sed 's/^[^"]*"//' | sed  's/".*//')
			# sed -i".bak" "s/^\;date\.timezone.*$/date\.timezone = \"${TIMEZONE}\" /g" /etc/php.ini

			#touch /var/log/rkhunter.log
			#chmod 644 /var/log/rkhunter.log

			chmod 0700 -R /var/www/easyscp/daemon/

			chmod 0777 /var/www/setup/theme/templates_c/
			chmod 0777 /var/www/setup/config.xml

			chcon --reference /etc/httpd/conf.d /etc/httpd/vhost.d
			if [ ! -f /etc/httpd/conf.d/vhost.conf ]; then
				echo "Include vhost.d/*.conf" >>/etc/httpd/conf.d/vhost.conf
   			fi
			chcon --reference /etc/httpd/conf.d/README /etc/httpd/conf.d/vhost.conf

			while :
			do
				read -p "Configure iptables [Y/N]? (if unsure, select no)" IPTables
				case "$IPTables" in
					[JjYy])
						#echo "ja"
						iptables -D INPUT -j REJECT --reject-with icmp-host-prohibited
						iptables -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
						iptables -A INPUT -j REJECT --reject-with icmp-host-prohibited
						break
						;;
					[Nn])
						#echo "nein"
						break
						;;
					*)
						echo "Wrong selection"

						;;
				esac
			done

			service httpd restart
			# /etc/init.d/httpd restart

			echo "Starting EasySCP Controller"
			/etc/init.d/easyscp_control start > /dev/null

			echo "Starting EasySCP Daemon"
			/etc/init.d/easyscp_daemon start > /dev/null

			echo ""
			echo "To finish Setup, please enter 'http://YOUR_DOMAIN/setup' into your Browser"

			break
			;;
		*)
			echo "Please type a number or the name of your distribution!"
			#Wenn vorher nichts passte
			;;
	esac


exit 0
