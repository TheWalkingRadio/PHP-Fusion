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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";

if (!checkrights("GRMC") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }
if (IsSeT($_GET['id']) && !isnum($_GET['id'])) { redirect("../../index.php"); }

include INFUSIONS."gr_member_charts_panel/infusion_db.php";
if (file_exists(INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_member_charts_panel/locale/German/index.php";
}

opentable($locale['grmc309']);
echo "<b>".$locale['grmc300']."</b> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;main_settings'>".$locale['grmc301']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;time'>".$locale['grmc302']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;service'>".$locale['grmc426']."</a>";
echo "<br><b>".$locale['grmc303']."</b> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;new'>".$locale['grmc304']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;enable_all' onclick='return Enable_all();'>".$locale['grmc305']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;disenable_all' onclick='return Disenable_all();'>".$locale['grmc306']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;delet_all' onclick='return Delet_all();'>".$locale['grmc307']."</a> ";
echo THEME_BULLET." <a href='".FUSION_SELF.$aidlink."&amp;reset' onclick='return Reset();'>".$locale['grmc308']."</a>";
closetable();


if (IsSeT($_GET['time'])) {
opentable($locale['grmc356']);
if (IsSeT($_POST['save'])) {
	$start_date = 0; $end_date = 0;
	if ($_POST['start_mday']!="--" && $_POST['start_mon']!="--" && $_POST['start_year']!="----") {
		$start_date = mktime($_POST['start_hours'],$_POST['start_minutes'],0,$_POST['start_mon'],$_POST['start_mday'],$_POST['start_year']);
	}
	if ($_POST['end_mday']!="--" && $_POST['end_mon']!="--" && $_POST['end_year']!="----") {
		$end_date = mktime($_POST['end_hours'],$_POST['end_minutes'],0,$_POST['end_mon'],$_POST['end_mday'],$_POST['end_year']);
	}
	if (isnum($start_date) && $start_date > 0 && isnum($end_date) && $end_date > 0) {
		$result = dbquery("UPDATE ".DB_GR_MC_SETTINGS." SET mcs_start='".$start_date."', mcs_end='".$end_date."' WHERE mcs_id='1'");
		echo "<div align='center'><br />".$locale['grmc355']."<br /><br /></div>";
	} else {
		echo "<div align='center'><br />".$locale['grmc352']."<br /><br /></div>";
	}
}
$mcs_settings_result = dbquery("SELECT * FROM ".DB_GR_MC_SETTINGS." WHERE mcs_id='1'");
$mc_settings = dbarray($mcs_settings_result);
echo "<form action='".FUSION_SELF.$aidlink."&amp;time' method='post'>\n<table align='center' cellpadding='0' cellspacing='0' class='tbl-border'>
<tr>
	<td class='tbl1'>".$locale['grmc353']."</td>
	<td class='tbl2'>
	<select name='start_mday' class='textbox'>\n<option>--</option>\n";
	for ($i=1;$i<=31;$i++) echo "<option".($i == showdate("%d", $mc_settings['mcs_start']) ? " selected" : "").">".$i."</option>\n";
	echo "</select>
	<select name='start_mon' class='textbox'>\n<option>--</option>\n";
	for ($i=1;$i<=12;$i++) echo "<option".($i == showdate("%m", $mc_settings['mcs_start']) ? " selected" : "").">".$i."</option>\n";
	echo "</select>
	<select name='start_year' class='textbox'>\n<option>----</option>\n";
	for ($i=2008;$i<=2020;$i++) echo "<option".($i == showdate("%Y", $mc_settings['mcs_start']) ? " selected" : "").">".$i."</option>\n";
	echo "</select> /
	<select name='start_hours' class='textbox'>\n";
	for ($i=0;$i<=24;$i++) echo "<option".($i == showdate("%H", $mc_settings['mcs_start']) ? " selected" : "").">".$i."</option>\n";
	echo "</select> :
	<select name='start_minutes' class='textbox'>\n";
	for ($i=0;$i<=60;$i++) echo "<option".($i == showdate("%M", $mc_settings['mcs_start']) ? " selected" : "").">".$i."</option>\n";
	echo "</select></td>
</tr>
<tr>
	<td class='tbl' align='center' colspan='2'> </td>
</tr>	
<tr>
	<td class='tbl1'>".$locale['grmc354']."</td>
	<td class='tbl2'>
	<select name='end_mday' class='textbox'>\n<option>--</option>\n";
	for ($i=1;$i<=31;$i++) echo "<option".($i == showdate("%d", $mc_settings['mcs_end']) ? " selected" : "").">".$i."</option>\n";
	echo "</select>
	<select name='end_mon' class='textbox'>\n<option>--</option>\n";
	for ($i=1;$i<=12;$i++) echo "<option".($i == showdate("%m", $mc_settings['mcs_end']) ? " selected" : "").">".$i."</option>\n";
	echo "</select>
	<select name='end_year' class='textbox'>\n<option>----</option>\n";
	for ($i=2008;$i<=2020;$i++) echo "<option".($i == showdate("%Y", $mc_settings['mcs_end']) ? " selected" : "").">".$i."</option>\n";
	echo "</select> /
	<select name='end_hours' class='textbox'>\n";
	for ($i=0;$i<=24;$i++) echo "<option".($i == showdate("%H", $mc_settings['mcs_end']) ? " selected" : "").">".$i."</option>\n";
	echo "</select> :
	<select name='end_minutes' class='textbox'>\n";
	for ($i=0;$i<=60;$i++) echo "<option".($i == showdate("%M", $mc_settings['mcs_end']) ? " selected" : "").">".$i."</option>\n";
	echo "</select></td>
</tr>
<tr>
	<td class='tbl' align='center' colspan='2'><input type='submit' name='save' value='".$locale['grmc337']."' class='button' /></td>
</tr>
</table>\n</form>\n<a href='".FUSION_SELF.$aidlink."'>".$locale['grmc331']."</a>\n";
} elseif (IsSeT($_GET['service'])) {
	opentable($locale['grmc426']);
	if (IsSeT($_POST['save']) && IsSeT($_POST['file'])) {
		$file = stripinput($_POST['file']);
		if (file_exists(INFUSIONS."gr_member_charts_panel/service/".$file)) {
			include INFUSIONS."gr_member_charts_panel/service/".$file;
			for ($i = 1; $i < (count($interpreter) + 1); $i++) {
				$result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." WHERE mcc_interpreter='".$interpreter[$i]."' AND mcc_title='".$title[$i]."'");
				if (!dbrows($result)) {
					$result = dbquery("INSERT INTO ".DB_GR_MC_CHARTS." (mcc_interpreter, mcc_title, mcc_autor) VALUES('".$interpreter[$i]."', '".$title[$i]."', 'granade.eu')"); 
				}
			}
		}
		redirect(FUSION_SELF.$aidlink."&service&ok");
	} else {
		echo "<div align='center'>";
		if (IsSeT($_GET['ok'])) {
			echo $locale['grmc428']."<br /><br />";
		}
		$service_files = makefilelist(INFUSIONS."gr_member_charts_panel/service/", ".|..|index.php", true);
		$service_list = makefileopts($service_files);
		echo "<form action='".FUSION_SELF.$aidlink."&service' method='post'>
		<select name='file' class='textbox' style='width:200px;'>
		<option value=''>".$locale['grmc427']."</option>
		<option value=''>-----------------------</option>
		".$service_list."</select><br />
		<input name='save' type='submit' class='button' value='".$locale['grmc332']."' />
		</from>\n</div>\n<a href='".FUSION_SELF.$aidlink."'>".$locale['grmc331']."</a>\n";
	}
} elseif (IsSeT($_GET['main_settings'])) {
opentable($locale['grmc333']);
if (IsSeT($_POST['save'])) {
	if (isnum($_POST['sender']) && isnum($_POST['top']) && isnum($_POST['vote'])) {
		$result = dbquery("UPDATE ".DB_GR_MC_SETTINGS." SET mcs_autor_select='".$_POST['sender']."', mcs_vote_select='".$_POST['vote']."', mcs_top_select='".$_POST['top']."' WHERE mcs_id='1'");
		echo "<div align='center'><br />".$locale['grmc351']."<br /><br /></div>";
	} else {
		echo "<div align='center'><br />".$locale['grmc352']."<br /><br /></div>";
	}
}
$mcs_settings_result = dbquery("SELECT * FROM ".$db_prefix."gr_mc_settings WHERE mcs_id='1'");
$mc_settings = dbarray($mcs_settings_result);
echo "<form action='".FUSION_SELF.$aidlink."&amp;main_settings' method='post'>\n<table align='center' cellpadding='0' cellspacing='0' class='tbl-border'>
<tr>
	<td class='tbl1'>".$locale['grmc334']."</td>
	<td class='tbl2'><select name='sender' class='textbox'>
	<option".($mc_settings['mcs_autor_select'] == 1 ? " selected='selected'" : "")." value='1'>".$locale['grmc340']."</option>
	<option".($mc_settings['mcs_autor_select'] == 0 ? " selected='selected'" : "")." value='0'>".$locale['grmc341']."</option>
	</select>\n</td>
</tr>
<tr>
	<td class='tbl' align='center' colspan='2'> </td>
</tr>	
<tr>
	<td class='tbl1'>".$locale['grmc335']."</td>
	<td class='tbl2'><select name='top' class='textbox'>
	<option".($mc_settings['mcs_top_select'] == 10 ? " selected='selected'" : "")." value='10'>".$locale['grmc342']."</option>
	<option".($mc_settings['mcs_top_select'] == 20 ? " selected='selected'" : "")." value='20'>".$locale['grmc343']."</option>
	<option".($mc_settings['mcs_top_select'] == 50 ? " selected='selected'" : "")." value='50'>".$locale['grmc344']."</option>
	<option".($mc_settings['mcs_top_select'] == 100 ? " selected='selected'" : "")." value='100'>".$locale['grmc345']."</option>
	<option".($mc_settings['mcs_top_select'] == 0 ? " selected='selected'" : "")." value='0'>".$locale['grmc346']."</option>
	</select>\n</td>
</tr>
<tr>
	<td class='tbl' align='center' colspan='2'> </td>
</tr>	
<tr>
	<td class='tbl1'>".$locale['grmc336']."</td>
	<td class='tbl2'><select name='vote' class='textbox'>
	<option".($mc_settings['mcs_vote_select'] == 1 ? " selected='selected'" : "")." value='1'>".$locale['grmc347']."</option>
	<option".($mc_settings['mcs_vote_select'] == 2 ? " selected='selected'" : "")." value='2'>".$locale['grmc348']."</option>
	<option".($mc_settings['mcs_vote_select'] == 7 ? " selected='selected'" : "")." value='7'>".$locale['grmc349']."</option>
	<option".($mc_settings['mcs_vote_select'] == 14 ? " selected='selected'" : "")." value='14'>".$locale['grmc350']."</option>
	</select>\n</td>
</tr>
<tr>
	<td class='tbl' align='center' colspan='2'><input type='submit' name='save' value='".$locale['grmc337']."' class='button' /></td>
</tr>
</table>\n</form>\n<a href='".FUSION_SELF.$aidlink."'>".$locale['grmc331']."</a>\n";
} elseif (IsSeT($_GET['new'])) {
	opentable($locale['grmc330']);
	if (IsSeT($_POST['save'])) {
		$interpreter = stripinput($_POST['interpreter']);
		$title = stripinput($_POST['title']);
		if ($interpreter != "" && $title != "") {
			$result = dbquery("INSERT INTO ".DB_GR_MC_CHARTS." (mcc_interpreter, mcc_title, mcc_autor) VALUES('".$interpreter."', '".$title."', '".$userdata['user_name']."')"); 
			redirect(FUSION_SELF.$aidlink);
		} else {
			redirect(FUSION_SELF.$aidlink."&new");
		}
	} else {
echo "<form action='".FUSION_SELF.$aidlink."&amp;new' method='post'>\n<table align='center' class='tbl-border' cellpadding='0' cellspacing='0'>
<tr>\n
	<td class='tbl1'>".$locale['grmc322']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='interpreter' /></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grmc323']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='title' /></td>
</tr>
<tr>
	<td align='center' classe='tbl2' colspan='2'><input class='button' name='save' type='submit' value='".$locale['grmc332']."' /></td>
</tr>
</table>\n</form>\n<a href='".FUSION_SELF.$aidlink."'>".$locale['grmc331']."</a>\n";		
	}	
} elseif (IsSeT($_GET['edit']) && isnum($_GET['id'])) {
	opentable($locale['grmc423']);
	if (IsSeT($_POST['save'])) {
		$interpreter = stripinput($_POST['interpreter']);
		$title = stripinput($_POST['title']);
		if ($interpreter != "" && $title != "") {
			$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_interpreter='".$interpreter."', mcc_title='".$title."' WHERE mcc_id='".$_GET['id']."'");
			redirect(FUSION_SELF.$aidlink);
		} else {
			redirect(FUSION_SELF.$aidlink."&edit&id=".$_GET['id']);
		}
	} else {
$result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." WHERE mcc_id='".$_GET['id']."'");
$data = dbarray($result);
echo "<form action='".FUSION_SELF.$aidlink."&amp;edit&amp;id=".$_GET['id']."' method='post'>\n<table align='center' class='tbl-border' cellpadding='0' cellspacing='0'>
<tr>\n
	<td class='tbl1'>".$locale['grmc322']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='interpreter' value='".$data['mcc_interpreter']."' /></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grmc323']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='title' value='".$data['mcc_title']."' /></td>
</tr>
<tr>
	<td align='center' classe='tbl2' colspan='2'><input class='button' name='save' type='submit' value='".$locale['grmc422']."' /></td>
</tr>
</table>\n</form>\n<a href='".FUSION_SELF.$aidlink."'>".$locale['grmc331']."</a>\n";	
	}	
} elseif (IsSeT($_GET['reset'])) {
	$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_votes='0'");
	$result2 = dbquery("TRUNCATE TABLE ".DB_GR_MC_VOTE."");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['delet']) && isnum($_GET['id'])) {
	$result = dbquery("DELETE FROM ".DB_GR_MC_CHARTS." WHERE mcc_id='".$_GET['id']."'");
	$result2 = dbquery("DELETE FROM ".DB_GR_MC_VOTE." WHERE mcv_voteid='".$_GET['id']."'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['delet_all'])) {
	$result = dbquery("TRUNCATE TABLE ".DB_GR_MC_CHARTS."");
	$result2 = dbquery("TRUNCATE TABLE ".DB_GR_MC_VOTE."");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['enable']) && !IsSet($_GET['disenable']) && isnum($_GET['id'])) {
	$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_free='1' WHERE mcc_id='".$_GET['id']."'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSet($_GET['disenable']) && !IsSeT($_GET['enable']) && isnum($_GET['id'])) {
	$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_free='0' WHERE mcc_id='".$_GET['id']."'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['enable_all']) && !IsSet($_GET['disenable_all'])) {
	$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_free='1'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSet($_GET['disenable_all']) && !IsSeT($_GET['enable_all'])) {
	$result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_free='0'");
	redirect(FUSION_SELF.$aidlink);
} else {
	opentable($locale['grmc320']);
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $rowstart_save = 0; }
	else { $rowstart_save = $_GET['rowstart']; }
	if (IsSeT($_GET['order']) && $_GET['order'] == "interpreter_asc") { $order = "interpreter_asc"; $order_save = "interpreter ASC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "interpreter_desc") { $order = "interpreter_desc"; $order_save = "interpreter DESC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "title_asc") { $order = "title_asc"; $order_save = "title ASC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "title_desc") { $order = "title_desc"; $order_save = "title DESC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "votes_asc") { $order = "votes_asc"; $order_save = "votes ASC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "votes_desc") { $order = "votes_desc"; $order_save = "votes DESC"; }
	else { $order = "free"; $order_save = "free"; }
	$charts_result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." ORDER BY mcc_".$order_save." LIMIT ".$rowstart_save.",20");
	if (dbrows($charts_result) != 0) {
		$mcs_settings_result = dbquery("SELECT * FROM ".DB_GR_MC_SETTINGS." WHERE mcs_id='1'");
		$mc_settings = dbarray($mcs_settings_result);
		echo "<div align='center'>".$locale['grmc317'].showdate("%d.%m.%Y %H:%M", $mc_settings['mcs_start']).$locale['grmc318'].showdate("%d.%m.%Y %H:%M", $mc_settings['mcs_end']).$locale['grmc319']."<br /><br />
		<table width='750' class='tbl-border' cellpadding='0' cellspacing='0'>
		<tr>
			<td width='250' class='tbl1'>".$locale['grmc322']." <img src='".INFUSIONS."gr_member_charts_panel/images/down.gif' onclick='return Interpreter_down();' /><img src='".INFUSIONS."gr_member_charts_panel/images/up.gif' onclick='return Interpreter_up();' /></td>
		  <td width='250' class='tbl1'>".$locale['grmc323']." <img src='".INFUSIONS."gr_member_charts_panel/images/down.gif' onclick='return Title_down();' /><img src='".INFUSIONS."gr_member_charts_panel/images/up.gif' onclick='return Title_up();' /></td>
		  <td width='120' class='tbl1'>".$locale['grmc324']."</td>
		  <td width='40' class='tbl1'>".$locale['grmc325']." <img src='".INFUSIONS."gr_member_charts_panel/images/down.gif' onclick='return Votes_down();' /><img src='".INFUSIONS."gr_member_charts_panel/images/up.gif' onclick='return Votes_up();' /></td>
		  <td width='40' class='tbl1'>".$locale['grmc326']."</td>
		  <td width='50' class='tbl1'>".$locale['grmc327']."</td>
		</tr>";
		while ($charts = dbarray($charts_result)) {			
			echo"<tr>
		    <td class='tbl2'>".$charts['mcc_interpreter']."</td>
		    <td class='tbl2'>".$charts['mcc_title']."</td>
		    <td class='tbl2'>".$charts['mcc_autor']."</td>
		    <td class='tbl2'>".$charts['mcc_votes']."</td>
		    <td class='tbl2'>";
		    if ($charts['mcc_free'] == 1) {
		    	echo "<a href='".FUSION_SELF.$aidlink."&amp;disenable&amp;id=".$charts['mcc_id']."' onclick='return Disenable();'><img src='".INFUSIONS."gr_member_charts_panel/images/check.gif' alt='".$locale['grmc340']."'></a>";
		    } else {
		    	echo "<a href='".FUSION_SELF.$aidlink."&amp;enable&amp;id=".$charts['mcc_id']."' onclick='return Enable();'><img src='".INFUSIONS."gr_member_charts_panel/images/delete_cross.gif' alt='".$locale['grmc341']."'></a>";
		    }
		    echo "</td>
		    <td class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;edit&amp;id=".$charts['mcc_id']."'>".$locale['grmc422']."</a><br />
		    <a href='".FUSION_SELF.$aidlink."&amp;delet&amp;id=".$charts['mcc_id']."' onclick='return Delet();'>".$locale['grmc328']."</a></td>
		  </tr>";
			
		}
		$rowsend = dbrows(dbquery("SELECT mcc_id FROM ".DB_GR_MC_CHARTS.""));
		echo "</table>".makePageNav($rowstart_save,20,$rowsend,5,FUSION_SELF.$aidlink."&amp;order=".$order."&amp;")."</div>";
	} else {
		echo "<div align='center'><br />".$locale['grmc329']."<br /></div>";
	}
}

