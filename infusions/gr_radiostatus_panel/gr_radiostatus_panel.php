<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (c) 2002 - 2011 Nick Jones
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
		$panel = 5;
	}
}

if ($panel == 1 || $panel == 4) {
	openside($locale['grrsp_01']);
} elseif ($panel != 5) {
	opentable($locale['grrsp_01']);
}

if(function_exists('fsockopen')) {
	add_to_head('<script type="text/javascript" src="'.RADIOSTATUS.'gr_radiostatus_inc.php?js='.$panel.'"></script>');
	add_handler('rs_start');
	echo '<!--radiostatus_start--><div id="radiostatus'.$panel.'" align="center"></div><!--radiostatus_ende-->';
	if ($panel != 5) {
		echo '<div id="radiostatus_gb'.$panel.'" style="display:none;" align="center">
		<form id="grussbox'.$panel.'" method="post" action="" onsubmit="return check_gb'.$panel.'();">
		<input type="hidden" name="gb'.$panel.'" value="" />';
		if ($panel == 1 OR $panel ==4) {
			echo '<table cellspacing="0" cellpadding="0" class="tbl-border" width="100%" align="center">
			<tr>
				<td class="tbl2" align="center"><strong>'.$locale['grrsg_01'].' <span id="rs_name'.$panel.'"></span></strong></td>
			</tr>
			<tr>
				<td class="tbl2" align="center">'.$locale['grrsg_02'].'<span style="color: rgb(255, 0, 0);">*</span></td>
			</tr>
			<tr>
				<td class="tbl2" align="center"><input type="text" name="name'.$panel.'"'.(iMEMBER ? ' value="'.$userdata['user_name'].'"' : '').' class="textbox" style="width:90%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="center">'.$locale['grrsg_03'].'<span style="color: rgb(255, 0, 0);">*</span></td>
			</tr>
			<tr>
				<td class="tbl2" align="center"><input type="text" name="ort'.$panel.'"'.(iMEMBER && array_key_exists('user_location', $userdata) ? ' value="'.$userdata['user_location'].'"' : '').' class="textbox" style="width:90%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="center">'.$locale['grrsg_04'].'</td>
			</tr>
			<tr>
				<td class="tbl2" align="center"><input type="text" name="interpret'.$panel.'" class="textbox" style="width:90%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="center">'.$locale['grrsg_05'].'</td>
			</tr>
			<tr>
				<td class="tbl2" align="center"><input type="text" name="titel'.$panel.'" class="textbox" style="width:90%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="center">'.$locale['grrsg_06'].'</td>
			</tr>
			<tr>
				<td class="tbl2" align="center"><textarea name="gruss'.$panel.'" class="textbox" rows="3" cols="20"></textarea></td>
			</tr>';
			if (!iMEMBER) {
				echo '<tr>
					<td class="tbl2" align="center">'.$locale['grrsg_07'].'<span style="color: rgb(255, 0, 0);">*</span></td>
				</tr>
				<tr>
					<td class="tbl2" align="center"><img id="captcha'.$panel.'" src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php" alt="" /><br /><a href="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_play.php"><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/audio_icon.gif" alt="" align="top" class="tbl-border" style="margin-bottom:1px" /></a> <a href="#" onclick=\'document.getElementById("captcha'.$panel.'").src = "'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php?sid=" + Math.random(); return false\'><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/refresh.gif" alt="" align="bottom" class="tbl-border" /></a></td>
				</tr>
				<tr>
					<td class="tbl2" align="center"><input type="text" name="code'.$panel.'" class="textbox" style="width:90%" /></td>
				</tr>';
			}
			echo '<tr>
				<td class="tbl2" align="center"><input type="submit" name="gb_save" value="'.$locale['grrsg_08'].'" class="button" /><br /></td>
			</tr>
			</table>';
		} else {
			echo '<table cellspacing="0" cellpadding="0" width="100%" class="tbl-border" align="center">
			<tr>
				<td class="tbl2" colspan="3" align="left"><strong>'.$locale['grrsg_01'].'</strong></td>
				<td class="tbl2" align="right"><strong><span id="rs_name'.$panel.'"></span></strong></td>
			</tr>
			<tr>
				<td class="tbl2" width="10%" align="left">'.$locale['grrsg_02'].'<span style="color: rgb(255, 0, 0);">*</span></td>
				<td class="tbl2" width="40%" align="left"><input type="text" name="name'.$panel.'"'.(iMEMBER ? ' value="'.$userdata['user_name'].'"' : '').' class="textbox" style="width:50%" /></td>
				<td class="tbl2" width="10%" align="left">'.$locale['grrsg_03'].'<span style="color: rgb(255, 0, 0);">*</span></td>
				<td class="tbl2" width="40%" align="left"><input type="text" name="ort'.$panel.'"'.(iMEMBER && array_key_exists('user_location', $userdata) ? ' value="'.$userdata['user_location'].'"' : '').' class="textbox" style="width:50%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="left">'.$locale['grrsg_04'].'</td>
				<td class="tbl2" align="left"><input type="text" name="interpret'.$panel.'" class="textbox" style="width:50%" /></td>
				<td class="tbl2" align="left">'.$locale['grrsg_05'].'</td>
				<td class="tbl2" align="left"><input type="text" name="titel'.$panel.'" class="textbox" style="width:50%" /></td>
			</tr>
			<tr>
				<td class="tbl2" align="left" valign="top">'.$locale['grrsg_06'].'</td>
				<td class="tbl2" align="left" valign="top"><textarea name="gruss'.$panel.'" class="textbox" rows="3" cols="30"></textarea></td>';
				if (iMEMBER) {
					echo '<td class="tbl2" colspan="2"></td>';
				} else {
					echo '<td class="tbl2" align="left" valign="top">'.$locale['grrsg_07'].'<span style="color: rgb(255, 0, 0);">*</span></td>
					<td class="tbl2" align="left"><img id="captcha'.$panel.'" src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php" alt="" align="left" />
						<a href="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_play.php"><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/audio_icon.gif" alt="" align="top" class="tbl-border" style="margin-bottom:1px" /></a><br />
						<a href="#" onclick=\'document.getElementById("captcha'.$panel.'").src = "'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php?sid=" + Math.random(); return false\'><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/refresh.gif" alt="" align="bottom" class="tbl-border" /></a><br />
						<input type="text" name="code'.$panel.'" class="textbox" style="width:50%" />
					</td>';
				}
			echo '</tr>
			<tr>
				<td class="tbl2" colspan="4" align="right"><input type="submit" name="gb_save" class="button" style="width:100px" value="'.$locale['grrsg_08'].'" /></td>
			</tr>
			</table>';
		}
		echo '<br /></form>
		</div>
		<div id="radiostatus_gb_check'.$panel.'" style="display:none;" align="center"></div>
		<div id="radiostatus_gb_wait'.$panel.'" style="display:none;" align="center">'.$locale['grrsg_09'].'<br /><br /></div>';
	}
	echo '<noscript><div align="center">'.$locale['grrsp_04'].'</div><br /></noscript>';
} else {
	echo '<div id="radiostatus" align="center">'.$locale['grrsp_02'].(iSUPERADMIN ? '<br />'.$locale['grrsp_03'] : '').'</div>';
}
/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
if ($panel == 1 || $panel == 4) {
	closeside();
} elseif ($panel != 5) {
	closetable();
}
?>