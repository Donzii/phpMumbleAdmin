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

/**
* @function sort_channels
*
* Sort tree object like the mumble client do.
*
* icePHP return a tree object sorted with case sensitive, no natural order ( like strCmp() do ).
* The mumble client sort order is strCaseCmp() model.
*
* This also permit to fix the channel order bug with phpICE >= 3.4.0 .
*
*/
function sort_channels( $a, $b ) {

	if ( $a->c->parent === $b->c->parent && $a->c->position !== $b->c->position ) {

		return $a->c->position - $b->c->position;

	} else {
		return strCaseCmp( $a->c->name, $b->c->name );
	}
}

class PMA_output_viewer {

	/**
	* Enable / disable viewer HTTP links.
	*/
	private $disabled = FALSE;

	/**
	* Enable / disable channel password display
	*/
	private $show_pw_channels = FALSE;

	private $show_linked_channels = FALSE;

	private $show_status_icons = FALSE;

	private $default_channel_id;
	private $vserver_name = '';

	private $selected_channel_id;
	private $selected_session_id; // Selected user

	/**
	* Array of all childrens id of the selected channel.
	* Used to disable childrens on channel move action.
	*/
	private $childrens = array();

	private $action = FALSE;
	private $action_to;
	private $css = '';
	private $href = '?channel';
	private $exclude_cid = array();
	private $direct_links = array();
	private $indirect_links = array();
	private $deep_status_menu = array();

	private $prx;
	private $tree;
	private $chans_list;
	private $users_list;

	private $output;

	function __construct( $prx, $tree, $chans, $users ) {

		$this->prx = $prx;
		$this->tree = $tree;
		$this->chans_list = $chans;
		$this->users_list = $users;
	}

	function set( $key, $value ) {
		$this->$key = $value;
	}

	function disable() {
		$this->disabled = TRUE;
	}

	function select_channel( $id ) {

		$this->selected_channel_id = $id;

		$chan = $this->get_selected_channel();

		if ( ! empty( $chan->links ) ) {

			$this->direct_links = $chan->links;
			$this->direct_links[] = $chan->id;

			$this->get_indirect_links( $chan->links );
		}
	}

	function select_user( $session ) {
		$this->selected_session_id = $session;
	}

	function set_action( $action ) {

		$this->action = TRUE;
		$this->css = 'action';

		switch( $action ) {

			case 'move_user':

				$user = $this->get_selected_user();

				$this->href = '?cmd=murmur_users_sessions&amp;move_user_to';

				// Deny to select current user channel
				$this->exclude_cid = array( $user->channel );

				break;

			case 'move_users_out':

				$chan = $this->get_selected_channel();

				$this->href = '?action=move_users_out&amp;to';

				// Deny to select current channel
				$this->exclude_cid = array( $chan->id );

				if ( $this->action_to !== NULL ) {

					$this->disable();
					$this->css = 'disabled';
				}
				break;

			case 'move_users_into_the_channel':

				$this->disable();
				$this->css = 'disabled';
				break;

			case 'move_channel':

				$chan = $this->get_selected_channel();

				$this->href = '?cmd=murmur_channel&amp;move_channel_to';

				$sub = $this->get_sub_channel( $this->tree, $chan->id );

				$this->get_all_childrens( $sub );

				// Deny to select parent channel, current and all it's children channels
				$this->exclude_cid = $this->childrens;
				$this->exclude_cid[] = $chan->id;
				$this->exclude_cid[] = $chan->parent;

				break;

			case 'link_channel':

				$chan = $this->get_selected_channel();

				$this->href = '?cmd=murmur_channel&amp;link_channel';

				if ( ! empty( $this->direct_links ) ) {

					$this->exclude_cid = $this->direct_links;

				} else {
					$this->exclude_cid = array( $chan->id );
				}

				break;

			case 'unlink_channel':

				$chan = $this->get_selected_channel();

				$this->href = '?cmd=murmur_channel&amp;unlink_channel';

				// Permit to select only directs links
				// Parse and get all channels which are not a direct link with the selected channel.
				$not_direct_link = array();

				foreach ( $this->chans_list as $cid => $obj ) {

					if ( ! in_array( $cid, $chan->links, TRUE ) ) {
						$not_direct_link[] = $cid;
					}
				}

				$this->exclude_cid = $not_direct_link;

				break;

			default:
				// Other actions are not viewer action
				$this->action = FALSE;
				$this->css = '';
				break;
		}
	}

	function get_css() {
		return $this->css;
	}

	function output() {
		$this->channel( $this->tree, 0, 0 );
		return $this->output;
	}

