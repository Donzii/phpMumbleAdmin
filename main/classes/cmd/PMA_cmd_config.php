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

class PMA_cmd_config extends PMA_cmd {

	function process() {

		if ( isset( $this->GET['setLang'] ) ) {
			$this->set_language_flag( $this->GET['setLang'] );

		} elseif ( isset( $this->POST['set_options'] ) ) {
			$this->set_options();

		} elseif ( isset( $this->GET['toggle_infopanel'] ) ) {
			$this->toggle_infopanel();

		} elseif ( isset( $this->GET['toggle_highlight_pmaLogs'] ) ) {
			$this->toggle_highlight_pmaLogs();

		} elseif ( isset( $this->GET['check_for_update'] ) ) {
			$this->check_for_update();

		} elseif ( isset( $this->POST['set_default_options'] ) ) {
			$this->set_default_options();

		} elseif ( isset( $this->POST['add_locales_profile'] ) ) {
			$this->add_locales_profile();

		} elseif ( isset( $this->POST['delete_locales_profile'] ) ) {
			$this->delete_locales_profile( $this->POST['delete_locales_profile'] );

		} elseif ( isset( $this->POST['set_settings_general'] ) ) {
			$this->set_settings_general();

		} elseif ( isset( $this->POST['set_settings_autoban'] ) ) {
			$this->set_settings_autoban();

		} elseif ( isset( $this->POST['set_settings_smtp'] ) ) {
			$this->set_settings_smtp();

		} elseif ( isset( $this->POST['set_settings_logs'] ) ) {
			$this->set_settings_logs();

		} elseif ( isset( $this->POST['set_settings_tables'] ) ) {
			$this->set_settings_tables();

		} elseif ( isset( $this->POST['set_settings_ext_viewer'] ) ) {
			$this->set_settings_ext_viewer();

		} elseif ( isset( $this->POST['set_mumble_users'] ) ) {
			$this->set_mumble_users();

		} elseif ( isset( $this->POST['set_pw_requests_options'] ) ) {
			$this->set_pw_requests_options();

		} elseif ( isset( $this->POST['set_settings_debug'] ) ) {
			$this->set_settings_debug();

		} elseif ( isset( $this->GET['send_debug_email'] ) ) {
			$this->send_debug_email( $this->GET['send_debug_email']  );
		}
	}

	private function set_language_flag( $lang ) {

		$this->redirection = 'referer';

		$this->PMA->cookie->set( 'lang', $lang );
		$this->PMA->cookie->update();
	}

	private function set_options() {

		if ( ! $this->PMA->user->is_min( CLASS_USER ) ) {
			$this->illegal_operation();
		}

		$this->PMA->cookie->set( 'lang', $this->POST['lang'] );
		$this->PMA->cookie->set( 'skin', $this->POST['skin'] );
		$this->PMA->cookie->set( 'timezone', $this->POST['timezone'] );
		$this->PMA->cookie->set( 'time', $this->POST['time'] );
		$this->PMA->cookie->set( 'date', $this->POST['date'] );
		$this->PMA->cookie->set( 'installed_localeFormat', $this->POST['locales'] );
		$this->PMA->cookie->set( 'uptime', (int) $this->POST['uptime'] );
		$this->PMA->cookie->set( 'vserver_login', $this->POST['vserver_login'] );
		$this->PMA->cookie->update();
	}

	private function toggle_infopanel() {

		if ( ! $this->PMA->user->is_min( CLASS_USER ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		$this->PMA->cookie->set( 'infoPanel', ! $this->PMA->cookie->get( 'infoPanel' ) );
		$this->PMA->cookie->update();
	}

	private function toggle_highlight_pmaLogs() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->PMA->cookie->set( 'highlight_pmaLogs', ! $this->PMA->cookie->get( 'highlight_pmaLogs' ) );
		$this->PMA->cookie->update();
	}

	private function check_for_update() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$updates = new PMA_updates();
		$updates->check();
	}

