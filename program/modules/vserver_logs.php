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

$maxLogsSize = $PMA->config->get('vlogs_size');

$start = microtime();
try {
    $getLogs = $prx->getLog(0, $maxLogsSize);
} catch (Ice_MemoryLimitException $e) {
    $PMA->messageError('Ice_MemoryLimitException_logs');
    $maxLogsSize = 100;
    $getLogs = $prx->getLog(0, $maxLogsSize);
}
$PMA->debug('getLog duration '.PMA_statsHelper::duration($start), 3);
/**
* Memo:
 * getLogLen() come with murmur 1.2.3.
 * Murmur_InvalidSecretException class comes with murmur 1.2.3
 * Bug:
 * There is a bug with murmur 1.2.3 and getLogLen,
 * getLogLen require icesecretwrite in this version.
 */
if (method_exists('Murmur_Server', 'getLogLen')) {
    try {
        $getLogsLen = $prx->getLogLen();
    } catch (Murmur_InvalidSecretException $e) {
        // Do nothing.
    }
}
/**
* Setup logs controller.
*/
$module->logs = new PMA_logsMumble();
$module->logs->set('dateFormat', $PMA->cookie->get('date'));
$module->logs->set('timeFormat', $PMA->cookie->get('time'));
$module->logs->set('allowReplacement', $PMA->cookie->get('replace_logs_str'));
$module->logs->set('allowHighlight', $PMA->cookie->get('highlight_logs'));
if (isset($_SESSION['search']['logs'])) {
    $module->logs->set('searchPattern', $_SESSION['search']['logs']);
}
if (! $PMA->config->get('vlogs_admins_highlights') && $PMA->user->is(PMA_USERS_LOWADMINS)) {
    // Never let SuperUsers highlight logs if it's not authorised.
    $module->logs->set('allowHighlight', false);
}
/**
* Add logs text replacement rules
*/
if ($module->logs->get('allowReplacement')) {
    $longString = 'Connection closed: The remote host closed the connection [1]';
    $module->logs->addReplacementRule('str', $longString, 'has left the server');
    $module->logs->addReplacementRule('reg_ex', '/^Stopped$/', 'Server stopped');
}
/**
* Add logs text filters rules
*/
require PMA_DIR_INCLUDES.'murmurLogsFiltersRules.inc'; // Get $logsfilters
foreach ($logsfilters as $array) {
    // Dont add "has left the server" if replacement is not enable
    if ($array['mask'] === 1024 && ! $module->logs->get('allowReplacement')) {
        continue;
    }
    $module->logs->addFilterRule(
        $array['mask'],
        $array['pattern'],
        (bool) ($array['mask'] & $PMA->cookie->get('logsFilters'))
    );
}
/**
* Add logs text highlights rules
*/
if ($module->logs->get('allowHighlight')) {
    if ($module->logs->get('allowReplacement')) {
        $module->logs->addHighlightRule('Lclosed', 'has left the server');
        $module->logs->addHighlightRule('Lerror', 'Server stopped');
    } else {
        $module->logs->addHighlightRule('Lclosed', 'Connection closed: The remote host closed the connection [1]');
    }
    $module->logs->addHighlightRule('Lwarn', 'Ignoring connection:');
    $module->logs->addHighlightRule('Lauth', 'Authenticated');
    $module->logs->addHighlightRule('Lauth', 'New connection:');
    $module->logs->addHighlightRule('Lauth', 'Connection closed');
    $module->logs->addHighlightRule('Linfo', 'Moved to channel');
    $module->logs->addHighlightRule('Ladmin', 'Moved user');
    $module->logs->addHighlightRule('Lwarn', 'not allowed to');
    $module->logs->addHighlightRule('Lerror', 'SSL Error:');
    // Memo: This rule must be before "Moved channel" to avoid a bug
    $module->logs->addHighlightRule('Ladmin', 'Removed channel');
    $module->logs->addHighlightRule('Ladmin', 'Moved channel');
    $module->logs->addHighlightRule('Ladmin', 'Changed speak-state');
    $module->logs->addHighlightRule('Ladmin', 'Added channel');
    $module->logs->addHighlightRule('Ladmin', 'Renamed channel');
    $module->logs->addHighlightRule('Ladmin', 'Updated ACL');
    $module->logs->addHighlightRule('Ladmin', 'Updated banlist');
    $module->logs->addHighlightRule('Lwarn', 'Server is full');
    $module->logs->addHighlightRule('Lwarn', 'Rejected connection');
    $module->logs->addHighlightRule('Linfo', 'Disconnecting ghost');
    $module->logs->addHighlightRule('Linfo', 'Certificate hash is banned.');
    $module->logs->addHighlightRule('Lwarn', '(Server ban)');
    $module->logs->addHighlightRule('Lwarn', '(Global ban)');
    $module->logs->addHighlightRule('Lwarn', 'Timeout');
    $module->logs->addHighlightRule('Lwarn', 'Generating new server certificate.');
    $module->logs->addHighlightRule('Lerror', 'The address is not available');
    $module->logs->addHighlightRule('Lerror', 'The bound address is already in use');
    // Memo: This rule must be before "Announcing server via bonjour" to avoid a bug
    $module->logs->addHighlightRule('Lwarn', 'Stopped announcing server via bonjour');
    $module->logs->addHighlightRule('Lwarn', 'Announcing server via bonjour');
    $module->logs->addHighlightRule('Linfo', 'Server listening on');
    $module->logs->addHighlightRule('Lwarn', 'Binding to address');
    $module->logs->addHighlightRule('Ladmin', 'Unregistered user');
    $module->logs->addHighlightRule('Ladmin', 'Renamed user');
    $module->logs->addHighlightRule('Ladmin', 'Kicked');
    $module->logs->addHighlightRule('Ladmin', 'Kickbanned');
}
/**
* Control each logs.
*/
foreach ($getLogs as $murmurLog) {
    $log = new PMA_logObject();
    $log->text = $murmurLog->txt;
    $log->timestamp = $murmurLog->timestamp;
    $module->logs->addLog($log);
}
/**
* Stats
*/
$stats = $module->logs->get('stats');

