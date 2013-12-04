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


PhpMumbleAdmin ( PMA ) is written for murmur 1.2.0 and higher.

// PMA is in beta
/////////////////////////

Make a backup of your database before use PMA.
It's quite simply to save a Mysql or Sqlite database without restarting your daemon.
PMA will never write directly into the database anyway, it's communicate over ICE.

// REQUIREMENTS
/////////////////////////////

A web server ( apache, lighthttpd etc... ).

PHP 5.0 and superior.

   php.ini :
      ...session.auto_start disabled
      ...magic_quotes_runtime disabled
      ...safe_mode disabled ( recommanded, this feature is obsolete since PHP 5.3 ).

icePHP module.
    ...More info to setup zeroc ice here: http://mumble.sourceforge.net/Ice

Locale files and folders permissions
///////////////////////////////////////////////////

phpMumbleAdmin need write access to theses folders.

avatars/
cache/
config/
logs/
sessions/

All others files and folders can be set to read.

// PHP SAFE MODE
//////////////////////////////

Note: This PHP feature is deprecated since PHP 5.3 and it removed with PHP 5.4.

If the safe_mode is activated on your system:

Slice profiles:
   ...If you have configured slice profiles for ice, PMA will need to parse the file located with "ice.profiles" parameter to get the list of all slice profiles.
   ...To be able to read this file, you have to set in php.ini:
                safe_mode_include_dir = "/path/to/ice_profiles/"
   ...Please report to the php documentation, there is many way to allow a file to be readable with safe_mode.
   ...This is not mandatory, if you dont allow PMA to read this file, PMA will use the default ice slice.