	/**
	* Line by line.
	*
	* Channels...
	*/
	private function channel( $obj, $deep, $last_id ) {

		$chan = $obj->c;

		// Check if current channel is selected.
		if ( $chan->id === $this->selected_channel_id ) {

			$css = ' class="selected"';

		// Special css for the "move users to" channel target.
		} elseif ( $this->action && $this->action_to === $chan->id ) {

			$css = ' class="moveTo"';

		} else {
			$css = '';
		}

		$this->output .= '<div'.$css.'>';

		// Open the HTML <a> tag
		if ( ! $this->disabled && ! in_array( $chan->id, $this->exclude_cid, TRUE ) ) {

			$close_A_tag = TRUE;

			$this->output .= '<a href="'.$this->href.'='.$chan->id.'">';
		}

		$this->output .= '<span class="block left">';

		$this->deep( $chan->id, $last_id, $deep );

		// Tree img
		if ( $chan->id > 0 ) {

			if ( $chan->id === $last_id ) {
				$img = 'tree_end';
			} else {
				$img = 'tree_mid';
			}

			$this->output .= HTML::img( 'pma/'.$img.'.png', 'left' );
		}

		// Channel icon
		if ( in_array( $chan->id, $this->direct_links, TRUE ) ) {

			$img = 'pma/tree_link_direct';

		} elseif ( in_array( $chan->id, $this->indirect_links, TRUE ) ) {

			$img = 'pma/tree_link_indirect';

		} elseif ( $chan->id === 0 ) {

			$img = 'mumble/mumble';

		} elseif ( $this->show_linked_channels && ! empty( $chan->links ) ) {

			$img = 'pma/tree_link_with';

		} else {
			$img = 'pma/tree_channel';
		}

		$this->output .= HTML::img( $img.'.png', 'left' );

		// Space padding
		$this->output .= HTML::img( IMG_SPACE_16, 'left' );

		// End block left
		$this->output .= '</span>';

		// Channel password img
		$this->get_chan_password_icon( $chan->id );

		$this->get_chan_status_icons( $chan );

		$this->get_chan_name( $chan );

		if ( isset( $close_A_tag ) ) {
			$this->output .= '</a>';
		}

		$this->output .= '</div>'.EOL;

		$count_users = count( $obj->users );
		$count_chans = count( $obj->children );

		if ( $count_users > 0 ) {

			usort( $obj->users, 'sort_obj_by_names' );

			$last_session = $obj->users[ $count_users - 1 ]->session;

			// Check if a sub channel exists after last user.
			$sub_channels = ( $count_chans > 0 );

			foreach ( $obj->users as $user ) {
				$this->user( $user, $deep + 1, $last_session, $sub_channels );
			}
		}

		if ( $count_chans > 0 ) {

			usort( $obj->children, 'sort_channels' );

			$last_sessid = $obj->children[ $count_chans - 1 ]->c->id;

			foreach ( $obj->children as $child ) {
				$this->channel( $child, $deep + 1, $last_sessid );
			}
		}
	}

	/**
	* Channel name
	*
	*/
	private function get_chan_name( $chan ) {

		$css = '';
		$name = $chan->name;

		// Root channel name
		if ( $chan->id === 0 ) {

			if ( $this->vserver_name !== '' ) {
				$name = $this->vserver_name;
			} else {
				$name = 'Root';
			}

			$css = ' b';
		}

		// Default channel name ( MEMO: remove this ? It's a bit redundant with the default channel icon )
// 		if ( $this->default_channel_id === $chan->id ) {
// 			$css = ' b i';
// 			$name = '+'.$name;
// 		}

		$this->output .= '<span class="name'.$css.'">'.html_encode( $name ).'</span>';
	}

	/**
	* Channel password
	*
	*/
	private function get_chan_password_icon( $id ) {

		if ( $this->show_pw_channels !== TRUE ) {
			return;
		}

		$this->prx->getACL( $id, $aclList, $NULL, $NULL );

		foreach ( $aclList as $acl ) {

			if ( ! $acl->inherited && PMA_helpers_ACL::is_token( $acl ) ) {

				$this->output .= HTML::img( 'gei/padlock_16.png', 'right' );
				break;
			}
		}
	}

	private function get_chan_status_icons( $chan ) {

		if ( $this->show_status_icons !== TRUE ) {
			return;
		}

		if ( $chan->temporary ) {
			$this->output .= HTML::img( 'tango/clock_16.png', 'right' );
		}

		if ( $chan->description !== '' ) {
			$this->output .= HTML::img( 'mumble/comment.png', 'right' );
		}

		if ( $this->default_channel_id === $chan->id ) {
			$this->output .= HTML::img( 'other/home_16.png', 'right' );
		}

		// Last : padding space img
		$this->output .= HTML::img( IMG_SPACE_16, 'right' );
	}


