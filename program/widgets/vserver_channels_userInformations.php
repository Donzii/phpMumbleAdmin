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

class PMA_userInfos
{
    public $datas = array();

    public function add($desc, $value, $tooltip = '')
    {
        $data = new stdClass();

        $data->value = $value;
        $data->desc = $desc;
        $data->tooltip = $tooltip;

        $this->datas[] = $data;
    }
}

$widget  = new PMA_userInfos();

// IP address
$widget->add($TEXT['ip_addr'], $module->sessionObj->ip);
// Registered user ID
$uid = ($module->sessionObj->userid >= 0) ? $module->sessionObj->userid : $TEXT['unregistered'];
$widget->add($TEXT['registration_id'], $uid);
// User session
$widget->add($TEXT['session_id'], $module->sessionObj->session);
// Online uptime
$widget->add($TEXT['online'], PMA_datesHelper::uptime($module->sessionObj->onlinesecs));
// Idle uptime
$widget->add($TEXT['idle'], PMA_datesHelper::uptime($module->sessionObj->idlesecs));
// TCP mode
$text = $module->sessionObj->tcponly ? $TEXT['yes'] : $TEXT['no'];
$widget->add($TEXT['tcp_mode'], $text);
// Bandwidth
$widget->add($TEXT['bandwidth'], convertSize($module->sessionObj->bytespersec * 10), $TEXT['bandwidth_info']);
// TCP & UDP pings
if (isset($module->sessionObj->tcpPing, $module->sessionObj->udpPing)) {
    $widget->add($TEXT['tcp_ping'], round($module->sessionObj->tcpPing, 2).' ms', $TEXT['ping_info']);
    $widget->add($TEXT['udp_ping'], round($module->sessionObj->udpPing, 2).' ms', $TEXT['ping_info']);
}
// Mumble version
$widget->add($TEXT['mumble_client'], $module->sessionObj->release);
// OS version
$widget->add($TEXT['os'], $module->sessionObj->osversion);
// Certificate hash sha1
if (isset($module->sessionObj->certSha1)) {
    $widget->add($TEXT['cert_hash'], $module->sessionObj->certSha1);
}
