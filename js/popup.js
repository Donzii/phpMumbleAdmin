
 /**
 * phpMumbleAdmin (PMA), web php administration tool for murmur (mumble server daemon).
 * Copyright (C) 2010 - 2015  Dadon Davidocument. PMA@ipnoz.net
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

/*
* JsBackground CSS id.
*/
var JsBgId = 'jsBackground';
/*
* Global variable to store hightLighted TD tag elements.
*/
var removeHightLights = new Array();

function popup(id)
{
    getPopup(id);
    // Disable HTML href links.
    return false;
}

function getPopup(id)
{
    var popup = document.getElementById(id);
    _PopupParent = popup.parentNode;

    if (createBackground()) {
        _PopupParent.hidden = false;
        popup.className += ' js';
        centerElement(popup);
        // Set dragable element (h3 tag)
        if (dragable = popup.getElementsByTagName('h3')[0]) {
            dragable['onmousedown'] = grab;
            // Set drag element (popup)
            dragElement = popup;
        }
        // Check autofocus
        var list = popup.getElementsByTagName('*');
        for (var i = 0; i < list.length; ++i) {
            if(list[i].hasAttribute('autofocus')) {
                list[i].focus();
                break;
            }
        }
    }
    return popup;
}

function unpop()
{
    if (el = document.getElementById(JsBgId)) {
        removeElement(el);
        removeTableHighLight();
        _PopupParent.hidden = true;
        _PopupParent.getElementsByTagName('form')[0].reset();
        // Disable HTML href links, only if JSbackground has been createdocument.
        return false;
    }
}

// Add a smoked background
function createBackground()
{
    // Allow one instance of backgroundocument.
    if (! document.getElementById(JsBgId)) {
        el = document.getElementsByTagName('body')[0].appendChild(document.createElement('div'));
        el.id = JsBgId;
        // Feet jsBackground to the user browser scroll size
        el.style.height = document.documentElement.scrollHeight +'px';
        el.style.width = '100%';
        return true;
    }
    return false;
}

function centerElement(el)
{
    // MSIE doesn't treat position:fixed correctly, so this compensates for positioning the element.
    if (document.all && ! window.opera) {
        el.style.top = document.documentElement.scrollTop +'px';
    }
    el.style.top = '35%';
    el.style.left = (document.documentElement.scrollWidth - el.offsetWidth)/2 +'px';
}

// hightLight table td element
function highLightTableColumns(el, columnArray)
{
    var td = el.parentNode.parentNode.getElementsByTagName('td');
    var len = columnArray.length;
    for (var i = 0; i < len; ++i) {
        var foo = td[columnArray[i]];
        foo.style.background = 'red';
        removeHightLights[i] = foo;
    }
}

function removeTableHighLight()
{
    var len = removeHightLights.length;
    for (var i = 0; i < len; ++i) {
        removeHightLights[i].style.background = '';
    }
}

/*
* Custom popups :
*/
function popupResetSrv(el, server_id, server_name)
{
    var popup = getPopup('serverReset');
    // Save the original confirmText.
    if (typeof(confirmTextRst) === 'undefined') {
        confirmTextRst = popup.getElementsByTagName('p')[0].innerHTML;
    }
    // Replace hidden server ID.
    popup.getElementsByTagName('input')[1].value = server_id;
    // Replace server name.
    popup.getElementsByTagName('h3')[0].innerHTML = server_name;
    // Replace confirm text server ID.
    popup.getElementsByTagName('p')[0].innerHTML = confirmTextRst.replace('%d', server_id);
    // highLight
    var array = new Array('1', '2');
    highLightTableColumns(el, array);
    // Disable HTML href links.
    return false;
}

function popupDeleteSrv(el, server_id, server_name)
{
    var popup = getPopup('serverDelete');
    // Save the original confirmText.
    if (typeof(confirmTextDel) === 'undefined') {
        confirmTextDel = popup.getElementsByTagName('p')[0].innerHTML;
    }
    // Replace hidden server ID.
    popup.getElementsByTagName('input')[1].value = server_id;
    // Replace server name.
    popup.getElementsByTagName('h3')[0].innerHTML = server_name;
    // Replace confirm text server ID.
    popup.getElementsByTagName('p')[0].innerHTML = confirmTextDel.replace('%d', server_id);
    // highLight
    var array = new Array('1', '2');
    highLightTableColumns(el, array);
    // Disable HTML href links.
    return false;
}

function popupDeleteAdmin(el, admin_uid, login)
{
    var popup = getPopup('adminDelete');
    // Replace hidden ID.
    popup.getElementsByTagName('input')[1].value = admin_uid;
    // Replace server name.
    popup.getElementsByTagName('h3')[0].innerHTML = login;
    // highLight
    var array = new Array('1', '2');
    highLightTableColumns(el, array);
    // Disable HTML href links.
    return false;
}

function popupDeleteMumbleID(el, mumble_uid, login)
{
    var popup = getPopup('mumbleRegistrationDeleteID');
    // Replace hidden ID.
    popup.getElementsByTagName('input')[1].value = mumble_uid;
    // Replace server name.
    popup.getElementsByTagName('h3')[0].innerHTML = login;
    // highLight
    var array = new Array('1', '2');
    highLightTableColumns(el, array);
    // Disable HTML href links.
    return false;
}

function popupDeleteBan(el, ban_key)
{
    var popup = getPopup('bansDelete');
    // Replace hidden ban key.
    popup.getElementsByTagName('input')[1].value = ban_key;
    popup.getElementsByTagName('h3')[0].innerHTML = '#'+ban_key;
    // highLight
    var array = new Array('0');
    highLightTableColumns(el, array);
    // Disable HTML href links.
    return false;
}
