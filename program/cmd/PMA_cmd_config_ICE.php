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

class PMA_cmd_config_ICE extends PMA_cmd
{
    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (isset($this->PARAMS['add_profile'])) {
            $this->addProfile($this->PARAMS['add_profile']);
        } elseif (isset($this->PARAMS['delete_profile'])) {
            $this->deleteProfile();
        } elseif (isset($this->PARAMS['set_default_profile'])) {
            $this->setDefaultProfile();
        } elseif (isset($this->PARAMS['edit_profile'])) {
            $this->editProfile();
        }
    }

    private function addProfile($name)
    {
        if ($name === '') {
            $this->messageError('empty_profile_name');
            $this->throwException();
        }

        $id = $this->PMA->profiles->add($name);
        $this->logUserAction('profile created ('.$id.'# '.$name.' )');
        $this->PMA->router->profile->setCurrentRoute($id);
    }

    private function deleteProfile()
    {
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }

        $id = $this->PMA->router->getRoute('profile');

        $profile = $this->PMA->userProfile;

        $this->PMA->profiles->delete($id);

        $this->logUserAction('profile deleted ('.$id.'# '.$profile['name'].' )');

        $this->PMA->admins = new PMA_datas_admins();
        $this->PMA->admins->deleteProfileAccess($id);

        // Set profile_id to a valid profile id
        if ($id === $this->PMA->config->get('default_profile')) {
            $first = $this->PMA->profiles->getFirst();
            $newProfile = $first['id'];
        } else {
            $newProfile = $this->PMA->config->get('default_profile');
        }
        $this->PMA->router->profile->setCurrentRoute($newProfile);
    }

    private function setDefaultProfile()
    {
        $id = $this->PMA->router->getRoute('profile');
        $this->PMA->config->set('default_profile', $id);
        $this->logUserAction('Default profile ('.$id.'# '.$this->PMA->profiles->getName($id).' )');
    }

    private function editProfile()
    {
        if (is_null($this->PMA->userProfile)) {
            $this->messageError('invalid_Ice_profile');
            $this->throwException();
        }

        $profile = $original = $this->PMA->userProfile;

        // Name
        if ($this->PARAMS['name'] !== '') {
            $profile['name'] = $this->PARAMS['name'];
        }

        // Toggle public
        $profile['public'] = isset($this->PARAMS['public']);

        // Host
        // An empty or digit host return an ice exection, deny it.
        if ($this->PARAMS['host'] !== '' && ! ctype_digit($this->PARAMS['host'])) {
            $profile['host'] = $this->PARAMS['host'];
            unset($_SESSION['page_vserver']);
        }

        // Port
        if (checkPort($this->PARAMS['port'])) {
            $profile['port'] = (int)$this->PARAMS['port'];
            unset($_SESSION['page_vserver']);
        } else {
            $this->messageError('invalid_port');
        }

        // Timeout
        $timeout = $this->PARAMS['timeout'];

        if (ctype_digit($timeout) && $timeout > 0) {
            $profile['timeout'] = (int)$timeout;
        } else {
            $this->messageError(array('invalid_numerical', 'timeout > 0'));
        }

        // Secret
        $profile['secret'] = $this->PARAMS['secret'];

        // Slprofile
        if (isset($this->PARAMS['slice_profile'])) {
            $profile['slice_profile'] = $this->PARAMS['slice_profile'];
        }

        // PHP-slice
        if (isset($this->PARAMS['slice_php'])) {
            $profile['slice_php'] = $this->PARAMS['slice_php'];
        }

        // HTTP address
        $profile['http-addr'] = $this->PARAMS['http_addr'];

        // Check if the profile has been modified
        $diff = arrayDiffStrict($profile, $original);

        if (! empty($diff)) {
            $this->PMA->profiles->modify($profile);
            $this->logUserAction('profile updated ('.$profile['id'].'# '.$profile['name'].' )');
        }
    }
}
