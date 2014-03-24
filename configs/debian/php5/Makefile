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

	$(SYSTEM_MAKE_DIRS) $(SYSTEM_CONF)/php5

	cp -R ./backup $(SYSTEM_CONF)/php5
	cp -R ./working $(SYSTEM_CONF)/php5

	cp ./suhosin.ini $(SYSTEM_CONF)/php5/working/suhosin.ini

	chown root:root $(SYSTEM_CONF)/php5/working/suhosin.ini
	chmod 0644 $(SYSTEM_CONF)/php5/working/suhosin.ini

uninstall:

	rm -rf $(SYSTEM_CONF)/php5
