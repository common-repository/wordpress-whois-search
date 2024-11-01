<?php

define('swSTATUS_ERROR', 0);
define('swSTATUS_AVAILABLE', 1);
define('swSTATUS_UNAVAILABLE', 2);

class SamsWhois extends wpWhoisPlugin {

	var $plugin_name = 'wp-whois';
	var $m_statustexts = false;
	var $m_servertemplate = false;
	var $m_status = 0;
	var $m_domain = '';
	var $m_servers = array();
	var $m_data = array();
	var $m_cachefolder = '';
	// options...

	var $m_cachelifetime = 0;
	var $m_cachefile = '';

	var $m_connectiontimeout = 5;
	var $m_sockettimeout = 30;

	var $m_redirectauth = false;

	var $m_usetlds = array();
	var $m_supportedtlds = array();
	var $m_serversettings = array();

	var $m_hilitestyle = 'font-weight: bold;';

	function SamsWhois($configfile = null) {	
		$this -> readconfig($configfile);
		$this -> m_cachefolder = dirname(__FILE__). DS . 'cache' . DS;
		$this -> m_statustexts = array('<span style="color:red;">' . __('Error', $this -> plugin_name) . '</span>','<span style="color:green;">' .  __('{domain} is Available.', $this -> plugin_name) . '</span>','<span style="color:red;">' . __('{domain} is Registered.', $this -> plugin_name) . '</span>');
		$this -> m_servertemplate = __('whois lookup at {server}...', $this -> plugin_name);
	}

	function readconfig($filename){
		if ($filename == '') $filename = dirname(__FILE__) . DS . 'config.txt';
		$data = join('',file($filename));
		$this -> m_serversettings = array();
		$this -> m_tlds = array();
		$this -> m_usetlds = array();
		if( preg_match('/\[servers\](.*)\[\/servers\].*\[tlds\](.*)\[\/tlds\]/is', $data, $matches) ){
			$servers = explode("\n",$matches[1]);
			$tlds = explode("\n",strtolower($matches[2]));
			$cnt = count($servers);
			$defaulthilight = '';
			if( preg_match('/^defaulthilight=(.*)/im', $data, $match ) ) $defaulthilight = $match[1];
			foreach( $servers as $server){
				$server = trim($server);
				$bits = explode('|', $server);
				if( count($bits) > 1 ){
					for( $i = count($bits); $i < 5; $i++){
						if( !isset($bits[$i]) ) $bits[$i] = '';
					}
					$server = explode("#", $bits[0]);
					if( !isset($server[1]) ) $server[1] = '';
					if( $bits[4] == '' ) $bits[4] = $defaulthilight;
					$this -> m_serversettings[$server[0]] = array(
								'server' => $server[0], 'available' => $bits[1], 'auth' => $bits[2], 
								'clean' => $bits[3], 'hilite' => $bits[4], 'extra' => $server[1]);
				}
			}
			
			foreach( $tlds as $tld ){
				$tld = trim($tld);
				$bits = explode('=', $tld);
				
				if(count($bits) == 2 && $bits[0] != '' && isset($this -> m_serversettings[$bits[1]])){
					$this -> m_usetlds[$bits[0]] = true;
					$this -> m_tlds[$bits[0]] = $bits[1];
				}
			}
		}
	}

	function SetTlds($tlds = 'com,net,org,info,biz,us,co.uk,org.uk'){
		$tlds = strtolower($tlds);
		$tlds = explode(',',$tlds);
		$this->m_usetlds = array();
		foreach( $tlds as $t ){
			$t = trim($t);
			if( isset($this->m_tlds[$t]) ) $this->m_usetlds[$t] = true;
		}
		return count($this->m_usetlds);
	}

