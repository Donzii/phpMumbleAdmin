
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

function check_ip(ip)
{
    if (check_ipv4(ip)) {
        return true;
    } else if (check_ipv6(ip)) {
        return true;
    }
    return false;
}

function check_ipv4(ip)
{
    regexp_ipv4 = new RegExp(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ );
    return regexp_ipv4.test(ip );
}

function check_ipv6(ip)
{
    regexp_ipv6 = new RegExp(/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/
    );
    return regexp_ipv6.test(ip );
}

function check_port(port)
{
    if (! check_digital(port)) {
        return false;
    }
    if (port <= 65535) {
        return true;
    }
    return false;
}

// Return true if >= 0, max 255 characters
function check_digital(str)
{
    regexp_digital =  new RegExp(/^[0-9]{1,255}$/);
    return regexp_digital.test(str);
}

function toggleInfoPanel()
{
    var infoPanel = document.getElementById('PMA_infoPanel');
    var cookieName = 'phpMumbleAdmin_conf';
    var confCookie = readCookie(cookieName);
    var expdate = new Date();

    if (infoPanel === null) {
        return;
    }
    // milliseconde require (180 days).
    expdate.setTime(expdate.getTime() + (180*24*60*60*1000));
    // Toggle cookie 'infoPanel' parameter
    if (confCookie.search("(\"infoPanel\";b:1;)" ) != -1) {
        var regex = new RegExp("(\"infoPanel\";b:1;)", "g");
        var replaceBool = "\"infoPanel\";b:0;";
    } else {
        var regex = new RegExp("(\"infoPanel\";b:0;)", "g");
        var replaceBool = "\"infoPanel\";b:1;";
    }
    // toggle the panel
    if (infoPanel.style.display == 'none') {
        infoPanel.style.display = 'block';
    } else {
        infoPanel.style.display = 'none';
    }
    // toggle img
    var img = document.getElementById('js_infopanel');
    if (img !== null) {
        if (infoPanel.style.display == 'none') {
            img.src = 'images/tango/add2_16.png';
        } else {
            img.src = 'images/tango/delete2_16.png';
        }
    }
    // Write the cookie
    var new_confCookie = confCookie.replace(regex, replaceBool);
    document.cookie = cookieName +'='+ escape(new_confCookie) +'; expires='+ expdate.toGMTString() +'; path=/;';
    // Do not follow the http link.
    return false;
}

// Toggle all select field of the ban duration.
function banDurationHlper(el)
{
    if (el.checked) {
        unselect('hour');
        unselect('day');
        unselect('month');
        unselect('year');
    } else {
        selectDefault('hour');
        selectDefault('day');
        selectDefault('month');
        selectDefault('year');
    }
}

function AdminFullAccessToggle(el)
{
    var form_id = 'adminsServersAccess';
    var scroll_id = 'serversScroll';

    if (el.checked) {
        uncheckAllBox(scroll_id);
    } else {
        if (! el.defaultChecked) {
            document.getElementById(form_id).reset();
        }
    }
}
