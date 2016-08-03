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

class PMA_logsPMA extends PMA_logs
{
    protected $searchLevel;
    protected $highlightsLevelRules = array();

    public function __construct()
    {
        parent::__construct();
        $this->stats['total_of_unfiltred'] = 0;
    }

    private function searchForLevel($level)
    {
        return (false !== stripos($level, $this->searchLevel));
    }

    private function applyHighlightLevel($level)
    {
        foreach ($this->highlightsLevelRules as $rule) {
            if (false !== stripos($level, $rule->level)) {
                $level = '<span class="'.$rule->css.'">'.$level.'</span>';
                break;
            }
        }
        return $level;
    }

    public function addHighlightLevelRule($css, $level)
    {
        $rule = new stdClass();
        $rule->css = $css;
        $rule->level = $level;
        $this->highlightsLevelRules[] = $rule;
    }

    /**
    * Add custom controls.
    */
    protected function logControl($log)
    {
        if (! is_null($this->searchLevel)) {
            if (! $this->searchForLevel($log->level)) {
                return;
            }
        }
        $this->incrementStat('total_of_unfiltred');
        $log->level = $this->applyHighlightLevel($log->level);
        $log = parent::logControl($log);
        if (is_object($log)) {
            // Add level and ip to $log->text.
            $log->text = $log->level.' - '.$log->ip.' - '.$log->text;
            return $log;
        }
    }
}
