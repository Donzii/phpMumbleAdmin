
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

var dragElement = null;
var target = null;
var dragXoffset = 0;
var dragYoffset = 0;
var grab_on = false;

function grab(e)
{
    if (e === null) {
        e = window.event;
    }
    dragXoffset = e.pageX - dragElement.offsetLeft;
    dragYoffset = e.pageY - dragElement.offsetTop;

    target = e.target != null ? e.target : e.srcElement;
    target.style.cursor = 'move';
    dragElement.style.opacity = '0.5';
    grab_on = true;

    //document.onmousedown =  function() { return false; };
    document.onmousemove = drag;
    document.onmouseup = drop;
    return false;
}

function drag(e)
{
    if (e === null) {
        e = window.event;
    }
    if (grab_on) {
        dragElement.style.left = e.pageX - dragXoffset +'px';
        dragElement.style.top = e.pageY - dragYoffset +'px';
    }
    return false;
}

function drop(e)
{
    grab_on = false;
    target.style.cursor = '';
    dragElement.style.opacity = '';
    document.onmouseup = null;
    document.onmousemove = null;
}

