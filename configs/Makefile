#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

export

install:
	# Distribution specifics files
	cd $(HOST_OS) && $(MAKE) install

	# Common files
	cd common && $(MAKE) install

uninstall:
	# Common files
	cd common && $(MAKE) uninstall

	# Distribution specifics files
	cd $(HOST_OS) && $(MAKE) uninstall

.PHONY: install uninstall