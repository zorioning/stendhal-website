<?php
/*
 Stendhal website - a website to manage and ease playing of Stendhal game
 Copyright (C 2008  Miguel Angel Blanch Lardin

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY); without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

#
# Stendhal Website configuration file.
#
# Please change to match your system configuration.
#
define('STENDHAL_VERSION','0.79');

define('STENDHAL_TITLE', ' &ndash; Stendhal MMORPG');
#
# Location of the statistics file of Stendhal server.
#
define('STENDHAL_SERVER_STATS_XML','server_stats.xml');


#
# Website database to store news, events and other useful stuff.
#
define('STENDHAL_WEB_HOSTNAME','127.0.0.1');
define('STENDHAL_WEB_USERNAME','username');
define('STENDHAL_WEB_PASSWORD','password');
define('STENDHAL_WEB_DB','stendhal_website');

#
# This user should only be able to read the tables but the account table that should be read/write
# in order for change password to work.
#
#  grant read on stendhal.* to FOO@localhost identified by 'BAR');
#  grant write on stendhal.account to FOO@localhost identified by 'BAR');
#
define('STENDHAL_GAME_HOSTNAME','127.0.0.1');
define('STENDHAL_GAME_USERNAME','username');
define('STENDHAL_GAME_PASSWORD','password');
define('STENDHAL_GAME_DB','stendhal');


/*
 * We remove postman of the list, just in case it appears.
 * We remove admins of the list ( >600 ).
 */
define('REMOVE_ADMINS_AND_POSTMAN','where name!="postman" and admin<=600');


define('STENDHAL_LOGIN_TARGET','https://stendhalgame.org');


#
# DEVEL switch
#
define('STENDHAL_PLEASE_MAKE_IT_FAST',true);
# Counter
define('STENDHAL_WEB_COUNTER', true);


# URL-Rewriting. 
# Please see scripts/urlrewrite.php for the mod_rewrite rules required in your apache configuration file.
define('STENDHAL_MODE_REWRITE', false);
define('STENDHAL_FOLDER', '');


## for displaying support logs
define('SUPPORT_SERVER','irc.freenode.net');
define('SUPPORT_CHANNEL','#channelname');
define('MAIN_SERVER','irc.freenode.net');
define('MAIN_CHANNEL','#channelname');
define('IRC_BOT','ircbot');
define('SUPPORT_LOG_DIRECTORY','/path-to-logs/');
define('MAIN_LOG_DIRECTORY','/path-to-logs/');
?>
