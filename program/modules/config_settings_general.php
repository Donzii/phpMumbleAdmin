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

$module->debug = $PMA->config->get('debug');

$module->set('siteTitle', $PMA->config->get('siteTitle'));
$module->set('siteComment', $PMA->config->get('siteComment'));
$module->set('logout', $PMA->config->get('auto_logout'));
$module->set('updateCheck', $PMA->config->get('update_check'));
$module->set('murmurVersion', $PMA->config->get('murmur_version_url'));
$module->set('ddlAuthPage', $PMA->config->get('ddl_auth_page'));
$module->set('ddlRefresh', $PMA->config->get('ddl_refresh'));
$module->set('ddlShowCacheUptime', $PMA->config->get('ddl_show_cache_uptime'));
$module->set('showAvatarSa', $PMA->config->get('show_avatar_sa'));
if (PMA_ICE_INT >= 30400) {
    $module->set('IcePhpIncludePath', $PMA->config->get('IcePhpIncludePath'));
}
$module->set('IcePhpIncludePathInfos', get_include_path());
