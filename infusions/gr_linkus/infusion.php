<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_LinkUs v3.1 for PHP-Fusion 7
| Filename: infusion.php
| Author: Ralf Thieme (Gr@n@dE)
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

include INFUSIONS."gr_linkus/infusion_db.php";
if (file_exists(INFUSIONS."gr_linkus/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_linkus/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_linkus/locale/German/index.php";
}

$inf_title = $locale['grlu100'];
$inf_description = $locale['grlu101'];
$inf_version = "3.1";
$inf_developer = "Ralf Thieme";
$inf_email = "scripte@granade.eu";
$inf_weburl = "http://www.granade.eu";

$inf_folder = "gr_linkus";

$inf_newtable[1] = DB_GR_LINKUS." (
lu_id							SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
lu_page						TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
lu_order					SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1',
lu_banner					VARCHAR(100) NOT NULL DEFAULT 'default',
lu_width					SMALLINT(5) NOT NULL DEFAULT '468',
lu_height					SMALLINT(5) NOT NULL DEFAULT '60',
lu_flash					TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (lu_id)
) ENGINE=MyISAM;";

$inf_insertdbrow[1] = DB_GR_LINKUS." VALUES('1', '1', '1', 'gross.gif', '468', '60', '0')";
$inf_insertdbrow[2] = DB_GR_LINKUS." VALUES('2', '2', '1', 'klein.gif', '88', '31', '0')";

$inf_droptable[1] = DB_GR_LINKUS;

$inf_adminpanel[1] = array(
	"title" => $locale['grlu102'],
	"image" => "",
	"panel" => "gr_linkus_admin.php",
	"rights" => "GRLU"
);

$inf_sitelink[1] = array(
	"title" => $locale['grlu103'],
	"url" => "../../linkus.php",
	"visibility" => "0"
);

?>