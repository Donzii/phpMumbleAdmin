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

class PMA_cmd_murmur_settings extends PMA_cmd {

	private $prx;

	private $default_conf;
	private $custom_conf;

	private $settings;

	function process() {

		if ( ! $this->PMA->user->is_min( CLASS_SUPERUSER_RU ) ) {
			$this->illegal_operation();
		}

		$sid = $_SESSION['page_vserver']['id'];

		if ( NULL === $this->prx = $this->PMA->meta->getServer( $sid ) ) {
			$this->end();
		}

		$this->default_conf = $this->PMA->meta->getDefaultConf();
		$this->custom_conf = $this->prx->getAllConf();

		// Port particularity
		$this->default_conf['port'] = (string) ( $this->default_conf['port'] + $sid - 1 );

		// Get $vserver_settings
		require 'main/include/vars.vserver_settings.php';
		$this->settings =  $vserver_settings;

		if ( isset( $this->POST['setConf'] ) ) {
			$this->set_settings();

		} elseif ( isset( $this->GET['reset_setting'] ) ) {
			$this->reset_setting( $this->GET['reset_setting'] );

		} elseif ( isset( $this->POST['reset_setting'] ) ) {
			$this->reset_setting_confirm( $this->POST['reset_setting'] );

		// ADD A CERTIFICATE - UPLOAD and FORM
		} elseif ( isset( $_FILES['add_certificate'] ) OR isset( $this->POST['add_certificate'] ) ) {
			$this->add_certificate();
		}
	}

	private function set_settings() {

		foreach( $this->settings as $key => $array ) {

			// SANITY:

			if ( ! isset( $this->POST[ $key ] ) ) {
				continue;
			}

			$new_value = $this->POST[ $key ];

			// Disallow Admins and SuperUsers to change SuperAdmins only parameters.
			if ( $array['right'] === 'SA' && ! $this->PMA->user->is_min( CLASS_ROOTADMIN ) ) {
				continue;
			}

			// Invalid form
			if ( $key !== 'welcometext' && strlen( $new_value ) > 255 ) {
				$this->debug( __file__ .': Too long value for "'.$key.'"', 1, TRUE );
				continue;
			}

			// Empty value
			if ( $new_value === '' ) {

				if ( $array['type'] === 'bool' ) {
					continue;
				}

				// Remove custom parameter if exists
				if ( isset( $this->custom_conf[ $key ] ) ) {

					// Memo: setConf with zero, one or multiple space will alway remove the key in the DB ( setConf( $key, '    ' ); ).
					$this->prx->setConf( $key, '' );
					continue;

				} else {
					continue;
				}
			}

			// Don't add custom value if it's the same as $this->default_conf
			if ( isset( $this->default_conf[ $key ] ) && $new_value === $this->default_conf[ $key ] ) {

				// A custom value is set for the key, remove it.
				if ( isset( $this->custom_conf[ $key ] ) ) {
					$this->prx->setConf( $key, '' );
				}

				continue;
			}

			// The custom value didn't change, do anything.
			if ( isset( $this->custom_conf[ $key ] ) && $new_value === $this->custom_conf[ $key ] ) {
				continue;
			}

			// SET CONF:

			// Host particularity
			if ( $key === 'host' ) {

				$this->prx->setConf( $key, $new_value );

				if ( $this->prx->isRunning() ) {
					$this->success( 'host_modified_success' );
				}

			// Port particularity
			} elseif ( $key === 'port' ) {

				if ( check_port( $new_value ) ) {

					$this->prx->setConf( $key, $new_value );

					if ( $this->prx->isRunning() ) {
						$this->success( 'port_modified_success' );
					}

				} else {
					msg_box( 'invalid_port', 'error' );
				}

			// Registername particularity
			} elseif ( $key === 'registername' ) {

				if ( $this->prx->validate_chars( 'channelname', $new_value ) ) {
					$this->prx->setConf( $key, $new_value );
				} else {
					msg_box( 'invalid_channel_name', 'error' );
				}

			// Integer particularity
			} elseif ( $array['type'] === 'integer' ) {

				if ( ctype_digit( $new_value ) ) {
					$this->prx->setConf( $key, $new_value );
				} else {
					msg_box( 'invalid_numerical', 'error', 'sprintf='.$key );
				}

			// Bool particularity
			} elseif ( $array['type'] === 'bool'  ) {

				if ( $new_value === 'true' OR $new_value === 'false' ) {
					$this->prx->setConf( $key, $new_value );
				}

			// Default
			} else {
				$this->prx->setConf( $key, $new_value );
			}
		}
	}

	private function reset_setting( $key ) {

		$this->prx->setConf($key, '' );

		if ( $this->prx->isRunning() ) {

			if ( $key === 'host' ) {
				$this->success( 'host_modified_success' );
			}

			if ( $key === 'port' ) {
				$this->success( 'port_modified_success' );
			}
		}
	}

	private function reset_setting_confirm( $key ) {

		if ( ! isset( $this->POST['confirmed'] ) ) {
			$this->end();
		}

		$this->prx->setConf( $key, '' );

		if ( $key === 'certificate' ) {

			// Delete the private key when we delete the certificate.
			$this->prx->setConf( 'key', '' );

			if ( $this->prx->isRunning() ) {
				$this->success( 'certificate_modified_success' );
			}
		}
	}

	private function add_certificate() {

		if ( isset( $_FILES['add_certificate'] ) ) {

			// Error on upload, file max 20 KB
			if ( $_FILES['add_certificate']['error'] !== 0 OR $_FILES['add_certificate']['size'] > 20480 ) {
				$this->error( 'invalid_certificate' );
			}

			$pem = file_get_contents( $_FILES['add_certificate']['tmp_name'] );

		} elseif ( isset( $this->POST['add_certificate'] ) ) {

			$pem = $this->POST['add_certificate'];
		}

		// Separate priv key and certificate.
		// Memo : Invalid PEM file will throw a php warning message.
		// Remove this warning, or redirection will not be possible.
		if ( ! function_exists( 'openssl_pkey_export' ) OR ! function_exists( 'openssl_x509_export' ) ) {
			$this->error( 'php_openssl_module_dont_exists' );
		}

		@openssl_pkey_export( $pem, $privatekey );
		@openssl_x509_export( $pem, $certificate );

		if ( ! isset( $privatekey, $certificate ) ) {
			$this->error( 'invalid_certificate' );
		}

		//  Checks if the priv key corresponds to the certificate.
		if ( ! openssl_x509_check_private_key( $certificate, $privatekey ) ) {
			$this->error( 'invalid_certificate' );
		}

		$this->prx->setConf( 'key', $privatekey );
		$this->prx->setConf( 'certificate', $certificate );

		if ( $this->prx->isRunning() ) {
			$this->success( 'certificate_modified_success' );
		}
	}
}

?>
