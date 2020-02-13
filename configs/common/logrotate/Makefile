#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

export

install:

	$(SYSTEM_MAKE_DIRS) $(ROOT_CONF)/logrotate.d

	cp ./easyscp $(ROOT_CONF)/logrotate.d

uninstall:

	rm -rf $(ROOT_CONF)/logrotate.d/easyscp
