<?php

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

if ( ! defined( 'PMA_STARTED' ) ) { die( 'ILLEGAL: You cannot call this script directly !' ); }

$JS->add_text( 'yes', $TEXT['yes'] );
$JS->add_text( 'no', $TEXT['no'] );
$JS->add_text( 'modify', $TEXT['modify'] );
$JS->add_text( 'submit', $TEXT['submit'] );
$JS->add_text( 'cancel', $TEXT['cancel'] );
$JS->add_text( 'add', $TEXT['add'] );
$JS->add_text( 'ok', $TEXT['ok'] );
$JS->add_text( 'pw_check_failed', $TEXT['password_check_failed'] );
$JS->add_text( 'invalid_ip', $TEXT['invalid_IP_address'] );
$JS->add_text( 'invalid_port', $TEXT['invalid_port'] );
$JS->add_text( 'invalid_number', $TEXT['invalid_numerical'] );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

<title><?php echo html_encode( $PMA->config->get( 'siteTitle' ) ); ?></title>

<meta name="description" content="<?php echo html_encode( $PMA->config->get( 'siteComment' ) ); ?>">
<meta name="generator" content="phpMumbleAdmin">

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv ="pragma" content="nocache">

<link href="images/mumble/mumble.png" rel="shortcut icon">

<link href="css/skel.css" rel="stylesheet" type="text/css">
<link href="css/skel.common.css" rel="stylesheet" type="text/css">
<link href="css/themes/<?php echo $PMA->cookie->get( 'skin' ); ?>" rel="stylesheet" type="text/css">

<script src="js/common.js" type="text/javascript"></script>
<script src="js/actionBox.js" type="text/javascript"></script>
<script src="js/drag.js" type="text/javascript"></script>
<script src="js/expand.js" type="text/javascript"></script>
<script src="js/page_<?php echo $_SESSION['page']; ?>.js" type="text/javascript"></script>

<!-- JS text object -->
<script type="text/javascript">
TEXT = new Object();
<?php echo $JS->get_text(); ?>
</script>

</head>

<body>
