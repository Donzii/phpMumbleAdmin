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

$module->set('allowOfflineAuth', $PMA->config->get('allowOfflineAuth'));
$module->set('suAuth', $PMA->config->get('SU_auth'));
$module->set('suEditUserPw', $PMA->config->get('SU_edit_user_pw'));
$module->set('suStartServer', $PMA->config->get('SU_start_vserver'));

$module->set('suRuActive', $PMA->config->get('SU_ru_active'));

$module->set('ruAuth', $PMA->config->get('RU_auth'));
$module->set('ruDeleteAcc', $PMA->config->get('RU_delete_account'));
$module->set('ruEditLogin', $PMA->config->get('RU_edit_login'));
$module->set('pwGenActive', $PMA->config->get('pw_gen_active'));
