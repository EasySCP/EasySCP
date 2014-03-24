#!/usr/bin/make -f

# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

SYSTEM_GUI_ROOT=$(SYSTEM_ROOT)/gui

export

install:

	$(SYSTEM_MAKE_DIRS) $(SYSTEM_GUI_ROOT)

	cp -dR ./domain_default_page $(SYSTEM_GUI_ROOT)
	cp -dR ./domain_disable_page $(SYSTEM_GUI_ROOT)
	cp -dR ./errordocs $(SYSTEM_GUI_ROOT)
	cp -dR ./htdocs $(SYSTEM_GUI_ROOT)
	cp -dR ./include $(SYSTEM_GUI_ROOT)
	cp -dR ./locale $(SYSTEM_GUI_ROOT)
	cp -dR ./phptmp $(SYSTEM_GUI_ROOT)
	cp -dR ./themes $(SYSTEM_GUI_ROOT)
	cp -dR ./tools $(SYSTEM_GUI_ROOT)
	cp -dR ./updates $(SYSTEM_GUI_ROOT)

uninstall:

	rm -rf $(SYSTEM_GUI_ROOT)

.PHONY: install uninstall
