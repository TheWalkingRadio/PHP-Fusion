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
require_once dirname(__FILE__).'/../../maincore.php';

header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

if (!defined('RADIOSTATUS_SELF')) {
	define('RADIOSTATUS_SELF', dirname($_SERVER['PHP_SELF']).'/');
}

if (!defined('RADIOSTATUS')) {
	define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/');
}

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

if (!isset($_GET['rs_id']) OR !isnum($_GET['rs_id'])) { $_GET['rs_id'] = 0; }
if ($_GET['rs_id'] != 0) {
	$result = dbquery("SELECT rs_id, rs_cache FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_status='1' AND rs_id='".$_GET['rs_id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		@mysql_free_result($result);
		if (file_exists(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php')) {
			require_once RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
			if ($cache['status']) {
				$history = $cache['history'];
				if (is_array($history)) {
					echo '<table cellpadding="2" cellspacing="2" width="100%" class="tbl-border" align="center">
					<tr>
						<td class="tbl2" width="20%">'.$locale['grrs_04'].'</td>
						<td class="tbl2" width="80%">'.$locale['grrs_05'].'</td>
						'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center">'.$locale['grrs_22'].'</td>' : '').'
					</tr>
					<tr>
						<td class="tbl1">'.$locale['grrs_06'].'</td>
						<td class="tbl1">'.$cache['song'].'</td>
						'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center"><a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($cache['song'])).'" target="_blank"><img src="'.RADIOSTATUS_SELF.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a></td>' : '').'
					</tr>';
					for ($i=0;$i < sizeof($history);$i++) {
						echo '<tr>
							<td class="tbl1">'.showdate("%H:%M:%S", $history[$i]['playedat']).'</td>
							<td class="tbl1">'.$history[$i]['title'].'</td>
							'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center"><a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($history[$i]['title'])).'" target="_blank"><img src="'.RADIOSTATUS_SELF.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a></td>' : '').'
						</tr>';
					}
					echo '</table><br />';
				}
			} else {
				echo '<div align="center"><img src="'.RADIOSTATUS.'images/offline.gif" border="0" alt="Offline" /></div>';
			}
		} else {
			echo '<div align="center">'.$locale['grrsi_24'].'</div>';
		}
	}
}

?>