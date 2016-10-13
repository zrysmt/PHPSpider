<?php 
	include("PHPCrawl/libs/PHPCrawler.class.php");
	class MyCrawler extends PHPCrawler 
	{ 
	  function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo) 
	  { 
	    // As example we just print out the URL of the document 
	    echo $PageInfo->url."<br>"; 
	    // echo $PageInfo->referer_url."\n"; 
	  } 
	}
	$crawler = new MyCrawler(); 
	$crawler->setURL("www.baidu.com"); 
	$crawler->addURLFilterRule("#\.(jpg|gif)$# i");
	//过滤到含有这些图片格式的URL
	$crawler->go();
 ?>