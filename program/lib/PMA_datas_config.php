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

class PMA_datas_config extends PMA_datas
{
    protected $storageKey = 'config_config';
    private $defaultConfig = array();

    public function __construct()
    {
        $this->getDatasFromDB();
        $this->mergeCustomConfig();
    }

    /**
    * Setup default config
    * and merge with custom config
    */
    private function mergeCustomConfig()
    {
        /**
        * WARNING: DO NOT MODIFY THIS PARAMETERS.
        * Use config/config.php instead of.
        * Config parameters validation is based on the type of theses properties.
        * See isValidParameter()
        */
        $this->defaultConfig['SA_login'] = '';
        $this->defaultConfig['SA_pw'] = '';
        $this->defaultConfig['siteTitle'] = 'PhpMumbleAdmin !';
        $this->defaultConfig['siteComment'] = 'A murmur administration panel...';
        $this->defaultConfig['default_profile'] = 1;
        $this->defaultConfig['allowOfflineAuth'] = false;
        $this->defaultConfig['SU_auth'] = false;
        $this->defaultConfig['SU_edit_user_pw'] = false;
        $this->defaultConfig['SU_start_vserver'] = false;
        $this->defaultConfig['SU_ru_active'] = false;
        $this->defaultConfig['RU_auth'] = false;
        $this->defaultConfig['RU_delete_account'] = false;
        $this->defaultConfig['RU_edit_login'] = false;
        $this->defaultConfig['pw_gen_active'] = false;
        $this->defaultConfig['pw_gen_explicit_msg'] = false;
        $this->defaultConfig['pw_gen_pending'] = 2;
        $this->defaultConfig['pw_gen_sender_email'] = '';
        $this->defaultConfig['vlogs_size'] = 5000;
        $this->defaultConfig['vlogs_admins_active'] = true;
        $this->defaultConfig['vlogs_admins_highlights'] = false;
        $this->defaultConfig['pmaLogs_keep'] = 0;
        $this->defaultConfig['pmaLogs_SA_actions'] = true;
        $this->defaultConfig['table_overview'] = 10;
        $this->defaultConfig['table_users'] = 10;
        $this->defaultConfig['table_bans'] = 10;
        $this->defaultConfig['ddl_auth_page'] = false;
        $this->defaultConfig['ddl_refresh'] = 1;
        $this->defaultConfig['ddl_show_cache_uptime'] = true;
        $this->defaultConfig['autoban_attempts'] = 10;
        $this->defaultConfig['autoban_frame'] = 120;
        $this->defaultConfig['autoban_duration'] = 300;
        $this->defaultConfig['auto_logout'] = 15;
        $this->defaultConfig['update_check'] = 1;
        $this->defaultConfig['smtp_host'] = '127.0.0.1';
        $this->defaultConfig['smtp_port'] = 25;
        $this->defaultConfig['smtp_default_sender_email'] = '';
        $this->defaultConfig['show_total_users'] = true;
        $this->defaultConfig['show_total_users_sa'] = false;
        $this->defaultConfig['show_online_users'] = true;
        $this->defaultConfig['show_online_users_sa'] = false;
        $this->defaultConfig['show_uptime'] = true;
        $this->defaultConfig['show_uptime_sa'] = false;
        $this->defaultConfig['show_avatar_sa'] = true;
        $this->defaultConfig['murmur_version_url'] = false;
        $this->defaultConfig['external_viewer_enable'] = false;
        $this->defaultConfig['external_viewer_width'] = 200;
        $this->defaultConfig['external_viewer_height'] = 400;
        $this->defaultConfig['external_viewer_vertical'] = true;
        $this->defaultConfig['external_viewer_scroll'] = true;
        $this->defaultConfig['default_lang'] = 'en_EN';
        $this->defaultConfig['default_skin'] = 'default.css';
        $this->defaultConfig['default_timezone'] = 'UTC';
        $this->defaultConfig['default_time'] = 'h:i A';
        $this->defaultConfig['default_date'] = '%d %b %Y';
        $this->defaultConfig['defaultSystemLocales'] = '';
        $this->defaultConfig['default_uptime'] = 2;
        $this->defaultConfig['systemLocalesProfiles'] = array();
        $this->defaultConfig['IcePhpIncludePath'] = '';
        $this->defaultConfig['debug'] = 0;
        $this->defaultConfig['debug_session'] = false;
        $this->defaultConfig['debug_object'] = false;
        $this->defaultConfig['debug_select_flag'] = false;
        $this->defaultConfig['debug_stats'] = false;
        $this->defaultConfig['debug_messages'] = false;
        $this->defaultConfig['debug_email_to'] = '';

        $custom = $this->defaultConfig;

        /**
        * Merge custom configuration
        */
        foreach ($this->datas as $key => $value) {
            if ($this->isValidParameter($key, $value)) {
                $custom[$key] = $value;
            }
        }
        $this->datas = $custom;
    }

    /**
    * Check for a valid config value
    * Based on the defaultConfig array
    *
    * @return boolean
    */
    private function isValidParameter($key, $value)
    {
        // Never accept new $value if not declared in the default config.
        if (! isset($this->defaultConfig[$key])) {
            return false;
        }
        $type = getType($value);
        // New $value must have the same type with the default value.
        if ($type !== getType($this->defaultConfig[$key])) {
            return false;
        }
        // New $value require a valid type
        switch ($type) {
            case 'array':
            case 'boolean':
            case 'string':
                return true;
            case 'integer':
                // Some integer $value require a valid range of number.
                // See the next switch.
                break;
            default:
                return false;
        }
        // Check for a valid range.
        if (is_int($value)) {
            switch ($key) {
                case 'debug':
                    return ($value >= 0 && $value <= 3);
                case 'pw_gen_pending':
                    return ($value >= 1 && $value <= 744);
                case 'vlogs_size':
                    return ($value === -1 OR $value > 0);
                case 'pmaLogs_keep':
                    return ($value >= 0);
                case 'table_overview':
                case 'table_users':
                case 'table_bans':
                    return ($value === 0 OR ($value >= 10 && $value <= 1000));
                case 'auto_logout':
                    return ($value >= 5 && $value <= 30);
                case 'update_check':
                    return ($value >= 0 && $value <= 31);
                case 'smtp_port':
                    return checkPort($value);
                default:
                    return true;
            }
        }
        // No $value should reach this point, by precaution, refuse it.
        return false;
    }

    public function get($key)
    {
        if (isset($this->datas[$key])) {
            return $this->datas[$key];
        }
    }

    public function set($key, $value)
    {
        if ($this->isValidParameter($key, $value) && $this->datas[$key] !== $value) {
            $this->datas[$key] = $value;
            $this->saveDatasInDB();
        }
    }

    /**
    * Toggle a boolean value only.
    */
    public function toggle($key)
    {
        if ($this->isValidParameter($key, true)) {
            $this->datas[$key] = ! $this->datas[$key];
            $this->saveDatasInDB();
        }
    }
}
