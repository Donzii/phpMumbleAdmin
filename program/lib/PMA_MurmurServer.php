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

class PMA_MurmurServer
{
    protected $prx;
    protected $defaultConf = array();
    protected $customConf;

    public function __construct($prx, $defaultConf)
    {
        $this->prx = $prx;
        $this->defaultConf = $defaultConf;
    }

    public function __toString()
    {
        return $this->prx->__toString();
    }

    /**
    * **********************************************
    * **********************************************
    * Add usefull methods
    * **********************************************
    * **********************************************
    */

    /**
    * Get server ID
    */
    public function getSid()
    {
        return $this->prxToID($this->prx);
    }

    /**
    * Return the vserver id from ice proxy object.
    * Useful to avoid too much ICE queries with $prx->id(); and save web server ressources.
    *
    * example:
    * $prx::__toString return "s/1 -t:tcp -h 127.0.0.1 -p 6502"
    *
    * @return int - or null on invalid $prx
    */
    public function prxToID($prx)
    {
        list($sid) = explode(' ', $prx);
        if (substr($sid, 0, 2) === 's/') {
            return (int) substr($sid, 2);
        }
    }

    /**
    * Kick all users of the vserver.
    *
    * @return string
    */
    public function kickAllUsers($text = '')
    {
        $text = $this->URLtoHTML($text);
        foreach ($this->getUsers() as $user) {
            $this->kickUser($user->session, $text);
        }
    }

    /**
    * Transforme a HTTP URL in HTML format
    * example : http://www.example.com
    * return : <a href="http://www.example.com">http://www.example.com</a>
    */
    public function URLtoHTML($str)
    {
        return preg_replace('/https?:\/\/[\pL\pN\-\.!~?&=+\*\'"(),\/]+/', '<a href="$0">$0</a>', $str);
    }

    private function validateChars($key, $str)
    {
        // Get patern for channelName or userName.
        $patern = $this->getParameter($key);
        /**
        *
        * WORKAROUND
        *
        * "\w" with preg_match do not work as intended ( any localized character )
        * Replace \w by \pL\pN ( L = letter, N = Number).
        * See http://www.pcre.org/pcre.txt
        * See http://www.php.net/manual/en/book.pcre.php

        * MEMO : this looks like obsolete with debian 7.0
        * I commented the workaround for the moment...
        *
        * $patern = str_replace(array('\\\w', '\\w', '\w' ), '\pL\pN', $patern);
        *
        */
        return (preg_match('/^'.$patern.'$/u', $str) === 1);
    }

    public function validateUserChars($str)
    {
        return $this->validateChars('username', $str);
    }

    public function validateChannelChars($str)
    {
        return $this->validateChars('channelname', $str);
    }

    /**
    * Check if the vserver accept HTML tag or remove them.
    *
    * @param string $str - the string value to check
    *
    * @return string - The stripped string (or unstripped).
    * @return boolean $stripped - Flag, if the message has been stripped.
    */
    public function removeHtmlTags($str, &$stripped)
    {
        $stripTags = $str;
        if ($this->getParameter('allowhtml') === 'false') {
            $stripTags = strip_tags($str);
        }
        $stripped = ($str !== $stripTags);
        return $stripTags;
    }

    /**
    * Return the specific parameter for a server (custom or default).
    *
    * @return string
    */
    public function getParameter($key)
    {
        $param = ''; // Return a string in any situation.

        $customConf = $this->getAllConf();

        if (isset($customConf[$key])) {
            $param = $customConf[$key];
        } else {
            // Memo: Murmur do not return some default parameters.
            if (isset($this->defaultConf[$key])) {
                $param = $this->defaultConf[$key];
                if ($key === 'port') {
                    // Murmur default port workaround.
                    $param += ($this->getSid() -1);
                }
            }
        }
        return $param;
    }

    /**
    * **********************************************
    * **********************************************
    * Public Murmur_Server methods
    * **********************************************
    * **********************************************
    */

    public function addChannel($name, $parent)
    {
        return $this->prx->addChannel($name, $parent);
    }

