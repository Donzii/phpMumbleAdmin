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

class PMA_cmd_config extends PMA_cmd
{
    public function process()
    {
        if (isset($this->PARAMS['setLang'])) {
            $this->setLanguageFlag($this->PARAMS['setLang']);
        } elseif (isset($this->PARAMS['set_options'])) {
            $this->setOptions();
        } elseif (isset($this->PARAMS['set_default_options'])) {
            $this->setDefaultOptions();
        } elseif (isset($this->PARAMS['add_locales_profile'])) {
            $this->addLocalesProfile();
        } elseif (isset($this->PARAMS['delete_locales_profile'])) {
            $this->deleteLocalesProfile($this->PARAMS['delete_locales_profile']);
        } elseif (isset($this->PARAMS['toggle_infopanel'])) {
            $this->toggleInfopanel();
        } elseif (isset($this->PARAMS['toggle_highlight_pmaLogs'])) {
            $this->toggleHighlightPmaLogs();
        } elseif (isset($this->PARAMS['check_for_update'])) {
            $this->checkForUpdate();
        } elseif (isset($this->PARAMS['set_settings_general'])) {
            $this->setSettingsGeneral();
        } elseif (isset($this->PARAMS['set_settings_autoban'])) {
            $this->setSettingsAutoban();
        } elseif (isset($this->PARAMS['set_settings_smtp'])) {
            $this->setSettingsSMTP();
        } elseif (isset($this->PARAMS['set_settings_logs'])) {
            $this->setSettingsLogs();
        } elseif (isset($this->PARAMS['set_settings_tables'])) {
            $this->setSettingsTables();
        } elseif (isset($this->PARAMS['set_settings_ext_viewer'])) {
            $this->setSettingsExternalViewer();
        } elseif (isset($this->PARAMS['set_mumble_users'])) {
            $this->setMumbleUsers();
        } elseif (isset($this->PARAMS['set_pw_requests_options'])) {
            $this->setPwRequestsOptions();
        } elseif (isset($this->PARAMS['set_settings_debug'])) {
            $this->setSettingsDebug();
        } elseif (isset($this->PARAMS['send_debug_email'])) {
            $this->sendDebugEmail($this->PARAMS['send_debug_email']);
        }
    }

    private function setLanguageFlag($lang)
    {
        $this->setRedirection('referer');
        $this->PMA->cookie->set('lang', $lang);
    }

