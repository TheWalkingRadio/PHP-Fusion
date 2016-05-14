<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_LinkUs v3.1 for PHP-Fusion 7
| Filename: linkus.php
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

if (IsSeT($_GET['page']) && !isnum($_GET['page'])) { redirect("index.php"); }

include INFUSIONS."gr_linkus/infusion_db.php";
if (file_exists(INFUSIONS."gr_linkus/locale/".LOCALESET."index.php")) {
	include INFUSIONS."gr_linkus/locale/".LOCALESET."index.php";
} else {
	include INFUSIONS."gr_linkus/locale/German/index.php";
}

if (IsSeT($_GET['page']) && $_GET['page'] == 2) { add_to_title($locale['global_200'].$locale['grlu103']." &gt;&gt; ".$locale['grlu111']); opentable($locale['grlu103']." &gt;&gt; ".$locale['grlu111']); $save_page=2; }
elseif (IsSeT($_GET['page']) && $_GET['page'] == 3) { add_to_title($locale['global_200'].$locale['grlu103']." &gt;&gt; ".$locale['grlu112']); opentable($locale['grlu103']." &gt;&gt; ".$locale['grlu112']); $save_page=3; }
elseif (IsSeT($_GET['page']) && $_GET['page'] == 4) { add_to_title($locale['global_200'].$locale['grlu103']." &gt;&gt; ".$locale['grlu113']); opentable($locale['grlu103']." &gt;&gt; ".$locale['grlu113']); $save_page=4; }
elseif (IsSeT($_GET['page']) && $_GET['page'] == 5) { add_to_title($locale['global_200'].$locale['grlu103']." &gt;&gt; ".$locale['grlu114']); opentable($locale['grlu103']." &gt;&gt; ".$locale['grlu114']); $save_page=5; }
else { add_to_title($locale['global_200'].$locale['grlu103']." &gt;&gt; ".$locale['grlu110']); opentable($locale['grlu103']." &gt;&gt; ".$locale['grlu110']); $save_page=1; }
	
echo "<table width='100%' align='center' class='tbl-border'>\n<tr>
<td width='20%' align='center' ".($save_page == 1 ? "class='tbl1'><b>".$locale['grlu110']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?page=1'>".$locale['grlu110']."</a>")."</td>
<td width='20%' align='center' ".($save_page == 2 ? "class='tbl1'><b>".$locale['grlu111']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?page=2'>".$locale['grlu111']."</a>")."</td>
<td width='20%' align='center' ".($save_page == 3 ? "class='tbl1'><b>".$locale['grlu112']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?page=3'>".$locale['grlu112']."</a>")."</td>
<td width='20%' align='center' ".($save_page == 4 ? "class='tbl1'><b>".$locale['grlu113']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?page=4'>".$locale['grlu113']."</a>")."</td>
<td width='20%' align='center' ".($save_page == 5 ? "class='tbl1'><b>".$locale['grlu114']."</b>" : "class='tbl2'><a href='".FUSION_SELF."?page=5'>".$locale['grlu114']."</a>")."</td>
</tr>\n</table>\n<br />\n<div align='center'>\n".$locale['grlu117']."\n<br />\n<br />\n";
	
$result = dbquery("SELECT * FROM ".DB_GR_LINKUS." WHERE lu_page='".$save_page."' ORDER BY lu_order");
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		echo "<hr class='hr' />";
		if (!$data['lu_flash']) {
			echo "<img src='".IMAGES."linkus/".$data['lu_banner']."' width='".$data['lu_width']."' height='".$data['lu_height']."' border='0' align='center' /><br />\n";
			echo "<textarea class='textbox' cols='90' rows='3' readonly='readonly'>&lt;a href=&quot;".$settings['siteurl']."&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;".$settings['siteurl']."images/linkus/".$data['lu_banner']."&quot; width=&quot;".$data['lu_width']."&quot; height=&quot;".$data['lu_height']."&quot; border=&quot;0&quot; title=&quot;".$settings['sitename']."&quot; alt=&quot;".$settings['sitename']."&quot;	&frasl;&gt;&lt;/a&gt;</textarea><br />\n";
		} else {
			echo "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab' width='".$data['lu_width']."' height='".$data['lu_height']."'> <param name='movie' value='".IMAGES."linkus/".$data['lu_banner']."'> <param name='quality' value='high'> <param name='wmode' value='transparent'> <embed src='".IMAGES."linkus/".$data['lu_banner']."' quality='high' wmode='transparent' width='".$data['lu_width']."' height='".$data['lu_height']."' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'> </embed></object><br />";
			echo "<textarea class='textbox' cols='90' rows='5' readonly='readonly'>&lt;a href=&quot;".$settings['siteurl']."&quot; title=&quot;".$settings['sitename']."&quot; alt=&quot;".$settings['sitename']."&quot; target=&quot;_blank&quot;&gt;&lt;object classid=&quot;clsid:d27cdb6e-ae6d-11cf-96b8-444553540000&quot; codebase=&quot;http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab&quot; width=&quot;".$data['lu_width']."&quot; height=&quot;".$data['lu_height']."&quot;&gt; &lt;param name=&quot;movie&quot; value=&quot;".$settings['siteurl']."images/linkus/".$data['lu_banner']."&quot;&gt; &lt;param name=&quot;quality&quot; value=&quot;high&quot;&gt; &lt;param name=&quot;wmode&quot; value=&quot;transparent&quot;&gt; &lt;embed src=&quot;".$settings['siteurl']."images/linkus/".$data['lu_banner']."&quot; quality=&quot;high&quot; wmode=&quot;transparent&quot; width=&quot;".$data['lu_width']."&quot; height=&quot;".$data['lu_height']."&quot; type=&quot;application/x-shockwave-flash&quot; pluginspage=&quot;http://www.macromedia.com/go/getflashplayer&quot;&gt; &lt;/embed&gt;&lt;/object&gt;&lt;/a&gt;</textarea><br />";
		}
	}
} else {
	echo "<hr class='hr' /><br />".$locale['grlu116']."<br /><br />";
}

echo "</div>\n<div align='right'><a href='http://www.granade.eu/scripte/linkus.html' target='_blank'>Link Us &copy;</a></div>";
closetable();

require_once THEMES."templates/footer.php";
?>