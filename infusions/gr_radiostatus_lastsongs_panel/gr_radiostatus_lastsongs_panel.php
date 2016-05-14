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

if (!defined('RADIOSTATUS')) {
	define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/');
}

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

if (isset($current_side)) {
	$panel = $current_side;
} else {
	if (isset($p_data) AND array_key_exists('panel_side', $p_data)) {
		$panel = $p_data['panel_side'];
	} else {
		$panel = 1;
	}
}

if ($panel == 1 || $panel == 4) {
	openside($locale['grrs_03']);
} else {
	opentable($locale['grrs_03']);
}

if (!isset($_GET['rs_id']) OR !isnum($_GET['rs_id'])) { $_GET['rs_id'] = 0; }
$result_menue = dbquery("SELECT rs_id, rs_name FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_status='1' ORDER BY rs_order");
if (dbrows($result_menue)) {
	if (dbrows($result_menue) == 1) {
		$data_menue = dbarray($result_menue);
		$_GET['rs_id'] = $data_menue['rs_id'];
	} else {
		echo '<form action="'.FUSION_SELF.(FUSION_QUERY ? '?'.FUSION_QUERY : '').'" method="get">
		<div align="center">
		<select name="rs_id" style="width:200px;" onchange="this.form.submit();" class="textbox">'."\n";
		while($data_menue = dbarray($result_menue)) {
			if ($_GET['rs_id'] == 0) { $_GET['rs_id'] = $data_menue['rs_id']; }
			echo '<option value="'.$data_menue['rs_id'].'"'.($_GET['rs_id'] == $data_menue['rs_id'] ? ' selected="selected"' : '').'>'.$data_menue['rs_name'].'</option>'."\n";
		}
		echo '</select></div>
		</form>';
	}
	@mysql_free_result($result_menue);
}

if ($_GET['rs_id'] != 0) {
	add_to_head('<script type="text/javascript">
	function updateRSLS(){
		$("#radiostatus_lastsong").load("'.INFUSIONS.'gr_radiostatus_lastsongs_panel/gr_radiostatus_lastsongs_inc.php?rs_id='.$_GET['rs_id'].'");
		setTimeout("updateRSLS()",30000);
	}
	$(document).ready(function(){
		updateRSLS();
	});
	</script>');
	echo '<div id="radiostatus_lastsong" align="center"></div>';
}

/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
if ($panel == 1 || $panel == 4) {
	closeside();
} else {
	closetable();
}
?>