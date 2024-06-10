<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); require(MAIN_PATH."/www/admin/include/php/incl_items.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowDrag' id='<?echo $winId;?>-global'>";
			n+="<div class='appTitelDrag' onMouseDown='dragWindowStart(\"<?echo $appId;?>\",\"<?echo $winId;?>-global\");'>Design (Visuelement)<div class='cmdClose' onClick='closeWindow(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main' style='width:800px;'></div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		dragWindowRestore("<?echo $appId;?>","<?echo $winId;?>-global");
<? if (strpos($dataArr[3],'-dynDesigns')===false) {$dynDesign=false;} else {$dynDesign=true;} if (strpos($dataArr[3],'-editSheet')===false) {$editSheet=false;} else {$editSheet=true;} if ($editSheet) { $ss1=sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=".$dataArr[4].")"); if ($n=sql_result($ss1)) { $fd[1]=$n['id']; $fd[2]=''; $fd[3]=''; $fd[4]=$n['styletyp']; for ($t=1;$t<=48;$t++) {$fd[$t+10]=$n['s'.$t];} } else { $fd[1]=-1; $fd[2]=''; $fd[3]=''; $fd[4]=0; for ($t=1;$t<=48;$t++) {$fd[$t+10]='';} } } else { $ss1=sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (id=".$dataArr[4].")"); if ($n=sql_result($ss1)) { $fd[1]=$n['id']; $fd[2]=$n['targetid']; $fd[3]=$n['defid']; $fd[4]=$n['styletyp']; for ($t=1;$t<=48;$t++) {$fd[$t+10]=$n['s'.$t];} } else { $fd[1]=-1; $fd[2]=$dataArr[2]; $fd[3]=0; $fd[4]=0; for ($t=1;$t<=48;$t++) {$fd[$t+10]='';} } } ?>
		var n="<div class='appMenu'>";
			n+="<div class='cmdButton cmdButtonL' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
			n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"saveItem\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));'><b>Übernehmen</b></div>";
<? if (!$editSheet) { if ($dynDesign) { ?>
				n+="<div class='cmdButton' onClick='ajax(\"copyDesign\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $fd[2];?>\");' style='float:right; margin-left:5px;'>Basis-Design übernehmen</div>";
<? } ?>
			n+="<div class='cmdButton' onClick='app1003_resetForm(\"<?echo $winId;?>\");' style='float:right;'>Eingaben zurücksetzen</div>";
<? } ?>
		n+="</div>";

		n+="<div id='<?echo $winId;?>-form1' class='appContent' style='padding:0;'>";
<? ?>
			n+="<div class='appContent' style='background:#e0e0d9; padding:8px; margin:0; border-radius:0;'>";
<? if ($editSheet) { ?>
				n+="<input type='hidden' id='<?echo $winId;?>-fd1' data-type='1' value='<?echo $fd[1];?>'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd4' data-type='1' value='<?echo $fd[4];?>'></input>";
<? } else { ?>
				n+="<input type='hidden' id='<?echo $winId;?>-fd1' data-type='1' value='<?echo $fd[1];?>'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd2' data-type='1' value='<?echo $fd[2];?>'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd4' data-type='1' value='<?echo $fd[4];?>'></input>";
<? } if (!($dynDesign || $editSheet)) { ?>
				n+="<input type='hidden' id='<?echo $winId;?>-fd11' data-type='1' value=''></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd12' data-type='1' value=''></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd21' data-type='1' value=''></input>";
<? } ?>
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0'>";
					n+="<colgroup>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='2%'>";
						n+="<col width='2%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
					n+="</colgroup>";
					n+="<tr valign='top'>";
