<?php 

	include 'Snoopy/Snoopy.class.php';
	$snoopy = new Snoopy();
    $url = "http://localhost:8000/spider/demo3/form-demo.html";
    // $snoopy->fetch($url);
    // $snoopy->fetchtext($url);
    $snoopy->fetchform($url);
    // $snoopy->fetchlinks($url);
    //默认情况下，相对链接将自动补全，转换成完整的URL。
    // var_dump($snoopy->results);
    // return $snoopy->results;
    $formvars["userName"] = "admin";
    $formvars["password"] = "admin"; 
    $action = "http://localhost:8000/spider/demo3/form-demo.php";//表单提交地址
    //1-openssl extension required for HTTPS
    //php.in ==> ;extension=php_openssl.dll
    //2-405 Not Allowed增加
    $snoopy->agent = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)"; //伪装浏览器
    $snoopy->referer = "http://www.icultivator.com"; //伪装来源页地址 http_referer
    $snoopy->rawheaders["Pragma"] = "no-cache"; //cache 的http头信息
	$snoopy->rawheaders["X_FORWARDED_FOR"] = "122.0.74.166"; //伪装ip
	//2-end
	//3-使用代理
	/*$snoopy->proxy_host = "http://www.icultivator.com";
	// HTTPS connections over proxy are currently not supported
	$snoopy->proxy_port = "8080"; //使用代理
	$snoopy->maxredirs = 2; //重定向次数
	$snoopy->expandlinks = true; //是否补全链接 在采集的时候经常用到
	$snoopy->maxframes = 5; //允许的最大框架数
	//注意抓取框架的时候 $snoopy->results 返回的是一个数组*/
    //3-end
	$snoopy->submit($action,$formvars);
	echo $snoopy->error; //返回报错信息
    echo $snoopy->results;
    
 ?>