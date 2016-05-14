<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_LinkUs v3.1 for PHP-Fusion 7
| Filename: gr_linkus_admin.php
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

if (!checkrights("GRLU") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }
if (IsSeT($_GET['id']) && !isnum($_GET['id'])) { redirect("../../index.php"); }
if (IsSeT($_GET['page']) && !isnum($_GET['page'])) { redirect("../../index.php"); }
if (IsSeT($_GET['order']) && !isnum($_GET['order'])) { redirect("../../index.php"); }

include INFUSIONS."gr_linkus/infusion_db.php";
if (file_exists(INFUSIONS."gr_linkus/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_linkus/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_linkus/locale/German/index.php";
}

if (IsSeT($_GET['delete']) && isnum($_GET['id'])) {
	$data = dbarray(dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_id='".$_GET['id']."'"));
	$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_order=lu_order-1 WHERE lu_order>'".$data['lu_order']."'");	
	$result = dbquery("DELETE FROM ".DB_GR_LINKUS." WHERE lu_id='".$_GET['id']."'");
	redirect(FUSION_SELF.$aidlink);
} elseif (IsSeT($_GET['up']) && isnum($_GET['id']) && isnum($_GET['order']) && isnum($_GET['page'])) {
	if ($_GET['order'] > 0) {
		$data = dbarray(dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_order='".$_GET['order']."' AND lu_page='".$_GET['page']."'"));
		$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_order=lu_order+1 WHERE lu_id='".$data['lu_id']."'");
		$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_order=lu_order-1 WHERE lu_id='".$_GET['id']."'");
	}
	redirect(FUSION_SELF.$aidlink."&page=".$_GET['page']);
} elseif (IsSeT($_GET['down']) && isnum($_GET['id']) && isnum($_GET['order']) && isnum($_GET['page'])) {
	$link_order = dbresult(dbquery("SELECT MAX(lu_order) FROM ".DB_GR_LINKUS." WHERE lu_page='".$_GET['page']."'"), 0) + 1;
	if ($_GET['order'] < $link_order) {
		$data = dbarray(dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_order='".$_GET['order']."' AND lu_page='".$_GET['page']."'"));
		$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_order=lu_order-1 WHERE lu_id='".$data['lu_id']."'");
		$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_order=lu_order+1 WHERE lu_id='".$_GET['id']."'");
	}
	redirect(FUSION_SELF.$aidlink."&page=".$_GET['page']);
}	elseif (IsSeT($_GET['edit']) && isnum($_GET['id'])) {
	if (IsSeT($_POST['save'])) {
		if (!IsSeT($_POST['page']) && !isnum($_POST['page'])) { redirect("../../index.php"); }
		if ($_POST['page'] == 1 || $_POST['page'] == 3) {
			$lu_width = "468";	$lu_height = "60";
			if ($_POST['page'] != 3) {	$lu_flash = "0";	} else { $lu_flash = "1"; }
		} elseif ($_POST['page'] == 2 || $_POST['page'] == 4) {
			$lu_width = "88";	$lu_height = "31";
			if ($_POST['page'] != 4) {	$lu_flash = "0";	} else { $lu_flash = "1"; }
		} elseif ($_POST['page'] == 5) {
			$lu_width = isnum($_POST['width']) ? $_POST['width'] : "0";
			$lu_height = isnum($_POST['height']) ? $_POST['height'] : "0";
			$lu_flash = isnum($_POST['flash']) ? $_POST['flash'] : "0";
		} else {
			redirect(BASEDIR."index.php");
		}
		$banner = stripinput($_POST['banner']);
		if ($banner != "" && $lu_width != 0 && $lu_height != 0 && $lu_flash != "") {
			$result = dbquery("UPDATE ".DB_GR_LINKUS." SET lu_banner='".$banner."', lu_width='".$lu_width."', lu_height='".$lu_height."', lu_flash='".$lu_flash."' WHERE lu_id='".$_GET['id']."'");
			redirect(FUSION_SELF.$aidlink."&page=".$_POST['page']);
		} else {
			redirect(BASEDIR."index.php");
		}
	} else {
opentable($locale['grlu104']);
$result = dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_id='".$_GET['id']."'");
$data = dbarray($result);
if ($data['lu_page'] == 1) {	$page = $locale['grlu110'];	} 
elseif ($data['lu_page'] == 2) {	$page = $locale['grlu111'];	} 
elseif ($data['lu_page'] == 3) {	$page = $locale['grlu112'];	} 
elseif ($data['lu_page'] == 4) {	$page = $locale['grlu113'];	} 
elseif ($data['lu_page'] == 5) {	$page = $locale['grlu114'];	}
else {	redirect(BASEDIR."index.php");	}
echo "<form action='".FUSION_SELF.$aidlink."&amp;edit&amp;id=".$_GET['id']."' method='post'><table class='tbl-border' align='center' width='80%'>
<tr>
	<td class='tbl1' width='30%'>".$locale['grlu127']."</td>
	<td class='tbl2'><input type='text' class='textbox' style='width:300px;' value='".$page."' readonly='readonly' />
	<input type='hidden' name='page' value='".$data['lu_page']."' /></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu121']."</td>
	<td class='tbl2'>";
	$image_files = makefilelist(IMAGES."linkus/", ".|..|index.php", true);
	$image_list = makefileopts($image_files, $data['lu_banner']);
	echo "<select name='banner' class='textbox' style='width:300px;'>
	<option value=''>".$locale['grlu120']."</option>
	$image_list</select></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu122']."</td>
	<td class='tbl2'><input type='text' name='width' class='textbox' style='width:300px;' maxlength='3' value='".$data['lu_width']."' /><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu123']."</td>
	<td class='tbl2'><input type='text' name='height' class='textbox' style='width:300px;' maxlength='3' value='".$data['lu_height']."' /><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu124']."</td>
	<td class='tbl2'><select name='flash' class='textbox' style='width:300px;'>
	<option".($data['lu_flash'] == 0 ? " selected='selected'" : "")." value='0'>".$locale['grlu126']."</option>\n<option".($data['lu_flash'] == 1 ? " selected='selected'" : "")." value='1'>".$locale['grlu125']."</option>\n
	</select><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1' colspan='2' align='center'><input type='submit' name='save' class='button' value='".$locale['grlu128']."' /></td>
</tr>
</table>\n</form>\n<br />\n<div align='center'>".$locale['grlu129']."</div>\n<br />\n";
	}
} elseif (IsSeT($_GET['new'])) {
	if (IsSeT($_POST['save'])) {
		if (!IsSeT($_POST['page']) && !isnum($_POST['page'])) { redirect("../../index.php"); }
		if ($_POST['page'] == 1 || $_POST['page'] == 3) {
			$lu_width = "468";	$lu_height = "60";
			if ($_POST['page'] != 3) {	$lu_flash = "0";	} else { $lu_flash = "1"; }
		} elseif ($_POST['page'] == 2 || $_POST['page'] == 4) {
			$lu_width = "88";	$lu_height = "31";
			if ($_POST['page'] != 4) {	$lu_flash = "0";	} else { $lu_flash = "1"; }
		} elseif ($_POST['page'] == 5) {
			$lu_width = isnum($_POST['width']) ? $_POST['width'] : "0";
			$lu_height = isnum($_POST['height']) ? $_POST['height'] : "0";
			$lu_flash = isnum($_POST['flash']) ? $_POST['flash'] : "0";
		} else {
			redirect(BASEDIR."index.php");
		}
		$banner = stripinput($_POST['banner']);
		if ($banner != "" && $lu_width != 0 && $lu_height != 0 && $lu_flash != "") {
			$link_order = dbresult(dbquery("SELECT MAX(lu_order) FROM ".DB_GR_LINKUS." WHERE lu_page='".$_POST['page']."'"), 0) + 1;
			$result = dbquery("INSERT INTO ".DB_GR_LINKUS." (lu_page, lu_order, lu_banner, lu_width, lu_height, lu_flash) VALUES ('".$_POST['page']."', '".$link_order."', '".$banner."', '".$lu_width."', '".$lu_height."', '".$lu_flash."')");
			redirect(FUSION_SELF.$aidlink."&page=".$_POST['page']);
		} else {
			redirect(BASEDIR."index.php");
		}
	} else {
opentable($locale['grlu104']);
echo "<form action='".FUSION_SELF.$aidlink."&amp;new' method='post'><table class='tbl-border' align='center' width='80%'>
<tr>
	<td class='tbl1' width='30%'>".$locale['grlu127']."</td>
	<td class='tbl2'><select name='page' class='textbox' style='width:300px;'>
	<option value='1'>".$locale['grlu110']."</option>
	<option value='2'>".$locale['grlu111']."</option>
	<option value='3'>".$locale['grlu112']."</option>
	<option value='4'>".$locale['grlu113']."</option>
	<option value='5'>".$locale['grlu114']."</option>
	</select></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu121']."</td>
	<td class='tbl2'>";
	$image_files = makefilelist(IMAGES."linkus/", ".|..|index.php", true);
	$image_list = makefileopts($image_files);
	echo "<select name='banner' class='textbox' style='width:300px;'>
	<option value=''>".$locale['grlu120']."</option>
	$image_list</select></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu122']."</td>
	<td class='tbl2'><input type='text' name='width' class='textbox' style='width:300px;' maxlength='3' /><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu123']."</td>
	<td class='tbl2'><input type='text' name='height' class='textbox' style='width:300px;' maxlength='3' /><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1'>".$locale['grlu124']."</td>
	<td class='tbl2'><select name='flash' class='textbox' style='width:300px;'>
	<option value='0'>".$locale['grlu126']."</option>\n<option value='1'>".$locale['grlu125']."</option>\n
	</select><span style='color:red'>*</span></td>
</tr>
<tr>
	<td class='tbl1' colspan='2' align='center'><input type='submit' name='save' class='button' value='".$locale['grlu128']."' /></td>
</tr>
</table>\n</form>\n<br />\n<div align='center'>".$locale['grlu129']."</div>\n<br />\n";
	}
} else {
	if (IsSeT($_GET['page']) && $_GET['page'] == 2) { opentable($locale['grlu104']." &gt;&gt; ".$locale['grlu111']); $save_page=2; }
	elseif (IsSeT($_GET['page']) && $_GET['page'] == 3) { opentable($locale['grlu104']." &gt;&gt; ".$locale['grlu112']); $save_page=3; }
	elseif (IsSeT($_GET['page']) && $_GET['page'] == 4) { opentable($locale['grlu104']." &gt;&gt; ".$locale['grlu113']); $save_page=4; }
	elseif (IsSeT($_GET['page']) && $_GET['page'] == 5) { opentable($locale['grlu104']." &gt;&gt; ".$locale['grlu114']); $save_page=5; }
	else { opentable($locale['grlu104']." &gt;&gt; ".$locale['grlu110']); $save_page=1; }
	
	echo "<table width='100%' align='center' class='tbl-border'>\n<tr>
		<td width='20%' align='center' ".($save_page == 1 ? "class='tbl1'><b>".$locale['grlu110']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;page=1'>".$locale['grlu110']."</a>")."</td>
		<td width='20%' align='center' ".($save_page == 2 ? "class='tbl1'><b>".$locale['grlu111']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;page=2'>".$locale['grlu111']."</a>")."</td>
		<td width='20%' align='center' ".($save_page == 3 ? "class='tbl1'><b>".$locale['grlu112']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;page=3'>".$locale['grlu112']."</a>")."</td>
		<td width='20%' align='center' ".($save_page == 4 ? "class='tbl1'><b>".$locale['grlu113']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;page=4'>".$locale['grlu113']."</a>")."</td>
		<td width='20%' align='center' ".($save_page == 5 ? "class='tbl1'><b>".$locale['grlu114']."</b>" : "class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;page=5'>".$locale['grlu114']."</a>")."</td>
	</tr>\n<tr>\n<td width='20%' align='center' class='tbl2'><a href='".FUSION_SELF.$aidlink."&new'>".$locale['grlu115']."</a></td>\n</tr>\n</table>\n<br />\n<div align='center'>\n";

	$result = dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_page='".$save_page."' ORDER BY lu_order");
	if (dbrows($result)) {
		$i=1;
		while ($data = dbarray($result)) {
			if (!$data['lu_flash']) {
				echo "<img src='".IMAGES."linkus/".$data['lu_banner']."' width='".$data['lu_width']."' height='".$data['lu_height']."' border='0' align='center' /><br />\n";
			} else {
				echo "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab' width='".$data['lu_width']."' height='".$data['lu_height']."'> <param name='movie' value='".IMAGES."linkus/".$data['lu_banner']."'> <param name='quality' value='high'> <param name='wmode' value='transparent'> <embed src='".IMAGES."linkus/".$data['lu_banner']."' quality='high' wmode='transparent' width='".$data['lu_width']."' height='".$data['lu_height']."' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'> </embed></object><br />";
			}
			echo "<a href='".FUSION_SELF.$aidlink."&amp;edit&amp;id=".$data['lu_id']."'>".$locale['grlu118']."</a> || <a href='".FUSION_SELF.$aidlink."&amp;delete&amp;id=".$data['lu_id']."'>".$locale['grlu119']."</a><br />";
			if (1 < dbrows($result)) {
				$up = $data['lu_order'] - 1;
				$down = $data['lu_order'] + 1;
				if ($i==1) {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;down&amp;page=".$save_page."&amp;order=".$down."&amp;id=".$data['lu_id']."'><img src='".THEME."images/down.gif' alt='' title='' style='border:0px;' /></a>";
				} elseif ($i < dbrows($result)) {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;up&amp;page=".$save_page."&amp;order=".$up."&amp;id=".$data['lu_id']."'><img src='".THEME."images/up.gif' alt='' title='' style='border:0px;' /></a>";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;down&amp;page=".$save_page."&amp;order=".$down."&amp;id=".$data['lu_id']."'><img src='".THEME."images/down.gif' alt='' title='' style='border:0px;' /></a>";
				} else {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;up&amp;page=".$save_page."&amp;order=".$up."&amp;id=".$data['lu_id']."'><img src='".THEME."images/up.gif' alt='' title='' style='border:0px;' /></a>";
				}
				echo "<hr class='hr' />";
			}
			$i++;
		}
	} else {
		echo $locale['grlu116']."<br />";
	}
}
echo "</div><div align='right'><a href='http://www.granade.eu/scripte/linkus.html' target='_blank'>Link Us &copy;</a></div>";
closetable();

require_once THEMES."templates/footer.php";
?>