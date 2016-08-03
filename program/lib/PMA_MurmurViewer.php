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

class PMA_ViewerData
{
    public $href;
    public $css = '';
    public $textCss = 'text';
    public $nameEnc = '';
    public $deepIcons = array();
    public $statusIcons = array();
}

/**
* MurmurTree to Array
*/
class PMA_MurmurViewer
{
    /**
    * Server proxy
    */
    protected $prx;
    /**
    * Viewer datas
    */
    protected $datas = array();
    /**
    * Viewer options
    */
    protected $opts = array();
    /**
    * Viewer parameters
    */
    protected $params = array();
    /**
    * Viewer css class.
    */
    public $css;

    public function __construct()
    {
        $this->opts['showChannelsLinks'] = false;
        $this->opts['showStatusIcons'] = false;
        $this->opts['showPasswordStatusIcons'] = false;
        $this->opts['channelSelection'] = false;
        $this->opts['usersSelection'] = false;

        $this->params['serverName'] = '';
        $this->params['defaultChanID'] = '';
        $this->params['selectedChanID'] = '';
        $this->params['selectedUserSessID'] = '';
        $this->params['selectedMoveTo'] = '';
        $this->params['directLinks'] = array();
        $this->params['indirectLinks'] = array();
        $this->params['disabledChannelsID'] = array();
        // Add a custom channel HREF.
        $this->params['channelHREF'] = '';
    }

    /**
    * Core of the class.
    * Transform a Murmur_Tree object into an array of PMA_ViewerData objects.
    */
    public function treeToArray(Murmur_Tree $tree)
    {
        $this->channel($tree, 0, 0);
    }

    public function setParam($key, $value)
    {
        if (isset($this->params[$key])) {
            $this->params[$key] = $value;
        }
    }

    public function enableOption($key)
    {
        if (isset($this->opts[$key])) {
            $this->opts[$key] = true;
        }
    }

    public function disableOption($key)
    {
        if (isset($this->opts[$key])) {
            $this->opts[$key] = false;
        }
    }

    /**
    * To show passwords, $prx is required.
    */
    public function enableOptionShowPasswords($prx)
    {
        if (is_object($prx)) {
            $this->prx = $prx;
            $this->enableOption('showPasswordStatusIcons');
        }
    }

    /**
    * Check for channel password status.
    *
    * @param $id - channel id to check.
    * @return boolean.
    */
    protected function doesChannelHavePassword($id)
    {
        $isPassword = false;
        $this->prx->getACL($id, $aclList, $null, $null);
        foreach ($aclList as $acl) {
            if (! $acl->inherited && PMA_MurmurAclHelper::isToken($acl)) {
                $isPassword = true;
                break;
            }
        }
        return $isPassword;
    }

    /**
    * Setup direct and undirect channel links array.
    */
    public function setupSelectedChannelLinks(array $channelsList)
    {
        $this->channelsList = $channelsList;
        $chan = $this->channelsList[$this->params['selectedChanID']];
        /**
        * Memo:
        * Check for no empty directLinks to avoid to
        * alway show current channel as linked.
        */
        if (! empty($chan->links)) {
            foreach ($chan->links as $id) {
                $this->params['directLinks'][] = $id;
            }
            $this->params['directLinks'][] = $chan->id;
        }
        // Now, get indirect links
        $this->setupIndirectLinks($this->params['directLinks']);
    }

    /**
    * Search for all indirect links.
    *
    * @param $directLinks - array of all direct link ids of a channel.
    */
    protected function setupIndirectLinks(array $directLinks)
    {
        foreach ($directLinks as $id) {
            if (isset($this->channelsList[$id])) {
                foreach ($this->channelsList[$id]->links as $cid) {
                    if (
                        ! in_array($cid, $directLinks, true) &&
                        ! in_array($cid, $this->params['indirectLinks'], true)
                    ) {
                        $this->params['indirectLinks'][] = $cid;
                        $this->setupIndirectLinks(array($cid));
                    }
                }
            }
        }
    }

