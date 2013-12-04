<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'PMA_STARTED' ) ) { die( 'ILLEGAL: You cannot call this script directly !' ); }

/**
* Define users class, sort by importance.
*/
define( 'CLASS_SUPERADMIN', 1 );
define( 'CLASS_ROOTADMIN', 2 );
define( 'CLASS_HEADADMIN', 4 ); // For the futur ;b
define( 'CLASS_ADMIN_FULL_ACCESS', 8 );
define( 'CLASS_ADMIN', 16 );
define( 'CLASS_SUPERUSER', 32 );
define( 'CLASS_SUPERUSER_RU', 64 );
define( 'CLASS_USER', 128 );
define( 'CLASS_UNAUTH', 256 );

/**
* Useful combinaison of user classes.
*/
define( 'ALL_ADMINS', CLASS_ADMIN_FULL_ACCESS + CLASS_ADMIN );
define( 'ALL_PMA_ADMINS', CLASS_ROOTADMIN + CLASS_HEADADMIN + ALL_ADMINS );
define( 'ALL_SUPERUSERS', CLASS_SUPERUSER + CLASS_SUPERUSER_RU );
define( 'LOW_LVL_ADMINS', ALL_ADMINS + ALL_SUPERUSERS );
define( 'ALL_REGISTERED_USERS', CLASS_SUPERUSER_RU + CLASS_USER );
define( 'MUMBLE_USERS', ALL_SUPERUSERS + CLASS_USER );

?>