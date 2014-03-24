#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

install:
	cp -R ./apache $(SYSTEM_CONF)
	cp -R ./awstats $(SYSTEM_CONF)
	cp -R ./cron.d $(SYSTEM_CONF)
	cp -R ./database $(SYSTEM_CONF)
	cp -R ./fcgi $(SYSTEM_CONF)
	cp -R ./pma $(SYSTEM_CONF)
	cp -R ./postfix $(SYSTEM_CONF)
	cp -R ./proftpd $(SYSTEM_CONF)
	cp -R ./roundcube $(SYSTEM_CONF)
	cp -R ./tpl $(SYSTEM_CONF)
	cp ./EasySCP_OS.xml $(SYSTEM_CONF)
	cp ./Iana_TLD.xml $(SYSTEM_CONF)

uninstall:
	rm -rf $(SYSTEM_CONF)/apache
	rm -rf $(SYSTEM_CONF)/awstats
	rm -rf $(SYSTEM_CONF)/cron.d
	rm -rf $(SYSTEM_CONF)/database
	rm -rf $(SYSTEM_CONF)/fcgi
	rm -rf $(SYSTEM_CONF)/pma
	rm -rf $(SYSTEM_CONF)/postfix
	rm -rf $(SYSTEM_CONF)/proftpd
	rm -rf $(SYSTEM_CONF)/roundcube
	rm -rf $(SYSTEM_CONF)/tpl
	rm -rf $(SYSTEM_CONF)/EasySCP_OS.xml
	rm -rf $(SYSTEM_CONF)/Iana_TLD.xml 

.PHONY: install uninstall
