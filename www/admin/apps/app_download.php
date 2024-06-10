<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); if (checkAdmin($sid,false)) { $fn=trim(httpGetVar('filename')); if (basename($fn)==$fn) { if (file_exists(MAIN_PATH.'/www/data/tmp/'.$fn)) { header('Location: ../../data/tmp/'.basename($fn)); } } else { if (file_exists($fn)) { exec('tar -cf "'.MAIN_PATH.'/www/data/tmp/'.basename($fn).'.tar" -C "'.dirname($fn).'" "'.basename($fn).'"'); header('Location: ../../data/tmp/'.basename($fn).'.tar'); } } } else { ?>
	<script type="text/javascript">
	parent.jsLogout();
	</script>
<? } sql_disconnect(); ?>