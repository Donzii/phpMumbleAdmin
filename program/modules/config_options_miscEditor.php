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

$module->editDefaultOptions = $PMA->user->isMinimum(PMA_USER_ROOTADMIN);
$module->editSuperAdmin = $PMA->user->is(PMA_USER_SUPERADMIN);
$module->editAdminPassword = $PMA->user->isPmaAdmin();

if ($module->editSuperAdmin) {
    $PMA->widgets->newHiddenPopup('optionsSuperAdminEditor');
}
if ($module->editAdminPassword) {
    $PMA->widgets->newHiddenPopup('optionsPasswordEditor');
}

$module->langs = PMA_optionsHelper::getLanguages($PMA->cookie->get('lang'));
$module->skins = PMA_optionsHelper::getSkins($PMA->cookie->get('skin'));
$module->timezones = PMA_optionsHelper::getTimezones($PMA->cookie->get('timezone'));
$module->timeFormats = PMA_optionsHelper::getTimeFormats($PMA->cookie->get('time'));
$module->dateFormats = PMA_optionsHelper::getDateFormats($PMA->cookie->get('date'));
$module->systemLocalesProfiles = PMA_optionsHelper::getSystemLocalesProfiles(
    $PMA->config->get('systemLocalesProfiles'),
    $PMA->cookie->get('installed_localeFormat')
);
$module->uptimeOptions = array();
for ($i = 1; $i <= 3; ++$i) {
    $opt = new stdClass();
    $opt->val = $i;
    $opt->uptime = PMA_datesHelper::uptime(21686399, $i); // 250 jours 59m59s
    $opt->select = ($i === $PMA->cookie->get('uptime'));
    $module->uptimeOptions[] = $opt;
}
$module->set('vserversLogin', $PMA->cookie->get('vserver_login'));
