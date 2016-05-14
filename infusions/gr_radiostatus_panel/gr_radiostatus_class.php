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
if (!defined('IN_FUSION')) { die('Access Denied'); }

class SHOUTcast {
	var $SHOUTcastData;
	var $error;
	var $trans_port;
	var $trans_ip;
	var $trans_pw;
	
	function GetStatus($arr=array()) 
	{
		$this->trans_ip = $arr['rs_transpw'];
		$this->trans_port = $arr['rs_transip'];
		$this->trans_pw = $arr['rs_transport'];
		
		if(empty($arr)) 
		{ 
			return(0);
		}
		
		if (!$fp = @fsockopen($arr['rs_ip'], $arr['rs_port'], $errno, $errstr,1)) 
		{
			$this->error = "$errstr ($errno)";
			return(0);
		} 
		else 
		{
			if ($arr['rs_server_typ'] == 1)
				fputs($fp, "GET /admin.cgi?pass=".$arr['rs_pw']."&mode=viewxml HTTP/1.1\r\n");
			else
				fputs($fp, "GET /admin.cgi?pass=".$arr['rs_pw']."&sid=".$arr['rs_server_id']."&mode=viewxml HTTP/1.1\r\n");
			
			fputs($fp, "User-Agent: Mozilla\r\n\r\n");
			stream_set_blocking($fp, false);
			socket_set_timeout($fp, 2);
			$status = socket_get_status($fp); 
			while (!feof($fp) AND !$status['timed_out']) {
				$this->SHOUTcastData .= fgets($fp, 512);
			}
			fclose($fp);
			if (isset($this->SHOUTcastData) AND stristr($this->SHOUTcastData, "HTTP/1.0 200 OK") == true) {
				$this->SHOUTcastData = trim(substr($this->SHOUTcastData, 42));
				return(1);
			} else {
				$this->error = "Bad login";
				return(0);
			}
		}
	}
	
	function GetCurrentListeners() {
		return $this->check_input($this->check_preg('CURRENTLISTENERS', $this->SHOUTcastData));
	}

	function GetPeakListeners() {
		return $this->check_input($this->check_preg('PEAKLISTENERS', $this->SHOUTcastData));
	}

	function GetMaxListeners() {
		return $this->check_input($this->check_preg('MAXLISTENERS', $this->SHOUTcastData));
	}

	function GetServerGenre() {
		return $this->check_input($this->check_preg('SERVERGENRE', $this->SHOUTcastData));
	}
	
	function GetServerURL() {
		return $this->check_input($this->check_preg('SERVERURL', $this->SHOUTcastData));
	}
	
	function GetServerTitle() {
		return $this->check_input($this->check_preg('SERVERTITLE', $this->SHOUTcastData));
	}
	
	function GetCurrentSongTitle($host, $port, $pass) {
		$sct = new Sc_Trans_API($host, $port, 'admin', $pass);
		$status = $sct->GetStatus();
		
		if ($status['data']['status']['activesource']['@attributes']['source'] == 'dj')
			return 'Live Sendung';
		else
			return $this->check_input($this->check_preg('SONGTITLE', $this->SHOUTcastData), true);
    }
	
	
	function GetDJFromSCTrans($host, $port, $pass)
	{
		$sct = new Sc_Trans_API($host, $port, 'admin', $pass);
		
		$status = $sct->GetStatus();
		
		if ($status['data']['status']['activesource']['@attributes']['source'] == 'dj')
			return $status['data']['status']['activesource']['name'];
		else
			return 'Auto DJ';	
	}
	
	function GetIRC() {
		return $this->check_input($this->check_preg('IRC', $this->SHOUTcastData));
	}
	
	function GetAIM() {
		return $this->check_input($this->check_preg('AIM', $this->SHOUTcastData));
	}
	
	function GetICQ() {
		return $this->check_input($this->check_preg('ICQ', $this->SHOUTcastData));
	}

	function GetStreamStatus() {
		return $this->check_input($this->check_preg('STREAMSTATUS', $this->SHOUTcastData));
	}
	
