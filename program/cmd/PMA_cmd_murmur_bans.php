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

class PMA_cmd_murmur_bans extends PMA_cmd
{
    private $prx;

    private $bansList;

    private $address; // Ban IP in decimal
    private $mask;

    public function process()
    {
        if (! $this->PMA->user->isMinimum(PMA_USER_SUPERUSER_RU)) {
            $this->messageError('illegal_operation');
            $this->throwException();
        }

        $this->getMurmurMeta();
        $this->prx = $this->getServerPrx($_SESSION['page_vserver']['id']);

        $this->bansList = $this->prx->getBans();
        /**
        * Reason sanity.
        * It's not really possible to add EOL with the mumble client.
        * So replace EOL with a space.
        * DISABLED FOR THE MOMENT.
        */
//         if (isset($this->PARAMS['reason'])) {
//             $this->PARAMS['reason'] = replaceEOL($this->PARAMS['reason']);
//         }

        if (isset($this->PARAMS['addBan'])) {
            $this->addBan();
        } elseif (isset($this->PARAMS['edit_ban_id'])) {
            $this->editBan($this->PARAMS['edit_ban_id']);
        } elseif (isset($this->PARAMS['delete_ban_id'])) {
            $this->deleteBan($this->PARAMS['delete_ban_id']);
        } elseif (isset($this->PARAMS['remove_ban_hash'])) {
            $this->removeHash($this->PARAMS['remove_ban_hash']);
        }
    }

    /**
    * SetBan helper
    * Memo : Mumble editor reorder bans with IPs.
    */
    private function setBans()
    {
        $this->prx->setBans($this->bansList);
    }

    /**
    * Common IP and bitmask sanity
    */
    private function ipMaskSanity()
    {
        $ip = $this->PARAMS['ip'];
        $this->mask = $this->PARAMS['mask'];

        // IP
        if (PMA_ipHelper::isIPv4($ip)) {
            $type = 'ipv4';
            $range = range(1, 32);
        } elseif (PMA_ipHelper::isIPv6($ip)) {
            $type = 'ipv6';
            $range = range(1, 128);
        } else {
            $this->messageError('invalid_IP_address');
            $this->throwException();
        }

        // Add last range mask on empty field
        if ($this->mask === '') {
            $this->mask = end($range);
        }

        $this->mask = (int)$this->mask;

        if (! in_array($this->mask, $range, true)) {
            $this->messageError('invalid_bitmask');
            $this->throwException();
        }

        if ($type === 'ipv4') {
            $this->address = PMA_ipHelper::stringToDecimalIPv4($ip);
            $this->mask = PMA_ipHelper::mask4To6($this->mask);
        } else {
            $this->address = PMA_ipHelper::stringToDecimalIPv6($ip);
        }
    }

    private function addBan()
    {
        $this->ipMaskSanity();

        $duration = 0;
        $time = time();

        // Setup duration
        if (
            ctype_digit($this->PARAMS['hour']) &&
            ctype_digit($this->PARAMS['day']) &&
            ctype_digit($this->PARAMS['month']) &&
            ctype_digit($this->PARAMS['year']) &&
            ! isset($this->PARAMS['permanent'])
        ) {
            $hours = (int)$this->PARAMS['hour'];
            $days = (int)$this->PARAMS['day'];
            $months = (int)$this->PARAMS['month'];
            $years = (int)$this->PARAMS['year'];
            $duration = mktime($hours, date('i', $time), date('s', $time), $months, $days, $years) - $time;
        }

        $add = new Murmur_Ban();
        $add->address = $this->address;
        $add->bits = $this->mask;
        $add->name = $this->PARAMS['name'];
        $add->hash = $this->PARAMS['hash'];
        $add->reason = $this->PARAMS['reason'];
        $add->start = $time;
        $add->duration = $duration;

        $this->bansList[] = $add;

        $this->setBans();

        if (isset($this->PARAMS['kickhim'])) {
            $this->prx->kickUser($_SESSION['page_vserver']['uSess']['id'], $this->PARAMS['reason']);
            unset($_SESSION['page_vserver']['uSess']);
        }
    }

    private function editBan($id)
    {
        $this->setRedirection('referer');

        $this->ipMaskSanity();

        // Invalid ban id
        if (! isset($this->bansList[$id])) {
            $this->messageError('invalid_ban_id');
            $this->throwException();
        }
        // Workaround : upgrading murmur 1.2.2 to 1.2.3 modify all bans start to "-1"
        if ($this->bansList[$id]->start === -1) {
            $this->bansList[$id]->start = time();
        }

        // Setup duration
        if (
            ctype_digit($this->PARAMS['hour']) &&
            ctype_digit($this->PARAMS['day']) &&
            ctype_digit($this->PARAMS['month']) &&
            ctype_digit($this->PARAMS['year']) &&
            ! isset($this->PARAMS['permanent'])
        ) {
            $start = $this->bansList[$id]->start;

            $hours = (int)$this->PARAMS['hour'];
            $days = (int)$this->PARAMS['day'];
            $months = (int)$this->PARAMS['month'];
            $years = (int)$this->PARAMS['year'];

            $duration = mktime($hours, date('i', $start ), date('s', $start ), $months, $days, $years) - $start;
        } else {
            $duration = 0;
        }
        /**
        * Memo: don't edit hash and start.
        */
        $this->bansList[$id]->address = $this->address;
        $this->bansList[$id]->bits = $this->mask;
        $this->bansList[$id]->name = $this->PARAMS['name'];
        $this->bansList[$id]->reason = $this->PARAMS['reason'];
        $this->bansList[$id]->duration = $duration;

        $this->setBans();
    }

    private function deleteBan($id)
    {
        if (! isset($this->PARAMS['confirmed'])) {
            $this->throwException();
        }
        if (! isset($this->bansList[$id])) {
            $this->messageError('invalid_ban_id');
            $this->throwException();
        }
        unset($this->bansList[$id]);
        $this->setBans();
    }

    private function removeHash($id)
    {
        if (! isset($this->bansList[$id])) {
            $this->messageError('invalid_ban_id');
            $this->throwException();
        }
        $this->bansList[$id]->hash = '';
        $this->setBans();
    }
}
