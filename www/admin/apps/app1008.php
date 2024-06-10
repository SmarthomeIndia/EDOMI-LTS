<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); require(MAIN_PATH."/www/admin/include/php/incl_items.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; global $global_charttyp; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowDrag' id='<?echo $winId;?>-global'>";
			n+="<div class='appTitelDrag' onMouseDown='dragWindowStart(\"<?echo $appId;?>\",\"<?echo $winId;?>-global\");'>Datenquelle (Diagramm)<div class='cmdClose' onClick='closeWindow(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main' style='width:700px;'></div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		dragWindowRestore("<?echo $appId;?>","<?echo $winId;?>-global");
<? cmd('start'); } if ($cmd=='start') { $ss1=sql_call("SELECT * FROM edomiProject.editChartList WHERE (id=".$dataArr[4].") ORDER BY id ASC"); if ($n=sql_result($ss1)) { $fd[1]=$n['id']; $fd[2]=$n['targetid']; $fd[3]=$n['archivkoid']; $fd[4]=$n['titel']; $fd[5]=$n['charttyp']; $fd[6]=$n['ymin']; $fd[7]=$n['ymax']; $fd[8]=$n['ystyle']; $fd[9]=$n['s1']; $fd[10]=$n['s2']; $fd[11]=$n['s3']; $fd[12]=$n['s4']; $fd[13]=$n['ygrid1']; $fd[14]=$n['ygrid2']; $fd[15]=$n['ygrid3']; $fd[16]=$n['yshow']; $fd[17]=$n['ynice']; $fd[18]=$n['yticks']; $fd[19]=$n['charttyp2']; $fd[20]=$n['ss1']; $fd[21]=$n['ss2']; $fd[22]=$n['ss3']; $fd[23]=$n['ss4']; $fd[24]=$n['xinterval']; $fd[25]=$n['yminmax']; $fd[26]=$n['extend1']; $fd[27]=$n['extend2']; $fd[28]=$n['yshowvalue']; $fd[29]=$n['yscale']; $fd[30]=$n['sort']; } else { $fd[1]=-1; $fd[2]=$dataArr[2]; $fd[3]=0; $fd[4]=''; $fd[5]=1; $fd[6]=''; $fd[7]=''; $fd[8]=1; $fd[9]=0; $fd[10]=100; $fd[11]=1; $fd[12]=0; $fd[13]=1; $fd[14]=20; $fd[15]=0; $fd[16]=0; $fd[17]=1; $fd[18]=0; $fd[19]=0; $fd[20]=0; $fd[21]=100; $fd[22]=1; $fd[23]=0; $fd[24]=0; $fd[25]=1; $fd[26]=0; $fd[27]=0; $fd[28]=0; $fd[29]=0; $fd[30]=0; } $typ1=''; $typ2=''; foreach ($global_charttyp as $i => $v) { if ($i>0) {$typ1.=$i.'|'.$v.';';} $typ2.=$i.'|'.$v.';'; } ?>
		var n="<div class='appMenu'>";
			n+="<div class='cmdButton cmdButtonL' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
			n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"saveItem\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));'><b>Übernehmen</b></div>";
		n+="</div>";
		n+="<div class='appContent' id='<?echo $winId;?>-form1' style='padding:4px;'>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd1' data-type='1' value='<?echo $fd[1];?>' class='control1'></input>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd2' data-type='1' value='<?echo $fd[2];?>' class='control1'></input>";
			n+="<input type='hidden' id='<?echo $winId;?>-fd30' data-type='1' value='<?echo $fd[30];?>' class='control1'></input>";
			n+="<table width='100%' border='0' cellpadding='5' cellspacing='0'>";
				n+="<tr><td colspan='4'>Datenarchiv<br><div id='<?echo $winId;?>-fd3' data-type='1000' data-root='50' data-value='<?echo $fd[3];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div></td></tr>";
				n+="<tr>";
					n+="<td colspan='2' class='formSubTitel'>Graph 1<hr></td>";
					n+="<td colspan='2' class='formSubTitel'>Graph 2<hr></td>";
				n+="</tr>";
				n+="<tr>";
					n+="<td>Typ<br><div id='<?echo $winId;?>-fd5' data-type='6' data-value='<?echo $fd[5];?>' data-list='<?echo $typ1;?>' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Datenpunkte im Mittelwert-Intervall<br><div id='<?echo $winId;?>-fd25' data-type='6' data-value='<?echo $fd[25];?>' data-list='0|Mittelwert;1|Mittelwert und MIN/MAX;2|Datenpunkte MIN und MAX;3|Datenpunkte MAX und MIN;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Typ<br><div id='<?echo $winId;?>-fd19' data-type='6' data-value='<?echo $fd[19];?>' data-list='<?echo $typ2;?>' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>&nbsp;</td>";
				n+="</tr>";
				n+="<tr valign='top'>";
					n+="<td>Farbe<br><div id='<?echo $winId;?>-fd9' data-type='1000' data-root='26' data-value='<?echo $fd[9];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Opazität<br><div id='<?echo $winId;?>-fd10' data-type='6' data-value='<?echo $fd[10];?>' data-list='100|100%;90|90%;80|80%;70|70%;60|60%;50|50%;40|40%;30|30%;20|20%;10|10%;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Farbe<br><div id='<?echo $winId;?>-fd20' data-type='1000' data-root='26' data-value='<?echo $fd[20];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Opazität<br><div id='<?echo $winId;?>-fd21' data-type='6' data-value='<?echo $fd[21];?>' data-list='100|100%;90|90%;80|80%;70|70%;60|60%;50|50%;40|40%;30|30%;20|20%;10|10%;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";
				n+="<tr valign='top'>";
					n+="<td>Linienstärke/Größe (px)<br><input type='text' id='<?echo $winId;?>-fd11' data-type='1' value='' class='control1' style='width:100%;'></input></td>";
					n+="<td>Schattenstärke<br><div id='<?echo $winId;?>-fd12' data-type='6' data-value='<?echo $fd[12];?>' data-list='0|kein Schatten;1|1 px;2|2 px;3|3 px;4|4 px;5|5 px;6|6 px;7|7 px;8|8 px;9|9 px;10|10 px;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Linienstärke/Größe (px)<br><input type='text' id='<?echo $winId;?>-fd22' data-type='1' value='' class='control1' style='width:100%;'></input></td>";
					n+="<td>Schattenstärke<br><div id='<?echo $winId;?>-fd23' data-type='6' data-value='<?echo $fd[23];?>' data-list='0|kein Schatten;1|1 px;2|2 px;3|3 px;4|4 px;5|5 px;6|6 px;7|7 px;8|8 px;9|9 px;10|10 px;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";

				n+="<tr><td colspan='4' class='formSubTitel'>Darstellungsoptionen<hr></td></tr>";
				n+="<tr>";
					n+="<td>Grenzwert (links)<br><div id='<?echo $winId;?>-fd26' data-type='6' data-value='<?echo $fd[26];?>' data-list='0|ohne;1|visuell verlängern;2|Vorläuferwert einbeziehen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td colspan='2'>Mittelwert-Interval (Sekunden, 0=automatisch, [leer]=deaktiviert)<br><input type='text' id='<?echo $winId;?>-fd24' data-type='1' value='<?echo $fd[24];?>' class='control1' style='width:100%;'></input></td>";
					n+="<td>Grenzwert (rechts)<br><div id='<?echo $winId;?>-fd27' data-type='6' data-value='<?echo $fd[27];?>' data-list='0|ohne;1|visuell verlängern;2|Nachfolgerwert einbeziehen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";

				n+="<tr><td colspan='4' class='formSubTitel'>Y-Achse<hr></td></tr>";
				n+="<tr>";
					n+="<td>Darstellung<br><div id='<?echo $winId;?>-fd8' data-type='6' data-value='<?echo $fd[8];?>' data-list='0|Legende: Individuell|<?echo $winId;?>-radio1;1|Legende: Archivname;2|Y-Achse nicht anzeigen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>";
						n+="<div id='<?echo $winId;?>-radio1'>";
							n+="Legende<br><input type='text' id='<?echo $winId;?>-fd4' data-type='1' value='' class='control1' style='width:100%;'></input>";
						n+="</div>";	
					n+="</td>";
					n+="<td>Anzeige erzwingen<br><div id='<?echo $winId;?>-fd16' data-type='6' data-value='<?echo $fd[16];?>' data-list='0|nein;1|Y-Achse immer anzeigen (sofern aktiviert);' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Aktuellsten Wert anzeigen<br><div id='<?echo $winId;?>-fd28' data-type='6' data-value='<?echo $fd[28];?>' data-list='0|nein;1|als Pfeil anzeigen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";

				n+="<tr valign='top'>";
					n+="<td colspan='4'>Skalierung<br><div id='<?echo $winId;?>-fd29' data-type='6' data-value='<?echo $fd[29];?>' data-list='0|individuell|<?echo $winId;?>-radiotr1;1|Diagramm-Einstellungen (Y-Achsen) verwenden;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";
				
				n+="<tr valign='top' id='<?echo $winId;?>-radiotr1'>";
					n+="<td>Min-Wert (leer=automatisch)<br><input type='text' id='<?echo $winId;?>-fd6' data-type='1' value='' class='control1' style='width:100%;'></input></td>";
					n+="<td>Max-Wert (leer=automatisch)<br><input type='text' id='<?echo $winId;?>-fd7' data-type='1' value='' class='control1' style='width:100%;'></input></td>";
					n+="<td>Gesamtintervall optimieren<br><div id='<?echo $winId;?>-fd17' data-type='6' data-value='<?echo $fd[17];?>' data-list='0|nein;1|ja (Algorithmus 1);' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Y-Intervalle (0=automatisch)<br><input type='text' id='<?echo $winId;?>-fd18' data-type='1' value='<?echo $fd[18];?>' class='control1' style='width:100%;'></input></td>";
				n+="</tr>";

				n+="<tr><td colspan='4' class='formSubTitel'>Horizontale Gitterlinien<hr></td></tr>";
				n+="<tr valign='top'>";
					n+="<td>Darstellung<br><div id='<?echo $winId;?>-fd13' data-type='6' data-value='<?echo $fd[13];?>' data-list='0|nicht anzeigen;1|in Diagrammfarbe anzeigen;2|in Graph-1-Farbe anzeigen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td>Opazität<br><div id='<?echo $winId;?>-fd14' data-type='6' data-value='<?echo $fd[14];?>' data-list='100|100%;90|90%;80|80%;70|70%;60|60%;50|50%;40|40%;30|30%;20|20%;10|10%;' class='control6' style='width:100%;'>&nbsp;</div></td>";
					n+="<td colspan='2'>Überlagernde Gitterlinien (bei mehreren Graphen)<br><div id='<?echo $winId;?>-fd15' data-type='6' data-value='<?echo $fd[15];?>' data-list='0|nur einmal anzeigen;1|für jeden Graph anzeigen;' class='control6' style='width:100%;'>&nbsp;</div></td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;

		document.getElementById("<?echo $winId;?>-fd4").value='<?ajaxValue($fd[4]);?>';
		document.getElementById("<?echo $winId;?>-fd6").value='<?ajaxValue($fd[6]);?>';
		document.getElementById("<?echo $winId;?>-fd7").value='<?ajaxValue($fd[7]);?>';
		document.getElementById("<?echo $winId;?>-fd11").value='<?ajaxValue($fd[11]);?>';
		document.getElementById("<?echo $winId;?>-fd22").value='<?ajaxValue($fd[22]);?>';

		controlInitAll("<?echo $winId;?>-form1");
<? } if ($cmd=='saveItem') { $dbId=db_itemSave('editChartList',$phpdataArr); if ($dbId>0) { ?>
			controlReturn("<?echo $winId;?>","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } else { ?>
			shakeObj("<?echo $winId;?>");
<? } } if ($cmd=='deleteItem') { db_itemDelete('editChartList',$phpdataArr[0]); ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } if ($cmd=='sortDecItem' || $cmd=='sortIncItem') { $ss1=sql_call("SELECT id,sort FROM edomiProject.editChartList WHERE (id=".$phpdataArr[0].")"); if ($item=sql_result($ss1)) { if ($cmd=='sortDecItem') {$ss2=sql_call("SELECT id,sort FROM edomiProject.editChartList WHERE (targetid=".$dataArr[2]." AND sort<".$item['sort'].") ORDER BY sort DESC LIMIT 0,1");} if ($cmd=='sortIncItem') {$ss2=sql_call("SELECT id,sort FROM edomiProject.editChartList WHERE (targetid=".$dataArr[2]." AND sort>".$item['sort'].") ORDER BY sort ASC LIMIT 0,1");} if ($n=sql_result($ss2)) { sql_call("UPDATE edomiProject.editChartList SET sort=".$n['sort']." WHERE (id=".$phpdataArr[0].")"); sql_call("UPDATE edomiProject.editChartList SET sort=".$item['sort']." WHERE (id=".$n['id'].")"); } } ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } } sql_disconnect(); ?>