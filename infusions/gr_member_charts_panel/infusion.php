<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Member_Charts v1.1 for PHP-Fusion 7
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

include INFUSIONS."gr_member_charts_panel/infusion_db.php";
if (file_exists(INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_member_charts_panel/locale/German/index.php";
}

$inf_title = $locale['grmc100'];
$inf_description = $locale['grmc101'];
$inf_version = "1.1";
$inf_developer = "Ralf Thieme";
$inf_email = "scripte@granade.eu";
$inf_weburl = "http://www.granade.eu";

$inf_folder = "gr_member_charts_panel";

$inf_newtable[1] = DB_GR_MC_CHARTS." (
mcc_id						SMALLINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
mcc_interpreter		VARCHAR(100) DEFAULT '-' NOT NULL,
mcc_title					VARCHAR(100) DEFAULT '-' NOT NULL,
mcc_votes					INT(10) DEFAULT '0' NOT NULL,
mcc_free					SMALLINT(5) DEFAULT '0' NOT NULL,
mcc_autor					VARCHAR(30) DEFAULT '' NOT NULL,
PRIMARY KEY (mcc_id)
) ENGINE=MyISAM;";

$inf_newtable[2] = DB_GR_MC_VOTE." (
mcv_id						SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
mcv_userid				VARCHAR(10) DEFAULT '0' NOT NULL,
mcv_ip						VARCHAR(50) DEFAULT '0' NOT NULL,
mcv_voteid				VARCHAR(10) DEFAULT '0' NOT NULL,
mcv_votetime			INT(10) DEFAULT '0' NOT NULL,
PRIMARY KEY (mcv_id)
) ENGINE=MyISAM;";

$inf_newtable[3] = DB_GR_MC_SETTINGS." (
mcs_id						SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
mcs_start					INT(10) DEFAULT '0' NOT NULL,
mcs_end						INT(10) DEFAULT '0' NOT NULL,
mcs_autor_select	VARCHAR(5) DEFAULT '1' NOT NULL,
mcs_vote_select		VARCHAR(5) DEFAULT '1' NOT NULL,
mcs_top_select		VARCHAR(100) DEFAULT '20' NOT NULL,
PRIMARY KEY (mcs_id)
) ENGINE=MyISAM;";

$inf_insertdbrow[1] = DB_GR_MC_CHARTS." (mcc_id, mcc_interpreter, mcc_title, mcc_votes, mcc_free, mcc_autor) VALUES('1', 'Gr@n@dE', 'www.granade.eu', '0', '1', 'Gr@n@dE')";
$inf_insertdbrow[2] = DB_GR_MC_SETTINGS." (mcs_id, mcs_start, mcs_end, mcs_autor_select, mcs_vote_select, mcs_top_select) VALUES('1', '".time()."', '".time()."', '1', '1', '20')";

$inf_droptable[1] = DB_GR_MC_CHARTS;
$inf_droptable[2] = DB_GR_MC_VOTE;
$inf_droptable[3] = DB_GR_MC_SETTINGS;

$inf_adminpanel[1] = array(
	"title" => $locale['grmc100'],
	"image" => "",
	"panel" => "gr_member_charts_admin.php",
	"rights" => "GRMC"
);

$inf_sitelink[1] = array(
	"title" => $locale['grmc102'],
	"url" => "../../member_charts.php",
	"visibility" => "0"
);
?>