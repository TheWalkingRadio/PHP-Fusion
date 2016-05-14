<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2009 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: © 2009 ptown67
| http://www.ptown67.de
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

include INFUSIONS."online_users_panel/infusion_db.php";

if (file_exists(INFUSIONS."online_users_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."online_users_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."online_users_panel/locale/German.php";
}

$inf_title = $locale['aou100'];
$inf_description = $locale['aou101'];
$inf_version = "2.0";
$inf_developer = "ptown67";
$inf_email = "info@ptown67.de";
$inf_weburl = "http://www.ptown67.de";
$inf_folder = "online_users_panel";

$inf_newtable[1] = DB_ONLINE_SETTINGS." (
online_superadmincolor VARCHAR(6) NOT NULL,
online_admincolor VARCHAR(6) NOT NULL,
online_modcolor VARCHAR(6) NOT NULL,
online_usercolor VARCHAR(6) NOT NULL,

online_showguests INT(1) UNSIGNED NOT NULL,
online_showmembers INT(1) UNSIGNED NOT NULL,
online_showmembersnum INT(2) UNSIGNED NOT NULL,
online_showmemberstime INT(10) UNSIGNED NOT NULL,
online_showbots INT(1) UNSIGNED NOT NULL,
online_showbotstime INT(10) UNSIGNED NOT NULL,
online_showallmembers INT(1) UNSIGNED NOT NULL,
online_shownewmember INT(1) UNSIGNED NOT NULL,

online_alexa INT(10) UNSIGNED NOT NULL,
online_exalead INT(10) UNSIGNED NOT NULL,
online_excite INT(10) UNSIGNED NOT NULL,
online_fast INT(10) UNSIGNED NOT NULL,
online_fireball INT(10) UNSIGNED NOT NULL,
online_google INT(10) UNSIGNED NOT NULL,
online_lycos INT(10) UNSIGNED NOT NULL,
online_msn INT(10) UNSIGNED NOT NULL,
online_yahoo INT(10) UNSIGNED NOT NULL
) TYPE=MyISAM;";

$inf_insertdbrow[1] = DB_ONLINE_SETTINGS." (
online_superadmincolor,
online_admincolor,
online_modcolor,
online_usercolor,
online_showguests,
online_showmembers,
online_showmembersnum,
online_showmemberstime,
online_showbots,
online_showbotstime,
online_showallmembers,
online_shownewmember,
online_alexa,
online_exalead,
online_excite,
online_fast,
online_fireball,
online_google,
online_lycos,
online_msn,
online_yahoo
) VALUES(
'003366',
'003366',
'003366',
'555555',
'1',
'1',
'10',
'300',
'1',
'300',
'1',
'1',
'0',
'0',
'0',
'0',
'0',
'0',
'0',
'0',
'0')";

$inf_droptable[1] = DB_ONLINE_SETTINGS;

$inf_adminpanel[1] = array(
	"title" => $locale['aou100'],
	"image" => "members.gif",
	"panel" => "online_users_panel_admin.php",
	"rights" => "AOU"
);
?>