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

class PMA_MurmurViewerAdmin extends PMA_MurmurViewer
{
    /**
    * Return all children channels id from the tree object.
    *
    * @param $treeObj - MurmurTree object.
    * @param $id - channel ID to search for.
    * @return array - all childrens ID from the channel $id..
    */
    public function getAllChildrensID(Murmur_tree $treeObj, $id)
    {
        // Init
        $this->getAllChildrensID = array();
        // Get the subChannel tree object of the selected channel $id:
        $subTree = $this->getSubTreeChannel($treeObj, $id);
        // Construct the array of all childrens ID:
        $this->getChildrensID($subTree);
        return $this->getAllChildrensID;
    }

    protected function getSubTreeChannel(Murmur_tree $treeObj, $id)
    {
        foreach ($treeObj->children as $subTreeObj) {
            if ($subTreeObj->c->id === $id) {
                return $subTreeObj;
            } else {
                $sub = $this->getSubTreeChannel($subTreeObj, $id);
                if (is_object($sub)) {
                    return $sub;
                }
            }
        }
    }

    protected function getChildrensID(Murmur_tree $treeObj)
    {
        foreach ($treeObj->children as $subTreeObj) {
            if (! in_array($subTreeObj->c->id, $this->getAllChildrensID, true)) {
                $this->getAllChildrensID[] = $subTreeObj->c->id;
            }
            $this->getChildrensID($subTreeObj);
        }
    }
}