	function Lookup($domain){
		$domain = strtolower($domain);
		$this -> m_servers = array();
		$this -> m_data = array();
		$this -> m_tld = $this -> m_sld = '';
		$this -> m_domain = $domain;
		if( $this -> splitdomain($this -> m_domain, $this -> m_sld, $this -> m_tld) ){
			$this -> m_servers[0] = $this -> m_tlds[$this -> m_tld];
			$this -> m_data[0] = $this -> dolookup($this -> m_serversettings[$this -> m_servers[0]]['extra'].$domain, $this -> m_servers[0]);
			
			if( $this->m_data[0] != '' ){
				if( strpos($this -> m_data[0], $this -> m_serversettings[$this -> m_servers[0]]['available']) === false ){
					$this -> m_status = swSTATUS_UNAVAILABLE;
				}else{
					$this -> m_status = swSTATUS_AVAILABLE;
				}
				if( $this -> m_serversettings[$this->m_servers[0]]['auth'] != '' && $this->m_redirectauth && $this->m_status == swSTATUS_UNAVAILABLE){
					if( preg_match('/'.$this->m_serversettings[$this->m_servers[0]]['auth'].'(.*)/i', $this->m_data[0], $match) ){
						$server = trim($match[1]);
						if( $server != '' ){
							$this -> m_servers[1] = $server;
							$command = isset($this->m_serversettings[$this->m_servers[1]]['extra']) ? $this->m_serversettings[$this->m_servers[1]]['extra'] : '';
							$dt = $this->dolookup($command.$this->m_domain, $this->m_servers[1]);
							$this -> m_data[1] = $dt;
						}
					}
				}
				return true;
			}else{
				return false;
			}
		}
		return false;
	}


	function ValidDomain($domain){
		$domain = strtolower($domain);
		return $this -> splitdomain($domain, $sld, $tld);
	}

	function GetTlds($alphabetical = false){
		$tlds = array_keys($this->m_usetlds);
		if( $alphabetical ) sort($tlds);
		return $tlds;
	}

	function TldOptions($current = '', $alphabetical = false){
		$opts = '';
		/*if( $alphabetical ){
			$tmp = array();
			
			foreach( $this->m_usetlds as $t=>$i) $tmp[] = $t;
			sort($tmp);
		}else{
			$tmp = array_keys($this->m_usetlds);
		}*/
		
		
		if ($alltlds = $this -> get_option('alltlds')) {
			$usetlds = $this -> get_option('usetlds');
		
			$tlds = array();
			foreach ($alltlds as $tld) {
				$current = (empty($current)) ? $tld : $current;
						
				if (!empty($usetlds) && in_array($tld, $usetlds)) {
					$tlds[] = $tld;
				}
			}
			
			$tmp = $tlds;
		
			/*if ($alphabetical) {
				$tmp = array();
				foreach ($tlds as $t => $i) {
					$tmp[] = $i;
				}
			
				sort($tmp);
			} else {
				$tmp = array_keys($tlds);
			}*/
			
			foreach($tmp as $t) {
				$sel = ($t == $current) ? ' SELECTED ' : '';
				$opts .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>' . "\n";
			}
		}
		
		return $opts;
	}

	function GetDomain(){
		return $this -> m_domain;
	}

	function GetServer($i = 0){
		return isset($this->m_servers[$i]) ? $this->m_servers[$i] : '';
	}

	function GetData($i = -1, $clean = true, $hilite = false) {					
		if( $i != -1 && isset($this -> m_data[$i])){
			$dt = htmlspecialchars(trim($this -> m_data[$i]));
			if ($clean) $this -> cleandata($this -> m_servers[$i], $dt);
			if ($hilite) $this -> hilightdata($this -> m_servers[$i], $dt);
			
			if ($this -> m_tld == "co.za") {
				preg_match("/(url\:)(.*)/si", $this -> m_data[$i], $matches);
				
				$message = __('This domain is registered, you can', $this -> plugin_name);	
				$message .= ' <a href="' . $matches[2] . '" target="_blank">' . __('view the WHOIS information', $this -> plugin_name) . '</a>.';
				return $message;
			}
			
			return $dt;
		} else {
			return trim(join("\n", $this -> m_data));
		}
		
		return '';
	}

	function GetStatus(){
		return $this -> m_status;
	}

	function GetStatusText(){
		$s = array('{domain}', '{sld}', '{tld}');
		$r = array($this->m_domain, $this->m_sld, $this->m_tld);
		
		return str_replace($s, $r, $this->m_statustexts[$this->m_status]);
	}

	function SetCacheLifetime($life = 0){
		$this -> m_cachelifetime = $life;
		return;
	}

	function GetServerCount(){ return count($this->m_servers);}
	function SetAvailableMessage($msg){ $this->m_statustexts[swSTATUS_AVAILABLE] = $msg;}
	function SetRegisteredMessage($msg){ $this->m_statustexts[swSTATUS_UNAVAILABLE] = $msg;}
	function SetServerText($txt){ $this->m_servertemplate = $txt;}
	
