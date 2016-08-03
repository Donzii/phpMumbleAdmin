<?php

 /*
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon David. PMA@ipnoz.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
* %1$s = "HTTP hostname or IP"
* %2$s = "Profile name"
* %3$s = "Mumble server name"
* %4$s = "HTTP url"
* %5$d = "delay time"
*/

$TEXT['pw_mail_title'] = 'Mumble Passwordgenerations Anfrage';

$TEXT['pw_mail_body'] =
'HTTP host : %1$s<br />
Ice profile : %2$s<br />
Mumble server : %3$s<br /><br />

If you don\'t know what this means, or you didn\'t request a password generation for your mumble account,
please just delete this email and nothing will be done.<br /><br />

Please confirm your password generation for your mumble account by following this link:<br /><br />

<a href="%4$s">%4$s</a><br /><br />

This link is valid for %5$d hour(s) and will redirect you to your new password.';
