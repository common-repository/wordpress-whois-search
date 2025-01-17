<?php

define('SC_NUMBERS', 1);
define('SC_LETTERS', 2);
define('SC_UPPERCASE', 4);
define('SC_LOWERCASE', 8);

define('SC_SET', 0);
define('SC_SET_IF', 1);

class SecureImageCode{

	// font size and name
	var $m_fontsize = 14;
	// this font must be in the same folder as this file
	var $m_font = 'AIRSTREA.TTF';

	// randomly offset each letter's vertical position
	// between (-$m_jittery and +$m_jittery
	var $m_jittery = 0;

	// set to true to write the code as one word
	// set to false to output each letter separately on the image
	var $m_oneword = true;

	// set to true to add random dots in the same colour as the text
	var $m_addnoise = true;
	// number of dots to add as a % of total number of pixels
	var $m_noiseratio = 10;

	// padding at the horizontal and vertical borders of the image
	var $m_paddingx = 6;
	var $m_paddingy = 4;

	// set the text color as r,g,b values from 0-255
	var $m_textcolor = array(0,0,0);

	// set the background color as r,g,b values from 0-255
	var $m_backgroundcolor = array(255,255,255);

	// the name of the $_SESSION variable used to store the code
	var $m_sessionvar = 'securecode';

	function SecureImageCode($length = 4, $mode = SC_NUMBERS, $font = ''){
		$this -> m_length = $length;
		$this -> m_mode = $mode;
		
		if($font != ''){
			$this -> m_font = $font;
		}
	}

	function CheckCode($code, $unset = true){	
		if (!empty($code)) {
			if (!empty($_REQUEST['hashcode'])) {
				if (md5($code) == $_REQUEST['hashcode']) {
					return true;
				}		
			}
		}
		
		return false;
	
		/*if( !isset($_SESSION[$this->m_sessionvar]) ) return false;
		$correct = $_SESSION[$this->m_sessionvar];
		if( !(($this->m_mode & SC_UPPERCASE) && ($this->m_mode & SC_LOWERCASE ) ) ){
			$code = strtolower($code);
			$correct = strtolower($correct);
		}
		if( $code == $correct ){
			if( $unset ) unset($_SESSION[$this->m_sessionvar]);
			return true;
		}
		return false;*/
	}

	function GenerateCode($number = '') {
		$length = $this -> m_length;
		$mode = $this -> m_mode;
		$code = '';
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		$numbers = '0123456789';
		$chars = '';
		if (!($mode & SC_LOWERCASE) && !($mode & SC_UPPERCASE)) $mode |= SC_UPPERCASE;
		
		if ($mode & SC_LETTERS) {
			if ($mode & SC_UPPERCASE) $chars .= strtoupper($letters);
			if ($mode & SC_LOWERCASE) $chars .= $letters;
		}
		
		if ($mode & SC_NUMBERS) {
			$chars .= $numbers;
		}
		
		$n = strlen($chars);
		
		for ($i = 0; $i < $length; $i++) {
			$l = mt_rand(0, $n-1);
			$code .= $chars[$l];
		}
		
		switch ($set) {
			//case SC_SET: $_SESSION[$this -> m_sessionvar] = $code; break;
			//case SC_SET_IF: if (!isset($_SESSION[$this -> m_sessionvar])) $_SESSION[$this -> m_sessionvar] = $code; break;
		}
		
		$_SESSION[$this -> m_sessionvar . '' . $number] = $code;
		return $code;
	}

	function GenerateImage($str = ''){
		header("Content-Type: image/png");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	
		if ($str == '') $str = isset($_SESSION[$this -> m_sessionvar . $this -> ms]) ? $_SESSION[$this -> m_sessionvar . $this -> ms] : $this -> GenerateCode($this -> ms);

		$font = dirname(__FILE__) . DS . $this->m_font;
		$sizes = imagettfbbox($this->m_fontsize, 0, $font, $str);

		$imagewidth = abs($sizes[2] - $sizes[0]);
		$height = abs($sizes[3] - $sizes[5])+ $this->m_paddingy +2;
		$xoff = $imagewidth / strlen($str);
		$width = $imagewidth + $this->m_paddingx;

		$img = imagecreate($width,$height);
		$bg = imagecolorallocate($img,$this->m_backgroundcolor[0], $this->m_backgroundcolor[1], $this->m_backgroundcolor[2]);
		$color = imagecolorallocate($img,$this->m_textcolor[0], $this->m_textcolor[1], $this->m_textcolor[2]);

		$x = ($width - $imagewidth) / 2;
		$yoff = abs($sizes[5]) + ($this->m_paddingy  / 2);

		if( $this->m_oneword == true ){
			imagettftext($img, $this->m_fontsize, 0, $x, $yoff + mt_rand(-$this->m_jittery, $this->m_jittery), $color, $font, $str);
		}else{
			for( $i = 0; $i < strlen($str); $i++){
				$y = $yoff + mt_rand(-$this->m_jittery, $this->m_jittery);
				imagettftext($img, $this->m_fontsize, 0, $x, $y, $color, $font, $str[$i]);
				$x += $xoff;
			}
		}
		
		if( $this -> m_addnoise ){
			$this -> imagenoise($img,$this->m_textcolour, $this->m_noiseratio);
		}
		
		return $img;
	}

	function imagenoise($img, $c, $percent){
		$w = imagesx($img);
		$h = imagesy($img);
		$size = $w * $h;
		$cnt = $size * $percent / 100;
		$col = imagecolorallocate($img,$c[0],$c[1],$c[2]);
		for( $i = 0; $i < $cnt; $i++){
			$x = mt_rand(0,$w-1);
			$y = mt_rand(0,$h-1);
			imagesetpixel($img,$x,$y,$col);
		}
	}
}

?>