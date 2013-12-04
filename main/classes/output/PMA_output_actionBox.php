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

class PMA_output_actionBox {

	public $output = '';

	// Custom datas to insert.
	public $custom_datas = '';

	private $method = 'post';
	private $css = 'medium';
	private $onSubmit = '';
	private $submit_txt = '';

	function set_conf( $key, $value ) {
		$this->$key = $value;
	}

	function input( $type, $name, $value ) {
		$this->output .= '<input '.$this->p( 'type', $type ).' '.$this->p( 'name', $name ).' '.$this->p( 'value', $value ).'>'.EOL;
	}

	/**
	* Properties
	*/
	function p( $property, $value ) {

		if ( $value !== '' ) {
			return $property.'="'.$value.'"';
		}
	}

	function checkbox( $key, $txt, $checked = FALSE ) {
		$this->output .= '<div class="pad"><label for="'.$key.'">'.$txt.'</label> <input type="checkbox" id="'.$key.'" name="'.$key.'" '.HTML::chked( $checked ).'></div>'.EOL;
	}

	/**
	* Cancel - button or with toolbox bar
	*/
	function cancel( $type = 'button' ) {

		global $TEXT;

		if ( $type === 'toolbox' ) {
			$this->output .= '<div class="toolbar"><a href="./">';
			$this->output .= HTML::img( IMG_CANCEL_22, 'button back right', $TEXT['cancel'] ).'</a>';
			$this->output .= '</div>'.EOL.EOL;
		} else {
			$this->output .= '<a href="./">'.HTML::img( IMG_CANCEL_16, 'button back', $TEXT['cancel'] ).'</a>'.EOL;
		}
	}

	function form() {

		if ( $this->onSubmit !== '' ) {
			$js = 'onSubmit="'.$this->onSubmit.'"';
		} else {
			$js = '';
		}

		$this->output .= '<form method="'.$this->method.'" action="" class="actionBox '.$this->css.'" '.$js.'>'.EOL;
	}

	function title( $txt ) {
		$this->output .= '<h1>'.$txt.'</h1>'.EOL;
	}

	function send( $txt, $name, $value ) {
		$this->output .= '<h1><label for="'.$name.'">'.$txt.'</label></h1>'.EOL;
		$this->output .= '<div class="pad"><input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'"></div>'.EOL;
	}

	function confirm( $name, $txt ) {

		if ( $name !== '' ) {
			$this->output .= '<div class="name">'.$name.'</div>'.EOL;
		}

		$this->output .= '<div class="pad">'.$txt.'</div>'.EOL;
	}

	function textarea( $name, $title, $datas ) {
		$this->output .= '<h1><label for="'.$name.'">'.$title.'</label></h1>'.EOL;
		$this->output .= '<div class="pad"><textarea id="'.$name.'" name="'.$name.'" rows="10" cols="4">'.$datas.'</textarea></div>'.EOL;
	}

	function table() {
		$this->output .= '<table class="config oBox">'.EOL;
	}

	function tr_title( $title ) {
		$this->output .= '<tr class="pad"><th class="title">'.$title.'</th></tr>'.EOL;
	}

	function tr( $type, $txt, $name, $value  ) {
		$this->output .= '<tr><th><label for="'.$name.'">'.$txt.'</label></th>';
		$this->output .= '<td><input type="'.$type.'" id="'.$name.'" name="'.$name.'" value="'.$value.'"></td></tr>'.EOL;
	}

	function tr_pad() {
		$this->output .= '<tr class="pad"><td class="hide"></td></tr>'.EOL;
	}

	function tr_custom( $datas ) {
		$this->output .= '<tr>'.$datas.'</tr>'.EOL;
	}

	function scroll( $title, $count ) {

		global $TEXT;

		$this->output .= '<h1>'.$title.'</h1>'.EOL;
		$this->output .= '<div style="text-align: left;">'.EOL;
		$this->output .= '<div class="pad">'.$TEXT['select_user_to_move'].'</div>'.EOL;

		if ( $count > 10 ) {
			$this->output .= '<div class="scroll oBox">'.EOL;
		}

		$this->output .= $this->custom_datas;

		if ( $count > 10 ) {
			$this->output .= '</div>'.EOL;
		}

		$this->output .= '</div>'.EOL;
		$this->output .= '<div class="pad"><input type="submit" value="'.$TEXT['move'].'"></div>'.EOL;
	}

	function confirm_stop_sid() {

		global $TEXT;

		$this->output .= '<h1>'.$TEXT['server_not_empty'].'</h1>'.EOL;
		// Kick users
		$this->output .= '<div class="pad txtL"><label for="kickUsers">'.$TEXT['kick_users'];
		$this->output .= HTML::info_bubble( HTML::img( IMG_INFO_16 ), $TEXT['kick_users_info'] ).'</label>';
		$this->output .= '<input type="checkbox" id="kickUsers" name="kickUsers"></div>'.EOL;
		// Msg
		$this->output .= '<div class="txtL"><label for="msg">'.$TEXT['stop_raison'].'</label></div>';
		$this->output .= '<div><textarea id="msg" name="msg" rows="10" cols="4"></textarea></div>'.EOL;
		$this->output .= '<div class="pad"><input type="submit" name="confirmed" value="'.$TEXT['stop_server'].'"></div>'.EOL;
	}

	function submit( $type = 'confirm' ) {

		global $TEXT;

		switch( $type ) {

			case 'table':
				$this->output .= '<tr><th class="mid" colspan="2"><input type="submit" value="'.$this->submit_txt.'"></th></tr>'.EOL;
				break;

			case 'confirm':
				$this->output .= '<div class="pad">';
				$this->output .= '<input type="submit" name="confirmed" value="'.$TEXT['yes'].'">';
				$this->output .= ' <input type="submit" class="b" name="cancel" value="'.$TEXT['no'].'">';
				$this->output .= '</div>'.EOL;
				break;
		}
	}

	function close( $type = 'form' ) {

		if ( $type === 'table' ) {
			$this->output .= '</table>'.EOL;
		} else {
			$this->output .= '</form>'.EOL.EOL;
		}
	}

	/**
	* "Select a channel in the viewer" HTML box
	*
	* @return string
	*/
	static function select_channel( $title, $unlink_all = FALSE ) {

		global $TEXT;

		$output = '<div class="actionBox">'.EOL;
		// Cancel
		$output .= '<a href="./">'.HTML::img( IMG_CANCEL_16, 'button back', $TEXT['cancel'] ).'</a>'.EOL;
		$output .= '<h1>'.$title.'</h1>'.EOL;
		$output .= '<div class="pad txtL">'.$TEXT['select_in_right_tree'].'</div>'.EOL;

		if ( $unlink_all === TRUE ) {
			$output .= '<div class="pad txtR"><a href="?cmd=murmur_channel&amp;unlink_all_channel">'.$TEXT['unlink_all_channels'].'</a></div>'.EOL;
		}

		$output .= '</div>'.EOL;

		return $output;
	}
}

?>