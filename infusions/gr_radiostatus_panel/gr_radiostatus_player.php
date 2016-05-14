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
require_once '../../maincore.php';

if (!defined('RADIOSTATUS')) { define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/'); }
require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

if (!isset($_GET['id']) OR !isnum($_GET['id'])) { die(); }
if (!isset($_GET['p']) OR !preg_check("/^(asx|pls|ram)$/", $_GET['p'])) { $_GET['p'] = 'asx'; }
$result = dbquery("SELECT rs_server_typ, rs_server_id, rs_ip, rs_port, rs_name, rs_tele, rs_flash, rs_cache, rs_access_gb_user FROM ".DB_GR_RADIOSTATUS." WHERE rs_status='1' AND rs_id='".$_GET['id']."'");
if (dbrows($result)) {
	$data = dbarray($result);
	if (file_exists(RADIOSTATUS.'cache/stream_'.$_GET['id'].'_'.$data['rs_cache'].'.php')) {
		require_once RADIOSTATUS.'cache/stream_'.$_GET['id'].'_'.$data['rs_cache'].'.php';
		$cache['player'] = (array_key_exists('player', $cache) ? $cache['player'] : 0);
		if (!empty($_GET['typ'])) {
			require_once THEME.'theme.php';
			echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
			echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".$locale['xml_lang']."' lang='".$locale['xml_lang']."'>\n";
			echo "<head>\n<title>".$settings['sitename']."</title>\n";
			echo "<meta http-equiv='Content-Type' content='text/html; charset=".$locale['charset']."' />\n";
			echo "<meta name='description' content='".$settings['description']."' />\n";
			echo "<meta name='keywords' content='".$settings['keywords']."' />\n";
			echo "<link rel='stylesheet' href='".THEME."styles.css' type='text/css' media='screen' />\n";
			if (file_exists(IMAGES."favicon.ico")) { echo "<link rel='shortcut icon' href='".IMAGES."favicon.ico' type='image/x-icon' />\n"; }
			if (function_exists("get_head_tags")) { echo get_head_tags(); }
			echo "<script type='text/javascript' src='".INCLUDES."jscript.js'></script>\n";
			echo "<script type='text/javascript' src='".INCLUDES.(!file_exists(INCLUDES.'jquery.js') ? 'jquery/' : '')."jquery.js'></script>\n";
			if ($_GET['typ'] != 'tele' AND $_GET['typ'] != 'gb') {
				echo '<script type="text/javascript" src="'.RADIOSTATUS.'swfobject.js"></script>';
			}
			if ($_GET['typ'] == 'gb' AND checkgroup($data['rs_access_gb_user'])) {
				echo '<script type="text/javascript">
				function check_gb() {
					$("#radiostatus_gb").hide("slow");
					$("#radiostatus_gb_wait").show();
					var gb = $("input[name=gb]");
					var name = $("input[name=name]");
					var ort = $("input[name=ort]");
					var interpret = $("input[name=interpret]");
					var titel = $("input[name=titel]");
					var gruss = $("textarea[name=gruss]");
					var code = $("input[name=code]");
					$.ajax({
						url: "'.RADIOSTATUS.'gr_radiostatus_inc.php",
						type: "POST",
						data: { gb: gb.val(), name: name.val(), ort: ort.val(), interpret: interpret.val(), titel: titel.val(), gruss: gruss.val(), code: code.val() },
						success: function (data) {
							$("#radiostatus_gb_wait").hide();
							$("#radiostatus_gb_check").html(data).show("slow");
						}
					});
					return false;
				}
				</script>';
			}
			echo "</head>\n<body>\n";
			opentable($settings['sitename'].': '.$data['rs_name']);
			if ($_GET['typ'] == 'tele') {
				echo '<div align="center" style="height:330px;overflow: auto;">'.nl2br(stripslashes($data['rs_tele'])).'</div><br />';
			} elseif ($_GET['typ'] == 'gb' AND isset($_GET['id']) AND isnum($_GET['id']) AND checkgroup($data['rs_access_gb_user'])) {
				echo '<div id="radiostatus_gb" align="center">
				<form id="grussbox" method="post" action="" onsubmit="return check_gb();">
				<input type="hidden" name="gb" value="'.$_GET['id'].'" />
				<table cellspacing="0" cellpadding="0" width="100%" class="tbl-border" align="center">
				<tr>
					<td class="tbl2" colspan="3" align="left"><strong>'.$locale['grrsg_01'].'</strong></td>
					<td class="tbl2" align="right"><strong><span id="rs_name"></span></strong></td>
				</tr>
				<tr>
					<td class="tbl2" width="10%" align="left">'.$locale['grrsg_02'].'<span style="color: rgb(255, 0, 0);">*</span></td>
					<td class="tbl2" width="40%" align="left"><input type="text" name="name"'.(iMEMBER ? ' value="'.$userdata['user_name'].'"' : '').' class="textbox" style="width:50%" /></td>
					<td class="tbl2" width="10%" align="left">'.$locale['grrsg_03'].'<span style="color: rgb(255, 0, 0);">*</span></td>
					<td class="tbl2" width="40%" align="left"><input type="text" name="ort"'.(iMEMBER && array_key_exists('user_location', $userdata) ? ' value="'.$userdata['user_location'].'"' : '').' class="textbox" style="width:50%" /></td>
				</tr>
				<tr>
					<td class="tbl2" align="left">'.$locale['grrsg_04'].'</td>
					<td class="tbl2" align="left"><input type="text" name="interpret" class="textbox" style="width:50%" /></td>
					<td class="tbl2" align="left">'.$locale['grrsg_05'].'</td>
					<td class="tbl2" align="left"><input type="text" name="titel" class="textbox" style="width:50%" /></td>
				</tr>
				<tr>
					<td class="tbl2" align="left" valign="top">'.$locale['grrsg_06'].'</td>
					<td class="tbl2" align="left" valign="top"><textarea name="gruss" rows="3" class="textbox" style="width:80%"></textarea></td>';
					if (iMEMBER) {
						echo '<td class="tbl2" colspan="2"></td>';
					} else {
						echo '<td class="tbl2" align="left" valign="top">'.$locale['grrsg_07'].'<span style="color: rgb(255, 0, 0);">*</span></td>
						<td class="tbl2" align="left"><img id="captcha" src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php" alt="" align="left" />
							<a href="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_play.php"><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/audio_icon.gif" alt="" align="top" class="tbl-border" style="margin-bottom:1px" /></a><br />
							<a href="#" onclick=\'document.getElementById("captcha").src = "'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php?sid=" + Math.random(); return false\'><img src="'.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/images/refresh.gif" alt="" align="bottom" class="tbl-border" /></a><br />
							<input type="text" name="code" class="textbox" style="width:50%" />
						</td>';
					}
				echo '</tr>
				<tr>
					<td class="tbl2" colspan="4" align="right"><input type="submit" name="gb_save" class="button" style="width:100px" value="'.$locale['grrsg_08'].'" /></td>
				</tr>
				</table><br /></form>
				</div>
				<div id="radiostatus_gb_check" style="display:none;" align="center" class="tbl2"></div>
				<div id="radiostatus_gb_wait" style="display:none;" align="center" class="tbl2">'.$locale['grrsg_09'].'<br /><br /></div>
				<noscript><div align="center">'.$locale['grrsp_04'].'</div><br /></noscript>';
			} elseif (preg_match("/mpeg/i", $cache['music']) && isset($cache['music'])) {
				echo '<div id="player" align="center">
				<p>
					<strong>'.$locale['grrspl_01'].'</strong><br />
					<a href="http://get.adobe.com/shockwave/">Download Shockwave Player</a>
				</p>
				</div>
				<script type="text/javascript">
					// <![CDATA[
					var so = new SWFObject("nativeradio2.swf", "nativeradio", "400", "200", "10", "#cccccc");
					so.addParam("scale", "noscale");
					'.($data['rs_flash'] != '' ? 'so.addVariable("swfcolor", "'.$data['rs_flash'].'");' : '').'
					so.addVariable("swfstreamurl", "http://'.$data['rs_ip'].':'.$data['rs_port'].($data['rs_server_typ'] == 1 ? "/listen".$data['rs_server_id'] : "").'");
					so.addVariable("swfpause", "0");
					so.write("player");
					// ]]> 
				</script>';
			} else {
				echo $locale['grrsi_24'];
			}
			/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
			echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
			closetable();
			echo "</body>\n</html>\n";
		} else {
			require_once INCLUDES.'class.httpdownload.php';
			$object = new httpdownload;
			if ($_GET['p'] == 'asx' AND $cache['player'] == 0) {
				$data = '<asx version="3.0">
				  <title>'.$data['rs_name'].'</title>
				  <entry>
				    <title>'.$data['rs_name'].'</title>
				    <ref href="http://'.$data['rs_ip'].':'.$data['rs_port'].($data['rs_server_typ'] == 1 ? "/listen".$data['rs_server_id'] : "").'"/>
				  </entry>
				</asx>';
				$object->set_mime('video/x-ms-asf');
			} elseif ($_GET['p'] == 'ram' AND $cache['player'] == 0) {
				$data = 'http://'.$data['rs_ip'].':'.$data['rs_port'].($data['rs_server_typ'] == 1 ? "/listen".$data['rs_server_id'] : "");
				$object->set_mime('audio/x-pn-realaudio');
			} elseif ($_GET['p'] == 'pls' OR $cache['player'] == 1) {
				$data = '[playlist]
NumberOfEntries=1
File1=http://'.$data['rs_ip'].':'.$data['rs_port'].($data['rs_server_typ'] == 1 ? "/listen".$data['rs_server_id'] : "/").($cache['player'] == 0 ? '' : ';stream.nsv').'
';
				$object->set_mime('audio/x-mpequrl');
			}
			ob_end_clean();
			$object->set_bydata($data);
			$object->use_resume = true;
			$object->set_filename('stream.'.$_GET['p']);
			$object->download();
		}
	}
}
@mysql_close();
?>