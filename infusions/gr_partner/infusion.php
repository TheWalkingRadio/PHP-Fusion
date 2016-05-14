<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Partner v1.1 for PHP-Fusion 7
| Filename: infusion.php
| Author: Ralf Thieme (Gr@n@dE)
| Co - Author: Daniel Horanuer
| Homepage: www.granade.eu
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

include INFUSIONS."gr_partner/infusion_db.php";
if (file_exists(INFUSIONS."gr_partner/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_partner/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_partner/locale/German/index.php";
}

$inf_title = $locale['grpa100'];
$inf_description = $locale['grpa101'];
$inf_version = "1.1";
$inf_developer = "Ralf Thieme, Daniel Horanuer";
$inf_email = "scripte@granade.eu";
$inf_weburl = "http://www.granade.eu";

$inf_folder = "gr_partner";

$inf_newtable[1] = DB_GR_PARTNER." (
grpa_id						SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
grpa_order				SMALLINT(5) UNSIGNED DEFAULT '1' NOT NULL,
grpa_page					TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
grpa_title				VARCHAR(50) DEFAULT '' NOT NULL,
grpa_hp						VARCHAR(50) DEFAULT '' NOT NULL,
grpa_pic					VARCHAR(100) DEFAULT '' NOT NULL,
PRIMARY KEY (grpa_id)
) ENGINE=MyISAM;";

$inf_insertdbrow[1] = DB_GR_PARTNER." VALUES('1', '1', '4', 'Gr@n@dEs Homeparty', 'http://www.granade.eu', '".IMAGES."partner/granade.gif')";

$inf_droptable[1] = DB_GR_PARTNER;

$inf_adminpanel[1] = array(
	"title" => $locale['grpa100'],
	"image" => "",
	"panel" => "gr_partner_admin.php",
	"rights" => "GRPA"
);

$inf_sitelink[1] = array(
	"title" => $locale['grpa102'],
	"url" => "../../partner.php",
	"visibility" => "0"
);
?>