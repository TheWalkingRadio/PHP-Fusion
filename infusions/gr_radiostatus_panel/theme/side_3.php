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
	On Air: ###mod###<br />
	<img src="###mod_pic###" border="0" height="80" alt="" /><br />
	###gb###<br />
	<br />
	<strong><u>Aktueller Titel:</u></strong><br />
	<br />
	<marquee behavior="scroll" scrollamount="2" scrolldelay="80" width="100%" onmouseover="this.stop()" onmouseout="this.start()">###song###</marquee><br />
	<br />
	<strong><u>Player:</u></strong><br />
	<br />
	###player###<br />
</div>