vservers logs:
   ...There is a bug with murmur where it's return a wrong log timestamp if your OS time is not UTC+00
   ...PMA include a workaround to display the real log time ( it's works only for linux ).
   ...PMA need to execute the "date" system command.
   ...in php.ini:
                safe_mode_exec_dir = /bin ( where the date command is located )
   ...This is not mandatory, if you dont allow the webserver to execute this command, the log time will just be wrong.

// INVALID SLICE DEFINITION FILE
////////////////////////////////////////////////////

If you get this error with PMA, please read docs/Invalid_slice_definition_file.txt

// Internet explorer
///////////////////////////

Internet explorer is not supported by PMA for the reason that it doesn't work on my computer anymore ^^ .
I usually test PMA with the lastest version of Firefox, Chrome and Opera. Sometime with epiphany too.

// SuperAdmin
/////////////////////

SuperAdmin is the master account for PMA.
If you want to login as SuperAdmin, let the server field empty.

Note: SuperAdmin can login in at anytime, even on ICE error.

// Admins
///////////////

There is two level of admins for PMA:

RootAdmins:
This is a powerfull admin class. RootAdmins have *SAME* rights as the SuperAdmin have except the ability to change SuperAdmin login / password.
RootAdmins can't manage RootAdmin accounts, only SuperAdmin can, but they can manage Admins.
* GIVE ROOTADMIN RIGHTS WITH CAUTION *

Admins:
Simple admin class comes without any privilege, but SuperAdmins have the ability to give them access to different virtual server for different ICE profile.
An admin can manage multiple virtual server, on multiple ICE profiles.
When you are editing an admin access, change the ICE profile for display a table of virtuals servers.
If a "full access" is given for an admin to a particular ICE profile, this admin will have the ability to ADD and REMOVE virtuals servers.

Remeber that admins have few more rights than a SuperUser.

Like SuperAdmin, RootAdmins and Admins have to let the server field empty for login.
Admins datas are stored in config/admins.php

// SuperUsers, Registered Users
///////////////////////////////////////////////

Each SuperUser ( SU ) and registered users ( RU ) can have web access with PMA.

Their authentication is based on the murmur mechanism, so they need to know the virtual server ID and must have set a password.

By default, SuperUsers and registered users do not have password and can't login.
Also by default, SuperUsers and registered users web access is disabled for PMA,
SuperAdmin can activate this in configuration->settings.
At last, SuperAdmin or Admins must activate the web access for each virtual server in the server table.

// SuperUsers_ru
/////////////////////////

This is a sub class specific to PMA which give to registered users SuperUser rights.

This class is useful if you need multiple admin access for a virtual server without create PMA admin accounts.

The registered user need an ACL in the Root channel with:
   ..."Write" allowed.
   ..."Apply to this channel" on.
   ..."Apply to subs channel" on ---> NEW RULE for PMA 0.4.3

SuperUsers_ru have access to everything a SuperUser have, exept the ability to modify the SuperUser account and this particular ACL rule.

This class is turned off by default, see configuration->settings to enable it.

Also to be active, you must authorize SuperUsers to login in configuration->settings.

// ICE profiles
////////////////////

PMA permit to manage multiple murmur daemon with ICE profiles.
Profiles are stored in config/profiles.php. To edit them, use configuration->ICE.

When PMA have two or more profiles, it's display a tab bar to switch on the different config.
SU or RU need to select the right profile before login in or they will have an authentication error. Indeed, PMA use the murmur authentication mecanism.
When SU or RU are authenticated, they can't switch between ICE profiles anymore.

SuperAdmin, RootAdmins and Admins can login in with all ICE profiles, just let the server field empty.

SuperAdmin can set a default ICE profile that will be use if no profile have been selected.

By default, new profiles are not public. SuperAdmin need to activate the access in configuration->ICE.

// Auto-bans
//////////////////

Auto-bans are similar to the murmur autoban system.

If attempts limits is reached during the time frame, the IP address of the PMA user will be banned for xxx secondes.
All banned IP are stored in cache/bans.php file.

Auto-bans are global to authentication and password generation, for successfull and unsuccessfull attempts.

To disactivate this function, set auto-bans attemps limit to 0.


// Generate random password
///////////////////////////////////////////

There is a system to generate random password for registered user.( SuperUser can't use it for security reason ).
This system permit to regenerate losted password too.
User need a valid account in the murmur database ( registered with the mumble client, with PMA or any other software ) and a valid email.
PMA will try to send a confirmation email to user address.

By following the link in the email, a new random password will be generated for the user. This will update old password, or create one if none exists.

You can activate this system ( disabled by default ) with the SuperAdmin account in configuration->users tab.
Remember to set a valid smtp host in configuration->settings.

The cache/ folder need to be writable by your http daemon or PMA will not be able to generate password for your registered users.

The explicit error message option permit to display why the request failed to your users.
The messages are : invalid server ID, invalid user name, invalid email and superuser denied.
It can be problematic because it's give to users with bad intentions some precious informations.
Use this with caution. Remember that PMA will always logs all requests with explicit informations.

// Slice profiles ( php-ICE 3.2 & 3.3 )
////////////////////////////////////////////////////

First, there is a good tutorial in english to understand and use slice profiles:

http://mumble.sourceforge.net/Ice#Using_different_ice.slice_on_same_host/

Slice profiles are useful to load multiple Mumur.ice ( in our case ).
It's permit to not reload the http daemon everytime you want to switch between murmur version.

PMA parse the slice profiles file located with "ice.profiles" option.
You just have to select the right profile for your murmur daemon in configuration->ICE.

// php-ICE 3.4
/////////////////////

php-ICE 3.4 is a bit different than 3.2 and 3.3.
php-ICE module do not need the path for Murmur.ice anymore.
In otherside, PMA need a php translated Murmur.ice ( with the slice2php compiler ).

This permit to load dynamically slices definitions with PMA.
This makes slice profiles obsolete.

PMA comes with translated slice file for each major stable release of murmur. You can find them in slice_php/ folder.
You can browse all files included into this folder in configuration->ICE tab if you have php-ICE 3.4.
Feel free to add your own slice_php file.
Remember that a valid slice_php for your murmur daemon is required by PMA, or you will get some random php error.

At last, PMA needs now to know the path to ice-3.4/php/ folder to be able to load "Ice.php".
ie: Add to php.ini :
include_path=/path/to/ice34/php/
include_path=C:\ice34\php\
etc...

// External viewer
/////////////////////////

All your visitors can have access to an "external" viewer if you enable it ( See configuration->settings->external viewer ).
They don't need to be authenticated to see it.

The address to reach the viewer is like this:

http://your.server.address/path/to/PMA/?ext_viewer&profile=1&server=*

The "profile" variable require a valid Ice profile id.

The "server" variable can be one or multiple servers id.
For multiple vservers, the seprator is "-".
The wildcard * can be set if you want to display all ( booted ) vservers.

example:
   &server=1
   &server=5
   &server=1-2-3-7-9
   &server=*

PMA will display a channel viewer for all running vservers.


// Please notice:
////////////////////////

userperchannel parameter come with murmur 1.2.1
icesecret come with murmur 1.2.1
Uptime come with murmur 1.2.2
icesecretread and icesecretwrite come with murmur 1.2.3
PMA can't unlink channel before murmur 1.2.3
PMA can't display users avatars before murmur 1.2.3
User last activity come with murmur 1.2.3
rememberchannel parameter come with murmur 1.2.3
user udp and tcp ping come with murmur 1.2.4

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

If you feel lonely or for some comments, feedback, bug report, feature request:

	mailto: PMA@ipnoz.net

I'm also idling in the IRC of the mumble dev ( nick name: ipnoz ) :

	irc://irc.freenode.org/mumble

