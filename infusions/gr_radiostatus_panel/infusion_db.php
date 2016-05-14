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

if (!defined('DB_GR_RADIOSTATUS')) {
	define('DB_GR_RADIOSTATUS', DB_PREFIX.'gr_radiostatus');
}

if (!defined('DB_GR_RADIOSTATUS_GRUSSBOX')) {
	define('DB_GR_RADIOSTATUS_GRUSSBOX', DB_PREFIX.'gr_radiostatus_grussbox');
}

if (!defined('DB_GR_RADIOSTATUS_ACTION')) {
	define('DB_GR_RADIOSTATUS_ACTION', DB_PREFIX.'gr_radiostatus_action');
}

if (!defined('DB_GR_RADIOSTATUS_TITLE')) {
	define('DB_GR_RADIOSTATUS_TITLE', DB_PREFIX.'gr_radiostatus_title');
}

// default settings
$gr_radiostatus_settings = array(
	'reload_cache' => '30',
	'reload_cache_extern' => '30',
	'reload_main' => '30000',
	'reload_side' => '15000',
	'reload_misc' => '30000',
	'reload_extern' => '30000',
	'reload_gb_admin' => '60000',
	'reload_listners' => '30000',
	'title_delete' => '90',
	'gb_time' => '%d.%m. %H:%M:%S',
	'buy_link' => '',
	'buy_pic' => 'musicload.png'
);

if (defined('DB_SETTINGS_INF')) {
	$set_result = dbquery("SELECT settings_name, settings_value FROM ".DB_SETTINGS_INF." WHERE settings_inf='gr_radiostatus_panel'");
	if (dbrows($set_result)) {
		while ($set_data = dbarray($set_result)) {
			$gr_radiostatus_settings[$set_data['settings_name']] = $set_data['settings_value'];
		}
		@mysql_free_result($set_result);
	}
}

eval(str_replace("\'","'",str_replace("\\\"","\"",base64_decode('ZnVuY3Rpb24gcnNfc3RhcnQoJGRpc3BsYXkpIHsNCglpZiAoIXByZWdfbWF0Y2goJy88YSBocmVmPSJodHRwOlwvXC93d3cuZ3JhbmFkZS5ldVwvc2NyaXB0ZVwvcmFkaW9zdGF0dXMuaHRtbCIgdGFyZ2V0PSJfYmxhbmsiPlJhZGlvc3RhdHVzICZjb3B5OzxcL2E+L2knLCAkZGlzcGxheSkpIHsNCgkJJGRpc3BsYXkgPSBwcmVnX3JlcGxhY2UoIl48IS0tcmFkaW9zdGF0dXNfc3RhcnQtLT4oLio/KTwhLS1yYWRpb3N0YXR1c19lbmRlLS0+XnNpIiwgIjwhLS1yYWRpb3N0YXR1c19zdGFydC0tPjwhLS1yYWRpb3N0YXR1c19lbmRlLS0+IiwgJGRpc3BsYXkpOw0KCX0NCglyZXR1cm4gJGRpc3BsYXk7DQp9'))));
?>