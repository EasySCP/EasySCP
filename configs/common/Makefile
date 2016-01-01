#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

install:
	cp -pR ./apache $(SYSTEM_CONF)
	cp -pR ./awstats $(SYSTEM_CONF)
	cp -pR ./database $(SYSTEM_CONF)
	cp -pR ./dovecot $(SYSTEM_CONF)
	cp -pR ./fcgi $(SYSTEM_CONF)
	cp -pR ./pma $(SYSTEM_CONF)
	cp -pR ./postfix $(SYSTEM_CONF)
	cp -pR ./proftpd $(SYSTEM_CONF)
	cp -pR ./roundcube $(SYSTEM_CONF)
	cp -pR ./tpl $(SYSTEM_CONF)
	cp -p ./EasySCP_OS.xml $(SYSTEM_CONF)
	cp -p ./Iana_TLD.xml $(SYSTEM_CONF)

uninstall:
	rm -rf $(SYSTEM_CONF)/apache
	rm -rf $(SYSTEM_CONF)/awstats
	rm -rf $(SYSTEM_CONF)/database
	rm -rf $(SYSTEM_CONF)/dovecot
	rm -rf $(SYSTEM_CONF)/fcgi
	rm -rf $(SYSTEM_CONF)/pma
	rm -rf $(SYSTEM_CONF)/postfix
	rm -rf $(SYSTEM_CONF)/proftpd
	rm -rf $(SYSTEM_CONF)/roundcube
	rm -rf $(SYSTEM_CONF)/tpl
	rm -rf $(SYSTEM_CONF)/EasySCP_OS.xml
	rm -rf $(SYSTEM_CONF)/Iana_TLD.xml 

.PHONY: install uninstall
