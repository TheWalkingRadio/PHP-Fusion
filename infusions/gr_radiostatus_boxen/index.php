<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2010 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Radiostatus v2.2 for PHP-Fusion 7
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
require_once '../../maincore.php';
require_once THEMES.'templates/header.php';

if (!defined('RADIOSTATUS')) {
	define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/');
}

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

function rs_check_skins() {
	$filter = explode('|', '.|..');
	$res = array();
	$folder = INFUSIONS.'gr_radiostatus_boxen/';
	$temp = opendir($folder);
	while ($file = readdir($temp)) {
		if (!in_array($file, $filter) AND is_dir($folder.$file) AND file_exists($folder.$file.'/index.php') AND file_exists($folder.$file.'/offline.php')) { $res[] = $file; }
	}
	closedir($temp);
	return $res;
}

opentable($locale['grbox_01']);
$skin_array = rs_check_skins();
$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_status='1' AND rs_status_boxen='1' ORDER BY rs_order");
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		echo $data['rs_name'].'<hr style="width:150px" /><br />
		<table cellpadding="1" cellspacing="1" width="100%" align="center"><tr>';
		$counter=0;
		foreach ($skin_array as $value) {
			if ($counter != 0 AND ($counter % 3 == 0)) { echo '</tr><tr>'; }
			echo '<td align="center">
				<iframe src="'.$settings['siteurl'].'infusions/gr_radiostatus_boxen/box.php?id='.$data['rs_id'].'&amp;style='.str_replace('skin_', '', $value).'" scrolling="no" frameborder="0" style="width:150px; height:200px;"></iframe><br />
				<textarea readonly="readonly" wrap="off" class="textbox" style="width:150px; height:40px; padding:5px 0 0; margin-top:3px;"><iframe src="'.$settings['siteurl'].'infusions/gr_radiostatus_boxen/box.php?id='.$data['rs_id'].'&amp;amp;style='.str_replace('skin_', '', $value).'" scrolling="no" frameborder="0" style="width:150px; height:200px;"></iframe></textarea>
			</td>';
			$counter++;
		}
		echo '</tr></table>';
		echo '<br />';
	}
} else {
	echo '<div align="center">'.$locale['grbox_02'].'</div>';
}

/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
closetable();

require_once THEMES.'templates/footer.php';
?>