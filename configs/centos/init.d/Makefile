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

	$(SYSTEM_MAKE_DIRS) $(ROOT_CONF)/rc.d/init.d

	cp easyscp_control $(ROOT_CONF)/rc.d/init.d

	chown root:root $(ROOT_CONF)/rc.d/init.d/easyscp_control
	chmod 0755 $(ROOT_CONF)/rc.d/init.d/easyscp_control

	cp easyscp_daemon $(ROOT_CONF)/rc.d/init.d

	chown root:root $(ROOT_CONF)/rc.d/init.d/easyscp_daemon
	chmod 0755 $(ROOT_CONF)/rc.d/init.d/easyscp_daemon

uninstall:

	rm -f $(ROOT_CONF)/rc.d/init.d/easyscp_control
	rm -f $(ROOT_CONF)/rc.d/init.d/easyscp_daemon
