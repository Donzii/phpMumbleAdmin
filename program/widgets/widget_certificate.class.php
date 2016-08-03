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

class PMA_certificate
{
    private $PEM;
    private $dateTimeFormat;

    private $error = false;
    public $errorText;

    public $datas = array();

    public function setPEM($PEM)
    {
        $this->PEM = $PEM;
    }

    public function setDateTimeFormat($format)
    {
        $this->dateTimeFormat = $format;
    }

    private function error($text)
    {
        $this->error = true;
        $this->errorText = $text;
    }

    public function isError()
    {
        return $this->error;
    }

    private function addTitle($text)
    {
        $data = new stdClass();
        $data->title = true;
        $data->text = strToUpper($text);
        $this->datas[] = $data;
    }

    private function addData($key, $text)
    {
        $data = new stdClass();
        $data->title = false;
        $data->key = $key;
        $data->text = $text;
        $this->datas[] = $data;
    }

    /**
    * Transforme PEM into array.
    */
    public function PemToArray()
    {
        if (! function_exists('openssl_x509_parse')) {
            return $this->error('php-openssl module is not installed');
        }
        $parse = openssl_x509_parse($this->PEM, false);

        if (! is_array($parse)) {
            return $this->error('invalid certificate');
        }
        // remove duplicate / useless entries.
        unset($parse['purposes'], $parse['validFrom'], $parse['validTo']);
        // Put orphelin values into the array key "OTHER".
        foreach ($parse as $key => $value) {
            if (! is_array($value)) {
                $parse['OTHERS'][$key] = $value;
                unset($parse[$key]);
            }
        }
        // Add titles and values.
        foreach ($parse as $key => $array) {
            $this->addTitle($key);
            ksort($array);
            foreach ($array as $k => $v) {
                if ($k === 'validFrom_time_t') {
                    $k = 'Valid from';
                    $v = strftime($this->dateTimeFormat, $v);
                } elseif ($k === 'validTo_time_t') {
                    $k = 'Valid to';
                    $v =  strftime($this->dateTimeFormat, $v);
                }
                $this->addData($k, $v);
            }
        }
    }
}
