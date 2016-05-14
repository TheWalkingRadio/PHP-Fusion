<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Partner v1.1 for PHP-Fusion 7
| Filename: partner.php
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
require_once "maincore.php";
require_once THEMES."templates/header.php";

include INFUSIONS."gr_partner/infusion_db.php";
if (file_exists(INFUSIONS."gr_partner/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_partner/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_partner/locale/German/index.php";
}
/*---------------------------------------------------+
| Einsellungen																			 |
+----------------------------------------------------+
| Bewerbungsansicht																	 |
| 0 = Alle Ansichten aus														 |
| 1 = Ansichten an, wenn kein Partner verfügbar			 |
| 2 = Ansichten an, wenn min. ein Partner verfügbar	 |
| 3 = Alle Ansichten an 														 |
+---------------------------------------------------*/
$default_ansicht_page_1 = 1; // Parter
$default_ansicht_page_2 = 1; // Werbepartner
$default_ansicht_page_3 = 1; // Sponsoren
$default_ansicht_page_4 = 1; // Werbung
/*---------------------------------------------------+
| Default Bild																			 |
+---------------------------------------------------*/
$default_bild = IMAGES."partner/default.jpg";
/*---------------------------------------------------+
| Default Email	(Neu => $email = "neu@granade.eu";	 |
+---------------------------------------------------*/
$email = $settings['siteemail'];
/*--------------------------------------------------*/

if (IsSeT($_GET['werbepartner'])) { add_to_title($locale['global_200'].$locale['grpa111']); opentable($locale['grpa111']); $page=2; $ansicht = $default_ansicht_page_2; }
elseif (IsSeT($_GET['sponsoren'])) { add_to_title($locale['global_200'].$locale['grpa112']); opentable($locale['grpa112']); $page=3; $ansicht = $default_ansicht_page_3; }
elseif (IsSeT($_GET['werbung'])) { add_to_title($locale['global_200'].$locale['grpa113']); opentable($locale['grpa113']); $page=4; $ansicht = $default_ansicht_page_4; }
else { add_to_title($locale['global_200'].$locale['grpa110']); opentable($locale['grpa110']); $partner=true; $page=1; $ansicht = $default_ansicht_page_1; }

echo "<table width='100%' align='center' class='tbl-border'>\n<tr>
	<td width='25%' align='center' ".($page == 1 ? "class='tbl1'><b>".$locale['grpa110']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?partner'>".$locale['grpa110']."</a>")."</td>
	<td width='25%' align='center' ".($page == 2 ? "class='tbl1'><b>".$locale['grpa111']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?werbepartner'>".$locale['grpa111']."</a>")."</td>
	<td width='25%' align='center' ".($page == 3 ? "class='tbl1'><b>".$locale['grpa112']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?sponsoren'>".$locale['grpa112']."</a>")."</td>
	<td width='25%' align='center' ".($page == 4 ? "class='tbl1'><b>".$locale['grpa113']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?werbung'>".$locale['grpa113']."</a>")."</td>
</tr>\n</table><br />";
	
$result = dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_page='".$page."' ORDER BY grpa_order");
if (dbrows($result)) {
	while ($data = dbarray($result)) {
	echo "<table width='500' align='center' class='tbl-border'>\n<tr>
		<td width='50%' align='center' class='tbl2'><a href='".$data['grpa_hp']."' target='_blank'><b>".$data['grpa_title']."</b></a></td>
	</tr>\n<tr>
		<td align='center' class='tbl1'><a href='".$data['grpa_hp']."' target='_blank'><img src='".$data['grpa_pic']."' width='468' height='60' border='0' alt='".$data['grpa_title']."' title='".$data['grpa_title']."' /></a></td>
	</tr>\n</table><br />";
	}
	if ($ansicht == 2 || $ansicht == 3) {
		echo "<table width='500' align='center' class='tbl-border'>\n<tr>
			<td colspan='2' align='center' class='tbl1'><img src='".$default_bild."' width='468' height='60' border='0' alt='".$settings['sitename']."' title='".$settings['sitename']."' /></td>
		</tr>\n<tr>
			<td width='50%' align='center' class='tbl2'><b>".$locale['grpa114']."</b></td>
			<td width='50%' align='center' class='tbl2'>".hide_email($email)."</td>
		</tr>\n</table><br />";
	}
} else {
	if ($ansicht == 1 || $ansicht == 3) {
		echo "<table width='500' align='center' class='tbl-border'>\n<tr>
			<td colspan='2' align='center' class='tbl1'><img src='".$default_bild."' width='468' height='60' border='0' alt='".$settings['sitename']."' title='".$settings['sitename']."' /></td>
		</tr>\n<tr>
			<td width='50%' align='center' class='tbl2'><b>".$locale['grpa114']."</b></td>
			<td width='50%' align='center' class='tbl2'>".hide_email($email)."</td>
		</tr>\n</table><br />";
	} else {
		echo "<div align='center'>".$locale['grpa123']."<br /><br /></div>";
	}
}
echo "<div align='right'><a href='http://www.granade.eu/scripte/partner.html' target='_blank'>Partner &copy;</a></div>";
closetable();

require_once THEMES."templates/footer.php";
?>