#!/bin/sh

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

apt-get purge pdns-server pdns-backend-mysql -y

echo "deb [arch=amd64] http://repo.powerdns.com/ubuntu xenial-auth-40 main" > /etc/apt/sources.list.d/pdns.list
echo "Package: pdns-*
Pin: origin repo.powerdns.com
Pin-Priority: 600" > /etc/apt/preferences.d/pdns

curl https://repo.powerdns.com/FD380FBB-pub.asc | sudo apt-key add - &&
apt-get update
apt-get install pdns-server pdns-backend-mysql -y
