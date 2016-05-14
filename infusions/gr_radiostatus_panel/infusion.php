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

include_once INFUSIONS.'gr_radiostatus_panel/infusion_db.php';
if (file_exists(INFUSIONS.'gr_radiostatus_panel/locale/'.LOCALESET.'index.php')) {
	include INFUSIONS.'gr_radiostatus_panel/locale/'.LOCALESET.'index.php';
} else {
	include INFUSIONS.'gr_radiostatus_panel/locale/German/index.php';
}

$inf_title = $locale['grrs_title'];
$inf_description = $locale['grrs_desc'];
$inf_version = '2.6';
$inf_developer = 'Ralf Thieme';
$inf_email = 'scripte@granade.eu';
$inf_weburl = 'http://www.granade.eu';
$inf_folder = 'gr_radiostatus_panel';

$inf_newtable[1] = DB_GR_RADIOSTATUS." (
rs_id							SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
rs_server_typ			TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_server_id			SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1',
rs_name						VARCHAR(20) NOT NULL DEFAULT 'Stream',
rs_ip							VARCHAR(255) NOT NULL DEFAULT '0.0.0.0',
rs_port						VARCHAR(5) NOT NULL DEFAULT '8000',
rs_pw							VARCHAR(255) NOT NULL DEFAULT '',
rs_apw						VARCHAR(255) NOT NULL DEFAULT '',
rs_ps							VARCHAR(255) NOT NULL DEFAULT '',
rs_tele						TEXT NOT NULL,
rs_flash					VARCHAR(255) NOT NULL DEFAULT '',
rs_cache					VARCHAR(255) NOT NULL DEFAULT '',
rs_usertyp				TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
rs_theme					TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
rs_panel					TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
rs_order					SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
rs_status					TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_status_gb			TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_status_gb_dj		TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_status_boxen		TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_gb_max					SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
rs_gb_max_user		SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1',
rs_gb_popup				TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
rs_access					TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
rs_access_l				TINYINT(3) UNSIGNED NOT NULL DEFAULT '102',
rs_access_a				TINYINT(3) UNSIGNED NOT NULL DEFAULT '102',
rs_access_gb			TINYINT(3) UNSIGNED NOT NULL DEFAULT '102',
rs_access_gb_user	TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (rs_id),
INDEX rs_panel (rs_panel, rs_access, rs_status),
INDEX rs_boxen (rs_access, rs_status, rs_status_boxen),
INDEX rs_boxen_id (rs_id, rs_access, rs_status, rs_status_boxen)
) ENGINE=MyISAM;";

$inf_newtable[2] = DB_GR_RADIOSTATUS_GRUSSBOX." (
rsgb_id						BIGINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
rsgb_stream				SMALLINT(5) NOT NULL DEFAULT '1',
rsgb_user_id			MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
rsgb_user_ip			VARCHAR(255) NOT NULL DEFAULT '0.0.0.0',
rsgb_user_name		VARCHAR(255) NOT NULL DEFAULT '',
rsgb_user_ort			VARCHAR(255) NOT NULL DEFAULT '',
rsgb_title				VARCHAR(255) NOT NULL DEFAULT '',
rsgb_interpret		VARCHAR(255) NOT NULL DEFAULT '',
rsgb_gruss				TEXT NOT NULL,
rsgb_time					INT(10) NOT NULL DEFAULT '0',
rsgb_status				TINYINT(1) NOT NULL DEFAULT '0',
PRIMARY KEY (rsgb_id)
) ENGINE=MyISAM;";

$inf_newtable[3] = DB_GR_RADIOSTATUS_ACTION." (
rsa_stream				SMALLINT(5) NOT NULL DEFAULT '1',
rsa_user_id				MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
rsa_sction				TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
rsa_time					INT(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM;";

$inf_newtable[4] = DB_GR_RADIOSTATUS_TITLE." (
rst_interpret			VARCHAR(255) NOT NULL DEFAULT '',
rst_title					VARCHAR(255) NOT NULL DEFAULT '',
rst_time					INT(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM;";

$inf_droptable[1] = DB_GR_RADIOSTATUS;
$inf_droptable[2] = DB_GR_RADIOSTATUS_GRUSSBOX;
$inf_droptable[3] = DB_GR_RADIOSTATUS_ACTION;
$inf_droptable[4] = DB_GR_RADIOSTATUS_TITLE;

$inf_adminpanel[1] = array(
	'title' => $locale['grrs_admin1'],
	'image' => '',
	'panel' => 'gr_radiostatus_admin.php',
	'rights' => 'GRRS'
);

$inf_sitelink[1] = array(
	'title' => $locale['grrs_link1'],
	'url' => 'gr_radiostatus_info.php',
	'visibility' => '0'
);

?>