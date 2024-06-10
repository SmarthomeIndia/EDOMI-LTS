<?
/* 
*/ 
?><? ?><? error_reporting(0); set_time_limit(30); $cmd=httpGetVar('cmd'); $vseId=httpGetVar('vseid'); $visuId=httpGetVar('visuid'); $sid=preg_replace("/[^A-F0-9]+/i",'',httpGetVar('sid')); $vid=preg_replace("/[^\.0-9]+/i",'',httpGetVar('vid')); $json1=json_decode(httpPostVar('data1'),true); $json2=json_decode(httpPostVar('data2'),true); ?>