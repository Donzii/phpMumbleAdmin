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

/**
* Display messages to user.
* MEMO: keep controllers in this script, to catch very last messages.
*/

class PMA_messageObject
{
    public $title;
    public $text = '';
    public $type = 'error';
    public $closeButton = false;
}

pmaLoadLanguage('messages');
$widget->messagesBox = array();

/**
* Ice errors
*/
if (isset($PMA->messages['iceError'])) {
    $data = new PMA_messageObject();
    if ($PMA->user->isMinimum(PMA_USER_ROOTADMIN)) {
        $data->title = pmaGetText('ice_error');
        $data->text = pmaGetText($PMA->messages['iceError']);
    } else {
        $data->text = pmaGetText('ice_error_unauth');
    }
    $widget->messagesBox[] = $data;
}
/**
* Messages
*/
if (isset($PMA->messages['box'])) {
    foreach ($PMA->messages['box'] as $array) {
        $array['key'] = pmaGetText($array['key']);
        if (isset($array['sprintf'])) {
            $array['key'] = sprintf($array['key'], $array['sprintf']);
        }
        $data = new PMA_messageObject();
        $data->text = $array['key'];
        $data->type = $array['type'];
        $data->closeButton = ($array['type'] === 'success');
        $widget->messagesBox[] = $data;
    }
}

foreach ($widget->messagesBox as $m): ?>
        <div class="messageBox">
            <div class="inside <?php echo $m->type; ?>">
<?php if ($m->closeButton): ?>
                <a href="./" class="button" title="<?php echo $TEXT['close']; ?>"
                    onClick="removeElement(this.parentNode.parentNode); return false;">
                    <img src="<?php echo IMG_CANCEL_12; ?>" alt="" />
                </a>
<?php endif;
if (! is_null($m->title)): ?>
                <h3><?php echo $m->title; ?></h3>
<?php endif; ?>
                <p><?php echo $m->text; ?></p>
            </div>
        </div>
<?php endforeach;
