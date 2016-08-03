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

class PMA_logObject
{
    public $text = '';
    public $timestamp = 0;
    public $ip = 0;
    public $level = '';
}

class PMA_logs
{
    protected $logs = array();

    public $comment;

    protected $dateFormat;
    protected $timeFormat;

    protected $lastTimestamp;

    protected $stats = array();

    protected $allowReplacement = false;
    protected $allowHighlight = false;

    protected $filtersRules = array();
    protected $replacementsRules = array();
    protected $highlightsRules = array();

    protected $searchPattern;

    public function __construct()
    {
        $this->lastTimestamp['day'] = null;
        $this->lastTimestamp['month'] = null;
        $this->lastTimestamp['year'] = null;

        $this->stats['total_of_logs'] = 0;
        $this->stats['total_search_found'] = 0;
        $this->stats['total_filtered_logs'] = 0;
        $this->stats['total_possible_filter_logs'] = 0;
        $this->stats['total_filtered_by_filter'] = array();
    }

    public function set($key, $value)
    {
        $this->$key = $value;
    }

    public function get($key)
    {
        return $this->$key;
    }

    public function addComment($string)
    {
        $this->comment = $string;
    }

    protected function incrementStat($key, $sub = null)
    {
        if (is_null($sub)) {
            ++$this->stats[$key];
        } else {
            ++$this->stats[$key][$sub];
        }
    }

    /**
    * Add a filter rule
    * @param $mask - bitmask
    * @param $pattern - pattern to search for
    * @param $active - is active filter ?
    */
    public function addFilterRule($mask, $pattern, $active)
    {
        $rule = new stdClass();
        $rule->mask = $mask;
        $rule->pattern = $pattern;
        $rule->active = $active;
        $this->filtersRules[] = $rule;

        // Enable filters stats
        $this->stats['total_filtered_by_filter'][$mask] = 0;
    }

    /**
    * Add a replacement rule
    * @param $method - "str" or "reg_ex"
    * @param $pattern - pattern to search for
    * @param $replacement - replacement string
    */
    public function addReplacementRule($method, $pattern, $replacement)
    {
        $rule = new stdClass();
        $rule->method = $method;
        $rule->pattern = $pattern;
        $rule->replacement = $replacement;
        $this->replacementsRules[] = $rule;
    }

    /**
    * Add a highlight rule
    * @param $css - css class to use
    * @param $pattern - string pattern to search for
    */
    public function addHighlightRule($css, $pattern)
    {
        $rule = new stdClass();
        $rule->css = $css;
        $rule->pattern = $pattern;
        $this->highlightsRules[] = $rule;
    }

    protected function applyLogReplacement($text)
    {
        $old = $text;

        foreach ($this->replacementsRules as $rule) {
            if ($rule->method === 'reg_ex') {
                $text = preg_replace($rule->pattern, $rule->replacement, $text);
            } else {
                $text = str_replace($rule->pattern, $rule->replacement, $text);
            }
            // One replacement by log
            if ($text !== $old) {
                break;
            }
        }
        return $text;
    }

    /**
    * Search in log for some text
    *
    * @param $text
    * @return boolean
    */
    protected function searchInLog($text)
    {
        if (false !== stripos($text, $this->searchPattern)) {
            $this->incrementStat('total_search_found');
            return true;
        }
        return false;
    }

    /**
    * Check if the log is filtered
    *
    * @param $text
    * @return boolean
    */
    protected function isfilteredLog($text)
    {
        foreach ($this->filtersRules as $rule) {
            if (false !== stripos($text, $rule->pattern)) {
                $this->incrementStat('total_possible_filter_logs');
                $this->incrementStat('total_filtered_by_filter', $rule->mask);
                if ($rule->active) {
                    $this->incrementStat('total_filtered_logs');
                    return true;
                }
                break;
            }
        }
        return false;
    }

    /**
    * Apply highlights rules on the log text
    *
    * @param $text
    * @return string - the new string of the log text
    */
    protected function applyHighlights($text)
    {
        foreach ($this->highlightsRules as $rule) {
            if (false !== stripos($text, $rule->pattern)) {
                $text = str_replace($rule->pattern, '<mark class="'.$rule->css.'">'.$rule->pattern.'</mark>', $text);
                break;
            }
        }
        return $text;
    }

    /**
    * Check if the day has change.
    *
    * @param $timestamp - timestamp of the current log.
    *
    * @return boolean
    */
    protected function isNewDay($timestamp)
    {
        list($day, $month, $year) = explode('/', date('d/m/Y', $timestamp));

        $last = $this->lastTimestamp;
        // Remember the log date for next log.
        $this->lastTimestamp['day'] = $day;
        $this->lastTimestamp['month'] = $month;
        $this->lastTimestamp['year'] = $year;

        // Check if current day has changed with the last log entry.
        return (
            $day !== $last['day']
            OR $month !== $last['month']
            OR $year !== $last['year']
        );
    }

    /**
    * Core of the class.
    * Log control sequence.
    * @return object - on success or null.
    */
    protected function logControl($log)
    {
        // Logs string replacement.
        if ($this->allowReplacement) {
            $log->text = $this->applyLogReplacement($log->text);
        }
        if (! is_null($this->searchPattern)) {
            if (! $this->searchInLog($log->text)) {
                return;
            }
        }
        if ($this->isfilteredLog($log->text)) {
            return;
        }
        // Add new day flag
        if (($this->isNewDay($log->timestamp))) {
            $log->newDay = strftime($this->dateFormat, $log->timestamp);
        }
        // Format time
        $log->time = strftime($this->timeFormat, $log->timestamp);
        // Html encode
        $log->text = htEnc($log->text, false);
        // Apply Highlights
        $log->text = $this->applyHighlights($log->text);

        return $log;
    }

    /**
    * Control the log before add it.
    */
    public function addLog($log)
    {
        $this->incrementStat('total_of_logs');
        $log = $this->logControl($log);
        if (is_object($log)) {
            $this->logs[] = $log;
        }
    }
}
