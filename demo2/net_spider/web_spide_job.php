<?php

include_once './web_site_info.php';
include_once './mysql_insert.php';
include_once './web_grab_history.php';

function make_own_dir($dirname) {
	$dir_list = split('[\\]', $dirname);
	$dir_name;
	if (count($dir_list)) {
		$dir_name = "";
		foreach($dir_list as $dn) {
			if (strlen($dir_name) > 0) {
				$dir_name = $dir_name ."\\";
			}
			$dir_name = $dir_name . $dn;
			if (!file_exists($dir_name)) {
				mkdir($dir_name);
			}
		}
	}
}

class web_crawl_job {
	var $m_level;
	var $m_url;
	var $url_info;
	var $sub_job;
	var $page_images;
	var $dbc;
	var $m_max_deep;
 
	function get_save_filename() {
		return $this->url_info->get_save_filename();
	}
 
 
	function sub_job_count() {
		return count($this->sub_job);
	}
 
	function do_sub_job() {
		$i;
		global $global_download;
		$count = count($this->sub_job);
		for ($i = 0; $i < $count; $i++) {
			$url = $this->url_info->calc_path($this->sub_job[$i]);

			if ($global_download->have_key($url)) {
				echo "have downloaded: ".$url ."\n";
				continue;
			}
			
			$pos = strpos($url, "http://");
			if ($pos === false) {
				echo "error url: $url\n";
			}else {
				//$global_download->add_key($url);
				$sub = new web_crawl_job($url, $this->m_level + 1, $this->dbc, $this->m_max_deep);
				unset($sub);
			}
			sleep(2);
		}
	}
	
	function down_page_pic() {
		$i;
		global $global_download;
		$count = count($this->page_images);
		for ($i = 0; $i < $count; $i++) {
			$url = $this->url_info->calc_path($this->page_images[$i]);
			if ($global_download->have_key($url)) {
				echo "have downloaded: ".$url ."\n";
				continue;
			}
			$pos = strpos($url, "http://");
			if ($pos === false) {
				echo "error image url: $url\n";
			}else {
				echo $url."\n";
				//$global_download[$url] = $url;
				$global_download->add_key($url);
				$content = file_get_contents($url);
				if (strlen($content) == 0) {
					continue;
				}
				if (strlen($content) == 0) {
					$retry_count = 0;
					while (strlen($content) == 0) {
						if ($retry_count++ >= 2) {
							break;
						}
						sleep(1);
						$content = file_get_contents($url);					
					}
				}
				$this->dbc->put_web_pic($url, addslashes($content), basename($url), strlen($content), $this->m_url);
			}
		}
	}
 
	function web_crawl_job($url, $level, $db_in_op, $m_dp) {
		$this->m_level = $level;
		$this->m_url = $url;
		$this->url_info = new web_site_info($url);
		$this->dbc = $db_in_op;
		$this->m_max_deep = $m_dp;
		global $global_download;

	
		echo "My Level is:" .$level ."\n";

		$reg = "#<a[^>]+href=(['\"])(.+)\\1#isU";
		$content = file_get_contents($url);
		if (strlen($content) == 0) {
			$retry_count = 0;
			while(strlen($content) == 0) {
				sleep(10);
				$content = file_get_contents($url);
				$retry_count = $retry_count + 1;
				if ($retry_count > 3) {
					$global_download->add_key($url);
					return;
				}
			}
		}
		$md5_str;
		
		preg_match_all($reg,   $content,   $m);
		foreach($m[2]   as   $src) {
			$this->sub_job[] = $src;
		}
		$reg   =   "#<img[^>]+src=(['\"])(.+)\\1#isU";
		preg_match_all($reg, $content, $m);
		foreach($m[2] as $src) {
			$this->page_images[] = $src;
		}
		$db_in_op->put_web_page($url, addslashes($content), strlen($content));
		$this->down_page_pic();
		
		$global_download->add_key($url);
		
		if ($this->m_level < $this->m_max_deep and count($this->sub_job) > 0) {
			$this->do_sub_job();
		}
	}
}

?>
