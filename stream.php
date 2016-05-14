<?php
$filename = "#EXTM3U\r\n#EXTINF:-1,The Walking Radio\r\nhttp://the-walking-radio.de:8000\r\n";

   header("Cache-Control: cache, must-revalidate");    
   header("Pragma: public");
   header("Content-type: audio/x-mpegurl");
   header("Content-Disposition: attachment; filename=listen.m3u");
 
echo($filename);
?>