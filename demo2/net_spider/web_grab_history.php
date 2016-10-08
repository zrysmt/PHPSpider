<?php

//include_once './config.php';
include_once './mysql_insert.php';


class web_grab_history {
	var $m_db_op;
	var $m_oldhistory;
	var $m_newhistory;
	var $m_subkey;
	
	function __construct($db_op) {
		$this->m_db_op = $db_op;
		//$db_op->get_grab_history($this->m_oldhistory);
	}
	
	function save_history() {
		foreach($this->m_newhistory as $md5 => $url) {
			$this->m_db_op->add_history($url, $md5);
		}
	}

	function load_subkey($subkey) {
		$this->m_subkey[$subkey] = $subkey;
		$this->m_db_op->get_grab_history($this->m_oldhistory, $subkey);
	}
	
	function __destruct() {}
	
	function have_key($url) {
		$ret = false;
		$md5 = md5($url);
		$subkey = $md5[0] .$md5[1] .$md5[2];


		if (strstr($url, "rar") > 0) {
			return true;
		}

		if (count($this->m_subkey) > 0 && array_key_exists($subkey, $this->m_subkey) == true) {
		}else {
			$this->load_subkey($subkey);
		}
		
		
		if (count($this->m_oldhistory)) {
			//$ret |= array_key_exists($url, $this->m_oldhistory);
			$ret |= array_key_exists($md5, $this->m_oldhistory);
		}

		if ($ret == true) {
			return $ret;
		}
		
		if (count($this->m_newhistory)) {
			//$ret |= array_key_exists($url, $this->m_newhistory);
			$ret |= array_key_exists($md5, $this->m_newhistory);
		}
		return $ret;		
	}
	
	function add_key($url) {
		$md5 = md5($url);
		//$this->m_newhistory[$url] =$url;
		$this->m_newhistory[$md5] = $url;
	    if (count($this->m_newhistory) > 400) {
			//
		}	
	}
	
	
}


?>