<? if (!$editSheet) { if ($dynDesign) { ?>
						n+="<td colspan='4' style='padding:0; border-radius:3px 0 0 3px;'>";
<? } else { ?>
						n+="<td colspan='12' style='padding:0; border-radius:3px 0 0 3px;'>";
<? } ?>
							n+="<table width='100%' height='100%' border='0' cellpadding='2' cellspacing='0'>";
								n+="<colgroup>";
									n+="<col width='50%'>";
									n+="<col width='50%'>";
								n+="</colgroup>";
								n+="<tr><td colspan='2' class='formSubTitel' style='padding-top:5px;'>Designvorlage<hr></td></tr>";
								n+="<tr>";
									n+="<td colspan='2'><div id='<?echo $winId;?>-fd3' data-type='1000' data-root='24' data-value='<?echo $fd[3];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div></td>";
								n+="</tr>";
							n+="</table>";
						n+="</td>";
<? } if ($dynDesign || $editSheet) { if (!$editSheet) { ?>
						n+="<td rowspan='19'><div style='width:1px;'></div></td>";
						n+="<td rowspan='19' style='border-left:1px solid #d0d0c9;'><div style='width:1px;'></div></td>";
						n+="<td colspan='4' style='padding:0; border-radius:3px 0 0 3px;'>";
<? } else { ?>
						n+="<td colspan='12' style='padding:0; border-radius:3px 0 0 3px;'>";
<? } ?>
							n+="<table width='100%' height='100%' border='0' cellpadding='2' cellspacing='0'>";
								n+="<colgroup>";
									n+="<col width='50%'>";
									n+="<col width='50%'>";
								n+="</colgroup>";
								n+="<tr valign='top'><td colspan='2' class='formSubTitel' style='padding-top:5px;'>Dynamisches Design<hr></td></tr>";
								n+="<tr valign='top'>";
									n+="<td>von KO-Wert<br><input type='text' id='<?echo $winId;?>-fd11' data-type='1' value='' class='control1' style='color:#ff0000; min-width:0; width:100%;'></input></td>";
									n+="<td>bis KO-Wert<input type='text' id='<?echo $winId;?>-fd12' data-type='1' value='' class='control1' style='color:#ff0000; min-width:0; width:100%;'></input></td>";
								n+="</tr>";
								n+="<tr valign='top'>";
									n+="<td colspan='2'>Beschriftung (leer=keine Änderung)<br><textarea id='<?echo $winId;?>-fd21' data-type='1' maxlength='10000' rows='3' wrap='soft' class='control1' onkeydown='if (event.keyCode==9) {appAll_enableTabKey(this);}' style='width:100%; height:50px; resize:none; tab-size:4; background:#e0ffe0;'></textarea></td>";
								n+="</tr>";
							n+="</table>";
						n+="</td>";
<? } ?>
					n+="</tr>";
				n+="</table>";
			n+="</div>";