	private function set_default_options() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		$this->PMA->config->set( 'default_lang', $this->POST['lang'] );
		$this->PMA->config->set( 'default_skin', $this->POST['skin'] );
		$this->PMA->config->set( 'default_timezone', $this->POST['timezone'] );
		$this->PMA->config->set( 'default_time', $this->POST['time'] );
		$this->PMA->config->set( 'default_date', $this->POST['date'] );
		$this->PMA->config->set( 'default_installed_locales', $this->POST['locales'] );
	}

	private function add_locales_profile() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		$array = $this->PMA->config->get( 'installed_localesProfiles' );

		$key = $this->POST['key'];
		$value = $this->POST['val'];

		if ( $key !== '' && $value !== '' && ! isset( $array[ $key ] ) && ! in_array( $value, $array ) ) {

			$array[ $key ] = $value;
			natCaseSort( $array );
			$this->PMA->config->set( 'installed_localesProfiles', $array );
		}
	}

	private function delete_locales_profile( $key ) {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		$array = $this->PMA->config->get( 'installed_localesProfiles' );

		if ( $key !== '' && isset( $array[ $key ] ) ) {

			unset( $array[ $key ] );
			$this->PMA->config->set( 'installed_localesProfiles', $array );
		}
	}

	private function set_settings_general() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->PMA->config->set( 'siteTitle', $this->POST['title'] );
		$this->PMA->config->set( 'siteComment', $this->POST['comment'] );

		if ( ctype_digit( $this->POST['auto_logout'] ) ) {
			$this->PMA->config->set( 'auto_logout', (int) $this->POST['auto_logout'] );
		}

		if ( ctype_digit( $this->POST['check_update'] ) ) {
			$this->PMA->config->set( 'update_check', (int) $this->POST['check_update'] );
		}

		$this->PMA->config->set( 'murmur_version_url', isset( $this->POST['murmur_vers_url'] ) );

		$this->PMA->config->set( 'ddl_auth_page', isset( $this->POST['activate_for_auth'] ) );

		if ( ctype_digit( $this->POST['refreshTime'] ) ) {
			$this->PMA->config->set( 'ddl_refresh', (int) $this->POST['refreshTime'] );
		}

		$this->PMA->config->set( 'ddl_show_cache_uptime', isset( $this->POST['show_uptime'] ) );

		$this->PMA->config->set( 'show_avatar_sa', isset( $this->POST['show_avatar_sa'] ) );
	}

	private function set_settings_autoban() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		if ( ctype_digit( $this->POST['attempts'] ) ) {
			$this->PMA->config->set( 'autoban_attempts', (int) $this->POST['attempts'] );
		}

		if ( ctype_digit( $this->POST['timeFrame'] ) ) {
			$this->PMA->config->set( 'autoban_frame', (int) $this->POST['timeFrame'] );
		}

		if ( ctype_digit( $this->POST['duration'] ) ) {
			$this->PMA->config->set( 'autoban_duration', (int) $this->POST['duration'] );
		}
	}

	private function set_settings_smtp() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->PMA->config->set( 'smtp_host', $this->POST['host'] );

		if ( check_port( $this->POST['port'] ) ) {
			$this->PMA->config->set( 'smtp_port', (int) $this->POST['port'] );
		} else {
			$this->error( 'invalid_port' );
		}

		$this->PMA->config->set( 'smtp_default_sender_email', $this->POST['default_sender'] );
	}

	private function set_settings_logs() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		// murmur logs
		if ( ctype_digit( $this->POST['murmur_logs_size'] ) OR $this->POST['murmur_logs_size'] === '-1' ) {
			$this->PMA->config->set( 'vlogs_size', (int) $this->POST['murmur_logs_size'] );
		}

		$this->PMA->config->set( 'vlogs_admins_active', isset( $this->POST['activate_admins'] ) );
		$this->PMA->config->set( 'vlogs_admins_highlights', isset( $this->POST['adm_hightlights_logs'] ) );

		// PMA logs
		if ( $this->PMA->user->is_min( CLASS_SUPERADMIN ) ) {

			if ( ctype_digit( $this->POST['log_keep'] ) ) {
				$this->PMA->config->set( 'pmaLogs_keep', (int) $this->POST['log_keep'] );
			}

			$this->PMA->config->set( 'pmaLogs_SA_actions', isset( $this->POST['log_SA'] ) );
		}
	}

	private function set_settings_tables() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		if ( ctype_digit( $this->POST['overview'] ) ) {
			$this->PMA->config->set( 'table_overview', (int) $this->POST['overview'] );
		}

		if ( ctype_digit( $this->POST['users'] ) ) {
			$this->PMA->config->set( 'table_users', (int) $this->POST['users'] );
		}

		if ( ctype_digit( $this->POST['bans'] ) ) {
			$this->PMA->config->set( 'table_bans', (int) $this->POST['bans'] );
		}

		$this->PMA->config->set( 'show_total_users', isset( $this->POST['set1'] ) );
		$this->PMA->config->set( 'show_total_users_sa', isset( $this->POST['set2'] ) );
		$this->PMA->config->set( 'show_online_users', isset( $this->POST['set3'] ) );
		$this->PMA->config->set( 'show_online_users_sa', isset( $this->POST['set4'] ) );
		$this->PMA->config->set( 'show_uptime', isset( $this->POST['set5'] ) );
		$this->PMA->config->set( 'show_uptime_sa', isset( $this->POST['set6'] ) );

	}

	private function set_settings_ext_viewer() {
		$this->PMA->config->set( 'external_viewer_enable', isset( $this->POST['enable'] ) );

		if ( ctype_digit( $this->POST['width'] ) ) {
			$this->PMA->config->set( 'external_viewer_width', (int) $this->POST['width'] );
		}
		if ( ctype_digit( $this->POST['height'] ) ) {
			$this->PMA->config->set( 'external_viewer_height', (int) $this->POST['height'] );
		}

		$this->PMA->config->set( 'external_viewer_vertical', isset( $this->POST['vertical'] ) );
		$this->PMA->config->set( 'external_viewer_scroll', isset( $this->POST['scroll'] ) );
	}

	private function set_mumble_users() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->PMA->config->set( 'SU_auth', isset( $this->POST['set1'] ) );
		$this->PMA->config->set( 'SU_edit_user_pw', isset( $this->POST['set3'] ) );
		$this->PMA->config->set( 'SU_start_vserver', isset( $this->POST['set4'] ) );
		$this->PMA->config->set( 'SU_ru_active', isset( $this->POST['set5'] ) );
		$this->PMA->config->set( 'RU_auth', isset( $this->POST['set6'] ) );
		$this->PMA->config->set( 'RU_delete_account', isset( $this->POST['set7'] ) );
		$this->PMA->config->set( 'RU_edit_login', isset( $this->POST['set8'] ) );
		$this->PMA->config->set( 'pw_gen_active', isset( $this->POST['set9'] ) );
	}

	private function set_pw_requests_options() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		$this->redirection = 'referer';

		$this->PMA->config->set( 'pw_gen_explicit_msg', isset( $this->POST['explicit_msg'] ) );
		$this->PMA->config->set( 'pw_gen_sender_email', $this->POST['sender_email'] );

		if ( ctype_digit( $this->POST['pending_delay'] ) ) {
			$this->PMA->config->set( 'pw_gen_pending', (int) $this->POST['pending_delay'] );
		}
	}


	private function set_settings_debug() {

		if ( ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		if ( ctype_digit( $this->POST['mode'] ) ) {
			$this->PMA->config->set( 'debug', (int) $this->POST['mode'] );
		}

		$this->PMA->config->set( 'debug_session', isset( $this->POST['session'] ) );
		$this->PMA->config->set( 'debug_object', isset( $this->POST['object'] ) );
		$this->PMA->config->set( 'debug_stats', isset( $this->POST['stats'] ) );
		$this->PMA->config->set( 'debug_select_flag', isset( $this->POST['flag'] ) );
		$this->PMA->config->set( 'debug_email_to', $this->POST['email'] );
	}

	private function send_debug_email( $key ) {

		if ( PMA_DEBUG < 1 &&  ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
			$this->illegal_operation();
		}

		require 'main/functions/pma_mail.php';

		$from = get_sender_email( $key );
		$to[] = array( 'type' => 'to', 'email' => PMA_config::instance()->get( 'debug_email_to' ) );
		$subject = PMA_NAME.' debug email';
		$headers = '';
		$message = 'This is an automatique debug message sent by '.PMA_NAME;

		$pma_mail = pma_mail( $from, $to, $subject, $headers, $message );

		if ( $pma_mail ) {
			$this->success( 'debug_mail_succced' );
		} else {
			$this->error( 'debug_mail_failed' );
		}
	}
}

?>
