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

/**
* Display debug stats variables and messages.
* MEMO: keep controllers in this script, to catch very last debugs.
*/

$widget->stats = array();
$widget->messages = array();
$widget->session = array();

if ($PMA->config->get('debug') > 0) {

    if ($PMA->config->get('debug_stats')) {
        // CMDs stats
        if (isset($_SESSION['cmd_stats'])) {
            $stats = $_SESSION['cmd_stats'];
            unset($_SESSION['cmd_stats']);
            $widget->stats[] = 'cmd duration: '.$stats['duration']. 's - '.$stats['memory'];
            if (! is_null($stats['ice'])) {
                $widget->stats[] = 'Ice queries: '.$stats['ice'][0].' during: '.$stats['ice'][1].' s';
            }
        }
        $duration = PMA_statsHelper::duration(PMA_STARTED);
        $memory = PMA_statsHelper::memory();
        $queries = PMA_statsHelper::iceQueries();
        $widget->stats[] = sprintf($TEXT['page_generated'], $duration, $memory);
        if (! is_null($queries)) {
            $widget->stats[] = 'Ice queries: '.$queries[0].' during '.$queries[1].' s';
        }
    }

    if ($PMA->config->get('debug_messages')) {
        foreach ($PMA->messages['debug'] as $key => $array) {
            // Remove too high level debug messages
            if ($PMA->config->get('debug') < $array['level']) {
                continue;
            }

            $data = new stdClass();
            $data->css = 'class';
            $data->lvl = $array['level'];
            $data->error = $array['error'];
            $data->class = '';
            $data->method = '';
            $data->redirection = false;

            $message = $array['msg'];

            if (substr($message, 0, 4) === 'PMA_') {
                $array = explode(' ', $message, 2);
                $method = $array[0];
                $message = '';
                if (isset($array[1])) {
                    $message = $array[1];
                }
                if (false !== strpos($method, '::')) {
                    list($class, $method) = explode('::', $method, 2);
                    $data->class = $class;
                    $data->method = $method;
                    if ($class === 'PMA_core') {
                        $data->css = 'core';
                        $data->redirection = ($method === 'redirection');
                    }
                } else {
                    $data->class = $method;
                }
            }

            $data->class =  htEnc($data->class);
            $data->method =  htEnc($data->method);
            $data->msg = htEnc($message);

            $widget->messages[] = $data;
        }
    }

    if ($PMA->config->get('debug_session')) {

        function arrayLoopSession(array $session, $deep = 0)
        {
            uksort($session, 'strcasecmp');
            $i = str_repeat(' ', $deep);
            $array = array();
            foreach ($session as $key => $value) {

                $obj = new stdClass();
                $obj->isArray = false;
                $obj->deep = $i;
                $obj->key = htEnc($key);
                $obj->value = '';

                if (is_array($value)) {
                    $obj->isArray = true;
                    $array[] = $obj;
                    $array = array_merge_recursive($array, arrayLoopSession($value, $deep+4));
                } else {
                    $obj->value = htEnc($value);
                    array_unshift($array, $obj); // Variables at the top of the array.
                    //$array[] = $obj;
                }
            }
            return $array;
        }

        $widget->session = arrayLoopSession($_SESSION);
    }
}
/**
* STATS.
*/
if (! empty($widget->stats)): ?>
        <div class="debug">
<?php foreach ($widget->stats as $stat): ?>
            <p><?php echo $stat; ?></p>
<?php endforeach; ?>
        </div>
<?php endif;
/**
* MESSAGES.
*/
if (! empty($widget->messages)): ?>
        <div class="debug">
            <h3>Debug messages:</h3>
<?php foreach ($widget->messages as $m): ?>
            <p>
                <span class="level">[<?php echo $m->lvl; ?>]</span>
<?php if ($m->class !== ''): ?>
                <span class="<?php echo $m->css; ?>"><?php echo $m->class; ?></span>
<?php endif;
if ($m->method !== ''): ?>
                <span class="method">::<?php echo $m->method; ?></span>
<?php endif;
if ($m->error): ?>
                <mark class="error">Error
<?php endif; ?>
                    <span class="message"><?php echo $m->msg; ?></span>
<?php if ($m->error): ?>
                </mark>
<?php endif; ?>
            </p>
<?php if ($m->redirection): ?>
            <hr />
<?php endif;
endforeach; ?>
        </div>
<?php endif;
/**
* SESSION.
*/
if (! empty($widget->session)): ?>
        <div class="debug">
            <h3>SESSION id: <mark class="sessid"><?php echo session_id(); ?></mark></h3>
<?php foreach ($widget->session as $o): ?>
            <p>
                <span class="deep"><?php echo $o->deep; ?></span>
<?php if ($o->isArray): ?>
                <span class="array">[<?php echo $o->key; ?>]</span>
<?php else: ?>
                <span class="key">$<?php echo $o->key; ?></span> :
                <span class="value"><?php echo $o->value; ?></span>
<?php endif; ?>
            </p>
<?php endforeach; ?>
        </div>
<?php endif;
/**
* PMA_core OBJECT.
*/
if ($PMA->config->get('debug_object')): ?>
        <div class="debug">
            <pre>
<?php var_dump($PMA); ?>
            </pre>
        </div>
<?php endif;