	function GetBitRate() {
		return $this->check_input($this->check_preg('BITRATE', $this->SHOUTcastData));
	}
	
	function GetContent() {
		return $this->check_input($this->check_preg('CONTENT', $this->SHOUTcastData));
	}
	
	function GetSongHistory() {
		$arrhistory = array();
		$song_source = $this->check_preg_all('SONG');
		if (is_array($song_source)) {
			for($i=1;$i < sizeof($song_source);$i++) {
				$arrhistory[$i-1] = array(
					'playedat' => $this->check_input($this->check_preg('PLAYEDAT', $song_source[$i])),
					'title' => $this->check_input($this->check_preg('TITLE', $song_source[$i]), true)
				);
			}
		}
		return $arrhistory;
	}
	
	function GetListeners() {
		$arrlisteners = array();
		$listener_source = $this->check_preg_all('LISTENER');
		if (is_array($listener_source)) {
			for($i=0;$i < sizeof($listener_source);$i++) {
				$arrlisteners[$i] = array(
					'hostname' => $this->check_input($this->check_preg('HOSTNAME', $listener_source[$i])),
					'useragent' =>  $this->check_input($this->check_preg('USERAGENT', $listener_source[$i])),
					'underruns' => $this->check_input($this->check_preg('UNDERRUNS', $listener_source[$i])),
					'connecttime' => $this->check_input($this->check_preg('CONNECTTIME', $listener_source[$i])),
					'pointer' => $this->check_input($this->check_preg('POINTER', $listener_source[$i])),
					'uid' => $this->check_input($this->check_preg('UID', $listener_source[$i]))
				);
			}
		}
		return $arrlisteners;
	}
	
