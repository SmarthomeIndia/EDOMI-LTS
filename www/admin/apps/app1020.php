<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/shared/php/incl_camera.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowDrag' id='<?echo $winId;?>-global'>";
		n+="<div class='appTitelDrag' onMouseDown='dragWindowStart(\"<?echo $appId;?>\",\"<?echo $winId;?>-global\");'>Kameraansicht: Einstellungen<div class='cmdClose' onClick='closeWindow(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
		n+="<div id='<?echo $winId;?>-main' style='width:1024px;'></div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		dragWindowRestore("<?echo $appId;?>","<?echo $winId;?>-global");
<? cmd('start'); } if ($cmd=='start') { ?>
		var srctyp=document.getElementById("<?echo $phpdataArr[0];?>-fd5").dataset.value;

		var n="<div class='appMenu'>";
			n+="<div class='cmdButton cmdButtonL' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
			n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"return\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $phpdata;?>\");'><b>Übernehmen</b></div>&nbsp;&nbsp;&nbsp;";
			n+="<div class='cmdButton' onClick='ajax(\"refreshImage\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $phpdata;?>\");' style='float:right;'>Bild aktualisieren</div>";
		n+="</div>";
		
		n+="<div id='<?echo $winId;?>-form1' class='appContent' style='padding:8px;'>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd1' data-type='1' value=''></input>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd2' data-type='1' value=''></input>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd3' data-type='1' value=''></input>";

			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0'>";
				n+="<tr valign='top'>";
					n+="<td colspan='2' width='495'>";
						n+="<canvas id='<?echo $winId;?>-canvas1' style='width:495px; height:371px; background:#343434;'></canvas>";
					n+="</td>";
					n+="<td width='3'><div style='width:3px;'></div></td>";
					n+="<td colspan='2' width='495'>";
						n+="<canvas id='<?echo $winId;?>-canvas2' style='width:495px; height:371px; background:#343434;'></canvas>";
					n+="</td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='5'>&nbsp;</div></td>";
				n+="</tr>";

		if (srctyp==1) {

				n+="<input type='hidden' id='<?echo $winId;?>-fd11' data-type='1' value='0'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd18' data-type='1' value='0'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd19' data-type='1' value='0'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd21' data-type='1' value='0'></input>";
	
				n+="<tr>";
					n+="<td colspan='2'>Zoom <span id='<?echo $winId;?>-v10' style='float:right;'></span><br><input id='<?echo $winId;?>-fd10' data-type='1' type='range' class='controlSlider' min='0' max='500' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",10,\"db_zoom\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td>Seitenverhältnis: Breite<br><input type='text' id='<?echo $winId;?>-fd15' data-type='1' value='' class='control1' onChange='app1020_changeValue(\"<?echo $winId;?>\",15,\"db_dstw\",this.value);' style='width:100%;'></input></td>";
					n+="<td>Höhe<br><input type='text' id='<?echo $winId;?>-fd16' data-type='1' value='' class='control1' onChange='app1020_changeValue(\"<?echo $winId;?>\",16,\"db_dsth\",this.value);' style='width:100%;'></input></td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Rotieren <span id='<?echo $winId;?>-v12' style='float:right;'></span><br><input id='<?echo $winId;?>-fd12' data-type='1' type='range' class='controlSlider' min='-180' max='180' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",12,\"db_a2\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>Auflösung <span id='<?echo $winId;?>-v20' style='float:right;'></span><br><input id='<?echo $winId;?>-fd20' data-type='1' type='range' class='controlSlider' min='10' max='100' value='50' step='5' onInput='app1020_changeValue(\"<?echo $winId;?>\",20,\"db_srcs\",this.value);' style='width:100%;'></td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Verschieben (horizontal) <span id='<?echo $winId;?>-v13' style='float:right;'></span><br><input id='<?echo $winId;?>-fd13' data-type='1' type='range' class='controlSlider' min='-100' max='100' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",13,\"db_x\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>&nbsp;</td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Verschieben (vertikal) <span id='<?echo $winId;?>-v14' style='float:right;'></span><br><input id='<?echo $winId;?>-fd14' data-type='1' type='range' class='controlSlider' min='-100' max='100' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",14,\"db_y\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>&nbsp;</td>";
				n+="</tr>";

		} else if (srctyp>=2) {

				n+="<tr>";
					n+="<td colspan='2'>Zoom <span id='<?echo $winId;?>-v10' style='float:right;'></span><br><input id='<?echo $winId;?>-fd10' data-type='1' type='range' class='controlSlider' min='0' max='500' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",10,\"db_zoom\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td>Seitenverhältnis: Breite<br><input type='text' id='<?echo $winId;?>-fd15' data-type='1' value='' class='control1' onChange='app1020_changeValue(\"<?echo $winId;?>\",15,\"db_dstw\",this.value);' style='width:100%;'></input></td>";
					n+="<td>Höhe<br><input type='text' id='<?echo $winId;?>-fd16' data-type='1' value='' class='control1' onChange='app1020_changeValue(\"<?echo $winId;?>\",16,\"db_dsth\",this.value);' style='width:100%;'></input></td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Neigen <span id='<?echo $winId;?>-v11' style='float:right;'></span><br><input id='<?echo $winId;?>-fd11' data-type='1' type='range' class='controlSlider' min='-90' max='90' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",11,\"db_a1\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>Auflösung <span id='<?echo $winId;?>-v20' style='float:right;'></span><br><input id='<?echo $winId;?>-fd20' data-type='1' type='range' class='controlSlider' min='10' max='100' value='50' step='5' onInput='app1020_changeValue(\"<?echo $winId;?>\",20,\"db_srcs\",this.value);' style='width:100%;'></td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Schwenken <span id='<?echo $winId;?>-v12' style='float:right;'></span><br><input id='<?echo $winId;?>-fd12' data-type='1' type='range' class='controlSlider' min='-180' max='180' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",12,\"db_a2\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>&nbsp;</td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Verschieben (horizontal) <span id='<?echo $winId;?>-v13' style='float:right;'></span><br><input id='<?echo $winId;?>-fd13' data-type='1' type='range' class='controlSlider' min='-100' max='100' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",13,\"db_x\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>Anpassung: Radius <span id='<?echo $winId;?>-v18' style='float:right;'></span><br><input id='<?echo $winId;?>-fd18' data-type='1' type='range' class='controlSlider' min='-100' max='100' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",18,\"db_srcr\",this.value);' style='width:100%;'></td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2'>Verschieben (vertikal) <span id='<?echo $winId;?>-v14' style='float:right;'></span><br><input id='<?echo $winId;?>-fd14' data-type='1' type='range' class='controlSlider' min='-100' max='100' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",14,\"db_y\",this.value);' style='width:100%;'></td>";
					n+="<td width='3' style='height:35px;'><div style='width:2px;'></div></td>";
					n+="<td colspan='2'>Anpassung: perspektivische Verzerrung <span id='<?echo $winId;?>-v19' style='float:right;'></span><br><input id='<?echo $winId;?>-fd19' data-type='1' type='range' class='controlSlider' min='-90' max='90' value='' step='1' onInput='app1020_changeValue(\"<?echo $winId;?>\",19,\"db_srcd\",this.value);' style='width:100%;'></td>";
				n+="</tr>";

		}
		
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;
		
		//Parameter aus app1000 übernehmen und Slider-Captions setzen
		for (var t=10;t<=20;t++) {
			var tmp=document.getElementById("<?echo $winId;?>-fd"+t); 
			if (tmp) {
				tmp2=document.getElementById("<?echo $phpdataArr[0];?>-fd"+t);
				if (tmp2) {tmp.value=tmp2.value;}
			}
			var tmp=document.getElementById("<?echo $winId;?>-v"+t); 
			if (tmp) {
				tmp2=document.getElementById("<?echo $winId;?>-fd"+t);
				if (tmp2) {tmp.innerHTML=app1020_formatCaption(t,tmp2.value);}
			}
		}
		controlInitAll("<?echo $winId;?>-form1");		

<? $imgFn=cmd('loadImage'); if ($imgFn!==false) { ?>
			camView=new class_camView();
			camView.setProperty("url","<?echo $imgFn;?>");		
			camView.setProperty("srccanvas",document.getElementById("<?echo $winId;?>-canvas1"));		
			camView.setProperty("dstcanvas",document.getElementById("<?echo $winId;?>-canvas2"));
			camView.setProperty("srccanvassize",495);		
			camView.setProperty("dstcanvassize",495);		
			camView.setProperty("srctyp",parseInt(srctyp));		
			camView.setProperty("db_zoom",parseInt(document.getElementById("<?echo $winId;?>-fd10").value));
			camView.setProperty("db_a1",parseInt(((srctyp==1)?"0":document.getElementById("<?echo $winId;?>-fd11").value)));
			camView.setProperty("db_a2",parseInt(document.getElementById("<?echo $winId;?>-fd12").value));
			camView.setProperty("db_x",parseInt(document.getElementById("<?echo $winId;?>-fd13").value));
			camView.setProperty("db_y",parseInt(document.getElementById("<?echo $winId;?>-fd14").value));
			camView.setProperty("db_dstw",parseInt(document.getElementById("<?echo $winId;?>-fd15").value));
			camView.setProperty("db_dsth",parseInt(document.getElementById("<?echo $winId;?>-fd16").value));
			camView.setProperty("db_srcr",parseInt(((srctyp==1)?"0":document.getElementById("<?echo $winId;?>-fd18").value)));
			camView.setProperty("db_srcd",parseInt(((srctyp==1)?"0":document.getElementById("<?echo $winId;?>-fd19").value)));
			camView.setProperty("db_srcs",parseInt(document.getElementById("<?echo $winId;?>-fd20").value));
			camView.initLoadRender();
<? } else { ?>
			closeWindow("<?echo $winId;?>");
			jsConfirm("Das Abrufen des Kamerabildes ist fehlgeschlagen.","","none");
<? } } if ($cmd=='loadImage') { $n=sql_getValues('edomiProject.editCam','url,mjpeg','id='.$phpdataArr[1]); if ($n!==false) { $imgFn=getLiveCamImgPreview($n['url'],$n['mjpeg']); if ($imgFn!==false) { return '../data/tmp/'.$imgFn.'?'.date('YmdHis'); } } return false; } if ($cmd=='refreshImage') { $imgFn=cmd('loadImage'); if ($imgFn!==false) { ?>
			camView.setProperty("url","<?echo $imgFn;?>");		
			camView.loadRender();
<? } else { ?>
			jsConfirm("Das Aktualisieren des Kamerabildes ist fehlgeschlagen.","","none");
<? } } if ($cmd=='return') { ?>
		for (var t=10;t<=20;t++) {
			var tmp=document.getElementById("<?echo $winId;?>-fd"+t); 
			if (tmp) {document.getElementById("<?echo $phpdataArr[0];?>-fd"+t).value=tmp.value;}
		}
		closeWindow("<?echo $winId;?>");
<? } } sql_disconnect(); ?>
