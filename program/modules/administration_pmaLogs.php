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

pmaLoadLanguage('logs');

$PMA->widgets->newWidget('widget_logs');

$module->logs = new PMA_logsPMA();
$module->logs->set('dateFormat', $PMA->cookie->get('date'));
$module->logs->set('timeFormat', $PMA->cookie->get('time'));
$module->logs->set('allowHighlight', $PMA->cookie->get('highlight_pmaLogs'));

$pmaLogs = @file(PMA_FILE_LOGS);
if (! is_array($pmaLogs)) {
    $pmaLogs = array();
    $PMA->messageError(PMA_FILE_LOGS.' not found.');
}
$pmaLogs = array_reverse($pmaLogs);
/**
* Setup filters ( based on subtab route).
*/
if ($PMA->router->getRoute('subtab') !== 'all') {
    $module->logs->set('searchLevel', $PMA->router->getRoute('subtab'));
}
/**
* Setup highLights.
*/
if ($module->logs->get('allowHighlight')) {
    $module->logs->addHighlightLevelRule('Lauth', 'auth.info');
    $module->logs->addHighlightLevelRule('Lerror', 'auth.error');
    $module->logs->addHighlightLevelRule('Ladmin', 'action.info');
    $module->logs->addHighlightLevelRule('Linfo', '.info');
    $module->logs->addHighlightLevelRule('Lwarn', '.warn');
    $module->logs->addHighlightLevelRule('Lerror', '.error');

    $module->logs->addHighlightRule('Lauth', 'Successful login');
    $module->logs->addHighlightRule('Lerror', 'Login error');
    $module->logs->addHighlightRule('Lerror', 'Password error');
    $module->logs->addHighlightRule('Lwarn', 'Virtual server deleted');
    $module->logs->addHighlightRule('Lwarn', 'Server stopped');
    $module->logs->addHighlightRule('Linfo', 'Virtual server reseted');
    $module->logs->addHighlightRule('Ladmin', 'Virtual server created');
    $module->logs->addHighlightRule('Linfo', 'Server started');
    $module->logs->addHighlightRule('Lwarn', 'profile deleted');
    $module->logs->addHighlightRule('Linfo', 'profile created');
    $module->logs->addHighlightRule('Ladmin', 'profile updated');
    $module->logs->addHighlightRule('Lwarn', 'Admin account deleted');
    $module->logs->addHighlightRule('Linfo', 'Admin account created');
    $module->logs->addHighlightRule('Ladmin', 'Admin account updated');
}

if ($PMA->config->get('pmaLogs_keep') > 0) {
    $cleanLogs = true;
    $keepLogsDuration = $PMA->config->get('pmaLogs_keep' )* 24 * 3600;
} else {
    $cleanLogs = false;
}

foreach ($pmaLogs as $key => $line) {
    /**
    * MEMO:
    * [0]timestamp ::: [1]localtime ::: [2]logLvl ::: [3]ip ::: [4]txt ::: [5]EOL
    */
    $line = explode(':::', $line);

    // Sanity
    if (count($line) !== 6) {
        unset($pmaLogs[$key]);
        $updatePmaLogFile = true;
        continue;
    }

    $log = new PMA_logObject();
    $log->timestamp = $line[0];
    $log->level = $line[2];
    $log->ip = $line[3];
    $log->text = $line[4];

    if ($cleanLogs) {
        // Remove too old logs
        if (time() > ($keepLogsDuration + $log->timestamp)) {
            unset($pmaLogs[$key]);
            $updatePmaLogFile = true;
            continue;
        }
    }
    $module->logs->addLog($log);
}
// Update log file
if (isset($updatePmaLogFile)) {
    $pmaLogs = array_reverse($pmaLogs);
    file_put_contents(PMA_FILE_LOGS, $pmaLogs);
}

$stats = $module->logs->get('stats');
$module->logs->addComment($stats['total_of_unfiltred'].' / '.$stats['total_of_logs']. ' logs');