	function GetServerText($i){ 
		return isset($this->m_servers[$i]) ? str_replace('{server}', $this -> m_servers[$i], $this -> m_servertemplate) : '';
	}

	/* internal functions */

	function splitdomain($domain, &$sld, &$tld){
		$domain = strtolower($domain);
		$sld = $tld = '';
		$domain = trim($domain);
		$pos = strpos($domain, '.');
		if( $pos != -1){
			$sld = substr($domain, 0, $pos);
			$tld = substr($domain, $pos+1);
			if( isset($this->m_usetlds[$tld]) && $sld != '' ) return true;
		}else{
			$tld = $domain;
		}
		return false;
	}

	function whatserver($domain){
		$sld = $tld = '';
		$this->splitdomain($domain, $sld, $tld);
		$server = isset($this->m_usetlds[$tld]) ? $this->m_tlds[$tld] : '';
		return $server;
	}

	function readfromcache($domain, $server){
		$domain = strtolower($domain);
		$server = strtolower($server);
		$cname = md5($domain.$server);
		if( $cname != '' ){
			$folder = ($this->m_cachefolder == '' ? dirname(__FILE__) . DS . 'cache' . DS : $this->m_cachefolder);
			if( $folder[strlen($folder)-1] != DS) $folder.= DS;
			$fname =$folder.$cname.'.cache';
			if( @file_exists($fname) ){
				if( @filemtime($fname) > time()-($this->m_cachelifetime * 60) ){
					return file_get_contents($fname);
				}
			}
		}
		return '';
	}

	function writetocache($domain, $server, &$data){
		$domain = strtolower($domain);
		$server = strtolower($server);
		$cname = md5($domain.$server);
		if( $cname != '' ){
			$folder = ($this->m_cachefolder == '' ? dirname(__FILE__) . DS . 'cache' . DS : $this->m_cachefolder);
			if( $folder[strlen($folder)-1] != DS) $folder.= DS;
			$fname =$folder.$cname.'.cache';
			if( @file_exists($fname) ) @unlink($fname);
			$fp = @fopen($fname,"w");
			if( $fp ){
				fwrite($fp, $data);
				fclose($fp);
			}
		}
	}


	function dolookup($domain, $server){
		$domain = strtolower($domain);
		$server = strtolower($server);
		if( $domain == '' || $server == '' ) return false;
		if( $this -> m_cachelifetime > 0 ){
			$data = $this -> readfromcache($domain, $server);
			if( $data != '' ) return $data;
		}
		$data = "";
		$fp = @fsockopen($server, 43,$errno, $errstr, $this -> m_connectiontimeout);
		if( $fp ){
			@fputs($fp, $domain."\r\n");
			@socket_set_timeout($fp, $this->m_sockettimeout);
			while( !@feof($fp) ){
				$data .= @fread($fp, 4096);
			}
			@fclose($fp);

			if ($this -> m_cachelifetime > 0) $this -> writetocache($domain, $server, $data);
			return $data;
		}else{
			return __("\nError - could not open a connection to ", $this -> plugin_name) . $server . "\n\n";
		}
	}

	function hilightdata($server, &$data){		
		if (isset($this -> m_serversettings[$server]) ){			
			$hi = $this -> m_serversettings[$server]['hilite'];
			
			if ($hi != ''){
				$hi = explode("#", $hi);
				
				foreach( $hi as $h ){
					$h = preg_replace('/[^a-z0-9_ :]/i', '.', $h);
					$reg = '/^(\s*)(('.$h.')(.*))/im';
					$data = preg_replace($reg, '$1<span class="swHilight">$2</span>', $data);
				}
			}
		}
	}

	function cleandata($server, &$data){
		if (isset($this -> m_serversettings[$server])) {
			$clean = $this -> m_serversettings[$server]['clean'];
			
			if ($clean != ''){
				$from = $clean[0];
				if ($from == '>' || $from == '<'){
					$clean = substr($clean,1);
					
					$pos = strpos(strtolower($data), strtolower($clean));
					
					if ($pos !== false){
						if($from == '>'){
							$data = trim(substr($data, 0, $pos));
						} else {
							$data = trim(substr($data, $pos + strlen($clean)));
						}
					}
				}
			}
		}
	}
}

?>