	function MakeCache($user_typ, $cache_art, $servertype, $host, $port, $pass) {
		$res  = '<?php'."\n";
		$res .= 'if (!defined(\'IN_FUSION\')) { die(\'Access Denied\'); }'."\n";
		$res .= '$cache = array();'."\n";
		if ($this->GetStreamStatus()) {
			if ($user_typ == 1) {
				if ($servertype != 3)
					$server_name = $this->GetAIM();
				else
					$server_name = $this->GetDJFromSCTrans($host, $port, $pass);
					
			} elseif ($user_typ == 2) {
				$server_name = $this->GetICQ();
			} elseif ($user_typ == 3) {
				$server_name = $this->GetIRC();
			} elseif ($user_typ == 4) {
				$server_name = $this->GetServerTitle();
			} elseif ($user_typ == 5) {
				$server_name = $this->GetServerGenre();
			} else {
				if ($servertype != 3)
					$server_name = $this->GetAIM();
				else
					$server_name = $this->GetDJFromSCTrans($host, $port, $pass);
			}
			
			$result_user = dbquery("SELECT user_id, user_name, user_avatar FROM ".DB_USERS." WHERE user_aim='".stripinput($server_name)."'");
			if (dbrows($result_user)) {
				$data_user = dbarray($result_user);
				$res .= '$cache[\'mod_ckeck\'] = true;'."\n";
				$res .= '$cache[\'mod_id\'] = \''.$data_user['user_id'].'\';'."\n";
				if ($cache_art) {
					$res .= '$cache[\'mod\'] = \'<a href="'.RADIOSTATUS_SELF.BASEDIR.'profile.php?lookup='.$data_user['user_id'].'">'.trimlink($data_user['user_name'], 30).'</a>\';'."\n";
					if ($data_user['user_avatar'] AND file_exists(IMAGES.'avatars/'.$data_user['user_avatar'])) {
						$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.IMAGES.'avatars/'.$data_user['user_avatar'].'\';'."\n";
					} else {
						$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.'images/nopic.gif\';'."\n";
					}
				} else {
					$res .= '$cache[\'mod\'] = \''.trimlink($data_user['user_aim'], 30).'\';'."\n";
					if ($data_user['user_avatar'] AND file_exists(IMAGES.'avatars/'.$data_user['user_avatar'])) {
						$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.IMAGES.'avatars/'.$data_user['user_avatar'].'\';'."\n";
					} else {
						$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.'images/nopic.gif\';'."\n";
					}
				}
				@mysql_free_result($result_user);
			} else {
				$res .= '$cache[\'mod_ckeck\'] = false;'."\n";
				$res .= '$cache[\'mod\'] = \'Auto DJ\';'."\n";
				$res .= '$cache[\'mod_id\'] = \'0\';'."\n";
				if ($cache_art) {
					$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.'images/autodj.gif\';'."\n";
				} else {
					$res .= '$cache[\'mod_pic\'] = \''.RADIOSTATUS_SELF.'images/autodj.gif\';'."\n";
				}
			}
			$res .= '$cache[\'song\'] = \''.(trim($this->GetCurrentSongTitle($host, $port, $pass)) != '' ? trim($this->GetCurrentSongTitle($host, $port, $pass)) : '').'\';'."\n";
			$res .= '$cache[\'aim\'] = \''.$this->GetAIM().'\';'."\n";
			$res .= '$cache[\'icq\'] = \''.$this->GetICQ().'\';'."\n";
			$res .= '$cache[\'irc\'] = \''.$this->GetIRC().'\';'."\n";
			$res .= '$cache[\'server_title\'] = \''.$this->GetServerTitle().'\';'."\n";
			$res .= '$cache[\'genre\'] = \''.$this->GetServerGenre().'\';'."\n";
			if ($cache_art) {
				$res .= '$cache[\'listner\'] = \''.$this->GetCurrentlisteners().'\';'."\n";
				$res .= '$cache[\'listner_max\'] = \''.$this->GetMaxListeners().'\';'."\n";
				$res .= '$cache[\'listner_peak\'] = \''.$this->GetPeakListeners().'\';'."\n";
				$res .= '$cache[\'bitrate\'] = \''.$this->GetBitrate().'\';'."\n";
				$res .= '$cache[\'listners\'] = array();'."\n";
				$listeners = $this->GetListeners();
				if (is_array($listeners)) {
					for($i=0;$i < sizeof($listeners);$i++) {
						$res .= '$cache[\'listners\'][] = array(\'hostname\' => \''.$listeners[$i]['hostname'].'\', \'useragent\' => \''.$listeners[$i]['useragent'].'\', \'underruns\' => \''.$listeners[$i]['underruns'].'\', \'connecttime\' => \''.$listeners[$i]['connecttime'].'\', \'pointer\' => \''.$listeners[$i]['pointer'].'\', \'uid\' => \''.$listeners[$i]['uid'].'\');'."\n";
					}
				}
				$res .= '$cache[\'history\'] = array();'."\n";
				$history = $this->GetSongHistory();
				if (is_array($history)) {
					for($i=0;$i < sizeof($history);$i++) {
						$res .= '$cache[\'history\'][] = array(\'playedat\' => \''.$history[$i]['playedat'].'\', \'title\' => \''.$history[$i]['title'].'\');'."\n";
					}
				}
				if (preg_match("/audio/i", $this->GetContent())) {
					$res .= '$cache[\'player\'] = \'0\';'."\n";
				} else {
					$res .= '$cache[\'player\'] = \'1\';'."\n";
				}
				$res .= '$cache[\'music\'] = \''.$this->GetContent().'\';'."\n";
			}
		}
		$res .= '$cache[\'status\'] = \''.$this->GetStreamStatus().'\';'."\n";
		$res .= '$cache[\'error\'] = \''.$this->GetError().'\';'."\n";
		$res .= '?>';
		return $res;
	}
	
	function check_preg($name, $source) {
		if (preg_match("/<".$name.">/i", $source) AND preg_match("/<\/".$name.">/i", $source)) {
			preg_match('#<'.$name.'>(.*?)</'.$name.'>#', $source, $matches);
			return $matches[1];
		} else {
			return '';
		}
	}
	
