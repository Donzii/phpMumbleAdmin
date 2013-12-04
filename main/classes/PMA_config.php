<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'PMA_STARTED' ) ) { die( 'ILLEGAL: You cannot call this script directly !' ); }

class PMA_config extends PMA_abs_datas {

	protected $datas_key = 'config';

	private $properties;

	/**
	* Singleton
	*/
	static function instance() {

		static $_instance;

		if ( is_null( $_instance ) ) {

			return $_instance = new self();
		}

		return $_instance;
	}

	private function __construct() {

		$this->set_default_properties();

		$this->get_datas();

		$this->load_custom_config();

		/**
		* Setup PHP debug mode
		*/
		define( 'PMA_DEBUG', $this->properties['debug'] );

		if ( PMA_DEBUG > 0 ) {
			error_reporting( -1 );
		}
	}

	function save_datas() {

		$this->datas = &$this->properties;
		parent::save_datas();
	}

	// Setup default config parameters.
	private function set_default_properties() {

		// WARNING: DO NOT MODIFY THIS.
		// Use config/config.php instead of.

		// Config parameters validation is based on the type of theses properties.
		// See is_valid_param()

		$this->properties['SA_login'] = '';
		$this->properties['SA_pw'] = '';
		$this->properties['siteTitle'] = 'PhpMumbleAdmin !';
		$this->properties['siteComment'] = 'A murmur administration panel...';
		$this->properties['default_profile'] = 1;
		$this->properties['SU_auth'] = FALSE;
		$this->properties['SU_edit_user_pw'] = FALSE;
		$this->properties['SU_start_vserver'] = FALSE;
		$this->properties['SU_ru_active'] = FALSE;
		$this->properties['RU_auth'] = FALSE;
		$this->properties['RU_delete_account'] = FALSE;
		$this->properties['RU_edit_login'] = FALSE;
		$this->properties['pw_gen_active'] = FALSE;
		$this->properties['pw_gen_explicit_msg'] = FALSE;
		$this->properties['pw_gen_pending'] = 2;
		$this->properties['pw_gen_sender_email'] = '';
		$this->properties['vlogs_size'] = 5000;
		$this->properties['vlogs_admins_active'] = TRUE;
		$this->properties['vlogs_admins_highlights'] = FALSE;
		$this->properties['pmaLogs_keep'] = 0;
		$this->properties['pmaLogs_SA_actions'] = TRUE;
		$this->properties['table_overview'] = 10;
		$this->properties['table_users'] = 10;
		$this->properties['table_bans'] = 10;
		$this->properties['ddl_auth_page'] = FALSE;
		$this->properties['ddl_refresh'] = 1;
		$this->properties['ddl_show_cache_uptime'] = TRUE;
		$this->properties['autoban_attempts'] = 10;
		$this->properties['autoban_frame'] = 120;
		$this->properties['autoban_duration'] = 300;
		$this->properties['auto_logout'] = 15;
		$this->properties['update_check'] = 1;
		$this->properties['smtp_host'] = '127.0.0.1';
		$this->properties['smtp_port'] = 25;
		$this->properties['smtp_default_sender_email'] = '';
		$this->properties['show_total_users'] = TRUE;
		$this->properties['show_total_users_sa'] = FALSE;
		$this->properties['show_online_users'] = TRUE;
		$this->properties['show_online_users_sa'] = FALSE;
		$this->properties['show_uptime'] = TRUE;
		$this->properties['show_uptime_sa'] = FALSE;
		$this->properties['show_avatar_sa'] = TRUE;
		$this->properties['murmur_version_url'] = FALSE;
		$this->properties['external_viewer_enable'] = FALSE;
		$this->properties['external_viewer_width'] = 200;
		$this->properties['external_viewer_height'] = 400;
		$this->properties['external_viewer_vertical'] = TRUE;
		$this->properties['external_viewer_scroll'] = TRUE;
		$this->properties['default_lang'] = 'en_EN';
		$this->properties['default_skin'] = 'default.css';
		$this->properties['default_timezone'] = 'UTC';
		$this->properties['default_time'] = 'h:i A';
		$this->properties['default_date'] = '%d %b %Y';
		$this->properties['default_installed_locales'] = '';
		$this->properties['installed_localesProfiles'] = array();
		$this->properties['debug'] = 0;
		$this->properties['debug_session'] = FALSE;
		$this->properties['debug_object'] = FALSE;
		$this->properties['debug_select_flag'] = FALSE;
		$this->properties['debug_stats'] = FALSE;
		$this->properties['debug_email_to'] = '';
	}

	/**
	* Load custom configuration
	*/
	private function load_custom_config() {

		foreach( $this->datas as $key => $value ) {

			if ( $this->is_valid_param( $key, $value ) ) {
				$this->properties[ $key ] = $value;
			}
		}

		unset( $this->datas );
	}

	/**
	*
	* Check for a valid config value
	*
	* @return bool
	*/
	private function is_valid_param( $key, $value ) {

		if ( ! isset( $this->properties[ $key ] ) ) {
			return FALSE;
		}

		if ( is_bool( $this->properties[ $key ] ) && is_bool( $value ) ) {
			return TRUE;
		}

		if ( is_string( $this->properties[ $key ] ) && is_string( $value ) ) {
			return TRUE;
		}

		if ( is_array( $this->properties[ $key ] ) && is_array( $value ) ) {
			return TRUE;
		}

		if ( is_int( $this->properties[ $key ] ) && is_int( $value ) ) {

			// Some integer need a valid value
			switch( $key ) {

				case 'debug':
					return ( $value >= 0 && $value <= 3 );

				case 'pw_gen_pending':
					return ( $value >= 1 && $value <= 744 );

				case 'vlogs_size':
					return ( $value === -1 OR $value > 0 );

				case 'pmaLogs_keep':
					return ( $value >= 0 );

				case 'table_overview':
				case 'table_users':
				case 'table_bans':
					return ( $value === 0 OR ( $value >= 10 && $value <= 1000 ) );

				case 'auto_logout':
					return ( $value >= 5 && $value <= 30 );

				case 'update_check':
					return ( $value >= 0 && $value <= 31 );

				case 'smtp_port':
					return check_port( $value );
			}

			return TRUE;
		}

		return FALSE;
	}

	function get( $key ) {

		if ( isset( $this->properties[ $key ] ) ) {
			return $this->properties[ $key ];
		}
	}

	function set( $key, $value ) {

		if ( $this->is_valid_param( $key, $value ) && $this->properties[ $key ] !== $value ) {

			$this->properties[ $key ] = $value;
			$this->save_datas();
		}
	}

	/**
	* Toggle a boolean value only.
	*/
	function toggle( $key ) {

		// Check that the key exists and is a boolean.
		if ( $this->is_valid_param( $key, TRUE ) ) {

			$this->properties[ $key ] = ! $this->properties[ $key ];
			$this->save_datas();
		}
	}
}

?>