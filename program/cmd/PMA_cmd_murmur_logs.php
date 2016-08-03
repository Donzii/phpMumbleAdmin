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

class PMA_cmd_murmur_logs extends PMA_cmd
{
    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (isset($this->PARAMS['toggle_log_filter'])) {
            $this->toggleLogsFilter($this->PARAMS['toggle_log_filter']);
        } elseif (isset($this->PARAMS['replace_logs_str'])) {
            $this->toggleLogsStringReplace();
        } elseif (isset($this->PARAMS['toggle_highlight'])) {
            $this->toggleLogsHighlight();
        } elseif (isset($this->PARAMS['logs_search'])) {
            $this->logs_search();
        } elseif (isset($this->PARAMS['reset_logs_search'])) {
            $this->resetLogsSearch();
        }
    }

    private function toggleLogsFilter($mask)
    {
        // Get $logsFilters
        require PMA_DIR_INCLUDES.'murmurLogsFiltersRules.inc';

        // 2047 = total of current logs filters
        if (! ctype_digit($mask) OR ! (2047 & (int)$mask)) {
            $this->debugError(__method__ .' Invalid bitmask: '.$mask);
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $mask = (int)$mask;
        $bitsTotal = $this->PMA->cookie->get('logsFilters');

        if ($mask & $bitsTotal) {
            $bitsTotal -= $mask;
        } else {
            $bitsTotal += $mask;
        }

        $this->PMA->cookie->set('logsFilters', $bitsTotal);
    }

    private function toggleLogsStringReplace()
    {
        $this->PMA->cookie->set('replace_logs_str', ! $this->PMA->cookie->get('replace_logs_str'));
    }

    private function toggleLogsHighlight()
    {
        $this->PMA->cookie->set('highlight_logs', ! $this->PMA->cookie->get('highlight_logs'));
    }

    private function logs_search()
    {
        if ($this->PARAMS['logs_search'] === '') {
            unset($_SESSION['search']['logs']);
        } else {
            $_SESSION['search']['logs'] = $this->PARAMS['logs_search'];
        }
    }

    private function resetLogsSearch()
    {
        unset($_SESSION['search']['logs']);
    }
}
