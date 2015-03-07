#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

export

install:

	cp -p ./easyscp_control.service $(SYSTEM_SYSTEMD)
	cp -p ./easyscp_daemon.service $(SYSTEM_SYSTEMD)

uninstall:

	rm -rf $(SYSTEM_SYSTEMD)/easyscp_control.service
	rm -rf $(SYSTEM_SYSTEMD)/easyscp_daemon.service
