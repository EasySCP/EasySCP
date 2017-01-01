#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

export

install:
		cd ./apache && $(MAKE) install
		cd ./init.d && $(MAKE) install
		cp -pR ./iptables $(SYSTEM_CONF)
		cp -pR ./pdns $(SYSTEM_CONF)
		cd ./systemd && $(MAKE) install
		cp -pR ./tpl $(SYSTEM_CONF)
		cp -p ./EasySCP_CMD.xml $(SYSTEM_CONF)
		cp -p ./EasySCP_Distro.xml $(SYSTEM_CONF)

uninstall:
		cd ./apache && $(MAKE) uninstall
		cd ./init.d && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/iptables
		rm -rf $(SYSTEM_CONF)/pdns
		cd ./systemd && $(MAKE) uninstall
		rm -rf $(SYSTEM_CONF)/tpl
		rm -rf $(SYSTEM_CONF)/easyscp.conf
		rm -rf $(SYSTEM_CONF)/EasySCP_CMD.xml
		rm -rf $(SYSTEM_CONF)/EasySCP_Config.xml
		rm -rf $(SYSTEM_CONF)/EasySCP_Config_DB.php
		rm -rf $(SYSTEM_CONF)/EasySCP_Distro.xml

