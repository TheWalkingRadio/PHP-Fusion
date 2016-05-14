<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Partner v1.1 for PHP-Fusion 7
| Filename: gr_partner_admin.php
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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";

if (!checkrights("GRPA") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }
if (IsSeT($_GET['id']) && !isnum($_GET['id'])) { redirect("../../index.php"); }
if (IsSeT($_GET['page']) && !isnum($_GET['page'])) { redirect("../../index.php"); }
if (IsSeT($_GET['order']) && !isnum($_GET['order'])) { redirect("../../index.php"); }

include INFUSIONS."gr_partner/infusion_db.php";
if (file_exists(INFUSIONS."gr_partner/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_partner/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_partner/locale/German/index.php";
}

if (IsSeT($_GET['delete']) && isnum($_GET['id'])) {
	$data = dbarray(dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_id='".$_GET['id']."'"));
	$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_order=grpa_order-1 WHERE grpa_order>'".$data['grpa_order']."'");	
	$result = dbquery("DELETE FROM ".DB_GR_PARTNER." WHERE grpa_id='".$_GET['id']."'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['up']) && isnum($_GET['id']) && isnum($_GET['order']) && isnum($_GET['page'])) {
	if ($_GET['order'] > 0) {
		$data = dbarray(dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_order='".$_GET['order']."' AND grpa_page='".$_GET['page']."'"));
		$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_order=grpa_order+1 WHERE grpa_id='".$data['grpa_id']."'");
		$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_order=grpa_order-1 WHERE grpa_id='".$_GET['id']."'");
	}
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['down']) && isnum($_GET['id']) && isnum($_GET['order']) && isnum($_GET['page'])) {
	$link_order = dbresult(dbquery("SELECT MAX(grpa_order) FROM ".DB_GR_PARTNER." WHERE grpa_page='".$_GET['page']."'"), 0) + 1;
	if ($_GET['order'] < $link_order) {
		$data = dbarray(dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_order='".$_GET['order']."' AND grpa_page='".$_GET['page']."'"));
		$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_order=grpa_order-1 WHERE grpa_id='".$data['grpa_id']."'");
		$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_order=grpa_order+1 WHERE grpa_id='".$_GET['id']."'");
	}
	redirect(FUSION_SELF.$aidlink);
}	elseif (IsSeT($_GET['edit']) && isnum($_GET['id'])) {
	opentable($locale['grpa121']);
	if(IsSeT($_POST['save'])) {
		$name = stripinput(trim(eregi_replace(" +", " ", $_POST['name'])));
		$hp = stripinput(trim(eregi_replace(" +", " ", $_POST['hp'])));
		$pic1 = stripinput(trim(eregi_replace(" +", " ", $_POST['pic1'])));
		$pic2 = stripinput(trim(eregi_replace(" +", " ", $_POST['pic2'])));
		if (isnum($_POST['page']) && $name!="" && $hp !="" && ($pic1!="" || $pic2!="")) {
			if ($pic1 != "") {
				$pic = IMAGES."partner/".$pic1;
			} else {
				$pic = $pic2;
			}
			$result = dbquery("UPDATE ".DB_GR_PARTNER." SET grpa_page='".$_POST['page']."', grpa_title='".$name."', grpa_hp='".$hp."', grpa_pic='".$pic."' WHERE grpa_id='".$_GET['id']."'");
		}
		redirect(FUSION_SELF.$aidlink);
	} else {
		$result = dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_id='".$_GET['id']."'");
		$data = dbarray($result);
		echo "<form action='".FUSION_SELF.$aidlink."&edit&id=".$_GET['id']."' method='post'><table class='tbl-border' align='center' width='80%'>
			<tr>
			 <td class='tbl1' width='30%'>".$locale['grpa116']."</td>
			 <td class='tbl2'><select name='page' class='textbox' style='width:300px;'>
			 <option".($data['grpa_page'] == 1 ? " selected" : "")." value='1'>".$locale['grpa110']."</option>
			 <option".($data['grpa_page'] == 2 ? " selected" : "")." value='2'>".$locale['grpa111']."</option>
			 <option".($data['grpa_page'] == 3 ? " selected" : "")." value='3'>".$locale['grpa112']."</option>
			 <option".($data['grpa_page'] == 4 ? " selected" : "")." value='4'>".$locale['grpa113']."</option>
			 </select></td>
			</tr>
			<tr>
			 <td class='tbl1'>".$locale['grpa117']."</td>
			 <td class='tbl2'><input type='text' name='name' class='textbox' style='width:300px;' maxlength='50' value='".$data['grpa_title']."' /></td>
			</tr>
			<tr>
			 <td class='tbl1'>".$locale['grpa118']."</td>
			 <td class='tbl2'><input type='text' name='hp' class='textbox' style='width:300px;' maxlength='50' value='".$data['grpa_hp']."' /> (mit http://)</td>
			</tr>
			<tr>
			 <td class='tbl1' rowspan='2' valign='top'>".$locale['grpa119']."</td>
			 <td class='tbl2'>";
			$image_files = makefilelist(IMAGES."partner/", ".|..|index.php", true);
			$image_list = makefileopts($image_files);
			echo "<select name='pic1' class='textbox' style='width:300px;'>
			<option value=''></option>
			$image_list</select> (Wird bevorzugt)</td>
			</tr>
			<tr>
			 <td class='tbl2'><input type='text' name='pic2' class='textbox' style='width:300px;' maxlength='100' value='".$data['grpa_pic']."' /> (mit http://)</td>
			</tr>
			<tr>
			 <td class='tbl1' colspan='2' align='center'><input type='submit' name='save' class='button' value='".$locale['grpa120']."' /></td>
			</tr>
		</table>\n</form>\n<br />";
	}	
} elseif (IsSeT($_GET['new'])) {
	opentable($locale['grpa115']);
	if(IsSeT($_POST['save'])) {
		$name = stripinput(trim(eregi_replace(" +", " ", $_POST['name'])));
		$hp = stripinput(trim(eregi_replace(" +", " ", $_POST['hp'])));
		$pic1 = stripinput(trim(eregi_replace(" +", " ", $_POST['pic1'])));
		$pic2 = stripinput(trim(eregi_replace(" +", " ", $_POST['pic2'])));
		if (isnum($_POST['page']) && $name!="" && $hp !="" && ($pic1!="" || $pic2!="")) {
			if ($pic1 != "") {
				$pic = IMAGES."partner/".$pic1;
			} else {
				$pic = $pic2;
			}
			$link_order = dbresult(dbquery("SELECT MAX(grpa_order) FROM ".DB_GR_PARTNER." WHERE grpa_page='".$_POST['page']."'"), 0) + 1;
			$result = dbquery("INSERT INTO ".DB_GR_PARTNER." (grpa_order, grpa_page, grpa_title, grpa_hp, grpa_pic) VALUES('".$link_order."', '".$_POST['page']."', '".$name."', '".$hp."', '".$pic."')"); 
		}		
		redirect(FUSION_SELF.$aidlink);
	} else {
		echo "<form action='".FUSION_SELF.$aidlink."&new' method='post'><table class='tbl-border' align='center' width='80%'>
			<tr>
			 <td class='tbl1' width='30%'>".$locale['grpa116']."</td>
			 <td class='tbl2'><select name='page' class='textbox' style='width:300px;'>
			 <option value='1'>".$locale['grpa110']."</option>
			 <option value='2'>".$locale['grpa111']."</option>
			 <option value='3'>".$locale['grpa112']."</option>
			 <option value='4'>".$locale['grpa113']."</option>
			 </select></td>
			</tr>
			<tr>
			 <td class='tbl1'>".$locale['grpa117']."</td>
			 <td class='tbl2'><input type='text' name='name' class='textbox' style='width:300px;' maxlength='50' /></td>
			</tr>
			<tr>
			 <td class='tbl1'>".$locale['grpa118']."</td>
			 <td class='tbl2'><input type='text' name='hp' class='textbox' style='width:300px;' maxlength='50' value='http://' /> (mit http://)</td>
			</tr>
			<tr>
			 <td class='tbl1' rowspan='2' valign='top'>".$locale['grpa119']."</td>
			 <td class='tbl2'>";
			$image_files = makefilelist(IMAGES."partner/", ".|..|index.php", true);
			$image_list = makefileopts($image_files);
			echo "<select name='pic1' class='textbox' style='width:300px;'>
			<option value=''></option>
			$image_list</select> (Wird bevorzugt)</td>
			</tr>
			<tr>
			 <td class='tbl2'><input type='text' name='pic2' class='textbox' style='width:300px;' maxlength='100' value='http://' /> (mit http://)</td>
			</tr>
			<tr>
			 <td class='tbl1' colspan='2' align='center'><input type='submit' name='save' class='button' value='".$locale['grpa120']."' /></td>
			</tr>
		</table>\n</form>\n<br />";
	}	
}	else {
	if (IsSeT($_GET['werbepartner'])) { opentable($locale['grpa111']); $page=2; }
	elseif (IsSeT($_GET['sponsoren'])) { opentable($locale['grpa112']); $page=3; }
	elseif (IsSeT($_GET['werbung'])) { opentable($locale['grpa113']); $page=4; }
	else { opentable($locale['grpa110']); $partner=true; $page=1; }
	
	echo "<table width='100%' align='center' class='tbl-border'>\n<tr>
		<td width='25%' align='center' ".($page == 1 ? "class='tbl1'><b>".$locale['grpa110']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;partner'>".$locale['grpa110']."</a>")."</td>
		<td width='25%' align='center' ".($page == 2 ? "class='tbl1'><b>".$locale['grpa111']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;werbepartner'>".$locale['grpa111']."</a>")."</td>
		<td width='25%' align='center' ".($page == 3 ? "class='tbl1'><b>".$locale['grpa112']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;sponsoren'>".$locale['grpa112']."</a>")."</td>
		<td width='25%' align='center' ".($page == 4 ? "class='tbl1'><b>".$locale['grpa113']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;werbung'>".$locale['grpa113']."</a>")."</td>
	</tr>\n<tr>\n<td width='25%' align='center' class='tbl2'><a href='".FUSION_SELF.$aidlink."&new'>".$locale['grpa115']."</a></td>\n</tr>\n</table><br />";
		
	$result = dbquery("SELECT * FROM ".DB_GR_PARTNER." WHERE grpa_page='".$page."' ORDER BY grpa_order");
	if (dbrows($result)) {
		$i=1;
		while ($data = dbarray($result)) {
		echo "<table width='500' align='center' class='tbl-border'>\n<tr>
			<td colspan='2' align='center' class='tbl1'><a href='".$data['grpa_hp']."' target='_blank'><img src='".BASEDIR.$data['grpa_pic']."' width='468' height='60' border='0' alt='".$data['grpa_title']."' title='".$data['grpa_title']."' /></a></td>
		</tr>\n<tr>
			<td width='50%' align='center' class='tbl2'><b>".$data['grpa_title']."</b></td>
			<td width='50%' align='center' class='tbl2'><a href='".$data['grpa_hp']."' target='_blank'>".$data['grpa_hp']."</a></td>
		</tr>\n</table><div align='center'><a href='".FUSION_SELF.$aidlink."&edit&id=".$data['grpa_id']."'>".$locale['grpa121']."</a> || <a href='".FUSION_SELF.$aidlink."&delete&id=".$data['grpa_id']."'>".$locale['grpa122']."</a><br />";
		if (1 < dbrows($result)) {
			$up = $data['grpa_order'] - 1;
			$down = $data['grpa_order'] + 1;
			if ($i==1) {
				echo "<a href='".FUSION_SELF.$aidlink."&amp;down&amp;page=".$page."&amp;order=".$down."&amp;id=".$data['grpa_id']."'><img src='".THEME."images/down.gif' alt='' title='' style='border:0px;' /></a>";
			} elseif ($i < dbrows($result)) {
				echo "<a href='".FUSION_SELF.$aidlink."&amp;up&amp;page=".$page."&amp;order=".$up."&amp;id=".$data['grpa_id']."'><img src='".THEME."images/up.gif' alt='' title='' style='border:0px;' /></a>";
				echo "<a href='".FUSION_SELF.$aidlink."&amp;down&amp;page=".$page."&amp;order=".$down."&amp;id=".$data['grpa_id']."'><img src='".THEME."images/down.gif' alt='' title='' style='border:0px;' /></a>";
			} else {
				echo "<a href='".FUSION_SELF.$aidlink."&amp;up&amp;page=".$page."&amp;order=".$up."&amp;id=".$data['grpa_id']."'><img src='".THEME."images/up.gif' alt='' title='' style='border:0px;' /></a>";
			}
			echo "<hr class='hr' />";
		}
		$i++;
		echo "</div>";
		}
	} else {
		echo "<div align='center'>".$locale['grpa123']."<br /><br /></div>";
	}
}
echo "<div align='right'><a href='http://www.granade.eu/scripte/partner.html' target='_blank'>Partner &copy;</a></div>";
closetable();

require_once THEMES."templates/footer.php";
?>