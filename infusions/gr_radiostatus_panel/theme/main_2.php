<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2010 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Radiostatus v2 for PHP-Fusion 7
| Author: Ralf Thieme
| Webseite: www.granade.eu
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined('IN_FUSION')) { die('Access Denied'); }
?>

<div align="center">
	<hr class="side-hr" />###rs_name###<hr class="side-hr" />
	<table align="center" border="0" cellpadding="2" cellspacing="2" width="100%">
		<tr>
			<td width="10%" align="left" valign="top" rowspan="4"><img src="###mod_pic###" border="0" height="100" alt="" /></td>
			<td width="10%" align="left"><strong>On Air:</strong></td>
			<td width="60%" align="left">###mod###</td>
			<td width="1%"rowspan="4"></td>
			<td width="19%" align="left" valign="top" rowspan="4"><strong>Player:</strong><br />###player###<br />###gb###</td>
		</tr>
		<tr>
			<td align="left"><strong>Zuh&ouml;rer: </strong></td>
			<td align="left">###listner###/###listner_max###</td>
		</tr>
		<tr>
			<td align="left"><strong>Bitrate: </strong></td>
			<td align="left">###bitrate### kb/s</td>
		</tr>
		<tr>
			<td align="left"><strong>Aktueller Titel:</strong></td>
			<td align="left"><marquee name="marquee" behavior="scroll" scrollamount="2" scrolldelay="80" width="100%" onmouseover="this.stop()" onmouseout="this.start()">###song###</marquee></td>
		</tr>
	</table>
</div>