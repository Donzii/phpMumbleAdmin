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

class PMA_helpers_options {

	static function get_languages() {

		$languages = array();

		$scan = scan_dir( PMA_DIR_LANGUAGES );

		$i = 0;

		foreach( $scan as $dir ) {

			$path = PMA_DIR_LANGUAGES.$dir;

			if ( ! is_dir( $path ) OR ! is_readable( $path.'/common.loc.php' )  ) {
				continue;
			}

			$languages[ $i ]['dir'] = $dir;
			$languages[ $i ]['name'] = $dir;

			// Set localized values
			if ( is_readable( $path.'/_LOCALE_CONFIG.php' ) ) {

				include $path.'/_LOCALE_CONFIG.php';

				if ( isset( $localeConf['name'] ) && $localeConf['name'] !== '' ) {
					$languages[ $i ]['name'] = $localeConf['name'];
				}

				if ( isset( $localeConf['localized'] ) && $localeConf['localized'] !== '' ) {
					$languages[ $i ]['name'] = $languages[ $i ]['name'].' ( '.$localeConf['localized'].' )';
				}

				unset( $localeConf );
			}
			++$i;
		}

		sort_array_by( $languages, 'name' );

		return $languages;
	}

	static function get_skins() {

		$scan = scan_dir( PMA_DIR_CSS.'themes/' );

		$skins = array();

		foreach( $scan as $entry ) {

			if ( substr( $entry, -4 ) === '.css' ) {

				$skins[] = $entry;
			}
		}

		return $skins;
	}

	static function get_timezones() {

		$tz = array();

		// PHP 5.2
		if ( ! function_exists( 'timezone_identifiers_list' ) ) {
			return $tz;
		}

		$zones = timezone_identifiers_list();
		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

		foreach ( $zones as $zone ) {

			// Return 2 or 3 value
			$explose = explode( '/', $zone );

			$continent = $explose[0];

			$city = str_replace( '_', ' ', end( $explose ) );

			if ( in_array( $continent, $continents, TRUE ) ) {
				$tz[ $continent ][ $zone ] = $city;

			} else {
				$tz['Other'][ $zone ] = $zone;
			}
		}

		return $tz;
	}

	// Return an array of all installed locales on the host system.
	static function get_installed_locales() {

		global $TEXT;

		// Memo: $installed_locales will return an array, always.
		if ( PMA_OS === 'linux' ) {

			exec( 'locale -a', $installed_locales );

		} else {
			$installed_locales = array();
		}

		// Add default at the top of the array
		$return['default'] = $TEXT['default'];

		foreach( $installed_locales as $key => $value ) {

			if ( $value === 'C' OR $value === 'POSIX' ) {
				unset( $installed_locales[ $key ] );
				continue;
			}

			$return[ $value ] = $value;
		}

		return $return;
	}
}

?>