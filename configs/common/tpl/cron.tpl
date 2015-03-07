# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

#
# This file is managed by EasySCP
#
# It contains the cronjobs of {$ADMIN}
#
# Any changes made to this file will be overwritten.
#
{section name=i loop=$COMMAND}
{$MINUTE[i]} {$HOUR[i]} {$DOM[i]} {$MONTH[i]} {$DOW[i]} {$USER[i]} {$COMMAND[i]}
{/section}