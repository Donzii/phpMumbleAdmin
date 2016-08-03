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

if (! defined('PMA_STARTED')) { die('ILLEGAL: You cannot call this script directly !'); }

$widget->show = false;

if ($PMA->user->isSuperior(PMA_USER_UNAUTH)) {
    $widget->show = true;
    $widget->title = sprintf(
        $TEXT['autologout_at'], strftime($PMA->cookie->get('time'), time() + ($PMA->config->get('auto_logout') * 60))
    );

    if ($PMA->user->is(PMA_USER_SUPERADMIN) && rand(1, 10) === 5) {
        // Never forget, you are the King in this place :D .
        $className = 'The King of the Hill';
    } else {
        $className = pmaGetClassName($PMA->user->class);
    }

    $widget->set('login', $PMA->user->login);
    $widget->set('className', $className);
}
