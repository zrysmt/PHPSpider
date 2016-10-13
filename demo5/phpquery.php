<?php 
 require('phpQuery/phpQuery.php');
 phpQuery::newDocumentFile('http://www.baidu.com/'); 
 $menu_a = pq("a"); 
 foreach($menu_a as $a){
    echo pq($a)->html()."<br>";
 } 
 foreach($menu_a as $a){
    echo pq($a)->attr("href")."<br>";
 } 
?>