echo "<div align='right'><a href='http://www.granade.eu/scripte/member_charts.html' target='_blank'>&copy;</a></div>";
closetable();
echo "<script type='text/javascript'>
function Delet_all() {
	return confirm('".$locale['grmc310']."');
}
function Delet() {
	return confirm('".$locale['grmc311']."');
}
function Reset() {
	return confirm('".$locale['grmc312']."');
}
function Enable() {
	return confirm('".$locale['grmc313']."');
}
function Enable_all() {
	return confirm('".$locale['grmc314']."');
}
function Disenable() {
	return confirm('".$locale['grmc315']."');
}
function Disenable_all() {
	return confirm('".$locale['grmc316']."');
}
function Interpreter_down() {
	document.location.href='".FUSION_SELF.$aidlink."&order=interpreter_asc';
}
function Interpreter_up() {
	document.location.href='".FUSION_SELF.$aidlink."&order=interpreter_desc';
}
function Title_down() {
	document.location.href='".FUSION_SELF.$aidlink."&order=title_asc';
}
function Title_up() {
	document.location.href='".FUSION_SELF.$aidlink."&order=title_desc';
}
function Votes_down() {
	document.location.href='".FUSION_SELF.$aidlink."&order=votes_asc';
}
function Votes_up() {
	document.location.href='".FUSION_SELF.$aidlink."&order=votes_desc';
}
</script>\n";
require_once THEMES."templates/footer.php";
?>