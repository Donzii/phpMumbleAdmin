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

function getServerSettingsDatas($settings, $default, $custom, $isSuperAdmin)
{
    global $TEXT;

    $tabindex = 0;
    $datas = array();

    foreach ($settings as $key => $array) {

        /**
        * Dont show SuperAdmins parameters.
        */
        if ($array['right'] === 'SA' && ! $isSuperAdmin) {
            continue;
        }

        $data = new stdClass();
        $data->tidx = ++$tabindex;
        $data->key = $key;
        $data->title = $array['name'];
        $data->css = '';
        $data->cssInput = '';
        $data->boolean = ($array['type'] === 'bool');
        $data->boolOptions = array();
        $data->maxlen = '255';
        $data->setting = '';
        $data->value = '';
        $data->reset = false;

        // Custom parameters
        if (isset($custom[$key])) {
            $data->setting = $data->value = $custom[$key];
            $data->css = 'modified';
            $data->reset = true;
        // Default parameters
        } elseif (isset($default[$key])) {
            $data->setting = $default[$key];
        }

        $data->value = htEnc($data->value);

        /**
        * Setup the boolean parameter.
        */
        if ($data->boolean) {
            $data->value = strToLower($data->value);
            if ($data->setting === 'true') {
                $data->setting = $TEXT['enabled'];
                $data->boolOptions[] = getBooleanOptions('disable');
            } elseif ($data->setting === 'false') {
                $data->setting = $TEXT['disabled'];
                $data->boolOptions[] = getBooleanOptions('enable');
            } else {
                $data->boolOptions[] = getBooleanOptions('enable');
                $data->boolOptions[] = getBooleanOptions('disable');
            }
        /**
        * Setup the string parameter.
        */
        } else {
            if (isset($array['maxlen'])) {
                $data->maxlen = $array['maxlen'];
                if ($data->maxlen === '5') {
                    $data->cssInput = 'small';
                }
            }
        }

        $datas[] = $data;
    }

    return $datas;
}

function getBooleanOptions($var)
{
    $opt = new stdClass();
    if ($var === 'enable') {
        $opt->var = 'true';
        $opt->text = 'enable';
    } else {
        $opt->var = 'false';
        $opt->text = 'disable';
    }
    return $opt;
}
