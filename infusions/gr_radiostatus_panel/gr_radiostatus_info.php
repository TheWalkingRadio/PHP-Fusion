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

if (!defined('RADIOSTATUS')) {
	define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/');
}

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

if (!isset($_GET['popup'])) {
	require_once THEMES.'templates/header.php';
	add_to_head('<script type="text/javascript" src="'.RADIOSTATUS.'jquery.autocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="'.RADIOSTATUS.'jquery.autocomplete.css" />');
} else {
	require_once THEME.'theme.php';
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
	echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".$locale['xml_lang']."' lang='".$locale['xml_lang']."'>\n";
	echo "<head>\n<title>".$settings['sitename']."</title>\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=".$locale['charset']."' />\n";
	echo "<meta name='description' content='".$settings['description']."' />\n";
	echo "<meta name='keywords' content='".$settings['keywords']."' />\n";
	echo "<link rel='stylesheet' href='".THEME."styles.css' type='text/css' media='screen' />\n";
	if (file_exists(IMAGES."favicon.ico")) { echo "<link rel='shortcut icon' href='".IMAGES."favicon.ico' type='image/x-icon' />\n"; }
	if (function_exists('get_head_tags')) { echo get_head_tags(); }
	echo "<script type='text/javascript' src='".INCLUDES."jscript.js'></script>\n";
	echo "<script type='text/javascript' src='".INCLUDES."jquery.js'></script>\n";
	echo '<script type="text/javascript" src="'.RADIOSTATUS.'jquery.autocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="'.RADIOSTATUS.'jquery.autocomplete.css" />';
	echo "</head>\n<body>\n";
}

