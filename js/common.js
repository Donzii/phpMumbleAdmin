
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

// Focus the end of an element (input or textarea).
function focusEnd(el)
{
    el.focus();
    el.value = '';
    el.value = el.defaultValue;
}

// Read a cookie
function readCookie(name)
{
    var start = document.cookie.indexOf(name + '=' );
    var end;

    if (start >= 0) {
        start += name.length + 1;
        end = document.cookie.indexOf(';', start );
        if (end < 0) {
            end = document.cookie.length;
        }
        return unescape(document.cookie.substring(start, end));
    }
}

/**
* Delete a HTML element (and all childrens).
*/
function removeElement(el)
{
    el.parentNode.removeChild(el);
}

function highLightElement(el)
{
    el.style.outline = '10px solid orange';
    setTimeout(
        function() {el.style.outline = '';}, 100
    );
}

// Uncheck a checkbox with it's name
function uncheck(name)
{
    document.getElementsByName(name)[0].checked = false;
}

// Reset a select field by it's id key
function unselect(id)
{
    document.getElementById(id).options[0].selected = true;
}

// Select the default value of a select field.
function selectDefault(id)
{
    var options = document.getElementById(id).options;
    var len = options.length;

    for (i = 0; i < len; ++i) {
        if (options[i].defaultSelected) {
            options[i].selected = true;
            break;
        }
    }
}

function checkAllBox(id)
{
    var list = document.getElementById(id ).getElementsByTagName('input');
    var len = list.length;

    for (i = 0; i < len; ++i) {
        list[i].checked = true;
    }
}

function uncheckAllBox(id)
{
    var list = document.getElementById(id ).getElementsByTagName('input');
    var len = list.length;

    for (i = 0; i < len; ++i) {
        list[i].checked = false;
    }
}

function invertAllChkBox(id)
{
    var list = document.getElementById(id ).getElementsByTagName('input' );
    var len = list.length;

    for (i = 0; i < len; ++i) {
        list[i].checked = ! list[i].checked;
    }
}