    public function getDatas()
    {
        return $this->datas;
    }

    protected function addDatas($data)
    {
        $this->datas[] = $data;
    }

    protected function getChannelHREF($id)
    {
        $href = null;
        if (
            $this->opts['channelSelection'] &&
            ! in_array($id, $this->params['disabledChannelsID'], true)
        ) {
            if ($this->params['channelHREF'] === '') {
                $href = '?channel='.$id;
            } else {
                $href = $this->params['channelHREF'].'='.$id;
            }
        }
        return $href;
    }

    protected function getUserHREF($id)
    {
        if ($this->opts['usersSelection']) {
            return '?userSession='.$id;
        }
    }

    /**
    * Do not add HTTP link for channel ID stored in this array.
    */
    public function disableChannelID($id)
    {
        $this->params['disabledChannelsID'][] = $id;
    }

    /**
    * Channels to array...
    */
    protected function channel($obj, $deep, $lastID)
    {
        $chan = clone $obj->c;

        $data = new PMA_ViewerData();

        if ($chan->id === 0) {
            $data->textCss .= ' root';
        }

        $data->href = $this->getChannelHREF($chan->id);

        // Check if current channel is selected.
        if ($chan->id === $this->params['selectedChanID']) {
            $data->css = 'selected';
        } elseif ($chan->id === $this->params['selectedMoveTo']) {
            // Special css for the "move users to" channel target.
            $data->css = 'moveTo';
        }

        $data->deepIcons = $this->getDeep($chan->id, $lastID, $deep);
        // Last tree image
        if ($chan->id > 0) {
            if ($chan->id === $lastID) {
                $src = 'images/pma/tree_end.png';
            } else {
                $src = 'images/pma/tree_mid.png';
            }
            $data->deepIcons[] = $src;
        }

        // Channel icon
        if ($this->opts['showChannelsLinks']) {
            if (in_array($chan->id, $this->params['directLinks'], true)) {
                $src = 'images/pma/tree_link_direct.png';
            } elseif (in_array($chan->id, $this->params['indirectLinks'], true)) {
                $src = 'images/pma/tree_link_indirect.png';
            } elseif ($chan->id === 0) {
                $src = 'images/mumble/mumble.png';
            } elseif (! empty($chan->links)) {
                $src = 'images/pma/tree_link_with.png';
            } else {
                $src = 'images/pma/tree_channel.png';
            }
        } else {
            if ($chan->id === 0) {
                $src = 'images/mumble/mumble.png';
            } else {
                $src = 'images/pma/tree_channel.png';
            }
        }
        $data->deepIcons[] = $src;

        if ($this->opts['showStatusIcons']) {
            $data->statusIcons[] = IMG_SPACE_16;
            if ($this->opts['showPasswordStatusIcons']) {
                if ($this->doesChannelHavePassword($chan->id)) {
                    $data->statusIcons[] = 'images/gei/padlock_16.png';
                }
            }
            if ($chan->temporary) {
                $data->statusIcons[] = 'images/tango/clock_16.png';
            }
            if ($chan->description !== '') {
                $data->statusIcons[] = 'images/mumble/comment.png';
            }
            if ($chan->id === $this->params['defaultChanID']) {
                $data->statusIcons[] = 'images/other/home_16.png';
            }
        }

        $name = $chan->name;
        if ($chan->id === 0) {
            if ($this->params['serverName'] !== '') {
                $name = $this->params['serverName'];
            } else {
                $name = 'Root';
            }
        }
        $data->nameEnc = htEnc($name);

        $this->addDatas($data);

        $countUsers = count($obj->users);
        $countChans = count($obj->children);

        if ($countUsers > 0) {
            uasort($obj->users, array('self', 'usersCmp'));
            $lastSessionID = $obj->users[$countUsers - 1]->session;
            // Check if a sub channel exists after last user.
            $subChannelExist = ($countChans > 0);
            foreach ($obj->users as $user) {
                $this->user($user, $deep +1, $lastSessionID, $subChannelExist);
            }
        }

        if ($countChans > 0) {
            uasort($obj->children, array('self', 'channelsCmp'));
            $lastChanID = $obj->children[$countChans -1]->c->id;
            foreach ($obj->children as $children) {
                $this->channel($children, $deep +1, $lastChanID);
            }
        }
    }

