<?php 
	include 'Snoopy/Snoopy.class.php';
	$snoopy = new Snoopy;
	// $snoopy->proxy_host = "122.0.74.166";
	// $snoopy->proxy_port = "80";
	$snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)";
	// $snoopy->referer = "http://www.sina.com.cn/";
	// $snoopy->cookies["SessionID"] = "238472834723489l";
	// $snoopy->cookies["favoriteColor"] = "RED";
	$snoopy->rawheaders["Pragma"] = "no-cache";
	$snoopy->maxredirs = 2;
	$snoopy->offsiteok = false;
	$snoopy->expandlinks = true;
	$snoopy->user = "joe";
	$snoopy->pass = "bloe";
	if($snoopy->fetchtext("http://www.sina.com.cn/")){
		echo "".htmlspecialchars($snoopy->results)."";
	}else{
		echo "error fetching document: ".$snoopy->error."";
	}


 ?>