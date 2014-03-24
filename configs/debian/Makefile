#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

export

install:
		cd ./apache && $(MAKE) install
		cp -R ./dovecot $(SYSTEM_CONF)
		cp -R ./fcgi $(SYSTEM_CONF)
		cd ./init.d && $(MAKE) install
		cp -R ./iptables $(SYSTEM_CONF)
		cd ./logrotate && $(MAKE) install
		cp -R ./pdns $(SYSTEM_CONF)
		cd ./php5 && $(MAKE) install
		cp -R ./postfix $(SYSTEM_CONF)
		cp -R ./proftpd $(SYSTEM_CONF)
		cp -R ./tpl $(SYSTEM_CONF) 
		cp ./EasySCP_CMD.xml $(SYSTEM_CONF)

uninstall:
		cd ./apache && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/dovecot
		cd ./init.d && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/iptables
		cd ./logrotate && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/pdns
		cd ./php5 && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/postfix
		rm -rf $(SYSTEM_CONF)/proftpd
		rm -rf $(SYSTEM_CONF)/tpl
		rm -rf $(SYSTEM_CONF)/easyscp.conf
		rm -rf $(SYSTEM_CONF)/EasySCP_CMD.xml
		rm -rf $(SYSTEM_CONF)/EasySCP_Config.xml
		rm -rf $(SYSTEM_CONF)/EasySCP_Config_DB.php

