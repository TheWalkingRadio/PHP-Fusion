<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Member_Charts v1.1 for PHP-Fusion 7
| Filename: gr_member_charts_panel.php
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

$panel_result = dbquery("SELECT * FROM ".DB_PANELS." WHERE panel_filename='gr_member_charts_panel' AND (panel_side='1' OR panel_side='4') AND panel_status='1'");
if (dbrows($panel_result)) {
	openside($locale['grmc200']);
} else {
	opentable($locale['grmc200']);
}

$mc_top_result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1' ORDER BY mcc_votes DESC LIMIT 0,10");
if (dbrows($mc_top_result)) {
$i = 1;
echo "<table class='tbl-border' cellpadding='0' cellspacing='0' align='center' width='100%'>";
while ($mc_top = dbarray($mc_top_result)) {
echo "<tr>\n<td class='".($i % 2 == 0 ? "tbl1" : "tbl2")."'>".$i.".<br /> (".$mc_top['mcc_votes'].")</td>
<td class='".($i % 2 == 0 ? "tbl2" : "tbl1")."'>".$mc_top['mcc_interpreter']."<br />".$mc_top['mcc_title']."</td>\n</tr>\n";
$i++;
}
echo "</table>";
} else {
	echo "<br />".$locale['grmc201']."<br /><br />";
}

if (dbrows($panel_result)) {
	echo "<div align='right'><a href='http://www.granade.eu/scripte/member_charts.html' target='_blank'>Member Charts &copy;</a></div>";
	closeside();
} else {
	echo "<div class='small1' align='center'>[ <a href='".BASEDIR."member_charts.php'>".$locale['grmc202']."</a> ]";
	if (iMEMBER) { echo " [ <a href='".BASEDIR."member_charts.php?new_song'>".$locale['grmc203']."</a> ]"; }
	echo "</div>\n<div align='right'><a href='http://www.granade.eu/scripte/member_charts.php' target='_blank'>Member Charts &copy;</a></div>";
	closetable();
}
?>