    protected function user($user, $deep, $lastSessID, $subChannelExist)
    {
        $data = new PMA_ViewerData();

        $data->textCss .= ' user';

        $data->href = $this->getUserHREF($user->session.'-'.rawUrlEncode($user->name));

        // Check if current user is selected.
        if ($user->session === $this->params['selectedUserSessID']) {
            $data->css = 'selected';
        }

        $data->deepIcons = $this->getDeep($user->session, $lastSessID, $deep);
        // Tree img
        if ($user->session === $lastSessID && ! $subChannelExist) {
            $img = 'tree_end';
        } else {
            $img = 'tree_mid';
        }
        $data->deepIcons[] = 'images/pma/'.$img.'.png';
        $data->deepIcons[] = 'images/pma/tree_user.png';

        if ($this->opts['showStatusIcons']) {
            $data->statusIcons[] = IMG_SPACE_16;
            if ($user->userid >= 0) {
                $data->statusIcons[] = 'images/mumble/user_auth.png';
            }
            if (isset($user->recording) && $user->recording) {
                $data->statusIcons[] = 'images/xchat/red_16.png';
            }
            if (isset($user->prioritySpeaker) && $user->prioritySpeaker) {
                $data->statusIcons[] = 'images/tango/microphone_16.png';
            }
            if ($user->comment !== '') {
                $data->statusIcons[] = 'images/mumble/comment.png';
            }
            if ($user->suppress) {
                $data->statusIcons[] = 'images/mumble/user_suppressed.png';
            }
            if ($user->mute) {
                $data->statusIcons[] = 'images/mumble/user_muted.png';
            }
            if ($user->deaf) {
                $data->statusIcons[] = 'images/mumble/user_deafened.png';
            }
            if ($user->selfMute) {
                $data->statusIcons[] = 'images/mumble/user_selfmute.png';
            }
            if ($user->selfDeaf) {
                $data->statusIcons[] = 'images/mumble/user_selfdeaf.png';
            }
        }

        // User name
        $name = htEnc($user->name);
        if ($user->userid === 0 && strToLower($user->name) !== 'superuser') {
            $name .= ' <i>(SuperUser)</i>';
        }
        $data->nameEnc = $name;

        $this->addDatas($data);
    }

    /**
    * This code come from the work of mumbleviewer v0.91 ( GPL 2).
    * Website: http://sourceforge.net/projects/mumbleviewer/
    *
    * It's permit to create channels and users deep.
    *
    * @return array
    */
    protected function getDeep($id, $lastID, $deep)
    {
        if ($id === $lastID) {
            $this->deepMenu[$deep] = 0;
        } else {
            $this->deepMenu[$deep] = 1;
        }
        $count = 1;
        $array = array();
        while ($count < $deep) {
            if ($this->deepMenu[$count] === 0) {
                $img = 'space';
            } else {
                $img = 'tree_line';
            }
            $array[] = 'images/pma/'.$img.'.png';
            ++$count;
        }
        return $array;
    }

    /**
    * Order channels like the mumble client do.
    *
    * icePHP return a tree object sorted with case sensitive, no natural order ("strCmp").
    * The mumble client order is case insensitive ("strCaseCmp").
    *
    * This also permit to fix the channel order bug with phpICE >= 3.4.0 .
    */
    protected function channelsCmp($a, $b)
    {
        if ($a->c->parent === $b->c->parent && $a->c->position !== $b->c->position) {
            return $a->c->position - $b->c->position;
        } else {
            return strCaseCmp($a->c->name, $b->c->name);
        }
    }

    /**
    * Order users by names.
    */
    protected function usersCmp($a, $b)
    {
        return strCaseCmp($a->name, $b->name);
    }
}