<? ?>
			n+="<div class='appContent' style='max-height:600px; overflow:auto; margin-top:0; padding:0 8px 8px 8px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0'>";
					n+="<colgroup>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='2%'>";
						n+="<col width='2%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
						n+="<col width='12%'>";
					n+="</colgroup>";

					n+="<tr valign='top'>";
						n+="<td colspan='10'><div style='height:10px;'></div></td>";
					n+="</tr>";
	
					n+="<tr valign='top'>";
						n+="<td colspan='4' class='formSubTitel' style='padding-top:2px;'>Allgemeine Eigenschaften<hr></td>";
						n+="<td rowspan='19'><div style='width:1px;'></div></td>";
						n+="<td rowspan='19' style='border-left:1px solid #e0e0d9;'><div style='width:1px;'></div></td>";
						n+="<td colspan='4' class='formSubTitel' style='padding-top:2px;'>Farbe und Hintergrund<hr></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='2'>X-Position (px)<br><input type='text' id='<?echo $winId;?>-fd13' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Y-Position (px)<br><input type='text' id='<?echo $winId;?>-fd14' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='4'>Farbe<br><div id='<?echo $winId;?>-fd25' data-type='1000' data-root='26' data-value='<?echo $fd[25];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Breite (px)<br><input type='text' id='<?echo $winId;?>-fd15' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Höhe (px)<br><input type='text' id='<?echo $winId;?>-fd16' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='4'>Hintergrundfarbe<br><div id='<?echo $winId;?>-fd19' data-type='1000' data-root='25' data-value='<?echo $fd[19];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Drehung (&deg;)<br><input type='text' id='<?echo $winId;?>-fd17' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Opazität (0..1)<br><input type='text' id='<?echo $winId;?>-fd18' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='4'>Hintergrundbild<br><div id='<?echo $winId;?>-fd20' data-type='1000' data-root='28' data-value='<?echo $fd[20];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%;'>&nbsp;</div></td>";
					n+="</tr>";
	
	
					n+="<tr valign='top'>";
						n+="<td colspan='4' class='formSubTitel'>Text<hr></td>";
						n+="<td colspan='4' class='formSubTitel'>Textschatten <span style='color:#a0a0a0;'>(vollständig angeben)</span><hr></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='4'>Schriftart<br><div id='<?echo $winId;?>-fd23' data-type='1000' data-root='150' data-value='<?echo $fd[23];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%;'>&nbsp;</div></td>";
	
						n+="<td colspan='4'>Farbe<br><div id='<?echo $winId;?>-fd32' data-type='1000' data-root='26' data-value='<?echo $fd[32];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Schriftgröße (px)<br><input type='text' id='<?echo $winId;?>-fd24' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td>Stil<br><div id='<?echo $winId;?>-fd26' data-type='6' data-value='<?echo $fd[26];?>' data-list='|-;1|normal;2|kursiv;' class='control6' style='min-width:0; width:100%;'>&nbsp;</div></td>";
						n+="<td>Stärke<br><div id='<?echo $winId;?>-fd27' data-type='6' data-value='<?echo $fd[27];?>' data-list='|-;1|normal;2|fett;' class='control6' style='min-width:0; width:100%;'>&nbsp;</div></td>";
	
						n+="<td colspan='2'>X-Abstand (px)<br><input type='text' id='<?echo $winId;?>-fd29' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Y-Abstand (px)<br><input type='text' id='<?echo $winId;?>-fd30' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Innenabstand (px)<br><input type='text' id='<?echo $winId;?>-fd22' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Ausrichtung<br><div id='<?echo $winId;?>-fd28' data-type='6' data-value='<?echo $fd[28];?>' data-list='|-;1|links;2|zentriert;3|rechts;4|Blocksatz;' class='control6' style='min-width:0; width:100%;'>&nbsp;</div></td>";
	
						n+="<td colspan='4'>Unschärfe (px)<br><input type='text' id='<?echo $winId;?>-fd31' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
					n+="</tr>";
	
	
					n+="<tr valign='top'>";
						n+="<td colspan='4' class='formSubTitel'>Schatten <span style='color:#a0a0a0;'>(vollständig angeben)</span><hr></td>";
						n+="<td colspan='4' class='formSubTitel'>Rahmen<hr></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='3'>Farbe<br><div id='<?echo $winId;?>-fd47' data-type='1000' data-root='26' data-value='<?echo $fd[47];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
						n+="<td>Schattenwurf<br><div id='<?echo $winId;?>-fd48' data-type='6' data-value='<?echo $fd[48];?>' data-list='|-;1|aussen;2|innen;' class='control6' style='min-width:0; width:100%;'>&nbsp;</div></td>";
	
						n+="<td colspan='2'><div class='labelBorder' style='border-left-color:#000000;'></div> : Farbe<br><div id='<?echo $winId;?>-fd37' data-type='1000' data-root='26' data-value='<?echo $fd[37];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
						n+="<td colspan='2'><div class='labelBorder' style='border-right-color:#000000;'></div> : Farbe<br><div id='<?echo $winId;?>-fd39' data-type='1000' data-root='26' data-value='<?echo $fd[39];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>X-Abstand (px)<br><input type='text' id='<?echo $winId;?>-fd43' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Y-Abstand (px)<br><input type='text' id='<?echo $winId;?>-fd44' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='2'><div class='labelBorder' style='border-top-color:#000000;'></div> : Farbe<br><div id='<?echo $winId;?>-fd38' data-type='1000' data-root='26' data-value='<?echo $fd[38];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
						n+="<td colspan='2'><div class='labelBorder' style='border-bottom-color:#000000;'></div> : Farbe<br><div id='<?echo $winId;?>-fd40' data-type='1000' data-root='26' data-value='<?echo $fd[40];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Unschärfe (px)<br><input type='text' id='<?echo $winId;?>-fd45' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Überstand (px)<br><input type='text' id='<?echo $winId;?>-fd46' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='2'>Breite (px)<br><input type='text' id='<?echo $winId;?>-fd41' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Muster<br><div id='<?echo $winId;?>-fd42' data-type='6' data-value='<?echo $fd[42];?>' data-list='|-;1|Linie;2|Punkte;3|Striche;' class='control6' style='min-width:0; width:100%;'>&nbsp;</div></td>";
					n+="</tr>";
	
	
					n+="<tr valign='top'>";
						n+="<td colspan='4' class='formSubTitel'>Animation<hr></td>";
						n+="<td colspan='4' class='formSubTitel'>Eckenradius<hr></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='4'>Animation<br><div id='<?echo $winId;?>-fd49' data-type='1000' data-root='27' data-value='<?echo $fd[49];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%;'>&nbsp;</div></td>";
	
						n+="<td colspan='2'><div class='labelBorder' style='border-top-left-radius:50%; border-top-color:#000000; border-left-color:#000000;'></div> : Radius (px)<br><input type='text' id='<?echo $winId;?>-fd33' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'><div class='labelBorder' style='border-top-right-radius:50%; border-top-color:#000000; border-right-color:#000000;'></div> : Radius (px)<br><input type='text' id='<?echo $winId;?>-fd34' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Dauer (s)<br><input type='text' id='<?echo $winId;?>-fd50' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'>Anzahl (0=&infin;)<br><input type='text' id='<?echo $winId;?>-fd51' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
	
						n+="<td colspan='2'><div class='labelBorder' style='border-bottom-left-radius:50%; border-bottom-color:#000000; border-left-color:#000000;'></div> : Radius (px)<br><input type='text' id='<?echo $winId;?>-fd36' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
						n+="<td colspan='2'><div class='labelBorder' style='border-bottom-right-radius:50%; border-bottom-color:#000000; border-right-color:#000000;'></div> : Radius (px)<br><input type='text' id='<?echo $winId;?>-fd35' data-type='1' value='' class='control1' style='min-width:0; width:100%; background:#e0ffe0;'></input></td>";
					n+="</tr>";
	
	
					n+="<tr valign='top'>";
						n+="<td colspan='4' class='formSubTitel'>Zusatzeigenschaften<hr></td>";
						n+="<td colspan='4' class='formSubTitel'>Eigene CSS-Eigenschaften<hr></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='2'>Zusatzvordergrundfarbe 1<br><div id='<?echo $winId;?>-fd52' data-type='1000' data-root='26' data-value='<?echo $fd[52];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
						n+="<td colspan='2'>Zusatzvordergrundfarbe 2<br><div id='<?echo $winId;?>-fd53' data-type='1000' data-root='26' data-value='<?echo $fd[53];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
	
						n+="<td colspan='4' rowspan='3'><textarea id='<?echo $winId;?>-fd58' data-type='1' maxlength='10000' rows='5' wrap='soft' class='control1' onkeydown='if (event.keyCode==9) {appAll_enableTabKey(this);}' style='width:100%; height:114px; resize:none; tab-size:4; background:#e0ffe0;'></textarea></td>";
					n+="</tr>";
	
					n+="<tr>";
						n+="<td colspan='2'>Zusatzhintergrundfarbe 1<br><div id='<?echo $winId;?>-fd54' data-type='1000' data-root='25' data-value='<?echo $fd[54];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
						n+="<td colspan='2'>Zusatzhintergrundfarbe 2<br><div id='<?echo $winId;?>-fd55' data-type='1000' data-root='25' data-value='<?echo $fd[55];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%; background:#e0ffe0;'>&nbsp;</div></td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td colspan='2'>Zusatzbild 1<br><div id='<?echo $winId;?>-fd56' data-type='1000' data-root='28' data-value='<?echo $fd[56];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%;'>&nbsp;</div></td>";
						n+="<td colspan='2'>Zusatzbild 2<br><div id='<?echo $winId;?>-fd57' data-type='1000' data-root='28' data-value='<?echo $fd[57];?>' data-options='typ=1' class='control10' style='min-width:0; width:100%;'>&nbsp;</div></td>";
					n+="</tr>";
	
				n+="</table>";
			n+="</div>";

		n+="</div>";

		document.getElementById("<?echo $winId;?>-main").innerHTML=n;

		document.getElementById("<?echo $winId;?>-fd11").value='<?ajaxValue($fd[11]);?>';
		document.getElementById("<?echo $winId;?>-fd12").value='<?ajaxValue($fd[12]);?>';
		document.getElementById("<?echo $winId;?>-fd13").value='<?ajaxValue($fd[13]);?>';
		document.getElementById("<?echo $winId;?>-fd14").value='<?ajaxValue($fd[14]);?>';
		document.getElementById("<?echo $winId;?>-fd15").value='<?ajaxValue($fd[15]);?>';
		document.getElementById("<?echo $winId;?>-fd16").value='<?ajaxValue($fd[16]);?>';
		document.getElementById("<?echo $winId;?>-fd17").value='<?ajaxValue($fd[17]);?>';
		document.getElementById("<?echo $winId;?>-fd18").value='<?ajaxValue($fd[18]);?>';
		document.getElementById("<?echo $winId;?>-fd21").value='<?ajaxValue($fd[21]);?>';
		document.getElementById("<?echo $winId;?>-fd22").value='<?ajaxValue($fd[22]);?>';
		document.getElementById("<?echo $winId;?>-fd24").value='<?ajaxValue($fd[24]);?>';
		document.getElementById("<?echo $winId;?>-fd29").value='<?ajaxValue($fd[29]);?>';
		document.getElementById("<?echo $winId;?>-fd30").value='<?ajaxValue($fd[30]);?>';
		document.getElementById("<?echo $winId;?>-fd31").value='<?ajaxValue($fd[31]);?>';
		document.getElementById("<?echo $winId;?>-fd33").value='<?ajaxValue($fd[33]);?>';
		document.getElementById("<?echo $winId;?>-fd34").value='<?ajaxValue($fd[34]);?>';
		document.getElementById("<?echo $winId;?>-fd35").value='<?ajaxValue($fd[35]);?>';
		document.getElementById("<?echo $winId;?>-fd36").value='<?ajaxValue($fd[36]);?>';
		document.getElementById("<?echo $winId;?>-fd41").value='<?ajaxValue($fd[41]);?>';
		document.getElementById("<?echo $winId;?>-fd43").value='<?ajaxValue($fd[43]);?>';
		document.getElementById("<?echo $winId;?>-fd44").value='<?ajaxValue($fd[44]);?>';
		document.getElementById("<?echo $winId;?>-fd45").value='<?ajaxValue($fd[45]);?>';
		document.getElementById("<?echo $winId;?>-fd46").value='<?ajaxValue($fd[46]);?>';
		document.getElementById("<?echo $winId;?>-fd50").value='<?ajaxValue($fd[50]);?>';
		document.getElementById("<?echo $winId;?>-fd51").value='<?ajaxValue($fd[51]);?>';
		document.getElementById("<?echo $winId;?>-fd58").value='<?ajaxValue($fd[58]);?>';

		controlInitAll("<?echo $winId;?>-form1");
