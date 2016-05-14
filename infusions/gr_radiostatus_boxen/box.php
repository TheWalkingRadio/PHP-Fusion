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
require_once dirname(__FILE__).'/../../maincore.php';

header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

if (!defined('RADIOSTATUS')) {
	define('RADIOSTATUS', INFUSIONS.'gr_radiostatus_panel/');
}
	
if (!defined('RADIOSTATUS_SELF')) {
	define('RADIOSTATUS_SELF', $settings['siteurl'].str_replace('../', '', INFUSIONS).'gr_radiostatus_boxen/');
}

require_once RADIOSTATUS.'infusion_db.php';
if (file_exists(RADIOSTATUS.'locale/'.LOCALESET.'index.php')) {
	require RADIOSTATUS.'locale/'.LOCALESET.'index.php';
} else {
	require RADIOSTATUS.'locale/German/index.php';
}

function rs_player($id,$skin,$width,$height) {
	global $settings,$locale;
	return '<a href="'.$settings['siteurl'].str_replace('../', '', RADIOSTATUS).'gr_radiostatus_player.php?id='.$id.'&amp;p=pls" title="'.$locale['grrsi_02'].'"><img src="'.RADIOSTATUS_SELF.'skin_'.$skin.'/winamp.png" alt="'.$locale['grrsi_02'].'" border="0" width="'.$width.'" height="'.$height.'" /></a>&nbsp;&nbsp;
	<a href="'.$settings['siteurl'].str_replace('../', '', RADIOSTATUS).'gr_radiostatus_player.php?id='.$id.'&amp;p=asx" title="'.$locale['grrsi_03'].'"><img src="'.RADIOSTATUS_SELF.'skin_'.$skin.'/wmp.png" alt="'.$locale['grrsi_03'].'" border="0" width="'.$width.'" height="'.$height.'" /></a>&nbsp;&nbsp;
	<a href="'.$settings['siteurl'].str_replace('../', '', RADIOSTATUS).'gr_radiostatus_player.php?id='.$id.'&amp;p=ram" title="'.$locale['grrsi_04'].'"><img src="'.RADIOSTATUS_SELF.'skin_'.$skin.'/realplayer.png" alt="'.$locale['grrsi_04'].'" border="0" width="'.$width.'" height="'.$height.'" /></a><br />';
}