if (isset($getLogsLen) && $getLogsLen > $maxLogsSize) {
    $totalLogs = $maxLogsSize.' / '.$getLogsLen;
} else {
    $totalLogs = $stats['total_of_logs'];
}

if ($module->showInfoPanel) {
    $PMA->skeleton->addInfoPanel(sprintf($TEXT['fill_logs'], '<mark>'.$totalLogs.'</mark>' ), 'occasional');
}
/**
* Setup filter menu
*/
$filtersMenu = new filtersMenu();
$filtersMenu->addFilterLink('?cmd=murmur_logs&amp;replace_logs_str', $TEXT['replace_logs_str'], getBooleanImgSrc($module->logs->get('allowReplacement')));
// Highlight link
if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN) OR $PMA->config->get('vlogs_admins_highlights')) {
    $filtersMenu->addFilterLink('?cmd=murmur_logs&amp;toggle_highlight', $TEXT['highlight_logs'], getBooleanImgSrc($module->logs->get('allowHighlight')));
}
$filtersMenu->addSeparation();
foreach ($module->logs->get('filtersRules') as $filter) {
    $css = $filter->active ? 'filtered' : 'unfiltered';
    $count = '(<span class="'.$css.'">'.$stats['total_filtered_by_filter'][$filter->mask].'</span>)';
    $txt = $filter->pattern.' '.$count;
    $img = getBooleanImgSrc($filter->active);
    $filtersMenu->addFilterLink('?cmd=murmur_logs&amp;toggle_log_filter='.$filter->mask, $txt, $img);
}
$filtersMenu->addSeparation();
$filtersMenu->addText($TEXT['log_filtered'].' : <span class="count">'.$stats['total_filtered_logs'].'</span>/'.$stats['total_possible_filter_logs']);
/**
* Setup search widget
*/
$PMA->widgets->newWidget('widget_search');

$searchWidget = new PMA_searchWidget();
$searchWidget->setCMDroute('murmur_logs');
$searchWidget->setCMDname('logs_search');
if (isset($_SESSION['search']['logs'])) {
    $searchWidget->setSearchValue($_SESSION['search']['logs']);
    $searchWidget->setTotalFound($stats['total_search_found']);
    $searchWidget->setRemoveSearchHREF('?cmd=murmur_logs&amp;reset_logs_search');
}
