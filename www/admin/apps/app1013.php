<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowConfirm' style='text-align:center;'>";
			n+="<div style='color:#ffffff;'>Markierte Visuelemente mit Cursortasten bewegen<br>(Übernehmen: ENTER / Abbruch: ESC)</div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		window.addEventListener("keydown",app2_moveElementsByKeyboardKeyEvent,false);
<? } } sql_disconnect(); ?>