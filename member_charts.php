<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Member_Charts v1.1 for PHP-Fusion 7
| Filename: member_charts.php
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
require_once "maincore.php";
require_once THEMES."templates/header.php";

if (IsSeT($_GET['id']) && !isnum($_GET['id'])) { redirect("index.php"); }

include INFUSIONS."gr_member_charts_panel/infusion_db.php";
if (file_exists(INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_member_charts_panel/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_member_charts_panel/locale/German/index.php";
}

if (iMEMBER && IsSeT($_GET['new_song'])) {
	add_to_title($locale['global_200'].$locale['grmc400']);
	opentable($locale['grmc400']);
	if (IsSeT($_POST['save'])) {
		$interpreter = stripinput($_POST['interpreter']);
		$title = stripinput($_POST['title']);
		if ($interpreter != "" && $title != "") {
			$result = dbquery("INSERT INTO ".DB_GR_MC_CHARTS." (mcc_interpreter, mcc_title, mcc_autor) VALUES('".$interpreter."', '".$title."', '".$userdata['user_name']."')"); 
			$result2 = dbquery("INSERT INTO ".DB_MESSAGES." (message_to, message_from, message_subject, message_message, message_smileys, message_read, message_datestamp, message_folder) VALUES ('1', '".$userdata["user_id"]."', '".$locale['grmc424']."', '".$locale['grmc425']."', 'n', '0', '".time()."', '0')"); 
			redirect(FUSION_SELF."?thanks");
		} else {
			redirect(FUSION_SELF."?new_song");
		}
	} else {
echo "<form action='".FUSION_SELF."?new_song' method='post'><table align='center' class='tbl-border' cellpadding='0' cellspacing='0'>
<tr>
	<td class='tbl1'>".$locale['grmc401']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='interpreter' /></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grmc402']."</td>
	<td class='tbl2'><input class='textbox' maxlength='100' style='width: 200px;' type='text' name='title' /></td>
</tr>
<tr>
	<td align='center' classe='tbl2' colspan='2'><input class='button' name='save' type='submit' value='".$locale['grmc403']."' /></td>
</tr>
</table>\n</form>\n<a href='".FUSION_SELF."'>".$locale['grmc404']."</a>\n";		
	}
} else {
if (IsSeT($_GET['thanks'])) {
	opentable($locale['grmc420']);
	echo "<div align='center'>".$locale['grmc421']."</div>";
	closetable();
}
add_to_title($locale['global_200'].$locale['grmc419']);
opentable($locale['grmc419']);
if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $rowstart_save = 0; }
else { $rowstart_save = $_GET['rowstart']; }
$mcs_settings_result = dbquery("SELECT * FROM ".DB_GR_MC_SETTINGS." WHERE mcs_id='1'");
$mc_settings = dbarray($mcs_settings_result);
$time = time();
$vtime = $time  - ($mc_settings['mcs_vote_select']*24*60*60);
if (iMEMBER) {
	$mcv_freigabe_result = dbquery("SELECT * FROM ".DB_GR_MC_VOTE." WHERE (mcv_userid='".$userdata['user_id']."' OR mcv_ip='".USER_IP."') AND mcv_votetime > '".$vtime."'");
	$mc_freigabe = dbrows($mcv_freigabe_result);
} elseif (!iMEMBER) {
	$mcv_freigabe_result = dbquery("SELECT * FROM ".DB_GR_MC_VOTE." WHERE mcv_ip='".USER_IP."' AND mcv_votetime > '".$vtime."'");
	$mc_freigabe = dbrows($mcv_freigabe_result);
}
echo "<div align='center'>";
if (($mc_settings['mcs_end'] >= $time && $time >= $mc_settings['mcs_start']) && $mc_freigabe == 0) {
	if (IsSet($_POST['vote_now']) && isnum($_GET['id'])) {
		$vote_result = dbquery("UPDATE ".DB_GR_MC_CHARTS." SET mcc_votes=mcc_votes+1 WHERE mcc_id='".$_GET['id']."'");
		$vote_result = dbquery("INSERT INTO ".DB_GR_MC_VOTE." (mcv_userid, mcv_ip, mcv_voteid, mcv_votetime) VALUES('".$userdata['user_id']."', '".USER_IP."', '".$_GET['id']."', '".time()."')"); 
		redirect(FUSION_SELF);
	}
	if (IsSeT($_GET['order']) && $_GET['order'] == "interpreter_desc") { $order = "interpreter_desc"; $order_save = "interpreter DESC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "title_asc") { $order = "title_asc"; $order_save = "title ASC"; }
	elseif (IsSeT($_GET['order']) && $_GET['order'] == "title_desc") { $order = "title_desc"; $order_save = "title DESC"; }
	else { $order = "interpreter_asc"; $order_save = "interpreter ASC"; }

	echo $locale['grmc406']."(".showdate("%d.%m.%Y %H:%M", $mc_settings['mcs_start'])." - ".showdate("%d.%m.%Y %H:%M", $mc_settings['mcs_end']).")<br /><br />";
	$charts_vote_result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1' ORDER BY mcc_".$order_save." LIMIT ".$rowstart_save.",20");
	if (dbrows($charts_vote_result) != 0) {
	 	echo "<table width='600' class='tbl-border' cellpadding='0' cellspacing='0'>
		<tr>
			<td width='250' class='tbl1'>".$locale['grmc410']." <img src='".INFUSIONS."gr_member_charts_panel/images/down.gif' onclick='return Interpreter_down();' /><img src='".INFUSIONS."gr_member_charts_panel/images/up.gif' onclick='return Interpreter_up();' /></td>
		  <td width='250' class='tbl1'>".$locale['grmc411']." <img src='".INFUSIONS."gr_member_charts_panel/images/down.gif' onclick='return Title_down();' /><img src='".INFUSIONS."gr_member_charts_panel/images/up.gif' onclick='return Title_up();' /></td>";
		  if ($mc_settings['mcs_autor_select'] == 1) {
		  	echo"<td width='100' class='tbl1'>".$locale['grmc412']."</td>";
		  }
		  echo "<td width='100' class='tbl1'>".$locale['grmc413']."</td>
		</tr>";
		while ($charts = dbarray($charts_vote_result)) {
		echo "<tr>
		  <td class='tbl2'>".$charts['mcc_interpreter']."</td>
		  <td class='tbl2'>".$charts['mcc_title']."</td>";
		  if ($mc_settings['mcs_autor_select'] == 1) {
		  	echo"<td class='tbl2'>".$charts['mcc_autor']."</td>";
		  }
		  echo "<td class='tbl2'><form action='".FUSION_SELF."?id=".$charts['mcc_id']."' method='post'>
		  <input type='submit' name='vote_now' value='".$locale['grmc414']."' class='button' /></form></td>
		</tr>";
		}
		$rowsend = dbrows(dbquery("SELECT mcc_id FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1'"));
		echo "</table><br>".makePageNav($rowstart_save,20,$rowsend,3,FUSION_SELF."?order=".$order."&amp;");
	} else {
		echo "<br />".$locale['grmc415'];
	}
} else {
	echo $locale['grmc416'];
	if ($mc_settings['mcs_top_select'] != 0) {
		echo "TOP ".$mc_settings['mcs_top_select']." ";
	}
	echo "power by ".$settings['sitename']."</b><br />";
	if ($mc_settings['mcs_top_select'] > 10 || $mc_settings['mcs_top_select'] == 0) {
		$max = 20;
	} else {
		$max = 10;
	}
	$charts_top_result = dbquery("SELECT * FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1' ORDER BY mcc_votes DESC LIMIT ".$rowstart_save.",".$max."");
	if (dbrows($charts_top_result) != 0) {
	 	if ($mc_freigabe != 0 && !IsSeT($_GET['thanks'])) {
			echo "<br />".$locale['grmc417']."<br />";
		}
		echo"<br /><table width='600' class='tbl-border' cellpadding='0' cellspacing='0'>
		<tr>
			<td width='50' class='tbl1'>".$locale['grmc407']."</td>
			<td width='250' class='tbl1'>".$locale['grmc401']."</td>
		  <td width='250' class='tbl1'>".$locale['grmc402']."</td>
		  <td width='50' class='tbl1'>".$locale['grmc408']."</td>
		</tr>";
		$i = $rowstart_save+1;
		while ($charts = dbarray($charts_top_result)) {
		 	echo "<tr>
		    <td class='tbl2'>".$i."</td>
		    <td class='tbl2'>".$charts['mcc_interpreter']."</td>
		    <td class='tbl2'>".$charts['mcc_title']."</td>
		    <td class='tbl2'>".$charts['mcc_votes']."</td>
		  </tr>";
		  $i++;
		}
		if ($mc_settings['mcs_top_select'] != 0) {
			$rowsend = dbrows(dbquery("SELECT mcc_id FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1' LIMIT 0,".$mc_settings['mcs_top_select'].""));
			echo "</table>".makePageNav($rowstart_save,$max,$rowsend,3,FUSION_SELF."?");
		} else {
			$rowsend = dbrows(dbquery("SELECT mcc_id FROM ".DB_GR_MC_CHARTS." WHERE mcc_free='1'"));
			echo "</table>".makePageNav($rowstart_save,$max,$rowsend,3,FUSION_SELF."?");
		}
	} else {
		echo "<br /><br />".$locale['grmc409'];
	}
}
if (iMEMBER) { echo "<div class='small1'>[ <a href='".FUSION_SELF."?new_song'>".$locale['grmc405']."</a> ]</div>"; }
}

echo "</div><div align='right'><a href='http://www.granade.eu/scripte/member_charts.html' target='_blank'>Member Charts &copy;</a></div>";
closetable();

echo "<script type='text/javascript'>
function Interpreter_down() {
	document.location.href='".FUSION_SELF."?order=interpreter_asc';
}
function Interpreter_up() {
	document.location.href='".FUSION_SELF."?order=interpreter_desc';
}
function Title_down() {
	document.location.href='".FUSION_SELF."?order=title_asc';
}
function Title_up() {
	document.location.href='".FUSION_SELF."?order=title_desc';
}
</script>\n";

require_once THEMES."templates/footer.php";  
?>