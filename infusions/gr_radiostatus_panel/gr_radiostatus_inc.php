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

function send_to_dnas($streamid, $listenerid, $action)
{

}

if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

if (isset($_GET['panel']) AND isnum($_GET['panel'])) {
	function rs_player($info) {
		global $locale, $settings;
		$player = $settings['siteurl'].str_replace('../', '', RADIOSTATUS);
		if ($info['rs_panel'] == 1 OR $info['rs_panel'] == 4) {
			$width = '20';
			$height = '20';
		} else {
			$width = '30';
			$height = '30';
		}
		$ausgabe = '';
		if ($info['typ'] == 0 AND preg_match("/mpeg/i", $info['music'])) {
			$ausgabe .= '<img src="'.RADIOSTATUS_SELF.'images/flash.png" border="0" width="'.$width.'" height="'.$height.'" alt="'.$locale['grrsi_01'].'" title="'.$locale['grrsi_01'].'" style="cursor:pointer;" onclick="window.open(\''.$player.'gr_radiostatus_player.php?id='.$info['rs_id'].'&amp;typ=flash\',\'flash\',\'height=258,width=420,top=\' + Math.round((screen.height - 258) / 2) + \',left=\' + Math.round((screen.width - 420) / 2) + \'\')" /> ';
		}
		$ausgabe .= '<a href="'.RADIOSTATUS_SELF.'gr_radiostatus_player.php?id='.$info['rs_id'].'&amp;p=pls" title="'.$locale['grrsi_02'].'"><img src="'.RADIOSTATUS_SELF.'images/winamp.png" alt="'.$locale['grrsi_02'].'" border="0" width="'.$width.'" height="'.$height.'" /></a> ';
		if ($info['typ'] == 0) {
			$ausgabe .= '<a href="'.RADIOSTATUS_SELF.'gr_radiostatus_player.php?id='.$info['rs_id'].'&amp;p=asx" title="'.$locale['grrsi_03'].'"><img src="'.RADIOSTATUS_SELF.'images/wmp.png" alt="'.$locale['grrsi_03'].'" border="0" width="'.$width.'" height="'.$height.'" /></a> ';
			$ausgabe .= '<a href="'.RADIOSTATUS_SELF.'gr_radiostatus_player.php?id='.$info['rs_id'].'&amp;p=ram" title="'.$locale['grrsi_04'].'"><img src="'.RADIOSTATUS_SELF.'images/realplayer.png" alt="'.$locale['grrsi_04'].'" border="0" width="'.$width.'" height="'.$height.'" /></a> ';
			if ($info['rs_ps'] != '') {
				$ausgabe .= '<a href="http://www.phonostar.de/listen.php?id='.$info['rs_ps'].'" target="_blank" title="'.$locale['grrsi_05'].'"><img src="'.RADIOSTATUS_SELF.'images/phonostar.png" border="0" width="'.$width.'" height="'.$height.'" alt="'.$locale['grrsi_05'].'" /></a> ';
			}
			if ($info['rs_tele'] != '') {
				$ausgabe .= '<img src="'.RADIOSTATUS_SELF.'images/telefon.png" border="0" width="'.$width.'" height="'.$height.'" alt="'.$locale['grrsi_06'].'" title="'.$locale['grrsi_06'].'" style="cursor:pointer;" onclick="window.open(\''.$player.'gr_radiostatus_player.php?id='.$info['rs_id'].'&amp;typ=tele\',\'tele\',\'height=400,width=510,top=\' + Math.round((screen.height - 400) / 2) + \',left=\' + Math.round((screen.width - 510) / 2) + \'\')" />';
			}
		}
		return $ausgabe;
	}
	function rs_theme($info, $typ) {
		global $locale,$gr_radiostatus_settings;
		if ($typ == 1 OR $typ == 4) {
			$name = 'side_';
		} elseif ($typ == 5) {
			$name = 'misc_';
		} elseif ($typ == 6) {
			$name = 'misc2_';
		} else {
			$name = 'main_';
		}
		ob_start();
		if (isset($info['status']) && $info['status']) {
			if (file_exists(RADIOSTATUS.'theme/'.$name.$info['rs_theme'].'.php')) {
				require RADIOSTATUS.'theme/'.$name.$info['rs_theme'].'.php';
			}
		} else {
			if (file_exists(RADIOSTATUS.'theme/offline_'.$name.$info['rs_theme'].'.php')) {
				require RADIOSTATUS.'theme/offline_'.$name.$info['rs_theme'].'.php';
			} elseif (file_exists(RADIOSTATUS.'theme/offline_default.php')) {
				require RADIOSTATUS.'theme/offline_default.php';
			}
		}
		$theme = ob_get_contents();
		ob_end_clean();
		if ($theme != '') {
			if (isset($info['status']) && $info['status']) {
				if (!empty($gr_radiostatus_settings['buy_link'])) {
					$buy_link = '<a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($info['song'])).'" target="_blank"><img src="'.RADIOSTATUS_SELF.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a>';
				} else {
					$buy_link = '';
				}
				$search = array('###rs_name###','###mod###','###mod_pic###','###gb###','###player###','###song###','###listner###','###listner_max###', '###listner_peak###','###bitrate###','###aim###','###icq###','###irc###','###genre###','###BUY_LINK###','###RADIOSTATUS_SELF###');
				$replace = array($info['rs_name'],$info['mod'],$info['mod_pic'],$info['gb'],rs_player($info['player_info']),$info['song'],$info['listner'],$info['listner_max'],$info['listner_peak'],$info['bitrate'],$info['aim'],$info['icq'],$info['irc'],$info['genre'],$buy_link,RADIOSTATUS_SELF);
			} else {
				$search = array('###rs_name###','###error###','###RADIOSTATUS_SELF###');
				$replace = array($info['rs_name'],(iSUPERADMIN ? $info['error'] : ''),RADIOSTATUS_SELF);
			}
			$ausgabe =  str_replace($search, $replace, $theme);
		} else {
			$ausgabe = $locale['grrsi_25'];
		}
		return $ausgabe;
	}
	$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_panel='".$_GET['panel']."' AND rs_status='1' ORDER BY rs_order");
	$count = dbrows($result);
	if ($count) {
		if ($_GET['panel'] == '6') {
			echo '<table><tr>';
		}
		while ($data = dbarray($result)) 
		{
			if (!file_exists(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php') OR @filemtime(RADIOSTATUS.'cache/stream_'.
			$data['rs_id'].'_'.$data['rs_cache'].'.php') <= (time()-$gr_radiostatus_settings['reload_cache'])) {
				require_once RADIOSTATUS.'gr_radiostatus_class.php';
				$server = new SHOUTcast();
				$server->GetStatus(array(
					'rs_ip'				=> $data['rs_ip'],
					'rs_port'			=> $data['rs_port'],
					'rs_pw'				=> $data['rs_pw'],
					'rs_transip'		=> $data['rs_transip'],
					'rs_transport'		=> $data['rs_transport'],
					'rs_transpw'		=> $data['rs_transpw'],
					'rs_server_typ'		=> $data['rs_server_typ'],
					'rs_server_id'		=> $data['rs_server_id']
				));
				
				$res = $server->MakeCache($data['rs_usertyp'], true, $data['rs_server_typ'], $data['rs_transip'], $data['rs_transport'], $data['rs_transpw']);
				$temp = fopen(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php','w');
				if (fwrite($temp, $res)) {
					fclose($temp);
				}
				eval('?>'.$res.'<?php ');
			} else {
				require_once RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
			}
			$cache['rs_id'] = $data['rs_id'];
			$cache['rs_name'] = $data['rs_name'];
			$cache['rs_theme'] = $data['rs_theme'];
			$cache['rs_panel'] = $data['rs_panel'];
			$cache['player_info'] = array(
				'rs_id' => $data['rs_id'],
				'rs_ps' => $data['rs_ps'],
				'rs_tele' => $data['rs_tele'],
				'rs_panel' => $_GET['panel'],
				'typ' => (array_key_exists('player', $cache) ? $cache['player'] : 0),
				'music' => (array_key_exists('music', $cache) ? $cache['music'] : '')
			);
			if (isset($cache['mod_ckeck']) AND $cache['mod_ckeck'] AND $data['rs_status_gb'] AND $data['rs_status_gb_dj'] AND checkgroup($data['rs_access_gb_user'])) {
				$cache['gb'] = '<img src="'.RADIOSTATUS_SELF.'images/gb.gif" alt="'.$locale['grrsg_01'].'" title="'.$locale['grrsg_01'].'" style="cursor:pointer;" border="0" width="65" height="25" ';
				if ($data['rs_gb_popup'])
				{
					$cache['gb'] .= 'onclick="window.open(\''.RADIOSTATUS_SELF.'gr_radiostatus_player.php?typ=gb&amp;id='.$cache['rs_id'].'\',\'GB\',\'height=350,width=550,top=\' + Math.round((screen.height - 350) / 2) + \',left=\' + Math.round((screen.width - 550) / 2) + \'\')" />';
				}
				else
				{
					$cache['gb'] .= 'onclick="updateGB'.$_GET['panel'].'(\''.$data['rs_id'].'\',\''.$data['rs_name'].'\')" />';
				}
			} else {
				$cache['gb'] = '';
			}
			if ($_GET['panel'] == '6') {
				echo '<td class="side-body" valign="top" width="'.(100 / $count).'%">'.rs_theme($cache, $_GET['panel']).'</td>';
			} else {
				echo rs_theme($cache, $_GET['panel']);
			}
		}
		
		if ($_GET['panel'] == '6') {
			echo '</tr></table>';
		}
		@mysql_free_result($result);
	} else {
		echo "<div align='center'>".$locale['grrsi_18']."</div>";
	}
} elseif (isset($_POST['gb']) AND isnum($_POST['gb'])) {
	sleep(3);
	require_once INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage.php';
	$securimage = new Securimage();
	if (iMEMBER OR (isset($_POST['code']) AND $securimage->check($_POST['code']) == true)) {
		$result = dbquery("SELECT rs_status_gb_dj, rs_gb_max, rs_access_gb_user, rs_gbintervall, rs_gb_max_user FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_id='".$_POST['gb']."' AND rs_status='1' AND rs_status_gb='1'");
		if (dbrows($result)) {
			$data = dbarray($result);
			if ($data['rs_status_gb_dj'] AND checkgroup($data['rs_access_gb_user'])) {
				$rows = dbcount("(rsgb_id)", DB_GR_RADIOSTATUS_GRUSSBOX, "rsgb_stream='".$_POST['gb']."' AND rsgb_user_ip='".USER_IP."'");
				if ($rows >= $data['rs_gb_max_user'])
					echo $locale['grrsg_11'];
				else
				{
					$limit_rows = dbcount("(rsgb_id)", DB_GR_RADIOSTATUS_GRUSSBOX, "rsgb_stream='".$_POST['gb']."' AND rsgb_user_ip='".USER_IP."' 
					AND rsgb_time>='".(time() - $data['rs_gbintervall'])."'");
				
					if ($limit_rows > 0)
						echo $locale['grrsg_11'];
					else
					{
						$name = isset($_POST['name']) ? stripinput($_POST['name']) : '';
						$ort = isset($_POST['ort']) ? stripinput($_POST['ort']) : '';
						$interpret = isset($_POST['interpret']) ? stripinput($_POST['interpret']) : '';
						$titel = isset($_POST['titel']) ? stripinput($_POST['titel']) : '';
						$gruss = isset($_POST['gruss']) ? stripinput($_POST['gruss']) : '';
					
						if ($name != '' AND $ort != '' AND (($interpret != '' AND $titel != '') AND $gruss != '')) 
						{
							$result_in = dbquery("INSERT INTO ".DB_GR_RADIOSTATUS_GRUSSBOX." 
							(rsgb_stream, rsgb_user_id, rsgb_user_ip, rsgb_user_name, rsgb_user_ort, rsgb_title, rsgb_interpret, rsgb_gruss, rsgb_time) 
							VALUES ('".$_POST['gb']."', '".(iMEMBER ? $userdata['user_id'] : '0')."', '".USER_IP."', '".$name."', '".$ort."', '".$titel."', 
							'".$interpret."', '".$gruss."', '".time()."')");
							if ($data['rs_gb_max'] > 0)
							{
								$rows = dbcount("(rsgb_id)", DB_GR_RADIOSTATUS_GRUSSBOX, "rsgb_stream='".$_POST['gb']."'");
								if ($rows >= $data['rs_gb_max'])
								{
									$result_up = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_status_gb_dj='0' WHERE rs_id='".$_POST['gb']."'");
								}
							}
							echo $locale['grrsg_12'];
						}
						else
						{
							echo $locale['grrsg_13'];
						}
					}
				}
			} else {
				if ($data['rs_gb_max'] > 0 && isset($locale['grrsg_14'])) {
					echo $locale['grrsg_14'];
				} else {
					echo $locale['grrsg_15'];
				}
			}
		} else {
			echo $locale['grrsg_16'];
		}
	} else {
		echo $locale['grrsg_17'];
	}
	echo '<br /><br />';
} elseif (isset($_GET['gbadmin']) AND isnum($_GET['gbadmin'])) {
	$result = dbquery("SELECT rs_id, rs_cache FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access_gb")." AND rs_id='".$_GET['gbadmin']."' AND rs_status='1' AND rs_status_gb='1'");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (file_exists(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php')) {
			require_once RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
			if (isset($cache['status']) && $cache['status']) {
				echo '<div class="tbl2" style="width:100%;" align="center">'.$locale['grrsi_14'].$cache['listner'].'/'.$cache['listner_max'].$locale['grrsi_26'].$cache['listner_peak'].'</div>';
			} else {
				echo '<div class="tbl2" style="width:100%;" align="center"><img src="'.INFUSIONS.'gr_radiostatus_panel/images/offline.gif" border="0" alt="Offline" /></div>';
			}
		}
		if (isset($_GET['id']) AND isnum($_GET['id'])) {
			if ($_GET['id'] == 0) {
				if (isset($_GET['status']) AND isnum($_GET['status'])) {
					$status = " AND rsgb_status='".$_GET['status']."'";
				} else {
					$status = '';
				}
				$result_del = dbquery('DELETE FROM '.DB_GR_RADIOSTATUS_GRUSSBOX.' WHERE rsgb_stream="'.$_GET['gbadmin'].'"'.$status);
			} else {
				$result_del = dbquery('DELETE FROM '.DB_GR_RADIOSTATUS_GRUSSBOX.' WHERE rsgb_id="'.$_GET['id'].'"');
			}
		} elseif (isset($_GET['uid']) AND isnum($_GET['uid']) AND isset($_GET['status']) AND isnum($_GET['status'])) {
			if ($_GET['uid'] == 0) {
				$result_up = dbquery("UPDATE ".DB_GR_RADIOSTATUS_GRUSSBOX." SET rsgb_status='".$_GET['status']."' WHERE rsgb_stream='".$_GET['gbadmin']."'");
			} else {
				$result_up = dbquery("UPDATE ".DB_GR_RADIOSTATUS_GRUSSBOX." SET rsgb_status='".$_GET['status']."' WHERE rsgb_id='".$_GET['uid']."'");
			}
		} else {
			$result_gb = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS_GRUSSBOX." WHERE rsgb_stream='".$_GET['gbadmin']."' AND rsgb_status='0' ORDER BY rsgb_time");
			if (dbrows($result_gb)) {
				echo '<table cellpadding="1" cellspacing="1" width="100%" align="center" class="tbl-border">
				<tr>
					<td class="tbl2" colspan="5"><strong>Unerledigt</strong></td>
				</tr>
				<tr>
					<td class="tbl2" width="15%">'.$locale['grrsg_25'].'</td>
					<td class="tbl2" width="15%">'.$locale['grrsg_26'].'</td>
					<td class="tbl2" width="20%">'.$locale['grrsg_27'].'</td>
					<td class="tbl2" width="30%">'.$locale['grrsg_28'].'</td>
					<td class="tbl2" width="10%"></td>
				</tr>';
				while ($data_gb = dbarray($result_gb)) {
					echo '<tr>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_user_name'].'<br />'.$data_gb['rsgb_user_ip'].'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_user_ort'].'<br />'.showdate($gr_radiostatus_settings['gb_time'], $data_gb['rsgb_time']).'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_interpret'].' - <br />'.$data_gb['rsgb_title'].'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_gruss'].'</td>
						<td class="tbl1" valign="top" align="center"><input type="submit" name="" value="'.$locale['grrsg_31'].'" style="cursor:pointer;" onclick="updateGB(\''.$data_gb['rsgb_id'].'\',\'1\')" class="button" /><br /><input type="submit" name="" value="'.$locale['grrsi_21'].'" style="cursor:pointer;" onclick="deleteGB(\''.$data_gb['rsgb_id'].'\')" class="button" /></td>
					</tr>';
				}
				echo '<tr>
					<td class="tbl2" colspan="5" align="center"><input type="submit" name="" value="'.$locale['grrsg_32'].'" style="cursor:pointer;" onclick="updateGB(\'0\',\'1\')" class="button" /> <input type="submit" name="" value="'.$locale['grrsi_22'].'" style="cursor:pointer;" onclick="deleteGB(\'0\',\'0\')" class="button" /></td>
				</tr></table>';
			} else {
				echo '<div class="tbl2" style="width:100%;" align="center">'.$locale['grrsg_19'].'</div>';
			}
			
			$result_gb = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS_GRUSSBOX." WHERE rsgb_stream='".$_GET['gbadmin']."' AND rsgb_status='1' ORDER BY rsgb_time");
			if (dbrows($result_gb)) {
				echo '<table cellpadding="1" cellspacing="1" width="100%" align="center" class="tbl-border">
				<tr>
					<td class="tbl2" colspan="5"><strong>Erledigt</strong></td>
				</tr>
				<tr>
					<td class="tbl2" width="15%">'.$locale['grrsg_25'].'</td>
					<td class="tbl2" width="15%">'.$locale['grrsg_26'].'</td>
					<td class="tbl2" width="20%">'.$locale['grrsg_27'].'</td>
					<td class="tbl2" width="30%">'.$locale['grrsg_28'].'</td>
					<td class="tbl2" width="10%"></td>
				</tr>';
				while ($data_gb = dbarray($result_gb)) {
					echo '<tr>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_user_name'].'<br />'.$data_gb['rsgb_user_ip'].'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_user_ort'].'<br />'.showdate($gr_radiostatus_settings['gb_time'], $data_gb['rsgb_time']).'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_interpret'].' - <br />'.$data_gb['rsgb_title'].'</td>
						<td class="tbl1" valign="top">'.$data_gb['rsgb_gruss'].'</td>
						<td class="tbl1" valign="top" align="center"><input type="submit" name="" value="'.$locale['grrsg_33'].'" style="cursor:pointer;" onclick="updateGB(\''.$data_gb['rsgb_id'].'\',\'0\')" class="button" /><br /><input type="submit" name="" value="'.$locale['grrsi_21'].'" style="cursor:pointer;" onclick="deleteGB(\''.$data_gb['rsgb_id'].'\')" class="button" /></td>
					</tr>';
				}
				echo '<tr>
					<td class="tbl2" colspan="5" align="center"><input type="submit" name="" value="'.$locale['grrsg_34'].'" style="cursor:pointer;" onclick="updateGB(\'0\',\'0\')" class="button" /> <input type="submit" name="" value="'.$locale['grrsi_22'].'" style="cursor:pointer;" onclick="deleteGB(\'0\',\'1\')" class="button" /></td>
				</tr></table>';
			} else {
				echo '<div class="tbl2" style="width:100%;" align="center">'.$locale['grrsg_30'].'</div>';
			}
			
			if (isset($_COOKIE[COOKIE_PREFIX.'gr_rs_gbadmin'])) {
				$cookie = stripinput($_COOKIE[COOKIE_PREFIX.'gr_rs_gbadmin']);
				if ($cookie == 'yes') {
					if (dbcount('(rsgb_id)', DB_GR_RADIOSTATUS_GRUSSBOX, 'rsgb_stream="'.$_GET['gbadmin'].'" AND rsgb_status="0" AND rsgb_time>"'.(time()-($gr_radiostatus_settings['reload_gb_admin']/1000)).'"')) {
						echo '<object type="audio/x-wav" data="'.RADIOSTATUS_SELF.'sound.wav" height="0" width="0">
							<param name="src" value="'.RADIOSTATUS_SELF.'sound.wav" />
							<param name="autostart" value="true" />
							<embed type="audio/x-wav" src="'.RADIOSTATUS_SELF.'sound.wav" autostart="true" width="0" height="0" controls="all" console="one" />
						</object>';
					}
				}
				unset($cookie);
			}
		}
	} else {
		echo $locale['grrsi_23'];
	}	
} elseif (isset($_GET['js']) AND isnum($_GET['js'])) {
header('Content-Type: text/javascript');
echo 'function updateRS'.$_GET['js'].'(){$("#radiostatus'.$_GET['js'].'").load("'.RADIOSTATUS_SELF.'gr_radiostatus_inc.php?panel='.$_GET['js'].'");setTimeout("updateRS'.$_GET['js'].'()",'.($_GET['js'] == 1 || $_GET['js'] == 4 ? $gr_radiostatus_settings['reload_side'] : ($_GET['js'] == 5 || $_GET['js'] == 6 ? $gr_radiostatus_settings['reload_misc'] : $gr_radiostatus_settings['reload_main'])).');}$(document).ready(function(){updateRS'.$_GET['js'].'();});
function updateGB'.$_GET['js'].'(id, name){$("#radiostatus_gb_check'.$_GET['js'].'").hide();var gb=$("input[name=gb'.$_GET['js'].']");if($("#radiostatus_gb'.$_GET['js'].'").is(":hidden")||gb.val()!=id){$("#rs_name'.$_GET['js'].'").html(name);$("input[name=gb'.$_GET['js'].']").attr({"value":id});$("input[name=interpret'.$_GET['js'].']").attr({"value":""});$("input[name=titel'.$_GET['js'].']").attr({"value":""});$("textarea[name=gruss'.$_GET['js'].']").attr({"value":""});$("input[name=code'.$_GET['js'].']").attr({"value":""});';
for($i = 1; $i < 5; $i++) {if ($i != $_GET['js']) {echo '$("#radiostatus_gb'.$i.'").hide("slow");';}}
echo '$("#captcha'.$_GET['js'].'").attr("src","'.RADIOSTATUS_SELF.INCLUDES.(!empty($settings['captcha']) ? 'captchas/' : '').'securimage/securimage_show.php?sid="+Math.random());$("#radiostatus_gb'.$_GET['js'].'").show("slow");}else{$("#radiostatus_gb'.$_GET['js'].'").hide("slow");}}function check_gb'.$_GET['js'].'(){$("#radiostatus_gb'.$_GET['js'].'").hide("slow");$("#radiostatus_gb_wait'.$_GET['js'].'").show();var gb=$("input[name=gb'.$_GET['js'].']");var name=$("input[name=name'.$_GET['js'].']");var ort=$("input[name=ort'.$_GET['js'].']");
var interpret=$("input[name=interpret'.$_GET['js'].']");var titel=$("input[name=titel'.$_GET['js'].']");var gruss=$("textarea[name=gruss'.$_GET['js'].']");var code=$("input[name=code'.$_GET['js'].']");$.ajax({url:"'.RADIOSTATUS_SELF.'gr_radiostatus_inc.php",type:"POST",data:{gb:gb.val(),name:name.val(),ort:ort.val(),interpret:interpret.val(),titel:titel.val(),gruss:gruss.val(),code:code.val()},success:function(data){$("#radiostatus_gb_wait'.$_GET['js'].'").hide();$("#radiostatus_gb_check'.$_GET['js'].'").html(data).show("slow");}});return false;}';
} elseif (isset($_GET['q']) AND isset($_GET['stream']) AND isnum($_GET['stream'])) {
	$result = dbquery("SELECT rs_id FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access_a")." AND rs_id='".$_GET['stream']."' AND rs_status='1'");
	if (dbrows($result)) {
		@mysql_free_result($result);
		$q = stripinput($_GET['q']);
		if (preg_match("/-/i", $q)) {
			$q_array = explode('-',$q,2);
			$interpret = trim($q_array['0']); $title = trim($q_array['1']);
			unset($q_array);
			$result = dbquery("SELECT rst_interpret, rst_title FROM ".DB_GR_RADIOSTATUS_TITLE." WHERE rst_interpret LIKE '".$interpret."%' AND rst_title LIKE '".$title."%' LIMIT 10");
		} else {
			$interpret = trim($q); $title = '';
			$result = dbquery("SELECT COUNT(rst_interpret) AS count, rst_interpret FROM ".DB_GR_RADIOSTATUS_TITLE." WHERE rst_interpret LIKE '".$interpret."%' GROUP BY rst_interpret LIMIT 10");
		}
		unset($q);
		if (dbrows($result)) {
			while ($data = dbarray($result)) {
				echo $data['rst_interpret'].(array_key_exists('rst_title',$data) && $data['rst_title'] != '' ? ' - '.$data['rst_title']:'').(array_key_exists('count',$data) && $data['count'] != '' ? '|('.$data['count'].' Eintrag/Eintr&auml;ge)':'')."\n";
			}
			@mysql_free_result($result);
		} else {
			echo "Es gibt keine &Uuml;bereinstimmung\n";
		}
	} else {
		echo $locale['grrsi_24'];
	}
} elseif (isset($_GET['listners']) AND isnum($_GET['listners'])) {
	if (!isset($_GET['rowstart']) OR !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	$result = dbquery("SELECT rs_id, rs_cache FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access_l")." AND rs_status='1' AND rs_id='".$_GET['listners']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (file_exists(RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php')) {
			require_once RADIOSTATUS.'cache/stream_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
			function ConvertSeconds($seconds) {
				$tmpseconds = substr('00'.$seconds % 60, -2);
				if ($seconds > 59) {
					if ($seconds > 3599) {
						$tmphours = substr('0'.intval($seconds / 3600), -2);
						$tmpminutes = substr('0'.intval($seconds / 60 - (60 * $tmphours)), -2);
						return ($tmphours.':'.$tmpminutes.':'.$tmpseconds);
					} else {
						return ('00:'.substr('0'.intval($seconds / 60), -2).':'.$tmpseconds);
					}
				} else {
					return ('00:00:'.$tmpseconds);
				}
			}
			
			if(isset($cache['listners']))
				$listeners = $cache['listners'];
			else
				$listeners = 0;

			if (iADMIN)
			{
				$Lcols = 5;
			}
			else
				$Lcols = 3;

			echo '<table cellpadding="2" cellspacing="2" width="100%" class="tbl-border" align="center">
			<tr>
				<td colspan="'.$Lcols.'" class="tbl2"><strong>'.$locale['grrs_07'].'</strong></td>
			</tr>
			<tr>
				<td class="tbl2" width="40%">'.$locale['grrs_08'].(iADMIN ? $locale['grrs_09'] : '').'</td>
				<td class="tbl2" width="10%">'.$locale['grrs_04'].'</td>
				<td class="tbl2" width="50%">'.$locale['grrs_10'].'</td>
				<td class="tbl2" colspan="2">Optionen</td>
			</tr>';
			if (is_array($listeners) AND sizeof($listeners)) {
				$user = array();
				$ips = '';
				for ($i=$_GET['rowstart'];($i < ($_GET['rowstart']+20) && $i < sizeof($listeners));$i++) {
					if ($i != $_GET['rowstart']) { $ips .= ','; }
					$ips .= "'".gethostbyname($listeners[$i]['hostname'])."'";
				}
				if ($ips != '') {
					$result2 = dbquery("SELECT user_id, user_name, user_ip FROM ".DB_USERS." WHERE user_ip IN(".$ips.")");
					if (dbrows($result2)) {
						while($data2 = dbarray($result2)) {
							if (!isset($user[$data2['user_ip']]) OR !is_array($user[$data2['user_ip']])) {
								$user[$data2['user_ip']] = array();
							}
							$user[$data2['user_ip']][] = array('user_id' => $data2['user_id'],'user_name' => $data2['user_name']);
						}
						@mysql_free_result($result2);
					}
				}
				for ($i=$_GET['rowstart'];($i < ($_GET['rowstart']+20) && $i < sizeof($listeners));$i++) {
					echo '<tr>
						<td class="tbl1">';
						if (array_key_exists(gethostbyname($listeners[$i]["hostname"]), $user)) {
							$j = 0;
							foreach ($user[gethostbyname($listeners[$i]["hostname"])] as $value) {
								if ($j > 0) { echo ', '; }
								echo '<a href="'.BASEDIR.'profile.php?lookup='.$value['user_id'].'">'.$value['user_name'].'</a>';
								$j++;
							}
						} else {
							$ListenerIP = gethostbyname($listeners[$i]['hostname']);
							echo $locale['grrs_11'].(iADMIN ? "(<a href='http://www.utrace.de/?query=".gethostbyname($listeners[$i]['hostname'])."'>".gethostbyname($listeners[$i]['hostname'])."</a>)" : "");
						}
						echo '</td>
						<td class="tbl1">'.ConvertSeconds($listeners[$i]['connecttime']).'</td>
						<td class="tbl1">'.($listeners[$i]['useragent'] != "" ? $listeners[$i]['useragent'] : 'Unbekannt').'</td>';
						if (iADMIN && checkrights("SCT"))
						{
							echo '<td class="tbl1"><a href="/administration/shoutcast.php?aid='.iAUTH.'&action=kickdst&kickdst='.$listeners[$i]['uid'].'">kick</a></td>';
						}
						
						if (iSUPERADMIN && checkrights("SCT"))
						{
							echo '<td class="tbl1"><a href="/administration/shoutcast.php?aid='.iAUTH.'&action=bandst&bandst='.$listeners[$i]['uid'].'
							&hostip='.$listeners[$i]['hostname'].'"><font color="red">ban</font></a></td>';
						}
	
					echo '</tr>';
				}
				if (20 < sizeof($listeners)) {
					echo '<tr>
						<td class="tbl1" colspan="'.$Lcols.'">'.makepagenav($_GET['rowstart'],20,sizeof($listeners),3, 'gr_radiostatus_info.php?id='.$data['rs_id'].(isset($_GET['popup']) ? '&amp;popup' : '').'&amp;').'</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="3" class="tbl2" align="center">'.$locale['grrs_12'].'</td>
				</tr>';
			}
			echo '</table><br />';
		}
	}
	
} else {
	echo $locale['grrsi_24'].'<br /><br />';
}
@mysql_close();
?>