    public function delete()
    {
        return $this->prx->delete();
    }

    public function getACL($id, &$aclList, &$aclGroup, &$inherit)
    {
        $this->prx->getACL($id, $aclList, $aclGroup, $inherit);
    }

    /**
    * One query allowed, cache result.
    */
    public function getAllConf()
    {
        if (is_null($this->customConf)) {
            $this->customConf = $this->prx->getAllConf();
        }
        return $this->customConf;
    }

    public function getBans()
    {
        return $this->prx->getBans();
    }

    public function getCertificateList($uid)
    {
        return $this->prx->getCertificateList($uid);
    }

    public function getChannelState($id)
    {
        return $this->prx->getChannelState($id);
    }

    public function getChannels()
    {
        return $this->prx->getChannels();
    }

    public function getConf($key)
    {
        return $this->prx->getConf($key);
    }

    public function getLog($first, $last)
    {
        return $this->prx->getLog($first, $last);
    }

    public function getLogLen()
    {
        return $this->prx->getLogLen();
    }

    public function getRegisteredUsers($filter)
    {
        return $this->prx->getRegisteredUsers($filter);
    }

    public function getRegistration($id)
    {
        return $this->prx->getRegistration($id);
    }

    public function getState($uid)
    {
        return $this->prx->getState($uid);
    }

    public function getTexture($uid)
    {
        return $this->prx->getTexture($uid);
    }

    public function getTree()
    {
        return $this->prx->getTree();
    }

    public function getUptime()
    {
        if (method_exists('Murmur_server', 'getUptime')) {
            return $this->prx->getUptime();
        }
    }

    public function getUsers()
    {
        return $this->prx->getUsers();
    }

    public function hasPermission($session, $channelid, $perm)
    {
        return $this->prx->hasPermission($session, $channelid, $perm);
    }

    public function id()
    {
        return $this->prx->id();
    }

    public function isRunning()
    {
        return $this->prx->isRunning();
    }

    public function kickUser($uid, $reason)
    {
        return $this->prx->kickUser($uid, $reason);
    }

    public function registerUser($array)
    {
        return $this->prx->registerUser($array);
    }

    public function removeChannel($id)
    {
        return $this->prx->removeChannel($id);
    }

    public function sendMessage($uid, $text)
    {
        return $this->prx->sendMessage($uid, $text);
    }

    public function sendMessageChannel($uid, $sub, $text)
    {
        return $this->prx->sendMessageChannel($uid, $sub, $text);
    }

    public function setACL($id, $aclList, $aclGroup, $inherit)
    {
        return $this->prx->setACL($id, $aclList, $aclGroup, $inherit);
    }

    public function setBans($array)
    {
        return $this->prx->setBans($array);
    }

    public function setChannelState($chan)
    {
        return $this->prx->setChannelState($chan);
    }

    public function setConf($key, $value)
    {
        return $this->prx->setConf($key, $value);
    }

    public function setState($state)
    {
        return $this->prx->setState($state);
    }

    public function setSuperuserPassword($str)
    {
        return $this->prx->setSuperuserPassword($str);
    }

    public function setTexture($uid, $texture)
    {
        return $this->prx->setTexture($uid, $texture);
    }

    public function start()
    {
        return $this->prx->start();
    }

    public function stop()
    {
        return $this->prx->stop();
    }

    public function unregisterUser($uid)
    {
        return $this->prx->unregisterUser($uid);
    }

    public function updateRegistration($uid, $array)
    {
        return $this->prx->updateRegistration($uid, $array);
    }

    public function verifyPassword($name, $pw)
    {
        return $this->prx->verifyPassword($name, $pw);
    }
/**
*
* Murmur_Server methods I didnt declared here
*
* addCallback
* addContextCallback
* addUserToGroup
* effectivePermissions
* getUserIds
* getUserNames
* redirectWhisperGroup
* removeCallback
* removeContextCallback
* removeUserFromGroup
* setAuthenticator
*
*/
}
