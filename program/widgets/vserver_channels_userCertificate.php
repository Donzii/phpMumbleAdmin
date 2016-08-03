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

if (! isset($module->sessionObj->certBlob)) {
    $PMA->messageError('illegal_operation');
    throw new PMA_widgetException();
}
/**
* Transforme DER to PEM
*/
$encode64 = base64_encode($module->sessionObj->certBlob);
/**
* PEM must be 65 characters per line.
*/
$lines = str_split($encode64, 65);
$body = join(PHP_EOL, $lines);

$certificate = '-----BEGIN CERTIFICATE-----'.PHP_EOL;
$certificate .= $body.PHP_EOL;
$certificate .= '-----END CERTIFICATE-----'.PHP_EOL;

$module->certificatePem = $certificate;