if (!isset($_GET['id']) OR !isnum($_GET['id'])) { $_GET['id'] = 0; }
if (!isset($_GET['rowstart']) OR !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

$result_menue = dbquery("SELECT rs_id, rs_name FROM ".DB_GR_RADIOSTATUS." WHERE (".groupaccess("rs_access")." OR ".groupaccess("rs_access_a")." OR ".groupaccess("rs_access_gb").") AND rs_status='1' ORDER BY rs_order");
if (dbrows($result_menue)) {
	if (dbrows($result_menue) == 1) {
		$data_menue = dbarray($result_menue);
		$_GET['id'] = $data_menue['rs_id'];
	} else {
		opentable($locale['grrs_01']);
		echo '<form action="'.FUSION_SELF.'" method="get">
		'.(isset($_GET['popup']) ? '<input type="hidden" name="popup" />' : '').'
		<div align="center">
		<select name="id" style="width:200px;" onchange="this.form.submit();" class="textbox">'."\n";
		while($data_menue = dbarray($result_menue)) {
			if ($_GET['id'] == 0) { $_GET['id'] = $data_menue['rs_id']; }
			echo '<option value="'.$data_menue['rs_id'].'"'.($_GET['id'] == $data_menue['rs_id'] ? ' selected="selected"' : '').'>'.$data_menue['rs_name'].'</option>'."\n";
		}
		echo '</select></div>
		</form>';
		closetable();
	}
	@mysql_free_result($result_menue);
}

if ($_GET['id'] != 0) {
	$result = dbquery("DELETE FROM ".DB_GR_RADIOSTATUS_TITLE." WHERE rst_time<'".(time()-86400)."'");
	$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." WHERE (".groupaccess("rs_access")." OR ".groupaccess("rs_access_a")." OR ".groupaccess("rs_access_gb").") AND rs_status='1' AND rs_id='".$_GET['id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		@mysql_free_result($result);
		if (checkgroup($data['rs_access_gb']) AND $data['rs_status_gb']) {
			if (isset($_POST['gbadmin_save'])) {
				$rs_status_gb_dj = (isset($_POST['status_gb_dj']) && isnum($_POST['status_gb_dj']) ? $_POST['status_gb_dj'] : '0');
				$rs_gb_max = (isset($_POST['gb_max']) && isnum($_POST['gb_max']) ? $_POST['gb_max'] : '0');
				$rs_gb_max_user = (isset($_POST['gb_max_user']) && isnum($_POST['gb_max_user']) ? $_POST['gb_max_user'] : '1');
				$result_up = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_status_gb_dj='".$rs_status_gb_dj."', rs_gb_max='".$rs_gb_max."', rs_gb_max_user='".$rs_gb_max_user."' WHERE rs_id='".$data['rs_id']."'");
				redirect(FUSION_SELF.'?id='.$data['rs_id'].(isset($_GET['popup']) ? '&popup' : ''));
			}
			if (isset($_POST['gbadmin_cookie'])) {
				$cookie = isset($_POST['cookie']) && isnum($_POST['cookie']) ? ($_POST['cookie'] == 1 ? 'yes' : 'no') : 'no';
				setcookie(COOKIE_PREFIX.'gr_rs_gbadmin', $cookie, time() + 31536000, '/', '', '0');
				redirect(FUSION_SELF.'?id='.$data['rs_id'].(isset($_GET['popup']) ? '&popup' : ''));
			}
			opentable($locale['grrsg_24']);
			echo '<div id="gbadmin"></div>
			<div align="center">
			<form action="'.FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&amp;popup' : '').'" method="post">
			<div class="tbl2" style="width:100%;" align="center">
			<table width="100%">
			<tr>
				<td>'.$locale['grrsg_01'].':</td><td><select name="status_gb_dj" class="textbox" style="width:110px;">
				<option value="1"'.($data['rs_status_gb_dj'] == 1 ? ' selected="selected"' : '').'>'.$locale['grrsi_19'].'</option>
				<option value="0"'.($data['rs_status_gb_dj'] == 0 ? ' selected="selected"' : '').'>'.$locale['grrsi_20'].'</option>
			</select></td>
			</tr>
			<tr>
				<td>'.$locale['grrsg_29'].':</td><td><select name="gb_max_user" class="textbox" style="width:110px;">';
				for($i=1;$i <= 20;$i=$i+1) {
					echo '<option value="'.$i.'"'.($data['rs_gb_max_user'] == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
				}


			echo '</select></td></tr><tr><td>'.$locale['grrsg_20'].':</td><td><select name="gb_max" class="textbox" style="width:110px;">
				<option value="0"'.($data['rs_gb_max'] == 0 ? ' selected="selected"' : '').'>'.$locale['grrsg_21'].'</option>';
				for($i=10;$i <= 100;$i=$i+10) {
					echo '<option value="'.$i.'"'.($data['rs_gb_max'] == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
				}

			echo '</select></td></tr></table>';
			echo '<br />
			<input type="submit" class="button" name="gbadmin_save" value="'.$locale['grrsg_22'].'" /><br />
			<span style="color:#FF0000;">'.sprintf($locale['grrsg_23'], ($gr_radiostatus_settings['reload_gb_admin']/1000)).'</span>
			</div></form></div><br />
			<div id="gbadmindiv" align="center"></div><br />';
			$code = '<script type="text/javascript">
			function updateGBadmin(){
				$("#gbadmindiv").load("'.RADIOSTATUS.'gr_radiostatus_inc.php?gbadmin='.$data['rs_id'].'");
				setTimeout("updateGBadmin()",'.$gr_radiostatus_settings['reload_gb_admin'].');
			}
			function deleteGB(gbid,status){
				$.get("'.RADIOSTATUS.'gr_radiostatus_inc.php", { gbadmin: "'.$data['rs_id'].'", id: gbid, status: status }).success(function() { updateGBadmin(); });
				
			}
			function updateGB(gbid,status){
				$.get("'.RADIOSTATUS.'gr_radiostatus_inc.php", { gbadmin: "'.$data['rs_id'].'", uid: gbid, status: status }).success(function() { updateGBadmin(); });
			}
			$(document).ready(function(){
				updateGBadmin();
			});
			</script>';
			if (!isset($_GET['popup'])) {
				add_to_head($code);
			} else {
				echo $code;
			}
			if (isset($_COOKIE[COOKIE_PREFIX.'gr_rs_gbadmin'])) {
				$cookie = stripinput($_COOKIE[COOKIE_PREFIX.'gr_rs_gbadmin']);
			} else {
				$cookie = 'no';
			}
			echo '<form action="'.FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&amp;popup' : '').'" method="post">
			<div class="tbl2" style="width:100%;" align="center">'.$locale['grrsi_27'].'
			<select name="cookie" class="textbox" style="width:110px;">
				<option value="1"'.($cookie == 'yes' ? ' selected="selected"' : '').'>'.$locale['grrsi_19'].'</option>
				<option value="0"'.($cookie == 'no' ? ' selected="selected"' : '').'>'.$locale['grrsi_20'].'</option>
			</select>&nbsp;&nbsp;&nbsp;
			<input type="submit" class="button" name="gbadmin_cookie" value="'.$locale['grrsg_22'].'" />
			</div></form>';
		}
		if (checkgroup($data['rs_access_a']) && $data['rs_streamcenter'] != 0) {
			if (checkgroup($data['rs_access_gb'])) {
				closetable();
			}
			opentable($locale['grrs_13']);
			if (isset($_POST['save']) AND isset($_POST['song']) AND $_POST['song'] != '') {
				$fp = @fsockopen($data['rs_ip'], $data['rs_port'], $errno, $errstr, 1);
				if ($fp) {
					stream_set_blocking($fp, false);
					fputs($fp, "GET /admin.cgi?pass=".$data['rs_pw'].($data['rs_server_typ'] == 1 ? "&sid=".$data['rs_server_id'] : "")."&mode=updinfo&song=".rawurlencode(utf8_decode($_POST['song']))." HTTP/1.1\nUser-Agent: Mozilla\n\n");
					fclose($fp);
				}
				$song = stripinput($_POST['song']);
				if (preg_match("/-/i", $song)) {
					$song_array = explode('-',$song,2);
					$interpret = trim($song_array['0']); $title = trim($song_array['1']);
					unset($song_array);
				} else {
					$interpret = trim($song); $title = '';
				}
				$result_s = dbquery("SELECT rst_interpret FROM ".DB_GR_RADIOSTATUS_TITLE." WHERE rst_interpret='".$interpret."' AND rst_title='".$title."' LIMIT 1");
				if (dbrows($result)) {
					$result_u = dbquery("UPDATE ".DB_GR_RADIOSTATUS_TITLE." SET rst_time='".time()."' WHERE rst_interpret='".$interpret."' AND rst_title='".$title."'");
				} else {
					$result_i = dbquery("INSERT INTO ".DB_GR_RADIOSTATUS_TITLE." (rst_interpret, rst_title, rst_time) VALUES ('".$interpret."', '".$title."', '".time()."')");
				}
				$result_a = dbquery("INSERT INTO ".DB_GR_RADIOSTATUS_ACTION." (rsa_stream, rsa_user_id, rsa_sction, rsa_time) VALUES ('".$_GET['id']."', '".$userdata['user_id']."', '1', '".time()."')");
				redirect(FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&popup' : ''));
			}
			echo '<script type="text/javascript">
			$(document).ready(function(){
				$("#song").autocomplete({
					url: "'.RADIOSTATUS.'gr_radiostatus_inc.php",
					useCache: false,
					minChars: 3,
					autoFill: true,
					extraParams: {
						stream: "'.$_GET['id'].'"
					},
					showResult: function(value, data) {
						return value + " <i>" + data + "</i>";
					}
				});
			});
			</script>
			<div class="tbl2" style="width:100%;" align="center">
			<p style="color: #FF0000;" align="center">'.$locale['grrs_14'].'</p><br /><strong>'.$locale['grrs_15'].'</strong><br />
			'.$locale['grrs_16'].'<br />
			<form action="'.FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&amp;popup' : '').'" method="post">
			<input id="song" type="text" class="textbox" style="width:90%" name="song" /><br />
			<input type="submit" class="button" name="save" value="'.$locale['grrs_17'].'" /></form></div><br />
			<div class="tbl2" style="width:100%;"><strong>'.$locale['grrs_18'].'</strong><br />';
			if ($data['rs_apw'] != '') {
				if (isset($_POST['kick'])) {
					$fp = @fsockopen($data['rs_ip'], $data['rs_port'], $errno, $errstr, 1);
					if ($fp) {
						stream_set_blocking($fp, false);
						fputs($fp, "GET /admin.cgi?pass=".$data['rs_apw'].($data['rs_server_typ'] == 1 ? "&sid=".$data['rs_server_id'] : "")."&mode=kicksrc HTTP/1.1\nUser-Agent: Mozilla\n\n");
						fclose($fp);
					}
					$result_a = dbquery("INSERT INTO ".DB_GR_RADIOSTATUS_ACTION." (rsa_stream, rsa_user_id, rsa_sction, rsa_time) VALUES ('".$_GET['id']."', '".$userdata['user_id']."', '2', '".time()."')");
					redirect(FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&popup' : ''));
				}
				echo '<script type="text/javascript">
				<!--
				function kickmecheck() {
					var result = confirm("'.$locale['grrs_23'].$data['rs_name'].$locale['grrs_24'].'")
					if (result == true){
						document.getElementById("kickme").submit();
					}
				}
				//-->
				</script>
				'.$locale['grrs_19'].'<br />
				<form id="kickme" action="'.FUSION_SELF.'?id='.$_GET['id'].(isset($_GET['popup']) ? '&amp;popup' : '').'" method="post"><input type="hidden" name="kick" /></form><input type="submit" class="button" name="kick" onclick="kickmecheck();" value="'.$locale['grrs_20'].'" />';
			} else {
				echo $locale['grrs_21'];
			}
			echo '</div><br /></div>';
		}
		if (checkgroup($data['rs_access']) AND file_exists(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php')) {
			if (checkgroup($data['rs_access_gb']) OR checkgroup($data['rs_access_a'])) {
				closetable();
			}
			require_once RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
			opentable($locale['grrs_02']);
			if ($cache['status']) {
					
				$history = $cache['history'];
				if (is_array($history)) 
				{
					echo '<table cellpadding="2" cellspacing="2" width="100%" class="tbl-border" align="center">
					<tr>
						<td colspan="'.($gr_radiostatus_settings['buy_link'] != '' ? '3' : '2').'" class="tbl2"><strong>'.$locale['grrs_03'].'</strong></td>
					</tr>
					<tr>
						<td class="tbl2" width="20%">'.$locale['grrs_04'].'</td>
						<td class="tbl2" width="'.($gr_radiostatus_settings['buy_link'] != '' ? '70' : '80').'%">'.$locale['grrs_05'].'</td>
						'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center">'.$locale['grrs_22'].'</td>' : '').'
					</tr>
					<tr>
						<td class="tbl1">'.$locale['grrs_06'].'</td>
						<td class="tbl1">'.$cache['song'].'</td>
						'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center"><a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($cache['song'])).'" target="_blank"><img src="'.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a></td>' : '').'
					</tr>';
					for ($i=0;$i < sizeof($history);$i++) {
						echo '<tr>
							<td class="tbl1">'.showdate("%H:%M:%S", $history[$i]['playedat']).'</td>
							<td class="tbl1">'.$history[$i]['title'].'</td>
							'.($gr_radiostatus_settings['buy_link'] != '' ? '<td class="tbl1" align="center"><a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($history[$i]['title'])).'" target="_blank"><img src="'.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a></td>' : '').'
						</tr>';
					}
					echo '</table>';
				}
				
				if (checkgroup($data['rs_access_l'])) {
				
					$listeners = $cache['listners'];
					echo '<div id="listners" align="center"></div>';
					$code = '<script type="text/javascript">
					function updateListners(){
						$("#listners").load("'.RADIOSTATUS.'gr_radiostatus_inc.php?listners='.$data['rs_id'].'&rowstart='.$_GET['rowstart'].'");
						setTimeout("updateListners()",'.$gr_radiostatus_settings['reload_listners'].');
					}
					$(document).ready(function(){
						updateListners();
					});
					</script>';
					if (!isset($_GET['popup'])) {
						add_to_head($code);
					} else {
						echo $code;
					}
				}

			} else {
				echo '<div align="center"><img src="'.INFUSIONS.'gr_radiostatus_panel/images/offline.gif" border="0" alt="Offline" /></div>';
			}
		}
	} else {
		opentable($locale['grrs_02']);
		echo "<div align='center'>".$locale['grrsi_18']."</div>";
	}
} else {
	opentable($locale['grrs_02']);
	echo "<div align='center'>".$locale['grrsi_18']."</div>";
}
/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
closetable();

if (!isset($_GET['popup'])) {
	require_once THEMES.'templates/footer.php';
} else {
	echo "</body>\n</html>\n";
	@mysql_close();
}
?>