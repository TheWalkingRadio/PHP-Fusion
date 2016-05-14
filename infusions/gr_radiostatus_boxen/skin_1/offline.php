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

<div style="margin:0; padding:0; width:150px; height:200px; background-image:url(###RADIOSTATUS_SELF###skin_1/hg.png); background-repeat: no-repeat;">
	<div style="margin:0; padding:0; height:70px;" align="center">
		<a href="###url###" title="Webseite aufrufen" target="_blank" style="margin:0; padding:0; display:block; height:100%; width:100%; text-decoration: none;"><br />###rs_name###</a>
	</div>
	<div style="margin:0; padding:0; height:130px; text-align:center;">
		Zur Zeit<br />
		leider kein<br />
		Sendebetrieb
	</div>
</div>