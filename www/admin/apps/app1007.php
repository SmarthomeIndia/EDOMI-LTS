<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); require(MAIN_PATH."/www/admin/include/php/incl_items.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<div class='appWindowDrag' id='<?echo $winId;?>-global'>";
			n+="<div class='appTitelDrag' onMouseDown='dragWindowStart(\"<?echo $appId;?>\",\"<?echo $winId;?>-global\");'><span id='<?echo $winId;?>-title'>Befehle</span><div class='cmdClose' onClick='closeWindow(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main'></div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
		dragWindowRestore("<?echo $appId;?>","<?echo $winId;?>-global");
<? cmd('start'); } if ($cmd=='start') { $ss1=sql_call("SELECT * FROM edomiProject.".$dataArr[5]." WHERE (id=".$dataArr[4].") ORDER BY id ASC"); if ($n=sql_result($ss1)) { $fd[1]=$n['id']; $fd[2]=$n['targetid']; $fd[3]=$n['cmd']; $fd[4]=$n['cmdid1']; $fd[5]=$n['cmdid2']; $fd[6]=$n['cmdoption1']; $fd[7]=$n['cmdoption2']; $fd[8]=$n['cmdvalue1']; $fd[9]=$n['cmdvalue2']; if ($dataArr[5]=='editSequenceCmdList') { $fd[10]=$n['delay']; $fd[11]=$n['sort']; } } else { $fd[1]=-1; $fd[2]=$dataArr[2]; $fd[3]=0; $fd[4]=0; $fd[5]=''; $fd[6]=0; $fd[7]=0; $fd[8]=''; $fd[9]=''; if ($dataArr[5]=='editSequenceCmdList') { $fd[10]=0; $fd[11]=0; } if ($phpdataArr[0]>0) {$fd[3]=$phpdataArr[0];} } ?>
		var n="<div class='appMenu'>";
<? if ($fd[1]<0 && $fd[3]>0) { ?> 
			n+="<div class='cmdButton cmdButtonL' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Zurück</div>";
			n+="<div class='cmdButton cmdButtonM' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
			n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"saveItem\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));'><b>Übernehmen</b></div>";
<? } else if ($fd[1]>0 || $fd[3]>0) { ?> 
			n+="<div class='cmdButton cmdButtonL' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
			n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"saveItem\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));'><b>Übernehmen</b></div>";
<? } else { ?>
			n+="<div class='cmdButton' onClick='closeWindow(\"<?echo $winId;?>\");'>Abbrechen</div>";
<? } ?>
		n+="</div>";
<? if (!($fd[3]>0)) { ?>
			n+="<div class='appContentBlank' style='width:600px; height:auto; padding:7px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0'>";
					n+="<colgroup>";
						n+="<col width='49%'>";
						n+="<col width='2%'>";
						n+="<col width='49%'>";
					n+="</colgroup>";
<? if ($dataArr[5]=='editLogicCmdList') { ?>
					n+="<tr>";
						n+="<td colspan='3' class='formSubTitel' style='padding-top:5px;'>Ausgangsboxen<hr></td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td colspan='3'>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"1\");'>KO: Eingangswert (Ausgangsbox) zuweisen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"13\");'>Datenarchiv: Eingangswert (Ausgangsbox) hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"14\");'>Meldungsarchiv: Eingangswert (Ausgangsbox) hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"18\");'>Visu/Visuaccount: Eingangswert (Ausgangsbox) als Visuseite aufrufen</div>";
						n+="</td>";
					n+="</tr>";
<? } ?>
					n+="<tr>";
						n+="<td class='formSubTitel' style='<?echo (($dataArr[5]=='editLogicCmdList')?'':'padding-top:5px;');?>'>Kommunikationsobjekte<hr></td>";
						n+="<td><div style='width:1px;'></div></td>";
						n+="<td class='formSubTitel' style='<?echo (($dataArr[5]=='editLogicCmdList')?'':'padding-top:5px;');?>'>Visualisierungen<hr></td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"2\");'>KO: Wert zuweisen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"7\");'>KO: Wert addieren</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"3\");'>KO: Wert eines anderen KOs zuweisen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"4\");'>KO: Wechseln zwischen 0 und Wert</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"6\");'>KO: Wechseln zwischen 0 und Wert (mit Status-KO)</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"19\");'>KO: Wechseln zwischen 1 und Wert (mit Status-KO)</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"5\");'>KO: Rasterwert addieren/subtrahieren</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"9\");'>KO: Wertliste vor/zurück</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"8\");'>KO: Abfragen (Read-Request)</div>";
						n+="</td>";
						n+="<td><div style='width:1px;'></div></td>";
						n+="<td>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"21\");'>Visu/Visuaccount: Visuseite/Popup aufrufen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"29\");'>Visu/Visuaccount: Popup schließen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"28\");'>Visu/Visuaccount: Alle Popups schließen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"24\");'>Visu: Ton abspielen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"25\");'>Visuaccount: Ton abspielen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"26\");'>Visu: Sprachausgabe</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"27\");'>Visuaccount: Sprachausgabe</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"23\");'>Visu/Visuaccount: Logout</div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td class='formSubTitel'>Archive<hr></td>";
						n+="<td><div style='width:1px;'></div></td>";
						n+="<td class='formSubTitel'>Sonstiges<hr></td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"40\");'>Datenarchiv: Wert hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"42\");'>Datenarchiv: KO-Wert hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"50\");'>Datenarchiv: Eintrag entfernen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"41\");'>Meldungsarchiv: Meldung hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"51\");'>Meldungsarchiv: Meldung entfernen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"12\");'>Kameraarchiv: Kamerabild hinzufügen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"52\");'>Kameraarchiv: Kamerabild entfernen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"53\");'>Anrufarchiv: Eintrag entfernen</div>";
						n+="</td>";
						n+="<td><div style='width:1px;'></div></td>";
						n+="<td>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"10\");'>Szene: Abrufen/lernen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"11\");'>Sequenz: Abrufen/stoppen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"17\");'>Makro: Ausführen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"15\");'>HTTP/UDP/SHELL: Ausführen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"16\");'>IR-Befehl: Senden</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"20\");'>Email: Senden</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"22\");'>Telefonbucheintrag: Anrufen/auflegen</div>";
							n+="<div class='controlListItem' onClick='ajax(\"start\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"30\");'>EDOMI: Steuerung</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById('<?echo $winId;?>-title').innerHTML="Befehle";
<? } else { ?>
			n+="<div class='appContent' id='<?echo $winId;?>-form1' style='width:450px;'>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd1' data-type='1' value='<?echo $fd[1];?>'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd2' data-type='1' value='<?echo $fd[2];?>'></input>";
				n+="<input type='hidden' id='<?echo $winId;?>-fd3' data-type='1' value='<?echo $fd[3];?>'></input>";
<? if ($fd[3]==1) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Eingangswert (Ausgangsbox) zuweisen";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==2) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wert zuweisen";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==3) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wert eines anderen KOs zuweisen";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="KO, dessen Wert übernommen werden soll<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='30' data-value='<?echo $fd[5];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==4) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wechseln zwischen 0 und Wert";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==5) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Rasterwert addieren/subtrahieren";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Rasterwert<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='1|addieren;-1|subtrahieren;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==6) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wechseln zwischen 0 und Wert (mit Status-KO)";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input><br><br>";
					n+="Status-KO<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='30' data-value='<?echo $fd[5];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==19) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wechseln zwischen 1 und Wert (mit Status-KO)";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input><br><br>";
					n+="Status-KO<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='30' data-value='<?echo $fd[5];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==7) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wert addieren";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==8) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Abfragen (Read-Request)";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==9) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="KO: Wertliste vor/zurück";
					n+="KO<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='30' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Schrittweite<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==10) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Szene: Abrufen/lernen";
					n+="Szene<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='40' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Aktion<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|abrufen;1|lernen;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==11) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Sequenz: Abrufen/stoppen";
					n+="Sequenz<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='90' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Aktion<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|abrufen;1|stoppen;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==12) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Kameraarchiv: Kamerabild hinzufügen";
					n+="Kameraarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='82' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==13) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Datenarchiv: Eingangswert (Ausgangsbox) hinzufügen";
					n+="Datenarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='50' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Zeitstempel<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|aktueller Zeitstempel;1|Vortag, 00:00:00 Uhr;2|Vortag, 12:00:00 Uhr;3|Vortag, 23:59:59 Uhr;11|aktueller Tag, 00:00:00 Uhr;12|aktueller Tag, 12:00:00 Uhr;13|aktueller Tag, 23:59:59 Uhr;21|Folgetag, 00:00:00 Uhr;22|Folgetag, 12:00:00 Uhr;23|Folgetag, 23:59:59 Uhr;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==14) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Meldungsarchiv: Eingangswert (Ausgangsbox) hinzufügen";
					n+="Meldungsarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='60' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Formatierung<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='155' data-value='<?echo $fd[5];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Zeitstempel<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|aktueller Zeitstempel;1|Vortag, 00:00:00 Uhr;2|Vortag, 12:00:00 Uhr;3|Vortag, 23:59:59 Uhr;11|aktueller Tag, 00:00:00 Uhr;12|aktueller Tag, 12:00:00 Uhr;13|aktueller Tag, 23:59:59 Uhr;21|Folgetag, 00:00:00 Uhr;22|Folgetag, 12:00:00 Uhr;23|Folgetag, 23:59:59 Uhr;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==15) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="HTTP/UDP/SHELL: Ausführen";
					n+="HTTP/UDP/SHELL<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='70' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==16) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="IR-Befehl: Senden";
					n+="IR-Befehl<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='75' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Kanal (IR-LED)<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='1|Kanal 1;2|Kanal 2;3|Kanal 1 und 2;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==17) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Makro: Ausführen";
					n+="Makro<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='95' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==18) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu/Visuaccount: Eingangswert (Ausgangsbox) als Visuseite aufrufen";
					n+="Visu<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='21' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Account (leer=alle)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='23' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==20) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Email: Senden";
					n+="Email<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='120' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==21) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu/Visuaccount: Visuseite/Popup aufrufen";
					n+="Visu-Seite<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='22' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Account (leer=alle)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='23' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==22) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Telefonbucheintrag: Anrufen/auflegen";
					n+="Telefonbucheintrag (leer=bestehende Verbindung beenden)<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='125' data-value='<?echo $fd[4];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Anrufdauer<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|unendlich;3|3 Sekunden;5|5 Sekunden;10|10 Sekunden;15|15 Sekunden;20|20 Sekunden;30|30 Sekunden;60|1 Minute;' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==23) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu/Visuaccount: Logout";
					n+="Visu (leer=alle)<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='21' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Account (leer=alle)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='23' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==24) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu: Ton abspielen";
					n+="Visu<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='21' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Ton (leer=stoppen)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='29' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==25) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visuaccount: Ton abspielen";
					n+="Account<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='23' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Ton (leer=stoppen)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='29' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==26) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu: Sprachausgabe";
					n+="Visu<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='21' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Text<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==27) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visuaccount: Sprachausgabe";
					n+="Account<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='23' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Text<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input>";
