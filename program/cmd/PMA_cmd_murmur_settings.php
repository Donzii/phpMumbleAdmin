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

class PMA_cmd_murmur_settings extends PMA_cmd
{
    private $prx;
    private $default_conf;
    private $custom_conf;

    private $settings;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();

        $sid = $_SESSION['page_vserver']['id'];
        $this->prx = $this->getServerPrx($sid);

        $this->default_conf = $this->PMA->meta->getDefaultConf();
        $this->custom_conf = $this->prx->getAllConf();
        // Port particularity
        $this->default_conf['port'] = (string) ($this->default_conf['port'] + $sid - 1);
        $this->settings = PMA_MurmurSettingsHelper::get($this->PMA->meta->getVersion('int'));
        if (isset($this->PARAMS['setConf'])) {
            $this->setSettings();
        } elseif (isset($this->PARAMS['reset_setting'])) {
            switch ($this->PARAMS['reset_setting']) {
                case 'key':
                case 'certificate':
                    $this->resetSettingConfirm($this->PARAMS['reset_setting']);
                    break;
                default:
                    $this->resetSetting($this->PARAMS['reset_setting']);
            }
        // ADD A CERTIFICATE - UPLOAD and FORM
        } elseif (isset($_FILES['add_certificate']) OR isset($this->PARAMS['add_certificate'])) {
            $this->addCertificate();
        }
    }

    private function setSettings()
    {
        foreach ($this->settings as $key => $array) {
            // SANITY:
            if (! isset($this->PARAMS[$key])) {
                continue;
            }
            $newValue = $this->PARAMS[$key];
            // Disallow Admins and SuperUsers to change SuperAdmins only parameters.
            if ($array['right'] === 'SA' && ! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
                continue;
            }
            // Invalid form
            if ($key !== 'welcometext' && strlen($newValue) > 255) {
                $this->debugError(__method__ .' Too long value for: '.$key);
                continue;
            }
            // Empty value
            if ($newValue === '') {
                if ($array['type'] !== 'bool') {
                    // Remove custom parameter if exists
                    if (isset($this->custom_conf[$key])) {
                        /**
                        * Memo: setConf() with "zero", "one" or "multiple" spaces
                        * will alway remove the key in murmur DB.
                        */
                        $this->prx->setConf($key, '');
                    }
                }
                continue;
            }
            // Don't add custom value if it's the same as $this->default_conf
            if (isset($this->default_conf[$key]) && $newValue === $this->default_conf[$key]) {
                // A custom value is set for the key, remove it.
                if (isset($this->custom_conf[$key])) {
                    $this->prx->setConf($key, '');
                }
                continue;
            }
            // The custom value didn't change, do anything.
            if (isset($this->custom_conf[$key]) && $newValue === $this->custom_conf[$key]) {
                continue;
            }
            // SET CONF:
            // Host particularity
            if ($key === 'host') {
                $this->prx->setConf($key, $newValue);
                if ($this->prx->isRunning()) {
                    $this->message('host_modified_success');
                }
            // Port particularity
            } elseif ($key === 'port') {
                if (checkPort($newValue)) {
                    $this->prx->setConf($key, $newValue);
                    if ($this->prx->isRunning()) {
                        $this->message('port_modified_success');
                    }
                } else {
                    $this->messageError('invalid_port');
                }
            // Registername particularity
            } elseif ($key === 'registername') {
                if ($this->prx->validateChannelChars($newValue)) {
                    $this->prx->setConf($key, $newValue);
                } else {
                    $this->messageError('invalid_channel_name');
                }
            // Integer particularity
            } elseif ($array['type'] === 'integer') {
                if (ctype_digit($newValue)) {
                    $this->prx->setConf($key, $newValue);
                } else {
                    $this->messageError(array('invalid_numerical', $key));
                }
            // Default
            } else {
                $this->prx->setConf($key, $newValue);
            }
        }
    }

    private function resetSetting($key)
    {
        $this->prx->setConf($key, '');
        if ($this->prx->isRunning()) {
            if ($key === 'host') {
                $this->message('host_modified_success');
            }
            if ($key === 'port') {
                $this->message('port_modified_success');
            }
        }
    }

    private function resetSettingConfirm($key)
    {
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }
        $this->prx->setConf($key, '');
        if ($key === 'certificate') {
            // Delete the private key when we delete the certificate.
            $this->prx->setConf('key', '');
            if ($this->prx->isRunning()) {
                $this->message('certificate_modified_success');
            }
        }
    }

    private function addCertificate()
    {
        if (! function_exists('openssl_pkey_export') OR ! function_exists('openssl_x509_export')) {
            $this->messageError('php_openssl_module_not_found');
            $this->throwException();
        }
        /**
        * Get PEM
        */
        if (isset($_FILES['add_certificate'])) {
            // Error on upload, file max 20 KB
            if ($_FILES['add_certificate']['error'] !== 0 OR $_FILES['add_certificate']['size'] > 20480) {
                $this->messageError('invalid_certificate');
                $this->throwException();
            }
            $pem = file_get_contents($_FILES['add_certificate']['tmp_name']);
        } elseif (isset($this->PARAMS['add_certificate'])) {
            $pem = $this->PARAMS['add_certificate'];
        }
        /**
        * Separate private key and certificate.
        * Memo : Invalid PEM file will throw a php warning message.
        *
        * openssl_pkey_export() :
        * You need to have a valid "openssl.cnf" installed for this function to operate correctly.
        */
        @openssl_pkey_export($pem, $privatekey);
        @openssl_x509_export($pem, $certificate);
        /**
        * Checks if the private key corresponds to the certificate.
        */
        if (! openssl_x509_check_private_key($certificate, $privatekey)) {
            $this->messageError('invalid_certificate');
            $this->throwException();
        }

        $this->prx->setConf('key', $privatekey);
        $this->prx->setConf('certificate', $certificate);
        if ($this->prx->isRunning()) {
            $this->message('certificate_modified_success');
        }
    }
}
