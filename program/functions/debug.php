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
* Debug functions.
*/

function pprint($datas)
{
    echo '<pre class="debug">';
    echo '<h2>PPRINT</h2>';
    print_r($datas);
    echo '</pre>';
}

function pdump($datas)
{
    echo '<pre class="debug">';
    echo '<h2>PDUMP</h2>';
    var_dump($datas);
    echo '</pre>';
}

function ptrace()
{
    pprint(debug_backtrace());
}

// function getParentFunction()
// {
//     $backtrace = debug_backtrace();
//     /**
//     * Memo:
//     * key "0" is this function
//     * key "1" is the function calling this.
//     */
//     if (isset($backtrace[2]['class'])) {
//         $class = $backtrace[2]['class'].$backtrace[2]['type'];
//     } else {
//         $class = '';
//     }
//     return $class.$backtrace[2]['function'];
// }