<? } if ($fd[3]==28) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu/Visuaccount: Alle Popups schließen";
					n+="Visu<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='21' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Account (leer=alle)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='23' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==29) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Visu/Visuaccount: Popup schließen";
					n+="Popup<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='22' data-value='<?echo $fd[4];?>' class='control10' data-options='typ=1;reset=0' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Account (leer=alle)<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='23' data-value='<?echo $fd[5];?>' class='control10' data-options='typ=1' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==30) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="EDOMI: Steuerung";
					n+="Aktion<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='1|EDOMI neustarten;4|EDOMI pausieren(!);2|Server neustarten;3|Server herunterfahren(!);9|Autobackup erstellen;' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==40) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Datenarchiv: Wert hinzufügen";
					n+="Datenarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='50' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Wert<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input><br><br>";
					n+="Zeitstempel<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|aktueller Zeitstempel;1|Vortag, 00:00:00 Uhr;2|Vortag, 12:00:00 Uhr;3|Vortag, 23:59:59 Uhr;11|aktueller Tag, 00:00:00 Uhr;12|aktueller Tag, 12:00:00 Uhr;13|aktueller Tag, 23:59:59 Uhr;21|Folgetag, 00:00:00 Uhr;22|Folgetag, 12:00:00 Uhr;23|Folgetag, 23:59:59 Uhr;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==41) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Meldungsarchiv: Meldung hinzufügen";
					n+="Meldungsarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='60' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Meldung<br><input type='text' id='<?echo $winId;?>-fd8' data-type='1' value='' class='control1' style='width:100%;'></input><br><br>";
					n+="Formatierung<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='155' data-value='<?echo $fd[5];?>' data-options='typ=1' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Zeitstempel<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|aktueller Zeitstempel;1|Vortag, 00:00:00 Uhr;2|Vortag, 12:00:00 Uhr;3|Vortag, 23:59:59 Uhr;11|aktueller Tag, 00:00:00 Uhr;12|aktueller Tag, 12:00:00 Uhr;13|aktueller Tag, 23:59:59 Uhr;21|Folgetag, 00:00:00 Uhr;22|Folgetag, 12:00:00 Uhr;23|Folgetag, 23:59:59 Uhr;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==42) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Datenarchiv: KO-Wert hinzufügen";
					n+="Datenarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='50' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="KO<br><div id='<?echo $winId;?>-fd5' data-type='1000' data-root='30' data-value='<?echo $fd[5];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Zeitstempel<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|aktueller Zeitstempel;1|Vortag, 00:00:00 Uhr;2|Vortag, 12:00:00 Uhr;3|Vortag, 23:59:59 Uhr;11|aktueller Tag, 00:00:00 Uhr;12|aktueller Tag, 12:00:00 Uhr;13|aktueller Tag, 23:59:59 Uhr;21|Folgetag, 00:00:00 Uhr;22|Folgetag, 12:00:00 Uhr;23|Folgetag, 23:59:59 Uhr;' class='control6' style='min-width:120px;'>&nbsp;</div>";
