<?
/*
*/
?><? ?><? error_reporting(0);
set_time_limit(300);
$cmd = httpGetVar('cmd');
$appId = httpGetVar('appid');
$winId = httpGetVar('winid');
$clientDatetime = httpGetVar('datetime');
$sid = preg_replace("/[^A-F0-9]+/i", '', httpGetVar('sid'));
$vid = preg_replace("/[^\.0-9]+/i", '', httpGetVar('vid'));
$data = httpPostVar('data');
$dataArr = explode(AJAX_SEPARATOR1, $data);
$phpdata = httpPostVar('phpdata');
$phpdataArr = explode(AJAX_SEPARATOR1, $phpdata); ?>
