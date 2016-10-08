<?php

include_once './config.php';

class my_insert {
	var $conn;
	
	function test() {
		echo "test be called";
	}
	
	function put_web_page($url, $content, $filesize) {
		$sql = "insert into web_page (page_url_md5, page_url, page_content, page_length, grab_time) values ('".md5($url)."','$url','$content',$filesize,'".date("Y-m-d H:i:s", time())."')";
		$result = mysql_query($sql);
		$id = mysql_insert_id();
		//echo ("new insert id is $id\n");
		return $id;
	}
	
	function put_web_pic($url, $content, $filename, $filesize, $ref_page) {
		$sql = "insert into pic_gallery (file_url_md5, file_url, file_data, file_name, file_size) values ('".md5($url)."','$url','$content','$filename',$filesize)";
		$result = mysql_query($sql);
		$id = mysql_insert_id();
		return $id;
	}
	
	function get_grab_history(&$oldhistory, $subkey) {
		$sql = "select id, file_url,url_md5 from grab_history where url_md5 like '$subkey%'";
		$result = mysql_query($sql);
		$num = mysql_num_rows($result);
		$i;
		for ($i = 0; $i < $num; $i++) {
			//$url = mysql_result($result, $i, "file_url");
			$url = mysql_result($result, $i, 1);
			$md5 = mysql_result($result, $i, 2);
			//$oldhistory[$url] = $url;
			$oldhistory[$md5] = $url;
			//if (count($oldhistory) % 1000 == 0) {
			//	$id = count($oldhistory);
			//	echo("the size of history is $id!\n");
			//}
		}
	}
	
	function add_history($url, $md5) {
		$sql = "insert into grab_history (file_url, url_md5) values ('$url', '$md5');";
		$result = mysql_query($sql);
		$id = mysql_insert_id();
		return $id;
	}
	
	function __construct() {
		global $mysql_host;
		global $mysql_user;
		global $mysql_pwd;
		global $mysql_db;
		$this->conn = mysql_connect($mysql_host, $mysql_user, $mysql_pwd);
		if (!$this->conn) die("error: mysql connect failed!");
		echo("mysql connect ok!\n");
		mysql_select_db($mysql_db, $this->conn);
	}
	
	function __destruct() {
		mysql_close($this->conn);
		echo "mysql disconnected!\n";
	}
	

}






?>


