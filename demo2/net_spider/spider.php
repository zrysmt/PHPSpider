<?php

$global_download;

include_once 'mysql_insert.php';
include_once 'web_grab_history.php';
include_once 'web_spide_job.php';
function print_use()
{
 echo "Usage:\nphp -f deepth spider.php url\n";
}
if ($argc == 1) {
 print_use();
 die;
}

$global_grab_deep = (int)$argv[1];
$url = $argv[2];
$db_op = new my_insert();


$global_download = new web_grab_history($db_op);

$tt = new web_crawl_job($url, 1, $db_op, $global_grab_deep);

/*
echo "sub mission: ";
echo $tt->sub_job_count();
echo "\n";
if ($tt->sub_job_count() > 0) {
 $tt->do_sub_job();
}
*/

$global_download->save_history();

echo "Mission Complished!\n";



?>