<? } if ($fd[3]==50) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Datenarchiv: Eintrag entfernen";
					n+="Datenarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='50' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Modus<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|neusten Eintrag entfernen;1|ältesten Eintrag entfernen;2|alle Einträge entfernen(!);' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==51) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Meldungsarchiv: Eintrag entfernen";
					n+="Meldungsarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='60' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Modus<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|neuste Meldung entfernen;1|älteste Meldung entfernen;2|alle Meldungen entfernen(!);' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==52) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Kameraarchiv: Eintrag entfernen";
					n+="Kameraarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='82' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Modus<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|neustes Kamerabild entfernen;1|ältestes Kamerabild entfernen;2|alle Kamerabilder entfernen(!);' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($fd[3]==53) { ?>
					document.getElementById('<?echo $winId;?>-title').innerHTML="Anrufarchiv: Eintrag entfernen";
					n+="Anrufarchiv<br><div id='<?echo $winId;?>-fd4' data-type='1000' data-root='127' data-value='<?echo $fd[4];?>' data-options='typ=1;reset=0' class='control10' style='width:100%;'>&nbsp;</div><br><br>";
					n+="Modus<br><div id='<?echo $winId;?>-fd6' data-type='6' data-value='<?echo $fd[6];?>' data-list='0|neusten Eintrag entfernen;1|ältesten Eintrag entfernen;2|alle Einträge entfernen(!);' class='control6' style='width:100%;'>&nbsp;</div>";