	/**
	* Line by line.
	*
	* Users...
	*/
	private function user( $user, $deep, $last_sessid, $sub_channels ) {

		// Check if current user is selected.
		if ( $this->selected_session_id === $user->session ) {
			$css = 'selected';
		} else {
			$css = '';
		}

		$this->output .= '<div class="'.$css.'">';

		// Open the HTML <a> tag
		if ( ! $this->action && ! $this->disabled ) {

			$close_A_tag = TRUE;

			$this->output .= '<a href="?userSession='.$user->session.'-'.rawUrlEncode( $user->name ).'">';
		}

		$this->deep( $user->session, $last_sessid, $deep );

		// Tree img
		if ( $user->session === $last_sessid && ! $sub_channels ) {
			$img = 'tree_end';
		} else {
			$img = 'tree_mid';
		}

		$this->output .= HTML::img( 'pma/'.$img.'.png', 'left' );

		// User icon
		$this->output .= HTML::img( 'pma/tree_user.png', 'left' );

		// Space padding
		$this->output .= HTML::img( IMG_SPACE_16, 'left' );

		$this->get_user_status_icons( $user );

		// User name
		$name = html_encode( $user->name );

		if ( $user->userid === 0 && strToLower( $user->name ) !== 'superuser' ) {
			$name .= ' <i>( SuperUser )</i>';
		}

		if ( $name !== '' ) {
			$this->output .= '<span class="name b">'.$name.'</span>';
		}

		if ( isset( $close_A_tag ) ) {
			$this->output .= '</a>';
		}

		$this->output .= '</div>'.EOL;
	}

	private function get_user_status_icons( $user ) {

		if ( $this->show_status_icons !== TRUE ) {
			return;
		}

		if ( $user->userid >= 0 ) {
			$this->output .= HTML::img( 'mumble/user_auth.png', 'right' );
		}
		if ( isset( $user->recording ) && $user->recording ) {
			$this->output .= HTML::img( 'xchat/red_16.png', 'right' );
		}
		if ( isset( $user->prioritySpeaker ) && $user->prioritySpeaker ) {
			$this->output .= HTML::img( 'tango/microphone_16.png', 'right' );
		}
		if ( $user->comment !== '' ) {
			$this->output .= HTML::img( 'mumble/comment.png', 'right' );
		}
		if ( $user->suppress ) {
			$this->output .= HTML::img( 'mumble/user_suppressed.png', 'right' );
		}
		if ( $user->mute ) {
			$this->output .= HTML::img( 'mumble/user_muted.png', 'right' );
		}
		if ( $user->deaf ) {
			$this->output .= HTML::img( 'mumble/user_deafened.png', 'right' );
		}
		if ( $user->selfMute ) {
			$this->output .= HTML::img( 'mumble/user_selfmute.png', 'right' );
		}
		if ( $user->selfDeaf ) {
			$this->output .= HTML::img( 'mumble/user_selfdeaf.png', 'right' );
		}

		// Last : padding space img
		$this->output .= HTML::img( IMG_SPACE_16, 'right' );
	}

	/**
	* This code come from the work of mumbleviewer v0.91 ( GPL 2 ).
	* Website: http://sourceforge.net/projects/mumbleviewer/
	*
	* It's permit to create channels and users viewer deep.
	*/
	private function deep( $id, $last_id, $deep ) {

		if ( $id === $last_id ) {
			$this->deep_status_menu[ $deep ] = 0;
		} else {
			$this->deep_status_menu[ $deep ] = 1;
		}

		$count = 1;

		while( $count < $deep ) {

			if ( $this->deep_status_menu[ $count ] === 0 ) {

				$img = 'space';
			} else {
				$img = 'tree_line';
			}

			$this->output .= HTML::img( 'pma/'.$img.'.png', 'left' );

			++$count;
		}
	}

	/**
	* Search for all indirect links ids of a given list of direct ids.
	*
	* @param $direct_links - array of all direct link ids of a channel.
	*
	*/
	private function get_indirect_links( $direct_links ) {

		foreach( $direct_links as $cid ) {

			if ( ! isset( $this->chans_list[ $cid ] ) OR empty( $this->chans_list[ $cid ]->links ) ) {
				continue;
			}

			foreach ( $this->chans_list[ $cid ]->links as $id ) {

				if ( in_array( $id, $direct_links, TRUE ) OR in_array( $id, $this->indirect_links, TRUE ) ) {

					continue;

				} else {
					$this->indirect_links[] = $id;
					$this->get_indirect_links( array( $id ) );
				}
			}
		}
	}

	/**
	* Return all children channels id from a tree object.
	*
	* @return array
	*/
	private function get_all_childrens( $tree ) {

		foreach( $tree->children as $obj ) {

			if ( ! in_array( $obj->c->id, $this->childrens, TRUE ) ) {
				$this->childrens[] = $obj->c->id;
			}

			$this->get_all_childrens( $obj );
		}

		return $this->childrens;
	}

	/**
	* Return a sub-channel object with all it's components
	*
	* @param $id - the id of the sub channel we want.
	*
	* @return object or NULL
	*/
	private function get_sub_channel( $obj, $id ) {

		foreach( $obj->children as $chan ) {

			if ( $chan->c->id === $id ) {
				return $chan;

			} else {

				$sub = $this->get_sub_channel( $chan, $id );

				if ( is_object( $sub ) ) {
					return $sub;
				}
			}
		}
	}

	private function get_selected_user() {
		return $this->users_list[ $this->selected_session_id ];
	}

	private function get_selected_channel() {
		return $this->chans_list[ $this->selected_channel_id ];
	}
}

?>