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
* Miscellaneous functions relative to PMA
*/

/**
* Autoload PMA classes
*/
function __autoload($class)
{
    if ($class === 'HTML') {
        require PMA_DIR_LIB.'helpers/'.$class.'.php';
    } elseif (substr($class, 0, 3) === 'PMA') {
        if (substr($class, 0, 7) === 'PMA_cmd') {
            require PMA_DIR_PROG.'cmd/'.$class.'.php';
        } elseif (substr($class, -6) === 'Helper') {
            require PMA_DIR_LIB.'helpers/'.$class.'.php';
        } else {
            require PMA_DIR_LIB.$class.'.php';
        }
    }
}

/**
* Load requested PMA file language
*/
function pmaLoadLanguage($id)
{
    global $TEXT;
    $lang = PMA_core::getInstance()->cookie->get('lang');
    // Load english file,
    // this language should be up to date ( even if my english is bad. Fixed translation are welcome :o).
    require PMA_DIR_LANGUAGES.'en_EN/'.$id.'.loc.php';
    // Load translated file if it's not english.
    if ($lang !== 'en_EN' && is_file($file = PMA_DIR_LANGUAGES.$lang.'/'.$id.'.loc.php')) {
        include $file;
    }
}

function pmaGetText($key)
{
    global $TEXT;
    if (isset($TEXT[$key])) {
        $text = $TEXT[$key];
    } else {
        $text = $key;
    }
    return $text;
}

/**
* Convert class constant by class name string
*
* @param $class integer
*
* @return string class name
*/
function pmaGetClassName($class)
{
    switch ($class ) {
        case PMA_USER_SUPERADMIN:
            return 'SuperAdmin';
        case PMA_USER_ROOTADMIN:
            return 'RootAdmin';
        case PMA_USER_HEADADMIN:
            return 'HeadAdmin';
        case PMA_USER_ADMIN:
            return 'Admin';
        case PMA_USER_SUPERUSER:
            return 'SuperUser';
        case PMA_USER_SUPERUSER_RU:
            return 'SuperUserRu';
        case PMA_USER_MUMBLE:
            return 'MumbleUser';
        case PMA_USER_UNAUTH:
            return 'unauth';
    }
}

/**
* Ice_Exceptions has differente functions to get exceptions messages (almost empty btw).
* This function try to find the getMessage function.
* @return array - class & text of an exception.
*/
function pmaGetExceptionClass($e)
{
    // Ice_unkown exception
    if (isset($e->unknown)) {
        $text = $e->unknown;
    // Ice_MarshalException exception
    } elseif (isset($e->reason)) {
        $text = $e->reason;
     // Ice_EndpointParseException exception
    } elseif (isset($e->str)) {
        $text = $e->str;
    } else {
        $text = $e->getMessage();
    }
    /**
    * MEMO : Ice_exceptions messages can return an EOL and break PMA logs
    */
    // $text = replaceEOL($text);
    $text = trim($text);

    $class = get_class($e);

    /**
    * Depending the version of php-Ice or Murmur,
    * some errors don't have the specific class we can found on superior version.
    * Rewrite the class.
    */
    switch (true) {
        case (false !== stripos($text, 'DNSException')):
            $class = 'Ice_DNSException';
            break;
        case (false !== stripos($text, 'ConnectionRefusedException')):
            $class = 'Ice_ConnectionRefusedException';
            break;
        case (false !== stripos($text, 'ConnectTimeoutException')):
            $class = 'Ice_ConnectTimeoutException';
            break;
        case (false !== stripos($text, 'InvalidSecretException')):
            $class = 'Murmur_InvalidSecretException';
            break;
    }

    $array['text'] = $text;
    $array['class'] = $class;
    return $array;
}

/**
* Common exceptions operations.
*/
function pmaExceptionsOperations($e)
{
    $PMA = PMA_core::getInstance();
    $array = pmaGetExceptionClass($e);
    $isMurmurException = false;

    if (substr($array['class'], 0, 7) === 'Murmur_') {

        $isMurmurException = true;

        switch ($array['class']) {
            case 'Murmur_InvalidChannelException':
                // The chanel don't exist, remove session navigation.
                unset(
                    $_SESSION['page_vserver']['cid'],
                    $_SESSION['page_vserver']['aclID'],
                    $_SESSION['page_vserver']['groupID']
                );
                break;
            case 'Murmur_InvalidSessionException':
                // The userSession ID is invalid, remove session navigation.
                unset($_SESSION['page_vserver']['uSess']);
                break;
            case 'Murmur_InvalidUserException':
                // The Mumble user registration ID is invalid, remove navigation.
                $PMA->router->removeMisc('mumbleRegistration');
                break;
            /**
            * MEMO :
            * icesecret come with murmur 1.21.
            * Murmur_InvalidSecretException class come with murmur 1.22.
            * icesecretwrite come with murmur 1.2.3.
            * icesecret and icesecretread will always be checked during ice connection initialization.
            */
            case 'Murmur_InvalidSecretException':
                // Rename the write InvalidSecretException
                $array['class'] = 'Murmur_InvalidSecretException_write';
                break;
            case 'Murmur_MurmurException';
                // Rename the unknown MurmurException
                $array['class'] = 'Murmur_UnknownException';
                break;
        }
    }
    /**
    * Send error and debugs messages.
    */
    if (! $isMurmurException) {
        $PMA->messageError('ice_invalid_slice_file');
        $PMA->debugError('The slice file is invalid.');
    }

    $PMA->messageError($array['class']);
    $PMA->debugError('Exception class => '.$array['class']);
    $PMA->debugError('Exception message => '.$array['text']);
    foreach($e->getTrace() as $array) {
        $PMA->debugError('trace : #'.$array['line'].' '.$array['file'].' ('.$array['function'].')');
    }
}