<? } if ($dataArr[5]=='editSequenceCmdList') { ?>
					n+="<br><br>Wartezeit nach diesem Befehl (Sekunden, 0=nicht warten)<br><input type='text' id='<?echo $winId;?>-fd10' data-type='1' value='' class='control1' style='width:100%;'></input>";
					n+="<input type='hidden' id='<?echo $winId;?>-fd11' data-type='1' value='<?echo $fd[11];?>'></input>";
<? } ?>
			n+="</div>";
<? } ?>
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;

		if (document.getElementById("<?echo $winId;?>-fd8")) {
			document.getElementById("<?echo $winId;?>-fd8").value='<?ajaxValue($fd[8]);?>';
		}
		if (document.getElementById("<?echo $winId;?>-fd9")) {
			document.getElementById("<?echo $winId;?>-fd9").value='<?ajaxValue($fd[9]);?>';
		}
		if (document.getElementById("<?echo $winId;?>-fd10")) {
			document.getElementById("<?echo $winId;?>-fd10").value='<?ajaxValue($fd[10]);?>';
		}

		controlInitAll("<?echo $winId;?>-form1");
<? } if ($cmd=='saveItem') { $dbId=db_itemSave($dataArr[5],$phpdataArr); if ($dbId>0) { ?>
			controlReturn("<?echo $winId;?>","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } else { ?>
			shakeObj("<?echo $winId;?>");
<? } } if ($cmd=='deleteItem') { db_itemDelete($dataArr[5],$phpdataArr[0]); ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } if ($cmd=='sortDecItem' || $cmd=='sortIncItem') { $ss1=sql_call("SELECT id,sort FROM edomiProject.".$dataArr[5]." WHERE (id=".$phpdataArr[0].")"); if ($item=sql_result($ss1)) { if ($cmd=='sortDecItem') {$ss2=sql_call("SELECT id,sort FROM edomiProject.".$dataArr[5]." WHERE (targetid=".$dataArr[2]." AND sort<".$item['sort'].") ORDER BY sort DESC LIMIT 0,1");} if ($cmd=='sortIncItem') {$ss2=sql_call("SELECT id,sort FROM edomiProject.".$dataArr[5]." WHERE (targetid=".$dataArr[2]." AND sort>".$item['sort'].") ORDER BY sort ASC LIMIT 0,1");} if ($n=sql_result($ss2)) { sql_call("UPDATE edomiProject.".$dataArr[5]." SET sort=".$n['sort']." WHERE (id=".$phpdataArr[0].")"); sql_call("UPDATE edomiProject.".$dataArr[5]." SET sort=".$item['sort']." WHERE (id=".$n['id'].")"); } } ?>
		controlReturn("","<?echo $dataArr[0];?>","<?echo $dataArr[2];?>");
<? } } sql_disconnect(); ?>