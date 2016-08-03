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
* Write a ( multi-dimensional ) array into a file
*
* @return Bool
*/
class PMA_arrayToFile
{
    /**
    * Ressource of the opened file
    */
    private $fp;
    /**
    * String of the file path
    */
    private $file;
    /**
    * Use the compact feature - boolean.
    */
    private $compact;

    public function __construct($file, $compact = false)
    {
        $this->file = $file;
        $this->compact = $compact;
    }

    /**
    * Core of the class
    */
    public function write(array $array)
    {
        /**
        * w = Open for writing only.
        * Place the file pointer at the beginning of the file and truncate the file to zero length.
        * If the file does not exist, attempt to create it.
        */
        $this->fp = @fopen($this->file, 'wb');

        if (is_resource($this->fp)) {
            /**
            * Start
            */
            fwrite($this->fp, '<?php'.PHP_EOL .PHP_EOL);
            fwrite($this->fp, 'if (! defined(\'PMA_STARTED\')) '.
                                        '{ die(\'ILLEGAL: You cannot call this script directly !\'); }'.PHP_EOL.PHP_EOL);
            fwrite($this->fp, '$array = array('.PHP_EOL);
            /**
            * Write the array
            */
            foreach ($array as $key => $value) {
                $this->writeArray($key, $value);
            }
            /**
            * End
            * Check if no error occured during last fwrite
            * Memo: fwrite returns the number of bytes written, or false on error
            */
            $last = fwrite($this->fp, ');'.PHP_EOL);

            fclose($this->fp);
        }
        return (isset($last) && is_int($last));
    }

    private function writeArray($key, $value, $count = 1)
    {
        if (! is_int($key)) {
            // String key type, add singles quotes.
            $key = '\''.$key.'\'';
        }

        // Enable compact mode
        if ($this->compact === true) {
            $indentation = '';
            $symbole = '=>';
            $EOL = '';
        } else {
            // indentation: 4 spaces
            $indentation = str_repeat('    ', $count);
            $symbole = ' => ';
            $EOL = PHP_EOL;
        }

        $key = $indentation.$key.$symbole;

        if (is_array($value)) {
            if (empty($value)) {
                fwrite($this->fp, $key.'array(),'.$EOL);
            } else {
                fwrite($this->fp, $key.'array('.$EOL);
                foreach ($value as $subKey => $subValue) {
                    $this->writeArray($subKey, $subValue, $count+1);
                }
                fwrite($this->fp, $indentation.'),'.$EOL);
            }
        } else {
            if (is_bool($value)) {
                $value = ($value) ? 'true': 'false';
            /**
            * Memo
            * To avoid some bugs,
            * transform a null value into an empty string.
            */
            } elseif (is_string($value) OR is_null($value)) {
                $value = $this->specialCharsTraitement($value);
                $value = '\''.$value.'\'';
            }
            fwrite($this->fp, $key.$value.','.$EOL);
        }
    }

    private function specialCharsTraitement($str)
    {
        // Add an anti-slash to anti-slash ( \ )
        $str = str_replace('\\', '\\\\', $str);
        // Add an anti-slash to single quote ( ' )
        $str = str_replace("'", "\'", $str);
        // Replace EOL with a space
        $str = replaceEOL($str);
        return $str;
    }
}
