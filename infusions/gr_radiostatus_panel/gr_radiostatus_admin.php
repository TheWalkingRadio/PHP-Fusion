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
require_once THEMES.'templates/admin_header.php';

if (!checkrights('GRRS') || !defined('iAUTH') || $_GET['aid'] != iAUTH) { redirect(BASEDIR.'index.php'); }
if (!defined('RADIOSTATUS')) { define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/'); }
if (file_exists(RADIOSTATUS.'update.php')) { redirect(RADIOSTATUS.'update.php'.$aidlink); }
if (isset($_GET['delete']) && !isnum($_GET['delete'])) { redirect(BASEDIR.'index.php'); }
if (isset($_GET['edit']) && !isnum($_GET['edit'])) { redirect(BASEDIR.'index.php'); }
if (isset($_GET['up']) && !isnum($_GET['up'])) { redirect(BASEDIR.'index.php'); }
if (isset($_GET['down']) && !isnum($_GET['down'])) { redirect(BASEDIR.'index.php'); }
if (isset($_GET['order']) && !isnum($_GET['order'])) { redirect(BASEDIR.'index.php'); }

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

function rs_access($status=1, $select=0) {
	global $locale;
	$res = '';
	if ($status) {
		$res .= '<option value="0"'.($select == 0 ? ' selected="selected"' : '').'>'.$locale['user0'].'</option>';
		$res .= '<option value="101"'.($select == 101 ? ' selected="selected"' : '').'>'.$locale['user1'].'</option>';
	}
	$res .= '<option value="102"'.($select == 102 ? ' selected="selected"' : '').'>'.$locale['user2'].'</option>';
	$res .= '<option value="103"'.($select == 103 ? ' selected="selected"' : '').'>'.$locale['user3'].'</option>';
	$result = dbquery("SELECT group_id, group_name FROM ".DB_USER_GROUPS." ORDER BY group_name");
	if (dbrows($result)) {
		while($data = dbarray($result)) {
			$res .= '<option value="'.$data['group_id'].'"'.($select == $data['group_id'] ? ' selected="selected"' : '').'>'.$data['group_name'].'</option>';
		}
	}
	return $res;
}

function rs_make_cache() {
	$cache = '<?php'."\n";
	$cache .= 'if (!defined(\'IN_FUSION\')) { die(\'Access Denied\'); }'."\n";
	$cache .= '$stream_data = array();'."\n";
	$result = dbquery("SELECT rs_id, rs_ip, rs_port, rs_transip, rs_transport, rs_cache, rs_usertyp FROM ".DB_GR_RADIOSTATUS." WHERE rs_status='1'");
	if (dbrows($result)) {
		while ($data = dbarray($result)) {
			$cache .= '$stream_data[\'http://'.$data['rs_ip'].':'.$data['rs_port'].'\'] = array(\'id\' => \''.$data['rs_id'].'\',\'cache\' => \''.$data['rs_cache'].'\',\'usertyp\' => \''.$data['rs_usertyp'].'\');'."\n";
		}
	}
	$cache .= '?>';
	$temp = fopen(RADIOSTATUS.'cache/stream_data.php','w');
	if (fwrite($temp, $cache)) {
		fclose($temp);
	}
}

function rs_check_theme() {
	$res = array();
	$folder = RADIOSTATUS.'theme/';
	$temp = opendir($folder);
	while ($file = readdir($temp)) {
		if ($file != 'index.php' AND !is_dir($folder.$file)) { $res[] = $file; }
	}
	closedir($temp);
	return $res;
}

function getCurrentURL() {
	global $aidlink;
	$s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
	$protocol = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')).$s;
	$port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);
	return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.(str_replace(array(basename(cleanurl($_SERVER['PHP_SELF'])),$aidlink,str_replace('../','',INFUSIONS.'gr_radiostatus_panel/')), '', $_SERVER['REQUEST_URI']));
}