	function check_preg_all($name) {
		if (preg_match("/<".$name.">/i", $this->SHOUTcastData) AND preg_match("/<\/".$name.">/i", $this->SHOUTcastData)) {
			preg_match_all('#<'.$name.'>(.*?)</'.$name.'>#', $this->SHOUTcastData, $matches);
			return $matches[1];
		} else {
			return '';
		}
	}
	
	function check_input($text, $title=false) {
		if ($title) {
			$search = array("\"", "'", "\\", '\"', "\'", "_");
			$replace = array("&quot;", "&#39;", "&#92;", "&quot;", "&#39;", " ");
			$text = trim($text);
		} else {
			$search = array("&", "\"", "'", "\\", '\"', "\'");
			$replace = array("&amp;", "&quot;", "&#39;", "&#92;", "&quot;", "&#39;");
		}
		$text = trim(str_replace($search, $replace, $text));
		return $text;
	}

	function GetError() { return $this->error; }
}

class Sc_Trans_API{
    private $host;
    private $port;
    private $user;
    private $pass;
    private $Sc_Trans_API;
    private $seq;
    private $cut='|'; // fur den DJ namen damit es keine Ãœberschneidung in der Config gibt fals doppelte namen  mit unterschiedlicher priority Bekommen
    private $pri='1'; // Standart priority ( Beliegbig einstellbar )
    
    public function __construct($host=NULL,$port=NULL,$user=NULL,$pass=NULL){
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->Sc_Trans_API = 'http://'.$user.':'.$pass.'@'.$host.':'.$port.'/api';
        $this->seq = '128';
        if(empty($host) && empty($port) && empty($user) && empty($pass)) 
			echo 'API Daten Fehlen  Host Adresse, Port, Username & Passwort!';
        else {
           
            if (!$fp = fsockopen($host, $port, $errno, $errstr ,5)) {
                  echo "API Host:Port Fehler ERROR: $errno - $errstr<br />\n";
            } else {
                return true;
            }
        }
    }
    
    // Sendet Daten An den Server
    private function Post_Data($post){
        $agent='Mozilla/5.0';
        if(!empty($post)){
            $curl=curl_init();
            curl_setopt($curl, CURLOPT_USERAGENT, $agent);
            curl_setopt($curl, CURLOPT_URL,$this->Sc_Trans_API);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION ,1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER ,1);
            $data = curl_exec($curl);
            curl_close ($curl);
            return json_decode(json_encode(simplexml_load_string($data)),TRUE);
        }
	else return  'API Fehler: Daten K&ouml;nnen nicht &Uuml;bermittelt werden';
    }
    
    /*Infos Abrufen Start*/
    // Get Staus Information
    public function GetStatus(){
        $post='op=getstatus&seq='.$this->seq;
        return self::Post_Data($post);
    }
 
    // DJ Kick
    public function DjKick($name=NULL,$time=NULL){
        if(empty($name)) $post = 'op=kickdj&seq='.$this->seq;
        else{
            if(self::CheckDj($name)==false){
                if(empty($time)) $time = date('H:i:s',strtotime('+2 Hour'));
                else $time = $time;
                $post = 'op=kickdj&seq='.$this->seq.'&duration='.$time.'&name='.$name;    
            }else return $dj='API -> DjKick: Name nicht Gefunden';
        }
        if(empty($dj))$dj = self::Post_Data($post);
        else $dj;
        if(isset($dj['error'])) return 'API -> DjKick: '.$dj['error'];
        else return 1;
        
    }
    
    // DJ Unkick
    public function DJUnKick($name=NULL){
        if(!empty($name)){
            if(self::CheckDj($name)==false){
                $post = 'op=kickdj&seq='.$this->seq.'&duration='.$time.'&name='.$name;    
                $dj = self::Post_Data($post);
                if(isset($dj['error'])) return 'API -> DjUnKick: '.$dj['error'];
                else return 1;
            }else return 'API -> DjUnKick: Name nicht Gefunden ';
        }
    }
}
?>