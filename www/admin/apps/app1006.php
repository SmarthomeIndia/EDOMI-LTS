<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowDrag' id='<?echo $winId;?>-global'>";
			n+="<div class='appTitelDrag' onMouseDown='dragWindowStart(\"<?echo $appId;?>\",\"<?echo $winId;?>-global\");'>Befehle (Ausgangsbox)<div class='cmdClose cmdCloseDisabled'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main' style='width:500px;'></div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		dragWindowRestore("<?echo $appId;?>","<?echo $winId;?>-global");
<? cmd('start'); } if ($cmd=='start') { ?>
		var n="<div class='appMenu'>";
			n+="<div class='cmdButton' onClick='controlReturn(\"<?echo $winId;?>\",\"<?echo $dataArr[0];?>\",\"<?echo $dataArr[2];?>\");'><b>Übernehmen</b></div>";
		n+="</div>";
		n+="<div class='appContentBlank' id='<?echo $winId;?>-form1'>";
			n+="<div id='<?echo $winId;?>-list1' data-type='1007' data-value='<?echo $dataArr[2];?>' data-itemid='0' data-db='<?echo $dataArr[5];?>' class='controlList' style='height:400px; border:none;'>&nbsp;</div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;
		controlInitAll("<?echo $winId;?>-form1");
<? } if ($cmd=='validateValue') { if ($phpdataArr[0]!='') { echo 'controlReturn("'.$winId.'","'.$dataArr[0].'","'.$phpdataArr[0].'");'; } else { echo 'shakeObj("'.$winId.'");'; } } } sql_disconnect(); ?>