if(function_exists('fsockopen')) {
	if (is_writable('cache')) {
		if (isset($_GET['delete'])) {
			$data = dbarray(dbquery("SELECT rs_order FROM ".DB_GR_RADIOSTATUS." WHERE rs_id='".$_GET['delete']."'"));
			$result = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_order=rs_order-1 WHERE rs_order>'".$data['rs_order']."'");	
			$result = dbquery("DELETE FROM ".DB_GR_RADIOSTATUS." WHERE rs_id='".$_GET['delete']."'");
			$result = dbquery("DELETE FROM ".DB_GR_RADIOSTATUS_GRUSSBOX." WHERE rsgb_stream='".$_GET['delete']."'");
			$result = dbquery("DELETE FROM ".DB_GR_RADIOSTATUS_ACTION." WHERE rsa_stream='".$_GET['delete']."'");
			rs_make_cache();
			redirect(FUSION_SELF.$aidlink);
		} elseif (isset($_GET['up']) && isset($_GET['order'])) {
			if ($_GET['order'] > 0) {
				$data = dbarray(dbquery("SELECT rs_id FROM ".DB_GR_RADIOSTATUS." WHERE rs_order='".$_GET['order']."'"));
				$result = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_order=rs_order+1 WHERE rs_id='".$data['rs_id']."'");
				$result = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_order=rs_order-1 WHERE rs_id='".$_GET['up']."'");
			}
			redirect(FUSION_SELF.$aidlink);
		} elseif (isset($_GET['down']) && isset($_GET['order'])) {
			$link_order = dbresult(dbquery("SELECT MAX(rs_order) FROM ".DB_GR_RADIOSTATUS), 0) + 1;
			if ($_GET['order'] < $link_order) {
				$data = dbarray(dbquery("SELECT rs_id FROM ".DB_GR_RADIOSTATUS." WHERE rs_order='".$_GET['order']."'"));
				$result = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_order=rs_order-1 WHERE rs_id='".$data['rs_id']."'");
				$result = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET rs_order=rs_order+1 WHERE rs_id='".$_GET['down']."'");
			}
			redirect(FUSION_SELF.$aidlink);
		} 
		elseif (isset($_GET['new']) OR isset($_GET['edit'])) 
		{
			if (isset($_GET['edit'])) 
			{
				$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." WHERE rs_id='".$_GET['edit']."'");
				if (dbrows($result)) {
					$data = dbarray($result);
					$server_typ = $data['rs_server_typ'];
					$server_id = ' value="'.$data['rs_server_id'].'"';
					$name = ' value="'.$data['rs_name'].'"';
					$ip = ' value="'.$data['rs_ip'].'"';
					$port = ' value="'.$data['rs_port'].'"';
					$transip = ' value="'.$data['rs_transip'].'"';
					$interval = $data['rs_gbintervall'];
					$transport = ' value="'.$data['rs_transport'].'"';
					$transpw = ' value="'.$data['rs_transpw'].'"';
					$ps = ' value="'.$data['rs_ps'].'"';
					$tele = nl2br(stripslashes($data['rs_tele']));
					$flash = ' value="'.$data['rs_flash'].'"';
					$usertyp = $data['rs_usertyp'];
					$stream_center = $data['rs_streamcenter'];
					$status = $data['rs_status'];
					$status_gb = $data['rs_status_gb'];
					$status_gb_dj = $data['rs_status_gb_dj'];
					$status_boxen = $data['rs_status_boxen'];
					$gb_popup = $data['rs_gb_popup'];
					$theme = $data['rs_theme'];
					$panel = $data['rs_panel'];
					$access = $data['rs_access'];
					$access_l = $data['rs_access_l'];
					$access_a = $data['rs_access_a'];
					$access_gb = $data['rs_access_gb'];
					$access_gb_user = $data['rs_access_gb_user'];
				} else {
					redirect(FUSION_SELF.$aidlink);
				}
			} else {
				$server_typ = '0'; 
				$server_id = ' value="1"'; 
				$name = ''; 
				$ip = ''; 
				$port = ''; 
				$transip = '';
				$interval = '60';
				$transport = '7999'; 
				$transpw = '';
				$ps = ''; 
				$tele = ''; 
				$flash = ''; 
				$usertyp = '1'; 
				$status = '0'; 
				$status_gb = '0';
				$status_gb_dj = '0'; 
				$status_boxen = '0';
				$stream_center = '0'; 
				$gb_popup = '0'; 
				$theme = '1'; 
				$panel = '1'; 
				$access = '0'; 
				$access_l = '102'; 
				$access_a = '102'; 
				$access_gb = '102'; 
				$access_gb_user = '0';
			}
			if (isset($_POST['save'])) 
			{
				$rs_server_typ = (isset($_POST['server_typ']) && isnum($_POST['server_typ']) ? $_POST['server_typ'] : '0');
				$rs_server_id = (isset($_POST['server_id']) && isnum($_POST['server_id']) ? $_POST['server_id'] : '1');
				$rs_name = (isset($_POST['name']) ? stripinput($_POST['name']) : (isset($_GET['edit']) ? $data['rs_name'] : ''));
				$rs_ip = (isset($_POST['ip']) ? stripinput($_POST['ip']) : (isset($_GET['edit']) ? $data['rs_ip'] : ''));
				$rs_port = (isset($_POST['port']) ? stripinput($_POST['port']) : (isset($_GET['edit']) ? $data['rs_port'] : ''));
				
				$rs_transip = (isset($_POST['transip']) ? stripinput($_POST['transip']) : (isset($_GET['edit']) ? $data['rs_transip'] : ''));
				$rs_transport = (isset($_POST['transport']) ? stripinput($_POST['transport']) : (isset($_GET['edit']) ? $data['rs_transport'] : ''));
				$rs_transpw = (isset($_POST['transpw']) && $_POST['transpw'] != '' ? stripinput($_POST['transpw']) : (isset($_GET['edit']) ? $data['rs_transpw'] : ''));
				
				$rs_pw = (isset($_POST['pw']) && $_POST['pw'] != '' ? stripinput($_POST['pw']) : (isset($_GET['edit']) ? $data['rs_pw'] : ''));
				$rs_apw = (isset($_POST['apw']) && $_POST['apw'] != '' ? trim(stripinput($_POST['apw'])) : (isset($_GET['edit']) ? $data['rs_apw'] : ''));
				$rs_ps = (isset($_POST['ps']) ? stripinput($_POST['ps']) : '');
				$rs_tele = (isset($_POST['tele']) ? addslash($_POST['tele']) : '');
				$rs_flash = (isset($_POST['flash']) ? stripinput($_POST['flash']) : '');
				$rs_usertyp = (isset($_POST['usertyp']) && isnum($_POST['usertyp']) ? $_POST['usertyp'] : '1');
				$rs_status = (isset($_POST['status']) && isnum($_POST['status']) ? $_POST['status'] : '0');
				$rs_status_gb = (isset($_POST['status_gb']) && isnum($_POST['status_gb']) ? $_POST['status_gb'] : '0');
				$rs_gbintervall = (isset($_POST['gb_timelimit']) && isnum($_POST['gb_timelimit']) ? $_POST['gb_timelimit'] : '60');
				$rs_status_gb_dj = (isset($_POST['status_gb_dj']) && isnum($_POST['status_gb_dj']) ? $_POST['status_gb_dj'] : '0');
				$rs_stream_center = (isset($_POST['streamcenter']) && isnum($_POST['streamcenter']) ? $_POST['streamcenter'] : '0');
				$rs_status_boxen = (isset($_POST['status_boxen']) && isnum($_POST['status_boxen']) ? $_POST['status_boxen'] : '0');
				$rs_gb_popup = (isset($_POST['gb_popup']) && isnum($_POST['gb_popup']) ? $_POST['gb_popup'] : '0');
				$rs_theme = (isset($_POST['theme']) ? stripinput($_POST['theme']) : '1');
				$rs_theme = str_replace(array('side_','misc_','main_','.php'), '', $rs_theme);
				$rs_theme = (isnum($rs_theme) ? $rs_theme : '1');
				$rs_panel = (isset($_POST['panel']) && isnum($_POST['panel']) ? $_POST['panel'] : '1');
				$rs_access = (isset($_POST['access']) && isnum($_POST['access']) ? $_POST['access'] : '0');
				$rs_access_l = (isset($_POST['access_l']) && isnum($_POST['access_l']) ? $_POST['access_l'] : '102');
				$rs_access_a = (isset($_POST['access_a']) && isnum($_POST['access_a']) ? $_POST['access_a'] : '102');
				$rs_access_gb = (isset($_POST['access_gb']) && isnum($_POST['access_gb']) ? $_POST['access_gb'] : '102');
				$rs_access_gb_user = (isset($_POST['access_gb_user']) && isnum($_POST['access_gb_user']) ? $_POST['access_gb_user'] : '0');
				
				if ($rs_ip != '' && $rs_port != '' && $rs_pw != '') {
					if (isset($_GET['edit'])) {
						$result_up = dbquery("UPDATE ".DB_GR_RADIOSTATUS." SET 
						rs_server_typ='".$rs_server_typ."', 
						rs_server_id='".$rs_server_id."', 
						rs_name='".$rs_name."', 
						rs_ip='".$rs_ip."', 
						rs_port='".$rs_port."', 
						rs_transip='".$rs_transip."', 
						rs_transport='".$rs_transport."',
						rs_pw='".$rs_pw."',  
						rs_transpw='".$rs_transpw."', 
						rs_apw='".$rs_apw."', 
						rs_ps='".$rs_ps."', 
						rs_tele='".$rs_tele."', 
						rs_flash='".$rs_flash."', 
						rs_usertyp='".$rs_usertyp."', 
						rs_status='".$rs_status."',
						rs_gbintervall='".$rs_gbintervall."', 
						rs_status_gb='".$rs_status_gb."',
						rs_status_gb_dj='".$rs_status_gb_dj."', 
						rs_status_boxen='".$rs_status_boxen."', 
						rs_gb_popup='".$rs_gb_popup."', 
						rs_theme='".$rs_theme."', 
						rs_panel='".$rs_panel."', 
						rs_access='".$rs_access."', 
						rs_access_l='".$rs_access_l."',
						rs_streamcenter='".$rs_stream_center."',
						rs_access_a='".$rs_access_a."', 
						rs_access_gb='".$rs_access_gb."', 
						rs_access_gb_user='".$rs_access_gb_user."' 
						WHERE rs_id='".$_GET['edit']."'");
					} else {
						$order = dbresult(dbquery("SELECT MAX(rs_order) FROM ".DB_GR_RADIOSTATUS), 0) + 1;
						$result_in = dbquery("INSERT INTO ".DB_GR_RADIOSTATUS." (
						rs_server_typ,
						rs_server_id,
						rs_name, 
						rs_ip, 
						rs_port, 
						rs_transip, 
						rs_transport, 
						rs_pw, 
						rs_transpw, 
						rs_apw, 
						rs_ps, 
						rs_tele, 
						rs_flash, 
						rs_cache, 
						rs_usertyp, 
						rs_status,
						rs_gbintervall,
						rs_status_gb, 
						rs_status_gb_dj, 
						rs_status_boxen, 
						rs_gb_popup, 
						rs_theme, 
						rs_panel, 
						rs_order, 
						rs_access, 
						rs_access_l,
						rs_streamcenter, 
						rs_access_a, 
						rs_access_gb, 
						rs_access_gb_user) 
						
						VALUES (
						'".$rs_server_typ."',
						'".$rs_server_id."',
						'".$rs_name."',
						'".$rs_ip."',
						'".$rs_port."',
						'".$rs_transip."',
						'".$rs_transport."',
						'".$rs_pw."',
						'".$rs_transpw."',
						'".$rs_apw."',
						'".$rs_ps."',
						'".$rs_tele."',
						'".$rs_flash."',
						'".rand(100000, 1000000000)."',
						'".$rs_usertyp."',
						'".$rs_status."',
						'".$rs_gbintervall."',
						'".$rs_status_gb."',
						'".$rs_status_gb_dj."',
						'".$rs_status_boxen."',
						'".$rs_gb_popup."',
						'".$rs_theme."',
						'".$rs_panel."',
						'".$order."',
						'".$rs_access."',
						'".$rs_access_l."',
						'".$rs_stream_center."',
						'".$rs_access_a."',
						'".$rs_access_gb."',
						'".$rs_access_gb_user."')
						");
					}
					rs_make_cache();
				}
				redirect(FUSION_SELF.$aidlink);
			}
			opentable($locale['grrsa_01']);
			if (isset($_GET['edit'])) {
				echo '<form method="post" action="'.FUSION_SELF.$aidlink.'&amp;edit='.$_GET['edit'].'">';
			} else {
				echo '<form method="post" action="'.FUSION_SELF.$aidlink.'&amp;new">';
			}

			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">
			<tr>
				<td class="tbl1" width="50%">'.$locale['grrsa_38'].'</td>
				<td class="tbl1" width="50%"><input type="text" name="name" class="textbox" style="width:200px;"'.$name.' /></td>
			</tr>
						<tr>
				<td class="tbl1">'.$locale['grrsa_51'].'</td>
				<td class="tbl1"><select name="usertyp" class="textbox" style="width:208px;">
				<option value="1"'.($usertyp == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_60'].'</option>
				<option value="2"'.($usertyp == 2 ? ' selected="selected"' : '').'>'.$locale['grrsa_61'].'</option>
				<option value="3"'.($usertyp == 3 ? ' selected="selected"' : '').'>'.$locale['grrsa_62'].'</option>
				<option value="4"'.($usertyp == 4 ? ' selected="selected"' : '').'>'.$locale['grrsa_63'].'</option>
				<option value="5"'.($usertyp == 5 ? ' selected="selected"' : '').'>'.$locale['grrsa_64'].'</option>
				</select>
			</td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsg_102'].'</td>
				<td class="tbl1"><select name="server_typ" class="textbox" style="width:208px;">
				<option value="0"'.($server_typ == 0 ? ' selected="selected"' : '').'>'.$locale['grrsg_103'].'</option>
				<option value="1"'.($server_typ == 1 ? ' selected="selected"' : '').'>'.$locale['grrsg_104'].'</option>
				<option value="3"'.($server_typ == 3 ? ' selected="selected"' : '').'>'.$locale['grrsg_109'].'</option>
				</select>
				</td>
			</tr>
			<tr>
				<td class="tbl1" width="50%">'.$locale['grrsg_105'].'</td>
				<td class="tbl1" width="50%"><input type="text" name="server_id" class="textbox" style="width:200px;"'.$server_id.' /></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_39'].'</td>
				<td class="tbl1"><input type="text" name="ip" class="textbox" style="width:200px;"'.$ip.' /> <span style="color: red;">*</span></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_40'].'</td>
				<td class="tbl1"><input type="text" name="port" class="textbox" style="width:200px;"'.$port.' /> <span style="color: red;">*</span></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_41'].'</td>
				<td class="tbl1"><input type="password" name="pw" class="textbox" style="width:200px;" /> <span style="color: red;">*</span></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_42'].'</td>
				<td class="tbl1"><input type="password" name="apw" class="textbox" style="width:200px;" /></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_91'].'</td>
				<td class="tbl1"><select name="status_boxen" class="textbox" style="width:208px;"><option value="1"'.($status_boxen == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option><option value="0"'.($status_boxen == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option></select></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_57'].'</td>
				<td class="tbl1"><select name="status" class="textbox" style="width:208px;"><option value="1"'.($status == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option><option value="0"'.($status == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option></select></td>
			</tr>
			</table>';
			closetable();

			opentable("Transcast-API");			
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">
			<tr>
				<td class="tbl1">'.$locale['grrsa_40'].'</td>
				<td class="tbl1"><input type="text" name="transport" class="textbox" style="width:200px;"'.$transport.' /> <span style="color: red;">*</span></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_41'].'</td>
				<td class="tbl1"><input type="password" name="transpw" class="textbox" style="width:200px;"'.$transpw.' /> <span style="color: red;">*</span></td>
			</tr></table>';
			closetable();

			opentable($locale['grrsa_43']);
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">

			<tr>
				<td class="tbl1">'.$locale['grrsa_44'].'</td>
				<td class="tbl1"><select name="status_gb" class="textbox" style="width:208px;">
				<option value="1"'.($status_gb == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option>
				<option value="0"'.($status_gb == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option>
				</select>
			</td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_95'].'</td>
				<td class="tbl1"><select name="status_gb_dj" class="textbox" style="width:208px;">
				<option value="1"'.($status_gb_dj == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option>
				<option value="0"'.($status_gb_dj == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option>
				</select>
			</td>
			</tr>
			<tr>
				<td class="tbl1">Zeitlimit zwischen den Einsendungen (Minuten)</td>
				<td class="tbl1"><select name="gb_timelimit" class="textbox" style="width:208px;">';
				
				for ($i = 1; $i <= 60; $i++)
				{
					$limit = $i * 60;
					echo '<option value="'.$limit.'"'.($limit == $interval ? ' selected="selected"' : '').'>'.$i.'</option>';
				}
				
				
				echo '</select>
			</td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_90'].'</td>
				<td class="tbl1"><select name="gb_popup" class="textbox" style="width:208px;">
				<option value="1"'.($gb_popup == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option>
				<option value="0"'.($gb_popup == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option>
				</select>
				</td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_94'].'</td>
				<td class="tbl1"><select name="access_gb_user" class="textbox" style="width:208px;">'.rs_access('1', $access_gb_user).'</select></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_45'].'</td>
				<td class="tbl1"><select name="access_gb" class="textbox" style="width:208px;">'.rs_access('0', $access_gb).'</select></td>
			</tr></table>';
			closetable();

			opentable("Stream Center");
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">

			<tr>
				<td class="tbl1">Zugriff erlauben</td>
				<td class="tbl1"><select name="streamcenter" class="textbox" style="width:208px;">
				<option value="1"'.($stream_center == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_58'].'</option>
				<option value="0"'.($stream_center == 0 ? ' selected="selected"' : '').'>'.$locale['grrsa_59'].'</option>
				</select>
				</td>
			</tr></table>';
			closetable();
		
			opentable($locale['grrsa_46']);
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">
			<tr>
				<td class="tbl1">'.$locale['grrsa_47'].'</td>
				<td class="tbl1"><input type="text" name="ps" class="textbox" style="width:200px;"'.$ps.' /></td>
			</tr>
			<tr>
				<td class="tbl1" valign="top">'.$locale['grrsa_48'].'</td>
				<td class="tbl1"><textarea name="tele" cols="30" rows="5" class="textbox" style="width:350px;">'.$tele.'</textarea></td>
			</tr>
			<tr>
				<td class="tbl1" valign="top">'.$locale['grrsa_49'].'</td>
				<td class="tbl1"><input type="text" name="flash" class="textbox" style="width:200px;"'.$flash.' /></td>
			</tr>
			<tr></table>';
			closetable();
		
			opentable("Sonstiges");
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border"  align="center">

			<tr>
				<td class="tbl1">'.$locale['grrsa_52'].'</td>
				<td class="tbl1"><select name="access" class="textbox" style="width:208px;">'.rs_access('1', $access).'</select></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_53'].'</td>
				<td class="tbl1"><select name="access_l" class="textbox" style="width:208px;">'.rs_access('0', $access_l).'</select></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_54'].'</td>
				<td class="tbl1"><select name="access_a" class="textbox" style="width:208px;">'.rs_access('0', $access_a).'</select></td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_55'].'</td>
				<td class="tbl1"><select id="panel" name="panel" class="textbox" style="width:208px;">
				<option value="1"'.($panel == 1 ? ' selected="selected"' : '').'>'.$locale['grrsa_66'].'</option>
				<option value="2"'.($panel == 2 ? ' selected="selected"' : '').'>'.$locale['grrsa_67'].'</option>
				<option value="3"'.($panel == 3 ? ' selected="selected"' : '').'>'.$locale['grrsa_68'].'</option>
				<option value="4"'.($panel == 4 ? ' selected="selected"' : '').'>'.$locale['grrsa_69'].'</option>
				<option value="5"'.($panel == 5 ? ' selected="selected"' : '').'>'.$locale['grrsa_78'].'</option>
				<option value="6"'.($panel == 6 ? ' selected="selected"' : '').'>'.$locale['grrsg_106'].'</option>
				</select>
				</td>
			</tr>
			<tr>
				<td class="tbl1">'.$locale['grrsa_56'].'</td>
				<td class="tbl1"><select name="theme" class="textbox" style="width:208px;"><option>'.$locale['grrsa_70'].'</option>'."\n";
				$theme_array = rs_check_theme();
				$theme_typ = array(1 => 'side_', 2 => 'main_', 3 => 'main_', 4 => 'side_', 5 => 'misc_', 6 => 'misc2_');
				add_to_head('<style type="text/css">.rs_side{display:'.($panel == 1 || $panel == 4 ? 'block' : 'none').';}.rs_main{display:'.($panel == 2 || $panel == 3 ? 'block' : 'none').';}.rs_misc{display:'.($panel == 5 ? 'block' : 'none').';.rs_misc2{display:'.($panel == 6 ? 'block' : 'none').';}</style>');
				foreach ($theme_array as $value) {
					$id = str_replace(array('side_','misc_','misc2_','main_','.php'), '', $value);
					if (preg_match("/side_/i", $value)) {
						$class = 'rs_side';
					} elseif (preg_match("/main_/i", $value)) {
						$class = 'rs_main';
					} elseif (preg_match("/misc2_/i", $value)) {
						$class = 'rs_misc2';
					} else {
						$class = 'rs_misc';
					}
					echo '<option class="'.$class.'" value="'.$value.'"'.(preg_match("/".$theme_typ[$panel]."/i", $value) && $theme == $id ? ' selected="selected"' : '').'>'.str_replace(array('side_','main_','misc_','.php'), array($locale['grrsa_71'],$locale['grrsa_72'],$locale['grrsa_73'],''), $value).'</option>'."\n";
				}
				echo '</select> [<a href="http://www.granade.eu/supportforum/viewthread_5.html">'.$locale['grrsg_101'].'</a>]</td>
			</tr>
			<tr>
				<td class="tbl2" colspan="2" align="center"><input type="submit" name="save" class="button" value="'.$locale['grrsa_74'].'" /></td>
			</tr>
			</table>
			</form>
			<a href="'.FUSION_SELF.$aidlink.'">'.$locale['grrsa_36'].'</a>';
			closetable();
			add_to_head('<script type="text/javascript">
			$(document).ready(function(){
				$("#panel").change(function () {
					var panel_sel = $("#panel option:selected");
					if(panel_sel.val() == 1 || panel_sel.val() == 4){
						$("option.rs_side").css("display","block");
						$("option.rs_main").css("display","none");
						$("option.rs_misc").css("display","none");
						$("option.rs_misc2").css("display","none");
					}
					if(panel_sel.val() == 2 || panel_sel.val() == 3){
						$("option.rs_side").css("display","none");
						$("option.rs_main").css("display","block");
						$("option.rs_misc").css("display","none");
						$("option.rs_misc2").css("display","none");
					}
					if(panel_sel.val() == 5){
						$("option.rs_side").css("display","none");
						$("option.rs_main").css("display","none");
						$("option.rs_misc").css("display","block");
						$("option.rs_misc2").css("display","none");
					}
					if(panel_sel.val() == 6){
						$("option.rs_side").css("display","none");
						$("option.rs_main").css("display","none");
						$("option.rs_misc").css("display","none");
						$("option.rs_misc2").css("display","block");
					}
				});
			});
			</script>');
		} elseif (isset($_GET['log'])) {
			if (isset($_GET['del'])) {
				if (isnum($_GET['log'])) {
					$result = dbquery("DELETE FROM ".DB_GR_RADIOSTATUS_ACTION." WHERE rsa_stream='".$_GET['log']."'");
				} else {
					$result = dbquery("TRUNCATE TABLE ".DB_GR_RADIOSTATUS_ACTION);
				}
				redirect(FUSION_SELF.$aidlink.'&log');
			}
			opentable($locale['grrsa_01'].$locale['grrsa_27']);
			$result = dbquery("SELECT ra.*, ru.user_name, rs.rs_name 
			FROM ".DB_GR_RADIOSTATUS_ACTION." ra 
			INNER JOIN ".DB_USERS." ru ON ru.user_id=ra.rsa_user_id 
			INNER JOIN ".DB_GR_RADIOSTATUS." rs ON rs.rs_id=ra.rsa_stream 
			".(isnum($_GET['log']) ? 'WHERE rsa_stream="'.$_GET['log'].'" ' : '')."ORDER BY rsa_time");
			if (dbrows($result)) {
				echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border" align="center">
				<tr>
					<td class="tbl2" width="20%">'.$locale['grrsa_28'].'</td>
					<td class="tbl2" width="20%">'.$locale['grrsa_29'].'</td>
					<td class="tbl2" width="20%">'.$locale['grrsa_30'].'</td>
					<td class="tbl2" width="20%">'.$locale['grrsa_31'].'</td>
				</tr>';
				while ($data = dbarray($result)) {
					echo '<tr>
						<td class="tbl1" width="25%"><a href="'.FUSION_SELF.$aidlink.'&amp;log='.$data['rsa_stream'].'">'.$data['rs_name'].'</a></td>
						<td class="tbl1" width="25%"><a href="'.BASEDIR.'profile.php?lookup='.$data['rsa_user_id'].'">'.$data['user_name'].'</a></td>
						<td class="tbl1" width="25%">'.($data['rsa_sction'] == 1 ? $locale['grrsa_32'] : $locale['grrsa_33']).'</td>
						<td class="tbl1" width="25%">'.showdate('longdate', $data['rsa_time']).'</td>
					</tr>';
				}
				echo '<tr>
					<td class="tbl2" colspan="4" align="center"><a href="'.FUSION_SELF.$aidlink.'&amp;log'.(isnum($_GET['log']) ? '='.$_GET['log'] :'').'&amp;del">'.$locale['grrsa_34'].'</a></td>
				</tr>
				</table>';
			} else {
				echo '<div align="center"><div class="tbl2" style="width:80%;" align="center">'.$locale['grrsa_35'].'</div></div><br />';
			}
			echo '<a href="'.FUSION_SELF.$aidlink.'">'.$locale['grrsa_36'].'</a>';
		} elseif (isset($_POST['settings'])) {
			function set_settings($name, $value) {
				$set_result = dbquery("SELECT settings_name FROM ".DB_SETTINGS_INF." WHERE settings_name='".$name."' AND settings_inf='gr_radiostatus_panel'");
				if (dbrows($set_result)) {
					$up_result = dbquery("UPDATE ".DB_SETTINGS_INF." SET settings_value='".$value."' WHERE settings_name='".$name."' AND settings_inf='gr_radiostatus_panel'");
				} else {
					$in_result = dbquery("INSERT INTO ".DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('".$name."', '".$value."', 'gr_radiostatus_panel')");
				}
			}
			foreach($_POST as $name => $value) {
				if (array_key_exists($name,$gr_radiostatus_settings)) {
					set_settings(stripinput($name),stripinput($value));
				}
			}
			redirect(FUSION_SELF.$aidlink);
		} else {
			if (getCurrentURL() != $settings['siteurl']) {
				echo '<div class="admin-message">'.$locale['grrsg_107'].'<br / >Debug: (got url):  '.getCurrentURL().'</div>';
			}
			opentable($locale['grrsa_01']);
			echo '<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border" align="center">
			<tr>
				<td class="tbl1" width="5%">'.$locale['grrsa_02'].'</td>
				<td class="tbl1" width="25%">'.$locale['grrsa_03'].'</td>
				<td class="tbl1" width="15%">'.$locale['grrsa_04'].'</td>
				<td class="tbl1" width="5%">'.$locale['grrsa_05'].'</td>
				<td class="tbl1" width="10%">'.$locale['grrsa_06'].'</td>
				<td class="tbl1" width="10%">'.$locale['grrsa_07'].'</td>
				<td class="tbl1" width="10%">'.$locale['grrsa_08'].'</td>
				<td class="tbl1" width="5%">'.$locale['grrsa_09'].'</td>
				<td class="tbl1" width="5%">'.$locale['grrsa_10'].'</td>
				<td class="tbl1" width="10%"></td>
			</tr>';
			$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." ORDER BY rs_order, rs_panel");
			if (dbrows($result)) {
				$i=1;
				while ($data = dbarray($result)) {
					echo '<tr>
						<td class="tbl2">'.$data['rs_id'].'</td>
						<td class="tbl2">'.$data['rs_name'].'</td>
						<td class="tbl2">'.$data['rs_ip'].'</td>
						<td class="tbl2">'.$data['rs_port'].'</td>
						<td class="tbl2">';
						if ($data['rs_usertyp'] == 1) {
							echo $locale['grrsa_11'];
						} elseif ($data['rs_usertyp'] == 2) {
							echo $locale['grrsa_12'];
						} elseif ($data['rs_usertyp'] == 3) {
							echo $locale['grrsa_13'];
						} elseif ($data['rs_usertyp'] == 4) {
							echo $locale['grrsa_14'];
						} elseif ($data['rs_usertyp'] == 5) {
							echo $locale['grrsa_15'];
						} else {
							echo $locale['grrsa_11'];
						}
						echo '</td>
						<td class="tbl2">';
						if ($data['rs_access'] == 0) {
							echo $locale['user0'];
						} elseif ($data['rs_access'] == 101) {
							echo $locale['user1'];
						} elseif ($data['rs_access'] == 102) {
							echo $locale['user2'];
						} elseif ($data['rs_access'] == 103) {
							echo $locale['user3'];
						} else {
							$group_result = dbquery("SELECT group_name FROM ".DB_USER_GROUPS." WHERE group_id='".$data['rs_access']."'");
							if (dbrows($group_result)) {
								$group_data = dbarray($group_result);
								if (checkrights('UG')) {
									echo '<a href="'.ADMIN.'user_groups.php'.$aidlink.'&amp;group_id='.$data['rs_access'].'">'.$group_data['group_name'].'</a>';
								} else {
									echo $group_data['group_name'];
								}
							} else {
								echo $locale['grrsa_04'];
							}
						}
						echo '</td>
						<td class="tbl2">';
						if ($data['rs_panel'] == 0) {
							echo $locale['grrsa_16'];
						} elseif ($data['rs_panel'] == 1) {
							echo $locale['grrsa_17'];
						} elseif ($data['rs_panel'] == 2) {
							echo $locale['grrsa_18'];
						} elseif ($data['rs_panel'] == 3) {
							echo $locale['grrsa_19'];
						} elseif ($data['rs_panel'] == 4) {
							echo $locale['grrsa_20'];
						} else {
							echo $locale['grrsa_21'];
						}
						echo '</td>
						<td class="tbl2">';
						if (1 < dbrows($result)) {
							$up = $data['rs_order'] - 1;
							$down = $data['rs_order'] + 1;
							if ($i==1) {
								echo '<a href="'.FUSION_SELF.$aidlink.'&amp;down='.$data['rs_id'].'&amp;order='.$down.'"><img src="'.get_image('down').'" alt="down" style="border:0px;" /></a>';
							} elseif ($i < dbrows($result)) {
								echo '<a href="'.FUSION_SELF.$aidlink.'&amp;up='.$data['rs_id'].'&amp;order='.$up.'"><img src="'.get_image('up').'" alt="up" style="border:0px;" /></a>';
								echo '<a href="'.FUSION_SELF.$aidlink.'&amp;down='.$data['rs_id'].'&amp;order='.$down.'"><img src="'.get_image('down').'" alt="down" style="border:0px;" /></a>';
							} else {
								echo '<a href="'.FUSION_SELF.$aidlink.'&amp;up='.$data['rs_id'].'&amp;order='.$up.'"><img src="'.get_image('up').'" alt="up" style="border:0px;" /></a>';
							}
						} else {
							echo '-';
						}
						echo '</td>
						<td class="tbl2">';
						if ($data['rs_status']) {
							echo '<img src="'.RADIOSTATUS.'images/check.png" alt="check" />';
						} else {
							echo '<img src="'.RADIOSTATUS.'images/uncheck.png" alt="uncheck" />';
						}
						echo '</td>
						<td class="tbl2"><a href="'.FUSION_SELF.$aidlink.'&amp;log='.$data['rs_id'].'">'.$locale['grrsa_26'].'</a><br /><a href="'.FUSION_SELF.$aidlink.'&amp;edit='.$data['rs_id'].'">'.$locale['grrsa_22'].'</a><br /><a href="'.FUSION_SELF.$aidlink.'&amp;delete='.$data['rs_id'].'">'.$locale['grrsa_23'].'</a></td>
					</tr>';
					$i++;
				}
			} else {
				echo '<tr>
					<td class="tbl1" colspan="10">'.$locale['grrsa_25'].'</td>
				</tr>';
			}
			echo '<tr>
				<td class="tbl1" colspan="9"></td>
				<td class="tbl2"><a href="'.FUSION_SELF.$aidlink.'&amp;new">'.$locale['grrsa_24'].'</a></td>
			</tr>
			</table>';
			
			if (defined('DB_SETTINGS_INF')) {
				$date_opts = "<option value=''>".$locale['grrsg_95']."</option>\n";
				$date_opts .= "<option value='%m/%d/%Y'>".strftime("%m/%d/%Y")."</option>\n";
				$date_opts .= "<option value='%d/%m/%Y'>".strftime("%d/%m/%Y")."</option>\n";
				$date_opts .= "<option value='%d-%m-%Y'>".strftime("%d-%m-%Y")."</option>\n";
				$date_opts .= "<option value='%d.%m.%Y'>".strftime("%d.%m.%Y")."</option>\n";
				$date_opts .= "<option value='%m/%d/%Y %H:%M'>".strftime("%m/%d/%Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%d/%m/%Y %H:%M'>".strftime("%d/%m/%Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%d-%m-%Y %H:%M'>".strftime("%d-%m-%Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%d.%m.%Y %H:%M'>".strftime("%d.%m.%Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%m/%d/%Y %H:%M:%S'>".strftime("%m/%d/%Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%d/%m/%Y %H:%M:%S'>".strftime("%d/%m/%Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%d-%m-%Y %H:%M:%S'>".strftime("%d-%m-%Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%d.%m.%Y %H:%M:%S'>".strftime("%d.%m.%Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%B %d %Y'>".strftime("%B %d %Y")."</option>\n";
				$date_opts .= "<option value='%d. %B %Y'>".strftime("%d. %B %Y")."</option>\n";
				$date_opts .= "<option value='%d %B %Y'>".strftime("%d %B %Y")."</option>\n";
				$date_opts .= "<option value='%B %d %Y %H:%M'>".strftime("%B %d %Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%d. %B %Y %H:%M'>".strftime("%d. %B %Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%d %B %Y %H:%M'>".strftime("%d %B %Y %H:%M")."</option>\n";
				$date_opts .= "<option value='%B %d %Y %H:%M:%S'>".strftime("%B %d %Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%d. %B %Y %H:%M:%S'>".strftime("%d. %B %Y %H:%M:%S")."</option>\n";
				$date_opts .= "<option value='%d %B %Y %H:%M:%S'>".strftime("%d %B %Y %H:%M:%S")."</option>\n";
				closetable();
				opentable($locale['grrsa_80']);
				echo '<form method="post" action="'.FUSION_SELF.$aidlink.'">
				<table cellpadding="1" cellspacing="1" width="100%" class="tbl-border" align="center">
				<tr>
					<td class="tbl1" width="50%">'.$locale['grrsa_82'].'</td>
					<td class="tbl1" width="50%"><input type="text" name="reload_cache" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_cache'].'" /> '.$locale['grrsa_92'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_83'].'</td>
					<td class="tbl1"><input type="text" name="reload_cache_extern" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_cache_extern'].'" /> '.$locale['grrsa_92'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_84'].'</td>
					<td class="tbl1"><input type="text" name="reload_main" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_main'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_85'].'</td>
					<td class="tbl1"><input type="text" name="reload_side" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_side'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_86'].'</td>
					<td class="tbl1"><input type="text" name="reload_misc" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_misc'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_93'].'</td>
					<td class="tbl1"><input type="text" name="reload_extern" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_extern'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_87'].'</td>
					<td class="tbl1"><input type="text" name="reload_gb_admin" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_gb_admin'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_108'].'</td>
					<td class="tbl1"><input type="text" name="reload_listners" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['reload_listners'].'" /> '.$locale['grrsa_81'].'</td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsg_97'].'</td>
					<td class="tbl1"><input type="text" name="title_delete" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['title_delete'].'" /> '.$locale['grrsg_98'].'</td>
				</tr>
				<tr>
					<td class="tbl1" valign="top">'.$locale['grrsg_96'].'</td>
					<td class="tbl1"><select name="gb_timetext" class="textbox" style="width:208px;">'.$date_opts.'</select><br />
						<input type="button" name="setgb_time" value=">>" onclick=\'gb_time.value=gb_timetext.options[gb_timetext.selectedIndex].value;gb_timetext.selectedIndex=0;\' class="button" />
						<input type="text" name="gb_time" value="'.$gr_radiostatus_settings['gb_time'].'" maxlength="50" class="textbox" style="width:180px;" /></td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_88'].'</td>
					<td class="tbl1"><input type="text" name="buy_link" class="textbox" style="width:200px;" value="'.$gr_radiostatus_settings['buy_link'].'" /></td>
				</tr>
				<tr>
					<td class="tbl1">'.$locale['grrsa_89'].'</td>
					<td class="tbl1"><select name="buy_pic" class="textbox" style="width:208px;">'.makefileopts(makefilelist(RADIOSTATUS.'images/', '.|..|index.php'), $gr_radiostatus_settings['buy_pic']).'</select></td>
				</tr>
				<tr>
					<td class="tbl2" colspan="2" align="center"><input type="submit" name="settings" class="button" value="'.$locale['grrsa_74'].'" /></td>
				</tr>
				</table>
				</form>';
			}
			closetable();
			opentable($locale['grrsg_99']);
			echo $locale['grrsg_100'];
		}
	} else {
		opentable($locale['grrsa_01']);
		echo '<div align="center>'.$locale['grrsa_77'].'</div>';
	}
} else {
	opentable($locale['grrsa_01']);
	echo "<div align='center'>".$locale['grrsp_03']."</div>";
}
closetable();
opentable($locale['grrsa_75']);
echo '<div align="center">'.$locale['grrsa_76'].'<a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank"><img src="http://www.granade.eu/scripte/radiostatus_26.jpg" border="0" alt="Version-Check" /></a><br />'.$locale['grrsa_79'].'</div>';
/* Das entfernen des Copyright ist nicht erlaubt und ist Strafbar! */
echo '<div class="small2" align="center"><hr class="side-hr" /><a href="http://www.granade.eu/scripte/radiostatus.html" target="_blank">Radiostatus &copy;</a></div>';
closetable();

require_once THEMES.'templates/footer.php';
?>