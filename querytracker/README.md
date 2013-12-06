
gtmurmur - GameTracker Murmur Plugin

Copyright 2011 - Andrew Davis / GameTracker / adavis |at| gameservers |dot| com

http://www.gametracker.com/downloads/gtmurmurplugin.php



OVERVIEW  
==============

This program is designed to run alongside Murmur to create a public server
which allows users to "query" for the current state of the Murmur server.

gtmurmur does the following:

1. Runs a TCP server on a given port.
2. Accepts incoming query requests.
3. Communicates to a local Murmur server through Murmur's built-in ICE server.
4. Echos the state of the Murmur server out to query clients. The data format
will be a variant of Mumble's "Channel Viewer Protocol"
( http://mumble.sourceforge.net/Channel_Viewer_Protocol ).

gtmurmur aims to be compatible with Murmur versions 1.2.0 and onward.


Mumble server plugin from the GameTracker team which allows a mumble server to be scanned by GameTracker.
- Compatible with Windows (XP, Vista, 7, Server 2003, Server 2008)
- Compatible with Linux (RHEL/Centos 5)
- Current version: 1.2.0
- Works with Mumble 1.2.0 - 1.2.4
- Release date: 01-24-2013



LICENSING  
===============

All licensing info is included in license.txt.



CHANGES  
=============

1.2.0 - 1/23/2013
- Added support for Mumble 1.2.4.
- Added utility 'test_ice'.
- Added utility 'test_gtmurmur'.
- Added 'QUICK SETUP' guide in this document.

1.1.1 - 11/2/2011
- Added licensing info.

1.1.0 - 10/5/2011
- Cleaned up make scripts on all platforms.
- Added GT branding under Windows.

1.0.0 - 9/23/2011:
- Initial release.



SYSTEM REQUIREMENTS 
=======================

* Windows: Windows XP, Vista, 7, 8, Server 2003, Server 2008
* Linux: CentOS 5.0 (compatible, or newer).

Static and dynamic builds of each program is included for each OS. Static
builds have a '-static' suffix. These builds do not benefit from OS security
updates, but are compatible with more flavors of your OS.



QUICK SETUP  
=================

These steps should get you some quick satisfaction. It is important to read
the rest of this document to ensure best security practices.

1. Install Murmur.

2. Open "murmur.ini".

3. Remove the line: "ice="tcp -h 127.0.0.1 -p 6502""

4. Remove the line: "icesecretwrite="

5. Locate the line: "[Ice]"

6. Anywhere ABOVE this line, add these following lines:

icesecretwrite=my1secret
ice="tcp -h 127.0.0.1 -p 6502"
gtmurmur_icesecret=my1secret
gtmurmur_ip=1.2.3.4
gtmurmur_port=27800
gtmurmur_mumble_url=mumble://server.com/?version=1.2.0
gtmurmur_use_ice_sdk=FALSE

7. Change "1.2.3.4" to the IP address of your Murmur server.

8. Restart Murmur.

9. Jot down the absolute path to your Murmur server. E.g:
"C:\Program Files (x86)\Murmur\" or "/home/someuser/murmur".

10. Open a command prompt or shell.

11. "cd" to the directory where GTMurmur is located.

12. Execute "gtmurmur-static PATH_TO_MURMUR_SERVER/murmur.ini". Replacing
"PATH_TO_MURMUR_SERVER" with the path that you have just jotted down.

13. Open port 27800 on your system firewall.

14. From the same computer, open a second command prompt or shell and "cd" to
the directory where GTMurmur is located.

15. Execute "test_ice 127.0.0.1 6502". If you get an error, check that murmur
is running, and that you updated murmur.ini correctly.

16. Execute "test_gtmurmur 1.2.3.4 27800". Replace 1.2.3.4 with the IP address
of your Murmur server. If you get an error, check that gtmurmur is running,
you updated murmur.ini correctly, and you have opened your firewall.



USAGE  
===========

gtmurmur [-conf FILE]
gtmurmur-static [-conf FILE]

The program arguments are described as follows:

1. "[-conf FILE]"
This specifies where the configuration file is for the Murmur server (Typically
"murmur.ini". gtmurmur shares its configuration with the Murmur server
configuration. See the "CONFIGURATION" section below on lines to add to the
Murmur server configuration file for gtmurmur.
* The default value for "-conf" is "murmur.ini".

gtmurmur is a server which runs forever.

All output of gtmurmur is printed to stdout. This includes client connects
and disconnects, socket errors, and program debug information.

---------------

test_ice <IP> <PORT>
test_ice-static <IP> <PORT>

test_ice checks if your ICE server is running, then quits. Pass in the IP
address and port. A textual error is printed on the screen within 5 seconds.


---------------

test_gtmurmur <IP> <PORT>
test_gtmurmur-static <IP> <PORT>

test_gtmurmur checks if your gtmurmur server is running, then quits. Pass in
the IP address and port. A textual error is printed on the screen within 5
seconds.



CONFIGURATION
===================

The following lines should be added to the gtmurmur configuration file. Please
note that gtmurmur is designed to share the Murmur configuration file
(Typically "murmur.ini"). Also note that some of the lines may already be
present in "murmur.ini", therefore should not be set twice.

Lines:


ice="tcp -h 127.0.0.1 -p 6502"
gtmurmur_icesecret=my1secret
gtmurmur_ip=1.2.3.4
gtmurmur_port=27800
gtmurmur_mumble_url=mumble://server.com/?version=1.2.0
gtmurmur_use_ice_sdk=FALSE


The lines listed above are described as follows:

1. "ice="tcp -h 127.0.0.1 -p 6502""
This enables the ICE server inside Murmur. gtmurmur uses this same key to
identify the Murmur ICE server in which it should connect.
* gtmurmur will quit if this field is not specified.

2. "gtmurmur_icesecret=my1secret"
This defines the ICE secret that gtmurmur sends to the Murmur ICE server. See
the section titled "SECURITY" and Murmur documentation for more information.
* This field is optional.
* The default value is "" (blank).

3. "gtmurmur_port=27800"
This defines the listening port in which gtmurmur will accept incoming
query clients. This is a TCP port.
* This field is optional.
* The default value is 27800.

4. "gtmurmur_mumble_url=mumble://server.com/?version=1.2.0"
This defines the mumble link to connect to this server.
This field gets echoed in the scan result as "x_connecturl".
* This field is optional.
* The default value is "" (blank).

5. "gtmurmur_use_ice_sdk=FALSE"
DEVELOPER ONLY.
gtmurmur is compiled with two implementations of querying the Murmur ICE
server. These are referred to as the "ICE" and "no-ICE" implementations. The
"no-ICE" implementation is a reverse engineer of the ICE protocol, which
supports multiple versions of Murmur (1.2.0+). The "ICE" implementation is
built against Murmur 1.2.3 and is only guaranteed to be compatible with
Murmur 1.2.3.
* This field is optional.
* Possible values: TRUE/FALSE.
* The default value is "FALSE".


SECURITY  
==============

Enabling Murmur's ICE server raises some security concerns. Since ICE is
designed to use local sockets, anyone with shell access can connect to
the ICE server. Murmur's ICE server offers full remote administration of
the Murmur server with no authentication. In Murmur 1.2.2 an "icesecret"
plaintext password was added to help secure the ICE connection.

Read the Murmur and ICE documentation for more information.



FIREWALL
==============

To help paint a picture on network usage, running gtmurmur alongside of Murmur
will utilize the following network ports:

TCP		1.2.3.4		64738		(Used by Murmur for voice/data)
UDP		1.2.3.4		64738		(Used by Murmur for voice/data)
TCP		127.0.0.1	6502		(Used by Murmur for local ICE server)
TCP		1.2.3.4		27800		(Used by gtmurmur for query server)

* 1.2.3.4 is replaced by the public IP address for your Murmur & gtmurmur
server.



GTMURMUR PROTOCOL  
=======================

This section describes the network protocol understood by gtmurmur.

---------------

78 6D 6C			"xml"

This sequence will cause gtmurmur to return the "Channel Viewer Protocol" in
XML format.

If the query could not be completed due to an error, an XML document in the
following format will be returned:

<?xml version="1.0" encoding="UTF-8" ?>
<server>
	<x_gtmurmur_error>Error description.</x_gtmurmur_error>
</server>

---------------

6A 73 6F 6E			"json"

This sequence will cause gtmurmur to return the "Channel Viewer Protocol" in
JSON format.

If the query could not be completed due to an error, a JSON document in the
following format will be returned:

{
	"x_gtmurmur_error": "Error description."
}

---------------
