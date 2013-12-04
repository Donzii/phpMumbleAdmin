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

class PMA_output_bans {

	static function duration( $obj = NULL, $checked = TRUE ) {

		global $TEXT;

		// Set end durations herited from edit ban page
		if ( is_object( $obj ) && $obj->duration !== 0 ) {

			$ts = $obj->start + $obj->duration;

			$end['hour'] = (int) date( 'H', $ts );
			$end['day'] = (int) date( 'd', $ts );
			$end['month'] = (int) date( 'm', $ts );
			$end['year'] = (int) date( 'Y', $ts );

		} else {
			$end['hour'] = NULL;
			$end['day'] = NULL;
			$end['month'] = NULL;
			$end['year'] = NULL;
		}

		$months[1] = $TEXT['january'];
		$months[2] = $TEXT['feburary'];
		$months[3] = $TEXT['march'];
		$months[4] = $TEXT['april'];
		$months[5] = $TEXT['may'];
		$months[6] = $TEXT['june'];
		$months[7] = $TEXT['july'];
		$months[8] = $TEXT['august'];
		$months[9] = $TEXT['september'];
		$months[10] = $TEXT['october'];
		$months[11] = $TEXT['november'];
		$months[12] = $TEXT['december'];

		$options['hour'] = '';
		for ( $i = 0; $i <= 23; ++$i ) {
			if ( $i === $end['hour'] ) {
				$options['hour'] .= '<option selected="selected" value="'.$i.'">'.$i.' H</option>';
			} else {
				$options['hour'] .= '<option value="'.$i.'">'.$i.' H</option>';
			}
		}

		$options['day'] = '';
		for  ( $i = 1; $i <= 31; ++$i ) {
			if ( $i === $end['day'] ) {
				$options['day'] .= '<option selected="selected" value="'.$i.'">'.$i.'</option>';
			} else {
				$options['day'] .= '<option value="'.$i.'">'.$i.'</option>';
			}
		}

		$options['month'] = '';
		for  ( $i = 1; $i <= 12; ++$i ) {
			if ( $i === $end['month'] ) {
				$options['month'] .= '<option selected="selected" value="'.$i.'">'.$months[ $i ].'</option>';
			} else {
				$options['month'] .= '<option value="'.$i.'">'.$months[ $i ].'</option>';
			}
		}

		// Start to current year
		$Y = date( 'Y' );

		$options['year'] = '';
		for  ( $i = $Y; $i <= 2037; ++$i ) {
			if ( $i === $end['year'] ) {
				$options['year'] .= '<option selected="selected" value="'.$i.'">'.$i.'</option>';
			} else {
				$options['year'] .= '<option value="'.$i.'">'.$i.'</option>';
			}
		}

		$output = '<div id="ban_duration">'.EOL;
		$output .= '<div>'.$TEXT['end'].'</div>'.EOL;

		$output .= '<select id="hour" name="hour" onChange="uncheck( \'permanent\' );">';
		$output .= '<option>'.$TEXT['hour'].'</option>';
		$output .= $options['hour'];
		$output .= '</select>'.EOL;

		$output .= '<select id="day" name="day" onChange="uncheck( \'permanent\' );">';
		$output .= '<option>'.$TEXT['day'].'</option>';
		$output .= $options['day'];
		$output .= '</select>'.EOL;

		$output .= '<select id="month" name="month" onChange="uncheck( \'permanent\' );">';
		$output .= '<option>'.$TEXT['month'].'</option>';
		$output .= $options['month'];
		$output .= '</select>'.EOL;

		$output .= '<select id="year" name="year" onChange="uncheck( \'permanent\' );">';
		$output .= '<option>'.$TEXT['year'].'</option>';
		$output .= $options['year'];
		$output .= '</select>'.EOL;

		// Permanent checkbox
		$output .= '<div><label for="permanent">'.$TEXT['permanent'].'</label>';
		$output .= '<input type="checkbox" id="permanent" name="permanent" '.HTML::chked( $checked ).' onClick="ban_duration( this );">';
		$output .= '</div>'.EOL;

		$output .= '</div>'.EOL.EOL;

		return $output;
	}
}

?>