    private function setOptions()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_MUMBLE)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->cookie->set('lang', $this->PARAMS['lang']);
        $this->PMA->cookie->set('skin', $this->PARAMS['skin']);
        $this->PMA->cookie->set('timezone', $this->PARAMS['timezone']);
        $this->PMA->cookie->set('time', $this->PARAMS['time']);
        $this->PMA->cookie->set('date', $this->PARAMS['date']);
        $this->PMA->cookie->set('installed_localeFormat', $this->PARAMS['locales']);
        $this->PMA->cookie->set('uptime', (int)$this->PARAMS['uptime']);
        $this->PMA->cookie->set('vserver_login', $this->PARAMS['vserver_login']);
    }

    private function toggleInfopanel()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_MUMBLE)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->cookie->set('infoPanel', ! $this->PMA->cookie->get('infoPanel'));
    }

    private function toggleHighlightPmaLogs()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->cookie->set('highlight_pmaLogs', ! $this->PMA->cookie->get('highlight_pmaLogs'));
    }

    private function checkForUpdate()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $updates = new PMA_updates();
        if ($this->PARAMS['check_for_update'] === 'debug') {
            $updates->setDebugMode();
        }
        if ($updates->check()) {
            $this->message(array('new_pma_version', $updates->get('current_version')));
        } else {
            $this->messageError('no_update_found');
        }
        $this->PMA->app->set('updates', $updates->getCacheParameters());
    }

    private function setDefaultOptions()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');

        $this->PMA->config->set('default_lang', $this->PARAMS['lang']);
        $this->PMA->config->set('default_skin', $this->PARAMS['skin']);
        $this->PMA->config->set('default_timezone', $this->PARAMS['timezone']);
        $this->PMA->config->set('default_time', $this->PARAMS['time']);
        $this->PMA->config->set('default_date', $this->PARAMS['date']);
        $this->PMA->config->set('defaultSystemLocales', $this->PARAMS['systemLocales']);
    }

    private function addLocalesProfile()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');

        $array = $this->PMA->config->get('systemLocalesProfiles');

        $key = $this->PARAMS['key'];
        $value = $this->PARAMS['val'];

        if ($key !== '' && $value !== '' && ! isset($array[$key]) && ! in_array($value, $array)) {

            $array[$key] = $value;
            natCaseSort($array);
            $this->PMA->config->set('systemLocalesProfiles', $array);
        }
    }

    private function deleteLocalesProfile($key)
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->setRedirection('referer');

        $array = $this->PMA->config->get('systemLocalesProfiles');

        if ($key !== '' && isset($array[$key])) {
            unset($array[$key]);
            $this->PMA->config->set('systemLocalesProfiles', $array);
        }
    }

    private function setSettingsGeneral()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->PMA->config->set('siteTitle', $this->PARAMS['title']);
        $this->PMA->config->set('siteComment', $this->PARAMS['comment']);

        if (ctype_digit($this->PARAMS['auto_logout'])) {
            $this->PMA->config->set('auto_logout', (int)$this->PARAMS['auto_logout']);
        }
        if (ctype_digit($this->PARAMS['check_update'])) {
            $this->PMA->config->set('update_check', (int)$this->PARAMS['check_update']);
        }
        $this->PMA->config->set('murmur_version_url', isset($this->PARAMS['murmurVersionUrl']));
        $this->PMA->config->set('ddl_auth_page', isset($this->PARAMS['ddlAuthPage']));

        if (ctype_digit($this->PARAMS['ddlRefresh'])) {
            $this->PMA->config->set('ddl_refresh', (int)$this->PARAMS['ddlRefresh']);
        }
        $this->PMA->config->set('ddl_show_cache_uptime', isset($this->PARAMS['show_uptime']));
        $this->PMA->config->set('show_avatar_sa', isset($this->PARAMS['show_avatar_sa']));
        $this->PMA->config->set('IcePhpIncludePath', $this->PARAMS['incPath']);
    }

    private function setSettingsAutoban()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        if (ctype_digit($this->PARAMS['attempts'])) {
            $this->PMA->config->set('autoban_attempts', (int)$this->PARAMS['attempts']);
        }
        if (ctype_digit($this->PARAMS['timeFrame'])) {
            $this->PMA->config->set('autoban_frame', (int)$this->PARAMS['timeFrame']);
        }
        if (ctype_digit($this->PARAMS['duration'])) {
            $this->PMA->config->set('autoban_duration', (int)$this->PARAMS['duration']);
        }
    }

    private function setSettingsSMTP()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->config->set('smtp_host', $this->PARAMS['host']);
        $this->PMA->config->set('smtp_port', (int)$this->PARAMS['port']);
        $this->PMA->config->set('smtp_default_sender_email', $this->PARAMS['default_sender']);
        $this->PMA->config->set('debug_email_to', $this->PARAMS['email']);
    }

    private function setSettingsLogs()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        // murmur logs
        if (ctype_digit($this->PARAMS['murmur_logs_size']) OR $this->PARAMS['murmur_logs_size'] === '-1') {
            $this->PMA->config->set('vlogs_size', (int)$this->PARAMS['murmur_logs_size']);
        }
        $this->PMA->config->set('vlogs_admins_active', isset($this->PARAMS['activate_admins']));
        $this->PMA->config->set('vlogs_admins_highlights', isset($this->PARAMS['adm_hightlights_logs']));
        // PMA logs
        if ($this->PMA->user->isMinimum(PMA_USER_SUPERADMIN)) {
            if (ctype_digit($this->PARAMS['log_keep'])) {
                $this->PMA->config->set('pmaLogs_keep', (int)$this->PARAMS['log_keep']);
            }
            $this->PMA->config->set('pmaLogs_SA_actions', isset($this->PARAMS['log_SA']));
        }
    }

    private function setSettingsTables()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        if (ctype_digit($this->PARAMS['overview'])) {
            $this->PMA->config->set('table_overview', (int)$this->PARAMS['overview']);
        }
        if (ctype_digit($this->PARAMS['users'])) {
            $this->PMA->config->set('table_users', (int)$this->PARAMS['users']);
        }
        if (ctype_digit($this->PARAMS['bans'])) {
            $this->PMA->config->set('table_bans', (int)$this->PARAMS['bans']);
        }
        $this->PMA->config->set('show_total_users', isset($this->PARAMS['totalUsers']));
        $this->PMA->config->set('show_total_users_sa', isset($this->PARAMS['totalUsersSa']));
        $this->PMA->config->set('show_online_users', isset($this->PARAMS['totalOnline']));
        $this->PMA->config->set('show_online_users_sa', isset($this->PARAMS['totalOnlineSa']));
        $this->PMA->config->set('show_uptime', isset($this->PARAMS['uptime']));
        $this->PMA->config->set('show_uptime_sa', isset($this->PARAMS['uptimeSa']));

    }

    private function setSettingsExternalViewer()
    {
        $this->PMA->config->set('external_viewer_enable', isset($this->PARAMS['enable']));
        if (ctype_digit($this->PARAMS['width'])) {
            $this->PMA->config->set('external_viewer_width', (int)$this->PARAMS['width']);
        }
        if (ctype_digit($this->PARAMS['height'])) {
            $this->PMA->config->set('external_viewer_height', (int)$this->PARAMS['height']);
        }
        $this->PMA->config->set('external_viewer_vertical', isset($this->PARAMS['vertical']));
        $this->PMA->config->set('external_viewer_scroll', isset($this->PARAMS['scroll']));
    }

    private function setMumbleUsers()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->config->set('allowOfflineAuth', isset($this->PARAMS['allowOfflineAuth']));
        $this->PMA->config->set('SU_auth', isset($this->PARAMS['allowSuperUserAuth']));
        $this->PMA->config->set('SU_edit_user_pw', isset($this->PARAMS['allowSuperUserEditPw']));
        $this->PMA->config->set('SU_start_vserver', isset($this->PARAMS['allowSuperUserStartSrv']));
        $this->PMA->config->set('SU_ru_active', isset($this->PARAMS['allowSuperUserRuClass']));
        $this->PMA->config->set('RU_auth', isset($this->PARAMS['allowRuAuth']));
        $this->PMA->config->set('RU_delete_account', isset($this->PARAMS['allowRuDelAccount']));
        $this->PMA->config->set('RU_edit_login', isset($this->PARAMS['allowRuModifyLogin']));
        $this->PMA->config->set('pw_gen_active', isset($this->PARAMS['pwGenActive']));
    }

    private function setPwRequestsOptions()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        $this->PMA->config->set('pw_gen_explicit_msg', isset($this->PARAMS['explicit_msg']));
        $this->PMA->config->set('pw_gen_sender_email', $this->PARAMS['sender_email']);

        if (ctype_digit($this->PARAMS['pending_delay'])) {
            $this->PMA->config->set('pw_gen_pending', (int)$this->PARAMS['pending_delay']);
        }
    }


    private function setSettingsDebug()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        if (ctype_digit($this->PARAMS['mode'])) {
            $this->PMA->config->set('debug', (int)$this->PARAMS['mode']);
        }
        $this->PMA->config->set('debug_session', isset($this->PARAMS['session']));
        $this->PMA->config->set('debug_object', isset($this->PARAMS['object']));
        $this->PMA->config->set('debug_stats', isset($this->PARAMS['stats']));
        $this->PMA->config->set('debug_messages', isset($this->PARAMS['messages']));
        $this->PMA->config->set('debug_select_flag', isset($this->PARAMS['flag']));
    }

    private function sendDebugEmail()
    {
        if (
            $this->PMA->config->get('debug') < 1
            OR ! $this->PMA->user->isMinimum(PMA_USER_ROOTADMIN)
        ) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }
        /**
        * Setup mail object
        */
        $mail = new PMA_mail();
        $mail->setHost($this->PMA->config->get('smtp_host'));
        $mail->setPort($this->PMA->config->get('smtp_port'));
        $mail->setDefaultSender($this->PMA->config->get('smtp_default_sender_email'));
        $mail->setXmailer(PMA_NAME);
        $mail->addTo($this->PMA->config->get('debug_email_to'), 'Debug name');
        $mail->setSubject('[DEBUG MAIL]: '.PMA_NAME);
        $mail->setMessage('This is an automatique debug message sent by '.PMA_NAME);
        /**
        * Send mail
        */
        $mail->send_mail();
        foreach ($mail->smtpDialogues as $dial) {
            $this->debug($dial->debug);
        }
        if ($mail->smtpError) {
            $this->log('smtp.error', $mail->smtpErrorMessage);
        }

        if ($mail->smtpError) {
            $this->messageError('debug_mail_failed');
        } else {
            $this->message('debug_mail_succeed');
        }
    }
}
