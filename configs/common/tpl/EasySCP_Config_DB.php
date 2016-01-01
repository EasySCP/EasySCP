<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/* Allgemeine Daten für die Datenbankanbindung und direkter Connect zur Datenbank. Nach Einbindung dieses Segments kann direkt mit MySQL-Befehlen auf die Datenbank zugegriffen werden. */

class DB_Config {
	/* Datenbankserver - In der Regel die IP */
	protected static $DB_HOST		= '{$DB_HOST}';

	/* Datenbankname */
	public static $DB_DATABASE		= '{$DB_DATABASE}';

	/* Datenbank Port */
	protected static $DB_PORT		= 3306;

	/* Datenbankuser */
	protected static $DB_USER		= '{$DB_USER}';

	/* Datenbankpasswort */
	protected static $DB_PASSWORD	= '{$DB_PASSWORD}';

	/* Datenbank Prefix */
	public static $DB_PREFIX		= '';

	/* Datenbank KEY */
	protected static $DB_KEY		= '{$DB_KEY}';

	/* Datenbank IV */
	protected static $DB_IV			= '{$DB_IV}';
}
?>