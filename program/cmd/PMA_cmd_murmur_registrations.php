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

class PMA_cmd_murmur_registrations extends PMA_cmd
{
    private $prx;
    // Registration id of the session
    private $id;
    // Registration array of the session
    private $registration;
    // By default, we assume that user do not modify it's own account.
    private $isOwnRegistration = false;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_MUMBLE)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $MiscNav = $this->PMA->router->getMiscNavigation('mumbleRegistration');
        // Mumble users must get their own registration
        if ($this->PMA->user->is(PMA_USER_MUMBLE)) {
            $MiscNav = $this->PMA->user->mumbleUID;
        }

        if (! is_null($MiscNav)) {
            $this->id = $MiscNav;
            // SuperUser_ru cant have access to SuperUser account
            if ($this->id === 0 && $this->PMA->user->is(PMA_USER_SUPERUSER_RU)) {
                $this->messageError('illegal_operation');
                $this->throwException();
            }
            $this->registration = $this->prx->getRegistration($this->id);
            // Check if user modify it's own account.
            if ($this->id === $this->PMA->user->mumbleUID) {
                $this->isOwnRegistration = true;
            }
        }

        if (isset($this->PARAMS['add_new_account'])) {
            $this->addNewAccount($this->PARAMS['add_new_account']);
        } elseif (isset($this->PARAMS['delete_account_id'])) {
            $this->deleteAccountID($this->PARAMS['delete_account_id']);
        } elseif (isset($this->PARAMS['delete_account'])) {
            $this->deleteAccount();
        } elseif (isset($this->PARAMS['editRegistration'])) {
            $this->registrationEditor();
        } elseif (isset($this->PARAMS['remove_avatar'])) {
            $this->removeAvatar();
        } elseif (isset($this->PARAMS['registrations_search'])) {
            $this->registrationsSearch($this->PARAMS['registrations_search']);
        } elseif (isset($this->PARAMS['reset_registrations_search'])) {
            $this->resetRegistrationsSearch();
        }
    }

    private function addNewAccount($login)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        // Memo : registerUser() return the uid of the new account
        // Memo : registerUser() verify for invalid characters but not updateRegistration()
        if (! $this->prx->validateUserChars($login)) {
            $this->messageError('invalid_username');
            $this->throwException();
        }

        try {
            $uid = $this->prx->registerUser(array($login));
        } catch (Murmur_InvalidUserException $e) {
            $this->messageError('username_exists');
            $this->throwException();
        }

        $this->message('registration_created_success');

        if (isset($this->PARAMS['redirect_to_new_account'])) {
            $this->setRedirection('?mumbleRegistration='.$uid);
        }
    }

    private function deleteAccountID($id)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (! isset($this->PARAMS['confirmed'])) {
           $this->throwException();
        }

        if (! ctype_digit($id)) {
            $this->messageError('invalid_numerical');
            $this->throwException();
        }

        $id = (int)$id;

        if ($id > 0) {
            $this->prx->unregisterUser($id);
            $this->message('registration_deleted_success');
        } else {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
    }

    private function deleteAccount()
    {
        // Check if registered user have the right to delete his account
        if ($this->PMA->user->is(PMA_USER_MUMBLE) && ! $this->PMA->config->get('RU_delete_account')) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }
        if ($this->id > 0) {
            $this->prx->unregisterUser($this->id);

            if ($this->isOwnRegistration) {
                $this->PMA->logout();
            } else {
                $this->PMA->router->removeMisc('mumbleRegistration');
            }
            $this->message('registration_deleted_success');
        } else {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
    }

    private function registrationEditor()
    {
        // Setup login
        if (! isset($this->registration[0])) {
            $this->registration[0] = '';
        }
        // Setup email
        if (! isset($this->registration[1])) {
            $this->registration[1] = '';
        }
        // Setup comment
        if (! isset($this->registration[2])) {
            $this->registration[2] = '';
        }

        $original = $this->registration;

        $allowModifyLogin = (
            $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU) OR
            $this->PMA->config->get('RU_edit_login')
        );
        $allowModifyPw = (
            $this->PMA->user->isMinimum(PMA_USER_ADMIN) OR
            $this->PMA->config->get('SU_edit_user_pw') OR
            $this->isOwnRegistration
        );
        /**
        * Modify login
        */
        if ($allowModifyLogin) {
            if ($this->PARAMS['login'] !== '' && $this->PARAMS['login'] !== $this->registration[0]) {
                if (! $this->prx->validateUserChars($this->PARAMS['login'])) {
                    $this->messageError('invalid_username');
                    $this->throwException();
                }
                $this->registration[0] = $this->PARAMS['login'];
            }
        }
        /**
        * Modify email
        */
        if ($this->PARAMS['email'] !== $this->registration[1]) {
            $this->registration[1] = $this->PARAMS['email'];
        }
        /**
        * Modify comment
        */
        if ($this->PARAMS['comm'] !== $this->registration[2]) {
            $this->registration[2] = $this->prx->removeHtmlTags($this->PARAMS['comm'], $stripped);
            if ($stripped) {
                $this->messageError('vserver_dont_allow_HTML');
            }
        }
        /**
        * Modify password
        */
        if ($allowModifyPw && $this->PARAMS['new_pw'] !== '') {
            // Verify current password if we edit our account.
            if ($this->isOwnRegistration) {
                if (! isset($this->PARAMS['current'])) {
                    $this->messageError('illegal_operation');
                    $this->throwException();
                }
                // verifyPassword() return user ID on successfull authentification, else
                // -1 for failed authentication and -2 for unknown usernames.
                $auth = $this->prx->verifyPassword($this->PMA->user->login, $this->PARAMS['current']);

                if ($auth !== $this->PMA->user->mumbleUID) {
                    $this->messageError('auth_error');
                    $this->throwException();
                }
            }

            if ($this->PARAMS['new_pw'] !== $this->PARAMS['confirm_new_pw']) {
                $this->messageError('password_check_failed');
                $this->throwException();
            }
            $this->registration[4] = $this->PARAMS['new_pw'];
        }

        $diff = arraydiffstrict($this->registration, $original);

        if (! empty($diff)) {
            try {
                $this->prx->updateRegistration($this->id, $this->registration);
            } catch (Murmur_InvalidUserException $e) {
                $this->messageError('username_exists');
                $this->throwException();
            }
            if (isset($diff[0])) {
                // Update PMA user login name on success
                if ($this->isOwnRegistration) {
                    $this->PMA->user->setLogin($diff[0]);
                }
            }
           if (isset($diff[4])) {
                // Verify that's the password has changed:
                $verifyPassword = $this->prx->verifyPassword($this->registration[0], $diff[4]);
                if ($verifyPassword === $this->id) {
                    $this->message('change_pw_success');
                } else {
                    $this->messageError('change_pw_error');
                }
            }
        }
    }

    private function removeAvatar()
    {
        if (isset($this->PARAMS['confirmed'])) {
            $this->prx->setTexture($this->id, array());
        }
    }

    private function registrationsSearch($search)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if ($search === '') {
            unset($_SESSION['search']['registrations']);
        } else {
            $_SESSION['search']['registrations'] = $search;
        }
    }

    private function resetRegistrationsSearch()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        unset($_SESSION['search']['registrations']);
    }
}
