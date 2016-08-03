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

if (! $PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
    $PMA->redirection();
}

$module->langs = PMA_optionsHelper::getLanguages($PMA->config->get('default_lang'));
$module->skins = PMA_optionsHelper::getSkins($PMA->config->get('default_skin'));
$module->timezones = PMA_optionsHelper::getTimezones($PMA->config->get('default_timezone'));
$module->timeFormats = PMA_optionsHelper::getTimeFormats($PMA->config->get('default_time'));
$module->dateFormats = PMA_optionsHelper::getDateFormats($PMA->config->get('default_date'));
$module->systemLocales = PMA_optionsHelper::getSystemLocales($PMA->config->get('defaultSystemLocales'));
$module->systemLocalesProfiles = $PMA->config->get('systemLocalesProfiles');

$module->availableSystemLocales = array();
foreach ($module->systemLocales as $array) {
    // Dont show a system locale that already set in a profile
    if (! in_array($array['locale'], $module->systemLocalesProfiles)) {
        $module->availableSystemLocales[] = $array['locale'];
    }
}
