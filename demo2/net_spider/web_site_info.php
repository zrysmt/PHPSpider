<?php

class web_site_info {
	var $web_site;
	var $web_dir;
	var $web_filename;
 
	function get_save_path() {
		$ret;
		if (strlen($this->web_dir)) {
			$ret = $this->web_site."\\".$this->web_dir;
		}else {
			$ret = $this->web_site;
		}
  
		$ret = ereg_replace("/", "\\", $ret);
		return $ret;
	}
 
	function get_save_filename() {
		$ret = $this->get_save_path();
		if (strlen($this->web_filename) == 0) {
			$ret = $ret."\\index.html";
		}else {
			$ret = $ret ."\\".$this->web_filename;
		}
		$ret = ereg_replace("/", "\\", $ret);
		return $ret;
	}
 
	function web_site_info($url) {
		$temp = ereg_replace("http://", "", $url);
		$sp = split('[/]', $temp);
		$sc = count($sp);
		$i;

		echo("$url\n");
  
		$this->web_site = $sp[0];
  
		if ($sc == 1) {
			return;
		}
  
		if ($sc == 2) {
			$this->web_filename = $sp[1];
		}else {
			for ($i = 1; $i < $sc -1; $i++) {
				if ($i > 1) {
					$this->web_dir = $this->web_dir . "/";
				}
				$this->web_dir =  $this->web_dir . $sp[$i];
			}
			$this->web_filename = $sp[$sc-1];
		}
	}

	function calc_path($url_path) {
		$ret = "";
		$temp = "";
		$url = trim($url_path);
		$pos = strncmp($url, "http://", 7);
		if ($pos == 0) {
			return $url;
		}
		
		$pos = strncmp($url, "../", 3);
		if ($pos == 0) {
			$ret = $this->web_site ."/" .$this->web_dir;
			$ret = dirname($ret);
			$ret = "http://" .$ret ."/";
			$temp = ereg_replace("../", $ret, $url);
			return $temp;
		}
		
		$pos = strncmp($url, "./", 2);
		if ($pos == 0) {
			$ret = "http://" .$this->web_site ."/";
			if (strlen($this->web_dir) > 0)
				$ret = $ret .$this->web_dir ."/";
			$temp = ereg_replace("./", $ret, $url);
			return $temp;
		}
		
		
		
		$pos = strncmp($url, "/", 1);
		if ($pos == 0) {
			$ret = "http://" .$this->web_site .$url;
			return $ret;
		}
		
		$ret = "http://" .$this->web_site ."/";
		if (strlen($this->web_dir) > 0) {
			$ret = $ret .$this->web_dir ."/";
		}
		$ret = $ret .$url;
		return $ret;
		
	}
}

?>