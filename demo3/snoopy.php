<?php

include 'Snoopy/Snoopy.class.php';
/**
 * 爬虫程序 
 * 从给定的url获取html内容
 */
function _getUrlContent($url) {
    $snoopy = new Snoopy();
    $snoopy->fetch($url);
    // $snoopy->fetchtext($url);
    // $snoopy->fetchform($url);
    // $snoopy->fetchlinks($url);
    //默认情况下，相对链接将自动补全，转换成完整的URL。
    // var_dump($snoopy->results);
    return $snoopy->results;
} 
/**
 * 从html内容中筛选链接
 * 
 * @param string $web_content 
 * @return array 
 */
function _filterUrl($content) {
    $reg_tag_a = '/<a.*href=[\'\"]{0,1}(http:\/\/(?:jump.jinpai.58.com\/|jump.zhineng.58.com\/|sort.58.com\/zd_p\/)+[^>\'\"]*).*>/';
    // $reg_tag_a = '/(http:\/\/(?:jump.jinpai.58.com\/|jump.zhineng.58.com\/|sort.58.com\/zd_p\/)+[^>\'\"]*).*/';
    //http://jump.jinpai.58.com/精牌  http://jump.zhineng.58.com/ 精选   http://sort.58.com/zd_p/ 置顶 
    $result = preg_match_all($reg_tag_a, $content, $match_result);
    if ($result) {
        return $match_result[1];
    } 
} 

/**
 * 爬虫
 * 
 * @param string $url 
 * @return array 
 */
function crawler($url) {
    $content = _getUrlContent($url);
    $content_string ='';
    if ($content) {
        // var_dump($content);
        $url_list =  _filterUrl($content);
        if ($url_list) {
            return $url_list;
        } else {
            return ;
        } 
    } else {
        return ;
    } 
} 
/**
 * 伪装
 */
function disguise($url){
    $formvars["username"] = "admin";
    $formvars["pwd"] = "admin";
}
/**
 * 测试用主程序
 */
function main() {
    $file_path = "./snoopy-01.txt";
    $current_url = "http://sh.58.com/fangchan/?PGTID=0d100000-0000-2ce8-2f66-8e9c53f221e9&ClickID=1"; //初始url
    if(file_exists($file_path)){
        unlink($file_path);
    }
    $fp_puts = fopen($file_path, "ab"); //记录url列表
    $fp_gets = fopen($file_path, "r"); //保存url列表
    //do {
        $result_url_arr = crawler($current_url);
        if ($result_url_arr) {
            foreach ($result_url_arr as $url) {
                echo $url;
                //url处理
                fputs($fp_puts, $url . "\r\n");
            } 
        } 
    //} while ($current_url = fgets($fp_gets, 1024)); //不断获得url
} 
main();
 
?>