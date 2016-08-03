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

$logOptions = array();
for ($i = 100; $i <= 900; $i += 100) {
    $logOptions[] = $i;
}
for ($i = 1000; $i <= 9500; $i += 500) {
    $logOptions[] = $i;
}
for ($i = 10*1000; $i <= 100*1000; $i += 5000) {
    $logOptions[] = $i;
}

$module->logOptions = array();
foreach ($logOptions as $i) {
    $opt = new stdClass();
    $opt->val = $i;
    $opt->select = ($i === $PMA->config->get('vlogs_size'));
    $opt->format = number_format($i);
    $module->logOptions[] = $opt;
}

$module->set('vlogsAdmins', $PMA->config->get('vlogs_admins_active'));
$module->set('vlogsAdminsHighlights', $PMA->config->get('vlogs_admins_highlights'));
$module->set('pmaLogsKeep', $PMA->config->get('pmaLogs_keep'));
$module->set('pmaLogsSaActions', $PMA->config->get('pmaLogs_SA_actions'));
