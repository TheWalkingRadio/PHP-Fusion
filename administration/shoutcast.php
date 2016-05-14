<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: shoutcast.php
| Author: Guido Lipke (LipkeGu)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../maincore.php";

function dnas_ctl($arg)
{
	if (isset($arg) && $arg != "")
	{
		$output = shell_exec("sudo /etc/init.d/shoutcast " .$arg. " >/dev/null 2>/dev/null");
		
		show_infobox("Service", "Vorgang erfolgreich abgeschlossen");
	}
}

function dnas_kick_listener($listenerid, $action)
{
	$response = '';
	
	$result = dbquery("SELECT rs_id, rs_ip, rs_port, rs_apw, rs_server_typ, rs_server_id FROM ".DB_PREFIX."gr_radiostatus WHERE rs_status='1' LIMIT 2");
	
	while($data = dbarray($result))
		if (!$fp = @fsockopen($data['rs_ip'], $data['rs_port'], $errno, $errstr, 3)) 
		{
			opentable("Shoutcast - Fehler");
				echo "<a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."\">Server nicht gefunden!</a>";
			closetable();
			return(0);
		} 
		else 
		{
			if ($data['rs_server_typ'] == 1)
				fputs($fp, "GET /admin.cgi?pass=".$data['rs_apw']."&mode=".$action."&".$action."=".$listenerid." HTTP/1.1\r\n");
			else
			{
				if ($action == "bandst")
					$scparams = "&mode=".$action."&".$action."=".$listenerid."&banmsk=255&bandst=".$_GET['bandst']."&kickdst=".$_GET['hostip'];
				else
					$scparams = "&mode=".$action."&".$action."=".$listenerid;

				fputs($fp, "GET /admin.cgi?pass=".$data['rs_apw']."&sid=".$data['rs_server_id'].$scparams. " HTTP/1.1\r\n");
			}

			fputs($fp, "User-Agent: Mozilla\r\n\r\n");

			stream_set_blocking($fp, false);
			socket_set_timeout($fp, 2);
				
			$status = socket_get_status($fp); 
				
			while (!feof($fp) AND !$status['timed_out']) 
				$response .= fgets($fp, 512);
		
			fclose($fp);

			show_infobox("Zuhörer kicken", "Verbindung zum Hörer beendet!");
			return(0);
		}
}

