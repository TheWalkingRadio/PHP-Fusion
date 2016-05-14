<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Member_Charts v1.1 for PHP-Fusion 7
| Filename: infusion_db.php
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

if (!defined("DB_GR_MC_CHARTS")) {
	define("DB_GR_MC_CHARTS", DB_PREFIX."gr_mc_charts");
}

if (!defined("DB_GR_MC_VOTE")) {
	define("DB_GR_MC_VOTE", DB_PREFIX."gr_mc_vote");
}

if (!defined("DB_GR_MC_SETTINGS")) {
	define("DB_GR_MC_SETTINGS", DB_PREFIX."gr_mc_settings");
}
?>