function rs_theme($info, $skin) {
	global $locale,$settings;
	ob_start();
	if ($info['status']) {
		if (file_exists(INFUSIONS.'gr_radiostatus_boxen/skin_'.$skin.'/index.php')) {
			require INFUSIONS.'gr_radiostatus_boxen/skin_'.$skin.'/index.php';
		}
	} else {
		if (file_exists(INFUSIONS.'gr_radiostatus_boxen/skin_'.$skin.'/offline.php')) {
			require INFUSIONS.'gr_radiostatus_boxen/skin_'.$skin.'/offline.php';
		}
	}
	$theme = ob_get_contents();
	ob_end_clean();
	if ($theme != '') {
		if ($info['status']) {
			if (!empty($gr_radiostatus_settings['buy_link'])) {
				$buy_link = '<a href="'.$gr_radiostatus_settings['buy_link'].htmlentities(urlencode($info['song'])).'" target="_blank"><img src="'.RADIOSTATUS_SELF.RADIOSTATUS.'images/'.$gr_radiostatus_settings['buy_pic'].'" alt="Jetzt kaufen" border="0" /></a>';
			} else {
				$buy_link = '';
			}
			$search = array('###rs_name###','###url###','###mod###','###mod_pic###','###player###','###gb###','###song###','###aim###','###icq###','###irc###','###genre###','###BUY_LINK###','###RADIOSTATUS_SELF###');
			$replace = array($info['rs_name'],$settings['siteurl'],trimlink($info['mod'],$config['mod_name']),$info['mod_pic'],rs_player($info['rs_id'],$skin,$config['player_pic_width'],$config['player_pic_height']),$info['gb'],$info['song'],$info['aim'],$info['icq'],$info['irc'],$info['genre'],$buy_link,RADIOSTATUS_SELF);
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

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".$locale['xml_lang']."' lang='".$locale['xml_lang']."'>\n";
echo "<head>\n<title>".$settings['sitename']."</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=".$locale['charset']."' />\n";
echo "<meta name='description' content='".$settings['description']."' />\n";
echo "<meta name='keywords' content='".$settings['keywords']."' />\n";
echo '<script type="text/javascript">
	window.setTimeout("location.reload()",'.$gr_radiostatus_settings['reload_extern'].');
</script>';
if (file_exists(IMAGES."favicon.ico")) { echo "<link rel='shortcut icon' href='".IMAGES."favicon.ico' type='image/x-icon' />\n"; }
echo "</head>\n<body style='padding:0; margin:0;'>\n";
if (!isset($_GET['id']) OR !isnum($_GET['id']) OR !isset($_GET['style'])) {
	echo 'Falscher Aufruf!';
} else {
	$skin = stripinput($_GET['style']);
	$result = dbquery("SELECT * FROM ".DB_GR_RADIOSTATUS." WHERE ".groupaccess("rs_access")." AND rs_id='".$_GET['id']."' AND rs_status='1' AND rs_status_boxen='1'");
	if (dbrows($result)) {
		$data = dbarray($result);
		if (!file_exists(RADIOSTATUS.'cache/stream_extern_'.$data['rs_id'].'_'.$data['rs_cache'].'.php') OR @filemtime(RADIOSTATUS.'cache/stream_extern_'.$data['rs_id'].'_'.$data['rs_cache'].'.php') <= (time()-$gr_radiostatus_settings['reload_cache_extern'])) {
			require_once RADIOSTATUS.'gr_radiostatus_class.php';
			$server = new SHOUTcast();
			$server->GetStatus(array(
					'rs_ip'					=> $data['rs_ip'],
					'rs_port'				=> $data['rs_port'],
					'rs_pw'					=> $data['rs_pw'],
					'rs_server_typ'	=> $data['rs_server_typ'],
					'rs_server_id'	=> $data['rs_server_id']
				));
			$res = $server->MakeCache($data['rs_usertyp'],false);
			$temp = fopen(RADIOSTATUS.'cache/stream_extern_'.$data['rs_id'].'_'.$data['rs_cache'].'.php','w');
			if (fwrite($temp, $res)) {
				fclose($temp);
			}
			eval('?>'.$res.'<?php ');
		} else {
			require_once RADIOSTATUS.'cache/stream_extern_'.$data['rs_id'].'_'.$data['rs_cache'].'.php';
		}
		$cache['rs_id'] = $data['rs_id'];
		$cache['rs_name'] = $data['rs_name'];
		if (isset($cache['mod_ckeck']) AND $cache['mod_ckeck'] AND $data['rs_status_gb'] AND $data['rs_status_gb_dj'] AND checkgroup($data['rs_access_gb_user'])) {
			$cache['gb'] = '<img src="'.RADIOSTATUS_SELF.'skin_'.$skin.'/gb.gif" alt="'.$locale['grrsg_01'].'" title="'.$locale['grrsg_01'].'" style="cursor:pointer;" border="0" width="85" height="15" onclick="window.open(\''.$settings['siteurl'].str_replace('../', '', INFUSIONS).'gr_radiostatus_panel/gr_radiostatus_player.php?id='.$cache['rs_id'].'&amp;typ=gb\',\'GB\',\'height=260,width=510,top=\' + Math.round((screen.height - 260) / 2) + \',left=\' + Math.round((screen.width - 510) / 2) + \'\')" />';
		} else {
			$cache['gb'] = '';
		}
		echo rs_theme($cache, $skin);
		@mysql_free_result($result);
	} else {
		echo "<div align='center'>".$locale['grrsi_18']."</div>";
	}
}
echo "</body>\n</html>\n";
@mysql_close();
?>