function dnas_add_stream($type, $slots, $apw, $spw, $port)
{
	dbquery("INSERT INTO sc_settings (type, adminpw, streampw, port, slots, unique) 
	VALUES ('". strtolower($type) ."','". strtolower($apw) ."','". strtolower($spw) ."','". $port ."','". $slots ."','". $type ."')");
	
	show_infobox("Stream hinzufügen", "Vorgang erfolgreich abgeschlossen!");
}

function dnas_add_user($user, $pass, $priority)
{
	if (isset($user) && isset($pass) && $user !="" && $pass !="" && $priority !="")
	{
		if ($user != $pass)
		{
			dbquery("INSERT INTO sc_logins (user, pass, active, priority) VALUES ('". strtolower($user) ."', '". strtolower($pass) ."', '1', '". $priority ."')");
			show_infobox("Shoutcast - Moderator", "Der Moderator wurde erfolgreich hinzugefügt!");
		}
		else
		{
			opentable("Account - Fehler");
			echo "Eingabefehler! (Name darf nicht das Passwort sein oder enthalten!)<br><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."\">OK, Zur Startseite</a>";
			closetable();
		}
	}
}

function dnas_update_user($djid, $user, $pass, $priority)
{
	if (isset($djid) && isset($user) && isset($pass) && $user !="" && $pass !="" && $priority !="")
	{
		if ($user != $pass)
		{
			dbquery("UPDATE sc_logins SET archive='0', user='".strtolower($user)."', pass='".strtolower($pass)."', priority='".$priority."' WHERE id='". $djid ."' LIMIT 1");
			show_infobox("Shoutcast - Moderator", "Der Moderator wurde erfolgreich geändert!");
		}
		else
		{
			opentable("Account - Fehler");
			echo "Eingabefehler! (Name darf nicht das Passwort sein oder enthalten!)<br><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."\">OK, Zur Startseite</a>";
			closetable();
		}
	}
}

function dnas_del_user($djid)
{
	if (isset($djid) && $djid != "")
	{
		dbquery("DELETE FROM sc_logins WHERE id='". $djid ."' LIMIT 1");
		show_infobox("Shoutcast - Moderator", "Der Moderator wurde erfolgreich gelöscht!");
	}
}

function dnas_ban_user($djid)
{
	if (isset($djid) && $djid != "")
	{
		dbquery("UPDATE sc_logins SET active='0' WHERE id='". $djid ."' LIMIT 1");
		show_infobox("Shoutcast - Moderator", "Der Moderator wurde gesperrt!");
	}
}

function dnas_unb_user($djid)
{
	if (isset($djid) && $djid != "")
	{
		dbquery("UPDATE sc_logins SET active='1' WHERE id='". $djid ."' LIMIT 1");
		show_infobox("Shoutcast - Moderator", "Der Moderator wurde entgesperrt!");
	}
}

function dnas_edit_User($djid, $aid)
{
	opentable("Account bearbeiten");
	$result = dbquery("SELECT * FROM sc_logins WHERE id='".$djid."' ORDER BY user ASC LIMIT 1");
	while($data = dbarray($result))
	{
		echo "<form action=\"/administration/shoutcast.php?action=updatedj&aid=".$aid."\" method=\"GET\">
		<table cellpadding=\"0\" cellspacing=\"0\" class=\"center\" width=\"100%\">
		<tr class=\"tbl\">
			<td>Username: </td><td><input name=\"djname\" type=\"text\" value=\"".$data['user']."\"></td>
		</tr>
			<tr class=\"tbl\">
			<td>Password: </td><td><input name=\"djpass\" type=\"text\" value=\"".$data['pass']."\">
			<input name=\"aid\" type=\"hidden\" value=\"".$aid."\">
			<input name=\"djid\" type=\"hidden\" value=\"".$data['id']."\">
			<input name=\"action\" type=\"hidden\" value=\"updatedj\"></td>
		</tr>
		<tr class=\"tbl\">
			<td>PrioritÃ¤t: </td><td><select name=\"djprio\">
			<option value=\"7\">Gast-Moderator</option>		
			<option value=\"8\" selected>Moderator</option>";
		if (iADMIN && $data['priority'] <= 9)		
		{
			echo "<option value=\"9\">Administrator</option>";
		}
		elseif (iSUPERADMIN)		
		{
			echo "<option value=\"9\">Administrator</option>";
			echo "<option value=\"10\">Super Administrator</option>";			
		}

		echo "</select></td></tr>
		<tr class=\"tbl\">
			<td colspan=\"2\"><center><input type=\"submit\" value=\"Speichern\"></center></td>
		</tr>
		</table>
		</form>";
	} 
	closetable();
}

function dnas_getUser($alias)
{
	$result = dbquery("SELECT user_name FROM ".DB_PREFIX."users WHERE user_aim='". $alias ."' LIMIT 1");
	while($data = dbarray($result))
	{
		if ($data['user_name'] != "")
			return $data['user_name'];
		else
			return "<nicht vergeben>";
	} 
}

function dnas_rec_user($djid)
{
}

function dnas_unr_user($djid)
{
}

function dnas_save_users()
{
	$fp = fopen("/usr/share/www/___users.txt","a+");
	if($fp)
	{
		$result = dbquery("SELECT * FROM sc_logins ORDER BY user ASC");
		while ($data = dbarray($result)) 
		{
			if ($data['active'] == "1")
			{
				$zeile = $data['user'] .":". $data['pass'] .":". $data['priority'] .":". $data['archive'] ."\n";
				$length = strlen(strtolower($zeile));
				fwrite($fp,$zeile,$length);	
			}
		}

		fclose($fp);

		show_infobox("Shoutcast - Moderator", "Änderungen werden beim Neustart des Streams übernommen!");
	}
}

function dnas_save_bans()
{
	$fp = fopen("/usr/share/www/___bans.txt","a+");
	if($fp)
	{
		$result = dbquery("SELECT * FROM sc_banlist ORDER BY addr ASC");
		while ($data = dbarray($result)) 
		{
			if ($data['active'] == "1")
			{
				$zeile = $data['addr'] .":". $data['duration'] .":". $data['reason'] ."\n";
				$length = strlen(strtolower($zeile));
				fwrite($fp,$zeile,$length);	
			}
		}

		fclose($fp);

		show_infobox("Shoutcast", "Änderungen werden beim Neustart des Streams übernommen!");
	}
}


function show_infobox($title, $message)
{
	opentable($title);
	echo "<table cellpadding=\"0\" cellspacing=\"0\" class=\"center\" width=\"100%\">";
	echo "<tr class=\"tbl\"><td><img src=\"/images/msgbox/ok.png\" width=\"32\" height=\"32\"></td><td align=\"center\">".$message."<br /><br /><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."\">Ok und Weiter...</a></td>";
	echo "</tr></table>";
	closetable();
}

if (isset($_GET['action']))
{	
	if ($_GET['action'] == "start" || $_GET['action'] == "stop" || $_GET['action'] == "editdj" || $_GET['action'] == "updatedj" || $_GET['action'] == "restart" || $_GET['action'] == "adddj" || $_GET['action'] == "recdj" || $_GET['action'] == "bandst" || $_GET['action'] == "kickdst" || $_GET['action'] == "unrdj" || $_GET['action'] == "bandj" || $_GET['action'] == "unbdj" || $_GET['action'] == "deldj" || $_GET['action'] == "writeacc")
		$argument = $_GET['action'];
}

if (!checkrights("SCT") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) 
	redirect("../index.php");

$tmp_get = $_GET['aid'];


require_once THEMES."templates/admin_header_mce.php";

if (!isset($_GET['action']))
{
	opentable("Dienstkontrolle");
	echo "
	<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"center\">
	<tr class=\"tbl\">
		<td class=\"tbl\" colspan=\"3\" ><center>Stream und AutoDJ</center></td>
	</tr>
	<tr class=\"tbl\">
		<td class=\"tbl\"><center><a href=\"/administration/shoutcast.php?action=restart&aid=".$_GET['aid']."\">starten</a></center></td>
		<td class=\"tbl\"><center><a href=\"/administration/shoutcast.php?action=restart&aid=".$_GET['aid']."\">neustarten</a></center></td>
		<td class=\"tbl\"><center><a href=\"/administration/shoutcast.php?action=stop&aid=".$_GET['aid']."\">stoppen</a></center></td>
	</tr>
	</table>
";
closetable();

opentable("AutoDJ-Playlist & Website Kontrolle");
echo "<table cellpadding=\"0\" cellspacing=\"0\" class=\"center\" width=\"100%\">
<tr class=\"tbl\"><td>Playlist schreiben</td>";

if (file_exists("/usr/share/www/___music.txt"))
	echo "<td>Ja</td>";
else
	echo "<td>Nein</td>";

echo "</tr>";

echo "<tr class=\"tbl\"><td>Backup der Webseite</td>";

if (file_exists("/usr/share/www/Website_backup.tar.gz") && iADMIN)
	echo "<td>Ja! (<a href=\"/Website_backup.tar.gz\">Download</a>)</td>";
else
	echo "<td>Nein</td>";

echo "</tr>";

echo "</table>";



closetable();

opentable("AutoDJ - Account hinzufÃ¼gen");

echo "
	<form action=\"/administration/shoutcast.php?action=adddj&aid=".$tmp_get."\" method=\"GET\">
	<table cellpadding=\"0\" cellspacing=\"0\" class=\"center\" width=\"100%\">
	<tr class=\"tbl\">
		<td>Username: </td><td><input name=\"djname\" type=\"text\"></td>
	</tr>
	<tr class=\"tbl\">
		<td>Password: </td><td><input name=\"djpass\" type=\"text\">
		<input name=\"aid\" type=\"hidden\" value=\"".$tmp_get."\">
		<input name=\"action\" type=\"hidden\" value=\"adddj\"></td>
	</tr>
	<tr class=\"tbl\">
		<td>PrioritÃ¤t: </td><td><select name=\"djprio\">
		<option value=\"7\">Gast-Moderator</option>		
		<option value=\"8\" selected>Moderator</option>
";
if (iADMIN || iSUPERADMIN)		
		echo "<option value=\"9\">Administrator</option>";		
if (iSUPERADMIN)		
		echo "<option value=\"10\">Super Administrator</option>";		
echo "</select></td>
</tr>
	<tr class=\"tbl\">
		<td colspan=\"2\"><center><input type=\"submit\" value=\"HinzufÃ¼gen\"></center></td>
	</tr>
	</table>
	</form>
";
closetable();

opentable("AutoDJ - Vorhandene DJ-Accounts");
	echo "<table cellpadding=\"0\" width=\"100%\" cellspacing=\"0\" class=\"center\">
	<tr class=\"tbl\"><td colspan=\"7\" class=\"tbl\"><center>Vorhandene Accounts</center></td></tr>
	<tr class=\"tbl\"><th><center>Moderator</center></th><th><center>Passwort</center></th><th><center>PrioritÃ¤t</center></th><th colspan=\"4\"><center>Optionen</center></th></tr>";
		
	$result = dbquery("SELECT * FROM sc_logins ORDER BY priority DESC");
	while ($data = dbarray($result)) 
	{
		echo "<tr height=\"32px\" class=\"tbl\">";
		$djname = strtolower(dnas_getUser($data['user']));
		
		if ($data['priority'] >= 9)		
			$priority = "<font color=\"#FF0000\">Administrator</font>";
		else
		{
			if ($data['priority'] >= 7)
				$priority = "<font color=\"#FFFF00\">Moderator</font>";
		}
		
		if ($data['user'] == $djname)
		{	if (strlen($data['pass']) > 7)		
				echo "<td><center>". $djname ."</center></td><td><center>".$data['user'].":".$data['pass']."</center></td><td><center>". $priority ."</center></td>";
			else		
				echo "<td><center><font color=\"#FF0000\">". $djname ."</font></center></td><td><center><font color=\"#FF0000\">".$data['user'].":".$data['pass']."</font></center></td><td><center>". $priority ."</center></td>";
		}
		else
		{
			if($djname == "")
				echo "<td><center><font color=\"#FF0000\">". $data['user'] ." (unbenutzt)</font></center></td><td><center>".$data['user'].":".$data['pass']."</center></td><td><center>". $priority ."</center></td>";
			else
			{
				if (strlen($data['pass']) > 7)
					echo "<td><center>". $data['user'] ." (".$djname.")</center></td><td><center>".$data['user'].":".$data['pass']."</center></td><td><center>". $priority ."</center></td>";
				else
					echo "<td><center><font color=\"#FF0000\">". $data['user'] ." (".$djname.")</font></center></td><td><center><font color=\"#FF0000\">".$data['user'].":".$data['pass']."</font></center></td><td><center>". $priority ."</center></td>";
			}
		}
		
		if ($data['active'] == "1")
			echo "	<td><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."&action=bandj&djid=".$data['id']."\">Sperren</a></td>";
		else
			echo "	<td><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."&action=unbdj&djid=".$data['id']."\">Entsperren</a></td>";
		
		if (iADMIN && $data['priority'] <= 9)
			echo "	<td><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."&action=editdj&djid=".$data['id']."\"><img src=\"/images/edit.png\" style=\"vertical-align: middle;\" /></a></td>";
		elseif (iSUPERADMIN)
			echo "	<td><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."&action=editdj&djid=".$data['id']."\"><img src=\"/images/edit.png\" style=\"vertical-align: middle;\" /></a></td>";
		echo "	<td><a href=\"/administration/shoutcast.php?aid=".$_GET['aid']."&action=deldj&djid=".$data['id']."\"><img src=\"/images/delete.png\" style=\"vertical-align: middle;\" /></a></td>";
		
		echo "</tr>";
	}
	
	if (!file_exists("/usr/share/www/___users.txt"))
	{
		if (!file_exists("/usr/share/www/DJLOCKED.tag"))
		{
			echo "<tr height=\"30px\" class=\"tbl\"><td colspan=\"6\">
			<center><a href=\"/administration/shoutcast.php?action=writeacc&aid=".$_GET['aid']."\">Speichern der Accounts</a></center></td></tr>";
		}
		else
			if (iSUPERADMIN)
			{
				echo "<tr height=\"30px\"><td colspan=\"5\"><center><font color=\"#FF0000\">Das Anlegen von Accounts ist gesperrt!</font></center></td></tr>";
				
				echo "<tr height=\"30px\"><td colspan=\"5\">
				<center><a href=\"/administration/shoutcast.php?action=writeacc&aid=".$_GET['aid']."\">Accounts trotzdem speichern!</a></center></td></tr>";
			}
			else
				echo "<tr height=\"30px\"><td colspan=\"5\"><center><font color=\"#FF0000\">Das Anlegen von Accounts ist gesperrt!</font></center></td></tr>";
	}
	else
		echo "<tr height=\"30px\"><td colspan=\"5\">
		<center><font color=\"#FF0000\">Ã„nderungen werden erst nach dem Neustart wirksam!</font></center></td></tr>";
		
	echo "</table>";
	closetable();	
	
	if (file_exists("/usr/share/www/logs/streamd.log"))
	{
		$dnas_log_content = nl2br(file_get_contents("/usr/share/www/logs/streamd.log"));
	
		if ($dnas_log_content != "")
		{
			opentable("Shoutcast - Bootstrapper");
				echo $dnas_log_content;
			closetable();
		}
	}
}
elseif (isset($_GET['action']) && isset($argument) && $argument != "")
	if ($argument == "start" || $argument == "stop" || $argument == "restart")
		dnas_ctl($argument);
	elseif ($argument == "editdj" && isset($_GET['djid']))
		dnas_edit_User($_GET['djid'], $_GET['aid']);
	elseif ($argument == "adddj" && isset($_GET['djname']) && isset($_GET['djpass']) && $_GET['djpass'] != "" && $_GET['djname'] != "" && $_GET['djprio'] != "")
		dnas_add_user($_GET['djname'], $_GET['djpass'], $_GET['djprio']);
	elseif ($argument == "kickdst" && isset($_GET['kickdst']) && $_GET['kickdst'] != "" )
		if (!checkrights("SCT") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) 
			redirect("../index.php");
		else
			dnas_kick_listener($_GET['kickdst'], $argument);
	elseif ($argument == "bandst" && isset($_GET['bandst']) && $_GET['bandst'] != "" && isset($_GET['ban']))
		if (!checkrights("SCT") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH && iSUPERADMIN) 
			redirect("../index.php");
		else
			dnas_kick_listener($_GET['ban'], $argument);
	elseif ($argument == "deldj" && isset($_GET['djid']) && $_GET['djid'] != "")
		dnas_del_user($_GET['djid']);
	elseif ($argument == "bandj" && isset($_GET['djid']) && $_GET['djid'] != "")
		dnas_ban_user($_GET['djid']);
	elseif ($argument == "unbdj" && isset($_GET['djid']) && $_GET['djid'] != "")
		dnas_unb_user($_GET['djid']);
	elseif ($argument == "updatedj" && isset($_GET['djid']) && isset($_GET['djname']) && isset($_GET['djpass']) && $_GET['djpass'] != "" && $_GET['djname'] != "" && $_GET['djprio'] != "")
		dnas_update_user($_GET['djid'], $_GET['djname'], $_GET['djpass'], $_GET['djprio']);
	elseif($argument == "writeacc")
		dnas_save_users();
	else
	{
		redirect("../index.php");
	}
else 
{
	opentable("Shoutcast - Fehler");
	echo "Shoutcast und AutoDJ sperre aktiv! :(";
	closetable();	
}
require_once THEMES."templates/footer.php";

?>
