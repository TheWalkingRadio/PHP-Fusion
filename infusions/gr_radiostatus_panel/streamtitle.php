<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (c) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Title: Gr_Radiostatus v2 for PHP-Fusion 7
| Author: Ralf Thieme
| Webseite: www.granade.eu
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
define('IN_FUSION', TRUE);
header('Content-type: text/plain');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0');
if (isset($_GET['titlelink']) AND file_exists('cache/stream_data.php')) {
	$_GET['titlelink'] = htmlspecialchars($_GET['titlelink']);
	require 'cache/stream_data.php';
	if (array_key_exists($_GET['titlelink'],$stream_data)) {
		if (file_exists('cache/stream_'.$stream_data[$_GET['titlelink']]['id'].'_'.$stream_data[$_GET['titlelink']]['cache'].'.php')) {
			require 'cache/stream_'.$stream_data[$_GET['titlelink']]['id'].'_'.$stream_data[$_GET['titlelink']]['cache'].'.php';
			if ($cache['status'] == 1) {
				echo html_entity_decode($cache['song']);
			}
		}
	}
}
?>