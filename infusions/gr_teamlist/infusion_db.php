<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright � 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Teamlist v2.2 for PHP-Fusion 7
| Filename: infusion_db.php
| Author: Ralf Thieme (Gr@n@dE)
| HP: www.granade.eu
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

if (!defined("DB_GR_TEAMLIST_GROUP")) {
	define("DB_GR_TEAMLIST_GROUP", DB_PREFIX."gr_teamlist_groups");
}
if (!defined("DB_GR_TEAMLIST_USERS")) {
	define("DB_GR_TEAMLIST_USERS", DB_PREFIX."gr_teamlist_users");
}

?>