<? } if ($cmd=='saveItem') { if (strpos($dataArr[3],'-editSheet')===false) { $dbId=db_itemSave('editVisuElementDesign',$phpdataArr,((strpos($dataArr[3],'-dynDesigns')===false)?false:true)); } else { $dbId=db_itemSave('editVisuElementDesignDef',$phpdataArr,1); } if ($dbId>0) { ?>
			controlReturn("<?echo $winId;?>","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } else { ?>
			shakeObj("<?echo $winId;?>");
<? } } if ($cmd=='copyDesign') { $ss1=sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=".$phpdataArr[0]." AND styletyp=0)"); if ($n=sql_result($ss1)) { if ($n['defid']>0) { $ss1=sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=".$n['defid'].")"); if ($nn=sql_result($ss1)) { $n=$nn; } } for ($t=1;$t<=48;$t++) { ?>
				app1003_setDefaultStyle("<?echo $winId;?>-fd<?echo ($t+10);?>","<?ajaxValue($n['s'.$t]);?>");
<? } ?>
			controlInitAll("<?echo $winId;?>-form1");
<? } } if ($cmd=='deleteItem') { db_itemDelete('editVisuElementDesign',$phpdataArr[0]); ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } if ($cmd=='duplicateItem') { db_itemDuplicate('editVisuElementDesign',$phpdataArr[0]); ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } } sql_disconnect(); ?>

