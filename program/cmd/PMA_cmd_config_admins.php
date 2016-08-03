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

class PMA_cmd_config_admins extends PMA_cmd
{
    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->PMA->admins = new PMA_datas_admins();

        if (isset($this->PARAMS['change_own_pw'])) {
            $this->changeOwnPassword();
        } elseif (isset($this->PARAMS['add_new_admin'])) {
            $this->addNewAdmin();
        } elseif (isset($this->PARAMS['remove_admin'])) {
            $this->removeAdmin($this->PARAMS['remove_admin']);
        } elseif (isset($this->PARAMS['edit_registration'])) {
            $this->editRegistration($this->PARAMS['edit_registration']);
        } elseif (isset($this->PARAMS['editAccess'])) {
                $this->editAccess($this->PARAMS['editAccess']);
        } elseif (isset($this->PARAMS['edit_SuperAdmin'])) {
            $this->editSuperAdmin();
        }
    }

    /**
    * Check if a login exists already,
    * using SuperAdmin and admin datas.
    *
    * @return boolean
    */
    private function loginExists($login)
    {
        $login = strToLower($login);
        $SA = strToLower($this->PMA->config->get('SA_login'));
        return (
            $login === $SA OR
            $this->PMA->admins->loginExists($login)
        );
    }

    /**
    * Save admins datas error helper
    */
    private function adminsDatasErrorHelper()
    {
        $this->messageError('cmd_process_error');
        $this->debugError('Couldn\'t saved admins datas in DB.');
    }

    /**
    * Check if the edited admin class is inferior.
    */
    private function registrationClassIsInferiorHelper($adm)
    {
        if (is_null($adm) OR ! $this->PMA->user->isSuperior($adm['class'])) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
    }

    private function changeOwnPassword()
    {
        if (! $this->PMA->user->isPmaAdmin()) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $id = $this->PMA->user->adminID;
        $adm = $this->PMA->admins->get($id);

        // Check current admin password
        if (! PMA_passwordHelper::check($this->PARAMS['current'], $adm['pw'])) {
            $this->messageError('auth_error');
            $this->throwException();
        }

        if (! PMA_passwordHelper::confirm($this->PARAMS['new_pw'], $this->PARAMS['confirm_new_pw'])) {
            $this->messageError('password_check_failed');
            $this->throwException();
        }

        $adm['pw'] = PMA_passwordHelper::crypt($this->PARAMS['new_pw']);

        $this->PMA->admins->modify($adm);
        $this->PMA->admins->forceSaveDatasInDB();

        if ($this->PMA->admins->isLastSaveSuccess()) {
            $this->message('change_pw_success');
        } else {
            $this->adminsDatasErrorHelper();
        }
    }

    private function addNewAdmin()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->PARAMS['class'] = (int)$this->PARAMS['class'];
        if (! $this->PMA->user->isSuperior(($this->PARAMS['class']))) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $login = $this->PARAMS['login'];

        if (! $this->PMA->admins->validateLoginChars($login)) {
            $this->messageError('invalid_username');
            $this->throwException();
        }

        if ($this->loginExists($login)) {
            $this->messageError('username_exists');
            $this->throwException();
        }

        if (! PMA_passwordHelper::confirm($this->PARAMS['new_pw'], $this->PARAMS['confirm_new_pw'])) {
            $this->messageError('password_check_failed');
            $this->throwException();
        }

        $id = $this->PMA->admins->add(
            $login,
            $this->PARAMS['new_pw'],
            $this->PARAMS['email'],
            $this->PARAMS['name'],
            $this->PARAMS['class']
        );
        $this->PMA->admins->forceSaveDatasInDB();

        if ($this->PMA->admins->isLastSaveSuccess()) {
            $this->logUserAction('Admin account created ('.$id.'# '.$login.' )');
            $this->message('registration_created_success');
        } else {
            $this->adminsDatasErrorHelper();
        }
    }

    private function removeAdmin($id)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }

        $id = (int)$id;

        $adm = $this->PMA->admins->get($id);
        $this->registrationClassIsInferiorHelper($adm);

        $this->PMA->admins->delete($id);
        $this->PMA->admins->forceSaveDatasInDB();

        if ($this->PMA->admins->isLastSaveSuccess()) {
            $this->logUserAction('Admin account deleted ('.$id.'# '.$adm['login'].' )');
            $this->message('registration_deleted_success');
        } else {
            $this->adminsDatasErrorHelper();
        }
    }

    private function editRegistration($id)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $adm = $original = $this->PMA->admins->get((int)$id);
        $this->registrationClassIsInferiorHelper($adm);

        // Login
        if ($this->PARAMS['login'] !== $adm['login']) {

            if (! $this->PMA->admins->validateLoginChars($this->PARAMS['login'])) {
                $this->messageError('invalid_username');
                $this->throwException();
            }

            if ($this->loginExists($this->PARAMS['login'])) {
                $this->messageError('username_exists');
                $this->throwException();
            }

            $adm['login'] = $this->PARAMS['login'];
        }

        // Password
        if ($this->PARAMS['new_pw'] !== '') {
            if (! PMA_passwordHelper::confirm($this->PARAMS['new_pw'], $this->PARAMS['confirm_new_pw'])) {
                $this->messageError('password_check_failed');
                $this->throwException();
            }
            $adm['pw'] = PMA_passwordHelper::crypt($this->PARAMS['new_pw']);
        }

        // Class
        $class = (int)$this->PARAMS['class'];
        /**
        * Check if the new class is authorized.
        */
        if ($this->PMA->user->isSuperior($class)) {
            $adm['class'] = $class;
        }

        // Email
        $adm['email'] = $this->PARAMS['email'];
        // Name
        $adm['name'] = $this->PARAMS['name'];
        // Check if the registration has been modified
        $diff = arrayDiffStrict($adm, $original);

        if (! empty($diff)) {
            $this->PMA->admins->modify($adm);
            $this->PMA->admins->forceSaveDatasInDB();

            if ($this->PMA->admins->isLastSaveSuccess()) {
                if (isset($diff['login'])) {
                    $this->logUserAction('Admin login updated ('.$adm['id'].'# '.$original['login'].' => '.$adm['login'].' )');
                }
                if (isset($diff['pw'])) {
                    $this->message('change_pw_success');
                    $this->logUserAction('Admin password updated ('.$adm['id'].'# '.$adm['login'].' )');
                }
                if (isset($diff['class'])) {
                    $className = pmaGetClassName($adm['class']);
                    $this->logUserAction('Admin class updated ('.$adm['id'].'# '.$adm['login'].' => '.$className.' )');
                }
            } else {
                $this->adminsDatasErrorHelper();
            }
        }
    }

    private function editAccess($id)
    {
        $this->setRedirection('referer');

        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $profile_id = $this->PMA->router->getRoute('profile');

        $adm = $this->PMA->admins->get((int)$id);
        $this->registrationClassIsInferiorHelper($adm);

        // Full Access
        if (isset($this->PARAMS['fullAccess'])) {
            $adm['access'][$profile_id] = '*';
        } else {

            $access = '';

            foreach ($this->PARAMS as $key => $value) {
                /**
                * Memo:
                * A digital key in POST return an integer
                * example $this->PARAMS[0], $this->PARAMS[1]
                */
                if (is_int($key) && $value === 'on') {
                    $access .= $key.';';
                }
            }

            if ($access !== '') {
                $adm['access'][$profile_id] = substr($access, 0, -1);
            } else {
                unset($adm['access'][$profile_id]);
            }
        }

        $this->PMA->admins->modify($adm);
        $this->PMA->admins->forceSaveDatasInDB();

        if ($this->PMA->admins->isLastSaveSuccess()) {
            $this->logUserAction('Admin access updated ('.$adm['id'].'# '.$adm['login'].' )');
        } else {
            $this->adminsDatasErrorHelper();
        }
    }

    private function editSuperAdmin()
    {
        if (! $this->PMA->user->is(PMA_USER_SUPERADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');
        /**
        * Check current SuperAdmin password.
        */
        if (! PMA_passwordHelper::check($this->PARAMS['current'], $this->PMA->config->get('SA_pw'))) {
            $this->messageError('auth_error');
            $this->throwException();
        }
        $updateLogin = false;
        $updatePw = false;
        /**
        * SuperAdmin login
        */
        if ($this->PARAMS['login'] !== $this->PMA->config->get('SA_login')) {
            if (! $this->PMA->admins->validateLoginChars($this->PARAMS['login'])) {
                $this->messageError('invalid_username');
                $this->throwException();
            }
            if ($this->loginExists($this->PARAMS['login'])) {
                $this->messageError('username_exists');
                $this->throwException();
            }
            $this->PMA->config->set('SA_login', $this->PARAMS['login']);
            $updateLogin = true;
        }
        /**
        * SuperAdmin password
        */
        if ($this->PARAMS['new_pw'] !== '') {
            if (! PMA_passwordHelper::confirm($this->PARAMS['new_pw'], $this->PARAMS['confirm_new_pw'])) {
                $this->messageError('password_check_failed');
                $this->throwException();
            }
            $this->PMA->config->set('SA_pw', PMA_passwordHelper::crypt($this->PARAMS['new_pw']));
            $updatePw = true;
        }

        if ($updateLogin OR $updatePw) {

            $this->PMA->config->forceSaveDatasInDB();

            if ($this->PMA->config->isLastSaveSuccess()) {
                if ($updateLogin) {
                    $this->PMA->user->setLogin($this->PARAMS['login']);
                    $this->logUserAction('SuperAdmin login updated');
                }
                if ($updatePw) {
                    $this->logUserAction('SuperAdmin password updated');
                    $this->message('change_pw_success');
                }
            } else {
                $this->adminsDatasErrorHelper();
            }
        }
    }
}
