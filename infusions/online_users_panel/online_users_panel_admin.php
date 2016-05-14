<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: online_users_admin.php
| Copyright © 2009 ptown67
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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";
include INFUSIONS."online_users_panel/infusion_db.php";

if (file_exists(INFUSIONS."online_users_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."online_users_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."online_users_panel/locale/German.php";
}

if (!checkrights("AOU") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

if(isset($_GET['status'])) {
if($_GET['status'] == "ok") echo "<div class='admin-message'>".$locale['aou210']."</div>";
if($_GET['status'] == "reset") echo "<div class='admin-message'>".$locale['aou211']."</div>";
}

if (!isset($_POST['shownewmember']))
	$_POST['shownewmember'] = "0";

if (!isset($_POST['showmembers']))
	$_POST['showmembers'] = "0";

if (!isset($_POST['showguests']))
	$_POST['showguests'] = "1";

if (!isset($_POST['modcolor']))
	$_POST['modcolor'] = "003366";

if(isset($_POST['update'])) {
$result = dbquery("UPDATE ".DB_ONLINE_SETTINGS." SET 
online_superadmincolor = '".$_POST['superadmincolor']."', 
online_admincolor = '".$_POST['admincolor']."', 
online_modcolor = '".$_POST['modcolor']."', 
online_usercolor = '".$_POST['usercolor']."', 
online_showguests = '".$_POST['showguests']."', 
online_showmembers = '".$_POST['showmembers']."', 
online_showmembersnum = '".$_POST['showmembersnum']."', 
online_showbots = '".$_POST['showbots']."', 
online_showallmembers = '".$_POST['showallmembers']."', 
online_shownewmember = '".$_POST['shownewmember']."'
");
redirect (FUSION_SELF."?aid=".$_GET['aid']."&status=ok");
}

$result = dbquery("SELECT * FROM ".DB_ONLINE_SETTINGS);
$data = dbarray($result);

opentable($locale['aou200']);

echo "<form name='aousettings' method='post' action='".FUSION_SELF."?aid=".$_GET['aid']."'>";
echo "<table cellpadding='5' cellspacing='1' width='100%' align='center' class='tbl-border'>";

// Anzahl Gäste
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou205']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='checkbox' name='showguests' value='1'";
if ($data['online_showguests'] != "0") echo "checked";
echo "/>";
echo "</td>";
echo "</tr>";

// Anzahl Mitglieder
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou206']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='checkbox' name='showmembers' value='1'";
if ($data['online_showmembers'] != "0") echo "checked";
echo "/>";
echo "</td>";
echo "</tr>";

// Länge der Liste
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou201']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='text' name='showmembersnum' size='2' class='textbox' value='".$data['online_showmembersnum']."'/>";
echo "</td>";
echo "</tr>";

// Adminfarbe
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou212']."</td>";
echo "<td class='tbl1' align='left'>";
echo "#<input type='text' name='superadmincolor' size='6' class='textbox' value='".$data['online_superadmincolor']."'/>";
echo "</td>";
echo "</tr>";

// CoAdminfarbe
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou213']."</td>";
echo "<td class='tbl1' align='left'>";
echo "#<input type='text' name='admincolor' size='6' class='textbox' value='".$data['online_admincolor']."'/>";
echo "</td>";
echo "</tr>";

// Moderatorfarbe
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou214']."</td>";
echo "<td class='tbl1' align='left'>";
echo "#<input type='text' name='modcolor' size='6' class='textbox' value='".$data['online_modcolor']."'/>";
echo "</td>";
echo "</tr>";

// Userfarbe
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou215']."</td>";
echo "<td class='tbl1' align='left'>";
echo "#<input type='text' name='usercolor' size='6' class='textbox' value='".$data['online_usercolor']."'/>";
echo "</td>";
echo "</tr>";

// Suchmaschinen-Robots
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou209']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='checkbox' name='showbots' value='1'";
if ($data['online_showbots'] != "0") echo "checked";
echo "/>";
echo "</td>";
echo "</tr>";

// Anzahl Registrierungen
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou207']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='checkbox' name='showallmembers' value='1'";
if ($data['online_showallmembers'] != "0") echo "checked";
echo "/>";
echo "</td>";
echo "</tr>";

// Neustes Mitglied
echo "<tr>";
echo "<td class='tbl1' height='25px' width='1%' style='white-space:nowrap'>".$locale['aou208']."</td>";
echo "<td class='tbl1' align='left'>";
echo "<input type='checkbox' name='shownewmember' value='1'";
if ($data['online_shownewmember'] != "0") echo "checked";
echo "/>";
echo "</td>";
echo "</tr>";

// Reset / Übernehmen
echo "<tr>";
echo "<td class='tbl1' colspan='2' align='right'>";
echo "<input type='submit' name='update' class='button' value='".$locale['aou202']."'></td>";
echo "</tr>";

echo "</table>";
echo "</form>";

closetable();
require_once THEMES."templates/footer.php";
?>