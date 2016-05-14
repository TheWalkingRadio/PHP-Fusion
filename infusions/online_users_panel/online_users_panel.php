<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: online_users_panel.php
| Author: ptown67
| Website: http://pennerprofi.bplaced.net
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

include INFUSIONS."online_users_panel/infusion_db.php";

add_to_head("
	<link rel='stylesheet' type='text/css' href='".INFUSIONS."online_users_panel/tooltip.css' />
	<script src='".INCLUDES."jquery.tooltip.pack.js' type='text/javascript'></script>
	<script type='text/javascript'>
	$(function() {
	$('#boxover a').tooltip({
	track: true,
	delay: 0,
	showURL: false,
	showBody: ' - ',
	fade: 250
	});
	});
	</script>
	");

if (isset($_SERVER['HTTP_USER_AGENT']) && array_key_exists('HTTP_USER_AGENT', $_SERVER))
	$trackUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
else
	$trackUserAgent = "";

$bots = array(
'archiver' => 'alexa',
'exabot' => 'exalead',
'fast' => 'fast',
'firefly' => 'fireball',
'googlebot' => 'google',
'msnbot' => 'msn',
'architextspider' => 'excite',
'lycos_spider' => 'lycos',
'slurp' => 'yahoo'
);

foreach ($bots as $bot_ua => $bot_db) {
if (stristr($trackUserAgent, $bot_ua)) { $result = dbquery("UPDATE ".DB_ONLINE_SETTINGS." SET online_".$bot_db."='".time()."'"); }
}

$result = dbquery("SELECT * FROM ".DB_ONLINE_SETTINGS);
$online = dbarray($result);

$result = dbquery("SELECT * FROM ".DB_ONLINE." WHERE online_user=".($userdata['user_level'] != 0 ? "'".$userdata['user_id']."'" : "'0' AND online_ip='".USER_IP."'"));
if (dbrows($result)) {
$result = dbquery("UPDATE ".DB_ONLINE." SET online_lastactive='".time()."' WHERE online_user=".($userdata['user_level'] != 0 ? "'".$userdata['user_id']."'" : "'0' AND online_ip='".USER_IP."'")."");
} else {
$result = dbquery("INSERT INTO ".DB_ONLINE." (online_user, online_ip, online_lastactive) VALUES ('".($userdata['user_level'] != 0 ? $userdata['user_id'] : "0")."', '".USER_IP."', '".time()."')");
}
$result = dbquery("DELETE FROM ".DB_ONLINE." WHERE online_lastactive<".(time()-300)."");

openside($locale['global_010']);
$result = dbquery("SELECT ton.*, tu.user_id,user_name FROM ".DB_ONLINE." ton LEFT JOIN ".DB_USERS." tu ON ton.online_user=tu.user_id");
$guests = 0; $members = array();
while ($data = dbarray($result)) {
if ($data['online_user'] == "0") {
$guests++;
} else {
array_push($members, array($data['user_id'], $data['user_name']));
}
}

if ($online['online_showguests'] == 1) echo THEME_BULLET." ".$locale['global_011'].": ".$guests."<br />\n";
if ($online['online_showmembers'] == 1) echo THEME_BULLET." ".$locale['global_012'].": ".count($members)."<br />\n";

if ($online['online_showguests'] == 0 && $online['online_showmembers'] == 0 && $online['online_showmembersnum'] != 0) echo "";
elseif ($online['online_showmembersnum'] != 0) {
echo "<hr />\n";
echo "<table width='100%' cellpadding='0' cellspacing='0'>";
$result = dbquery("SELECT * FROM ".$db_prefix."users ORDER BY user_lastvisit DESC LIMIT 0,".$online['online_showmembersnum']);
if (dbrows($result) != 0) {
while ($data = dbarray($result)) {
$lastseen = time() - $data['user_lastvisit'];
$iW=sprintf("%2d",floor($lastseen/604800));
$iD=sprintf("%2d",floor($lastseen/(60*60*24)));
$iH=sprintf("%02d",floor((($lastseen%604800)%86400)/3600));
$iM=sprintf("%02d",floor(((($lastseen%604800)%86400)%3600)/60));
$iS=sprintf("%02d",floor((((($lastseen%604800)%86400)%3600)%60)));

if ($lastseen < 300) $lastseen = "<img src='".INFUSIONS."online_users_panel/images/online.png' border='0' alt='Online' />";
elseif ($lastseen < 600) $lastseen = "<img src='".INFUSIONS."online_users_panel/images/10min.png' border='0' alt='10Min' />";
elseif ($lastseen < 1800) $lastseen = "<img src='".INFUSIONS."online_users_panel/images/30min.png' border='0' alt='30Min' />";
elseif ($lastseen < 3600) $lastseen = "<img src='".INFUSIONS."online_users_panel/images/60min.png' border='0' alt='60Min' />";
else $lastseen = "<img src='".INFUSIONS."online_users_panel/images/offline.png' border='0' alt='Offline' />";

if ($data['user_level'] == -103 || $data['user_level'] == 103) { $level = $locale['user3']; $color = $online['online_superadmincolor']; }
if ($data['user_level'] == -102 || $data['user_level'] == 102) { $level = $locale['user2']; $color = $online['online_admincolor']; }
if ($data['user_level'] == -101 || $data['user_level'] == 101) { $level = $locale['user1']; $color = $online['online_usercolor']; }

echo "<tr>\n<td class='side-small' align='left'>".THEME_BULLET." <a href='".BASEDIR."profile.php?lookup=".$data['user_id']."' title='".trimlink($data['user_name'],30)." [".$level."] - Dabei seit: ".showdate("longdate", $data['user_joined'])." - Zuletzt Online: ".showdate("longdate", $data['user_lastvisit'])."' class='side' style='color: #".$color."'>";
echo trimlink($data['user_name'],13)."</a></td><td class='side-small' align='right'>".$lastseen."</td></tr>";
}
}
}

if ($online['online_showbots'] == 1) {
if ($online['online_showmembersnum'] == 0) echo "<table width='100%' cellpadding='0' cellspacing='0'>";

$bot_list = array(
$online['online_alexa'] => 'Alexa',
$online['online_exalead'] => 'Exalead',
$online['online_excite'] => 'Excite',
$online['online_fast'] => 'Fast',
$online['online_fireball'] => 'Fireball',
$online['online_google'] => 'Google',
$online['online_lycos'] => 'Lycos',
$online['online_msn'] => 'MSN',
$online['online_yahoo'] => 'Yahoo'
);

foreach ($bot_list as $bot_time => $bot_name) {
if ((time() - $online['online_showbotstime']) <= $bot_time) echo "<tr>\n<td class='side-small' align='left'>".THEME_BULLET." ".$bot_name."</td><td class='side-small' align='right'><img src='".INFUSIONS."online_users_panel/images/robot.png' border='0' alt='Robot' /></td></tr>";
}
}

if ($online['online_showmembersnum'] != 0 || $online['online_showbots'] == 1) echo "</table>\n";

if ($online['online_showguests'] == 0 && $online['online_showmembers'] == 0 && $online['online_showmembersnum'] == 0) echo "";
elseif ($online['online_showallmembers'] != 0 || $online['online_shownewmember'] != 0) echo "<hr />\n";
if ($online['online_showallmembers'] == 1) echo THEME_BULLET." ".$locale['global_014'].": ".number_format(dbcount("(user_id)", DB_USERS, "user_status<='1'"))."<br />\n";
if (iADMIN && checkrights("M") && $settings['admin_activation'] == "1") {
echo THEME_BULLET." <a href='".ADMIN."members.php".$aidlink."&amp;status=2' class='side'>".$locale['global_015']."</a>";
echo ": ".dbcount("(user_id)", DB_USERS, "user_status='2'")."<br />\n";
}
if ($online['online_shownewmember'] == 1) {
$data = dbarray(dbquery("SELECT user_id,user_name FROM ".DB_USERS." WHERE user_status='0' ORDER BY user_joined DESC LIMIT 0,1"));
echo THEME_BULLET." ".$locale['global_016'].": <a href='".BASEDIR."profile.php?lookup=".$data['user_id']."' class='side'>".trimlink($data['user_name'],15)."</a>\n";
}
closeside();
?>