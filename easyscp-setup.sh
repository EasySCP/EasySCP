#!/bin/sh

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

Auswahl=""
OS=""

clear
# Looks Better
echo '___________                      ____________________________';
echo '\_   _____/____    _________.__./   _____/\_   ___ \______   \';
echo ' |    __)_\__  \  /  ___<   |  |\_____  \ /    \  \/|     ___/';
echo ' |        \/ __ \_\___ \ \___  |/        \\     \___|    |    ';
echo '/_______  (____  /____  >/ ____/_______  / \______  /____|    ';
echo '        \/     \/     \/ \/            \/         \/          ';

echo ""
echo "1 = CentOS"
echo ""
echo "2 = Debian"
echo ""
echo "3 = Ubuntu"
echo ""

while :
do
    read -p "Please select your distribution: " Name

    if [ "$Name" = "1" ] || [ "$Name" = "CentOS" ]; then
        Auswahl="CentOS"
    fi

    if [ "$Name" = "2" ] || [ "$Name" = "Debian" ]; then
        Auswahl="Debian"
    fi

    if [ "$Name" = "3" ] || [ "$Name" = "Ubuntu" ]; then
        Auswahl="Ubuntu"
    fi

	case "$Auswahl" in
		CentOS)
			echo "Using CentOS. Please wait."

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
				read -p "Secure your MySQL installation [Y/N]?" MySQL
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

			echo "Restarting Web Server"
			/bin/systemctl restart httpd.service

			echo "Starting EasySCP Controller"
			/bin/systemctl start easyscp_control.service

			echo "Starting EasySCP Daemon"
			/bin/systemctl start easyscp_daemon.service

			echo ""
			echo "To finish Setup, please enter 'http://YOUR_DOMAIN/setup' into your Browser"

			break
			;;
		Debian|Ubuntu)
			echo "Using $Auswahl. Please wait."

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
				read -p "Secure your MariaDB/MySQL installation [Y/N]?" MySQL
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

			# Checking for systemd-resolved and disable them
			echo "Checking for systemd-resolved"
			if [ -f /lib/systemd/system/systemd-resolved.service ]; then
				systemctl stop systemd-resolved
				systemctl disable systemd-resolved
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
		*)
			echo "Please type a number or the name of your distribution!"
			#Wenn vorher nichts passte
			;;
	esac
done

exit 0
