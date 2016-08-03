
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

window.onload = expandOnload;

function expandOnload(e)
{
    // Expand menu
    var expandElement = document.getElementsByClassName('expand')[0];
    if (typeof(expandElement) !== 'undefined') {
        expand(e, expandElement);
    }
}

/*
* This var is a flag to check if we are toggleing the expand button,
* otherwise, user can close expand on an outside click.
*/
var isTogglelingExpand = false;

function expand(e, buttonEl)
{
    var menuList = buttonEl.getElementsByTagName('ul')[0];
    buttonEl.style.cursor = 'pointer';
    menuList.style.display = 'none';
    menuList.style.cursor = 'default';

    buttonEl.addEventListener('click', function() { expandToggle(menuList); }, false);

    document.addEventListener('mouseup', removeExpandToggleFlag, false);
    document.addEventListener('click', function() { unexpand(menuList); }, false);

    return false;
}

function expandToggle(el)
{
    isTogglelingExpand = true;

    if (el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

function unexpand(el)
{
    // Unexpand if we are not toggleling.
    if (! isTogglelingExpand) {
        el.style.display = 'none';
    }
}

function removeExpandToggleFlag()
{
    isTogglelingExpand = false;
}
