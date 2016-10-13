<?php
/**
 *
 * 将demo1-02换成curl爬虫，速度快了
 * 爬虫程序 -- 原型
 *
 * 从给定的url获取html内容
 * 
 * @param string $url 
 * @return string 
 */
function _getUrlContent($url) {
    /*$handle = fopen($url, "r");
    if ($handle) {
        $content = stream_get_contents($handle, 1024 * 1024);//file_get_contents 读取资源流到一个字符串,第二个参数需要读取的最大的字节数。默认是-1（读取全部的缓冲数据）
        return $content;
    } else {
        return false;
    } */
    $ch=curl_init();  //初始化一个cURL会话
    //curl_setopt 设置一个cURL传输选项
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
    curl_setopt($ch, CURLOPT_USERPWD, "username:password");    
    curl_setopt($ch, CURLOPT_SSLVERSION,3); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch,CURLOPT_URL,$url);//设置需要获取的 URL 地址
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
    curl_setopt($ch,CURLOPT_HEADER,1);//启用时会将头文件的信息作为数据流输出
    $result=curl_exec($ch);//执行一个cURL会话
    $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);// 最后一个收到的HTTP代码
    if($code!='404' && $result){
        return $result;
    }
    curl_close($ch);//关闭cURL
} 
/**
 * 从html内容中筛选链接
 * 
 * @param string $web_content 
 * @return array 
 */
function _filterUrl($web_content) {
   /* $reg_tag_a = '/<[a|A].*?href=[\'\"]{0,1}([^>\'\"\]*).*?>/';*/
    $reg_tag_a = '/<a.*href=[\'\"]{0,1}(http:\/\/(?:jump.jinpai.58.com\/|jump.zhineng.58.com\/|sort.58.com\/zd_p\/)+[^>\'\"]*).*>/';
    //http://jump.jinpai.58.com/精牌  http://jump.zhineng.58.com/ 精选   http://sort.58.com/zd_p/ 置顶 
    $result = preg_match_all($reg_tag_a, $web_content, $match_result);
    if ($result) {
        return $match_result[1];
    } 
} 
/**
 * 修正相对路径
 * 
 * @param string $base_url 
 * @param array $url_list 
 * @return array 
 */
function _reviseUrl($base_url, $url_list) {
    $url_info = parse_url($base_url);//解析url
    $base_url = $url_info["scheme"] . '://';
    if ($url_info["user"] && $url_info["pass"]) {
        $base_url .= $url_info["user"] . ":" . $url_info["pass"] . "@";
    } 
    $base_url .= $url_info["host"];
    if ($url_info["port"]) {
        $base_url .= ":" . $url_info["port"];
    } 
    $base_url .= $url_info["path"];
    if (is_array($url_list)) {
        foreach ($url_list as $url_item) {
            if (preg_match('/^http/', $url_item)) {
                // 已经是完整的url
                $result[] = $url_item;
            } else {
                // 不完整的url
                $real_url = $base_url . '/' . $url_item;
                $result[] = $real_url;
            } 
        } 
        return $result;
    } else {
        return;
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
    if ($content) {
        $url_list = _reviseUrl($url, _filterUrl($content));
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
 * 测试用主程序
 */
function main() {
    $file_path = "./url-02.txt";
    $current_url = "http://sh.58.com/fangchan/?PGTID=0d100000-0000-2ce8-2f66-8e9c53f221e9&ClickID=1"; //初始url
    $phantomSrc = "E:\js库\phantomjs-2.1.1-windows\bin\phantomjs.exe";
    $jsSrc = "F:\php\www\spider\demo6\01.js";
    if(file_exists($file_path)){
        unlink($file_path);
    }
    $fp_puts = fopen($file_path, "ab"); //记录url列表
    $fp_gets = fopen($file_path, "r"); //保存url列表
   // do {
        $result_url_arr = crawler($current_url);
        if ($result_url_arr) {
            foreach ($result_url_arr as $url) {   
               /* exec("E:\jsLibs\phantomjs-2.1.1-windows\bin\phantomjs.exe F:/php/www/spider/demo6/01.js $url",$info);
                var_dump($info);*/
                fputs($fp_puts, $url . "\r\n");
            } 
        }
        fclose($fp_puts);
        fclose($fp_gets);
        /*$result_url_arr = crawler($current_url);
        if ($result_url_arr) {
            $js = "";
            foreach ($result_url_arr as $url) {  
                $js .= "window.open('".$url."');\n";      
                fputs($fp_puts, $url . "\r\n");
            } 
            echo "<script> $js;             
                 </script>"; 
        } */
    //} while ($current_url = fgets($fp_gets, 1024)); //不断获得url
    
} 
main();
 
?>