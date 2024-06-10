<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/shared/php/incl_dbinit.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); require(MAIN_PATH."/www/admin/include/php/incl_items.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { ?>
		var n="<table width='100%' height='100%' border='0'><tr valign='middle'><td align='center' onClick='flashWindow(this);'>";
		n+="<div class='appWindowNormal'>";
			n+="<div class='appTitel'>Verwaltung<div class='cmdClose' onClick='app103_quit(\"<?echo $winId;?>\",\"<?echo $dataArr[2];?>\");'></div><div id='<?echo $winId;?>-help' class='cmdHelp' data-helpid='<?echo $appId;?>' onClick='openWindow(9999,this.dataset.helpid);'></div></div>";
			n+="<div id='<?echo $winId;?>-main' style='width:850px;'>";

				n+="<div class='appMenu'>";
					n+="<div class='cmdButton cmdButtonL' onClick='window.open(\"http://undefined-URL\",\"_blank\");' style='width:80px;'>Homepage</div>";
					n+="<div class='cmdButton cmdButtonR' onClick='openWindow(9999,\"about\");' style='width:79px;'>Spenden</div>";
					n+="<iframe id='<?echo $winId;?>-iframe' name='<?echo $winId;?>-iframe' style='width:1px; height:1px; display:none;'></iframe>";
				n+="</div>";
		
				n+="<div class='appContentBlank'>";
					n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
						n+="<tr>";
							n+="<td width='165' style='padding-right:5px;'><div id='<?echo $winId;?>-menuRoot' class='columnMenu' style='height:550px;'>";
								n+="<div id='<?echo $winId;?>-menu12' class='columnMenuItem' onClick='ajax(\"menu12\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Arbeitsprojekt</div>";
								n+="<div id='<?echo $winId;?>-menu2' class='columnMenuItem' onClick='ajax(\"menu2\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Live-Projekt</div>";
								n+="<div id='<?echo $winId;?>-menu13' class='columnMenuItem' onClick='ajax(\"menu13\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Projektaktivierung</div>";
								n+="<div id='<?echo $winId;?>-menu15' class='columnMenuItem' onClick='ajax(\"menu15\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Visuaktivierung</div>";
	
	//### Import/Export: onClick='ajax(\"menu11\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'		
								n+="<div id='<?echo $winId;?>-menu11' class='columnMenuItem' style='margin-top:15px; color:#a0a0a0;'>Import & Export</div>";
								n+="<div id='<?echo $winId;?>-menu9' class='columnMenuItem' onClick='ajax(\"menu9\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>ETS-Import</div>";
								n+="<div id='<?echo $winId;?>-menu8' class='columnMenuItem' onClick='ajax(\"menu8\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Logikbausteine</div>";
								n+="<div id='<?echo $winId;?>-menu16' class='columnMenuItem' onClick='ajax(\"menu16\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Visuelemente</div>";
			
								n+="<div id='<?echo $winId;?>-menu1' class='columnMenuItem' style='margin-top:15px;' onClick='ajax(\"menu1\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Logdateien</div>";
								n+="<div id='<?echo $winId;?>-menu3' class='columnMenuItem' onClick='ajax(\"menu3\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Accounteinstellungen</div>";
								n+="<div id='<?echo $winId;?>-menu6' class='columnMenuItem' onClick='ajax(\"menu6\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Basis-Konfiguration</div>";
			
								n+="<div id='<?echo $winId;?>-menu14' class='columnMenuItem' style='margin-top:15px;' onClick='ajax(\"menu14\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Datenbanken</div>";
								n+="<div id='<?echo $winId;?>-menu4' class='columnMenuItem' onClick='ajax(\"menu4\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Updates</div>";
								n+="<div id='<?echo $winId;?>-menu5' class='columnMenuItem' onClick='ajax(\"menu5\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Datensicherung</div>";
								n+="<div id='<?echo $winId;?>-menu7' class='columnMenuItem' onClick='ajax(\"menu7\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Zurücksetzen</div>";
			
							n+="</div></td>";
							n+="<td><div id='<?echo $winId;?>-edit' class='columnContent' style='height:550px;'></div></td>";
						n+="</tr>";
					n+="</table>";
				n+="</div>";

			n+="</div>";
		n+="</div>";
		n+="</td></tr></table>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
<? if (!isEmpty($dataArr[0])) { cmd($dataArr[0]); } else { cmd('menu12'); } if ($dataArr[1]=='noMenu') { ?>
			document.getElementById("<?echo $winId;?>-menuRoot").style.pointerEvents="none";
			document.getElementById("<?echo $winId;?>-menuRoot").style.opacity="0.5";
<? } } if ($cmd=='menu1') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Logdateien (<?echo MAIN_PATH;?>/www/data/log)</b></span><br><br>";
						n+="<div class='controlList' style='width:100%; height:auto; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+='<tr style="color:#a0a0a0;">';
									n+='<td width="1">Log</td>';
									n+='<td>Info</td>';
									n+='<td>Format</td>';
									n+='<td>Größe</td>';
									n+='<td>Einträge</td>';
									n+='<td width="1">letzter Eintrag</td>';
								n+='</tr>';
<? $files=glob(MAIN_PATH.'/www/data/log/*.*',GLOB_NOSORT); $logs=Array(); foreach ($files as $pathFn) { if (is_file($pathFn)) { $info=''; $fn=basename($pathFn); $n=explode('_',$fn,2); $logTyp=$n[0]; $logInfo=array_shift(explode('.',$n[1])); if ($logTyp=='SYSLOG') {$info='6;'.date('Y-m-01',strtotime($logInfo)).';'.$fn.';für '.date('m/Y',strtotime($logInfo));} else if ($logTyp=='ERRLOG') {$info='5;'.date('Y-m-01',strtotime($logInfo)).';'.$fn.';für '.date('m/Y',strtotime($logInfo));} else if ($logTyp=='VISULOG') {$info='4;'.date('Y-m-01',strtotime($logInfo)).';'.$fn.';für '.date('m/Y',strtotime($logInfo));} else if ($logTyp=='LOGICLOG') { if ($logInfo=='0') { $info='3;99.99.9999 99:99:99;'.$fn.';'; } else { $tmp=str_replace('_',' ',$logInfo); $tmp=str_replace('x',':',$tmp); $info='3;'.$logInfo.';'.$fn.';'.date('d.m.Y / H:i:s',strtotime($tmp)); } } else if ($logTyp=='MONLOG') {$info='2;'.date('Y-m-d',strtotime($logInfo)).';'.$fn.';für '.date('d.m.Y',strtotime($logInfo));} else if ($logTyp=='CUSTOMLOG') {$info='1;'.$logInfo.';'.$fn.';'.$logInfo;} if (!isEmpty($info)) {array_push($logs,$info);} } } rsort($logs,SORT_STRING); clearstatcache(); for ($t=0;$t<count($logs);$t++) { $n=explode(';',$logs[$t]); $fileLines=getFileLines(MAIN_PATH.'/www/data/log/'.$n[2])-1; $fileDate=date('d.m.Y H:i:s',filemtime(MAIN_PATH.'/www/data/log/'.$n[2])); $fileSuffix=pathinfo($n[2],PATHINFO_EXTENSION); if ($n[0]==6) {$name='<span style="color:#000000;"><b>System-Log</b></span>'; $fileExpire=global_logSysKeep;} if ($n[0]==5) {$name='<span style="color:#ff0000;"><b>Fehler-Log</b></span>'; $fileExpire=global_logErrKeep;} if ($n[0]==4) {$name='<span style="color:#000000;"><b>Visu-Log</b></span>'; $fileExpire=global_logVisuKeep;} if ($n[0]==3) {$name='<span style="color:#e000e0;"><b>Logik-Log</b></span>'; $fileExpire=global_logLogicKeep;} if ($n[0]==2) {$name='<span style="color:#00a000;"><b>Monitor-Log</b></span>'; $fileExpire=global_logMonKeep;} if ($n[0]==1) {$name='<span style="color:#0000ff;"><b>Individual-Log</b></span>'; $fileExpire=global_logCustomKeep;} if ($fileExpire<1) {$fileExpire=1;} $fileExpire=pow((($fileExpire*86400)-(strtotime(date('d.m.Y H:i:s'))-strtotime($fileDate)))/($fileExpire*86400)*100,2)/100; if ($fileExpire<0) {$fileExpire=0;} ?>
								n+='<tr onMouseDown="app103_clickLog(\'<?echo $winId;?>\',\'<?echo $n[2];?>\',\'<?echo date('YmdHis');?>\');" class="controlListItem" style="display:table-row;">';
									n+='<td><?echo $name;?></td>';
									n+='<td style="max-width:300px; overflow-x:hidden;"><?echo $n[3];?></td>';
									n+='<td><?if (strToUpper($fileSuffix)=='HTM') {echo 'HTML';} else {echo 'TEXT';}?></td>';
									n+='<td><?echo round(getFileSize(MAIN_PATH.'/www/data/log/'.$n[2])/1024/1024,2);?> MB</td>';
									n+='<td><?echo $fileLines;?></td>';
									n+='<td><div style="display:inline-block; padding:0px 3px 0px 3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $fileExpire;?>%,transparent <?echo $fileExpire;?>%,transparent 100%);"><?echo str_replace(' ',' / ',$fileDate);?></div></td>';
								n+='</tr>';
<? } ?>
							n+='</table>';
						n+="</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu1_delete') { if (!isEmpty($phpdataArr[0])) { deleteFiles('"'.MAIN_PATH.'/www/data/log/'.$phpdataArr[0].'"'); } cmd('menu1'); } if ($cmd=='menu2') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $livePrj=getLiveProjektData(); if ($livePrj!==false) { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Live-Projekt</b></span><br><br>";
							n+="<div style='width:450px; word-wrap:break-word;'>";
								n+="<div class='controlEditInline' style='background:#ffffff;'><?ajaxEcho($livePrj['name']);?> <span class='id'><?echo $livePrj['id'];?></span></div>";
							n+="</div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo sql_getDateTime($livePrj['livedate']);?></b></td></tr>";
								n+="<tr><td align='right'>&gt;</td><td><span class='link' onClick='ajax(\"menu2\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"menu2_showDetails\",\"\");'><b>Weitere Informationen anzeigen</b></span></td></tr>";
							n+="</table>";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu13\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Projektaktivierung</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Live-Projekt löschen</div>";
						n+="</td>";
					n+="</tr>";
<? if ($dataArr[0]=='menu2_showDetails') { ?>
					n+="<tr>";
						n+="<td width='100%' colspan='2' valign='top'>";
							n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; border:none;'>";
<? app103_projectStatisticsJS(1); ?>
							n+="</div>";
						n+="</td>";
					n+="</tr>";
<? } ?>
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Live-Projekt</b></span><br><br>";
							n+="Es ist kein Live-Projekt verfügbar.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu13\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Projektaktivierung</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu2_download') { if (is_numeric($phpdataArr[1])) { if ($phpdataArr[0]=='edomiLive.archivKoData') { $fn=MAIN_PATH.'/www/data/tmp/Datenarchiv_ID'.$phpdataArr[1].'.csv'; $f=fopen($fn,'w'); $ss1=sql_call("SELECT * FROM ".$phpdataArr[0]." WHERE targetid=".$phpdataArr[1]." ORDER BY datetime ASC,ms ASC"); fwrite($f,'Datum,Uhrzeit,Mikrosekunden,Wert'."\n"); while ($n=sql_result($ss1)) { fwrite($f,sql_getDate($n['datetime']).','.sql_getTime($n['datetime']).','.$n['ms'].',"'.sql_encodeValue($n['gavalue']).'"'."\n"); } fclose($f); ?>
				document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode($fn);?>&sid=<?echo $sid;?>";
<? } else if ($phpdataArr[0]=='edomiLive.archivMsgData') { $fn=MAIN_PATH.'/www/data/tmp/Meldungsarchiv_ID'.$phpdataArr[1].'.csv'; $f=fopen($fn,'w'); $ss1=sql_call("SELECT * FROM ".$phpdataArr[0]." WHERE targetid=".$phpdataArr[1]." ORDER BY datetime ASC,ms ASC"); fwrite($f,'Datum,Uhrzeit,Mikrosekunden,Meldung'."\n"); while ($n=sql_result($ss1)) { fwrite($f,sql_getDate($n['datetime']).','.sql_getTime($n['datetime']).','.$n['ms'].',"'.sql_encodeValue($n['msg']).'"'."\n"); } fclose($f); ?>
				document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode($fn);?>&sid=<?echo $sid;?>";
<? } else if ($phpdataArr[0]=='edomiLive.archivPhoneData') { $fn=MAIN_PATH.'/www/data/tmp/Anrufarchiv_ID'.$phpdataArr[1].'.csv'; $f=fopen($fn,'w'); $ss1=sql_call("SELECT * FROM ".$phpdataArr[0]." WHERE targetid=".$phpdataArr[1]." ORDER BY datetime ASC,ms ASC"); fwrite($f,'Datum,Uhrzeit,Mikrosekunden,Rufnummer,Typ'."\n"); while ($n=sql_result($ss1)) { if ($n['typ']==0) { fwrite($f,sql_getDate($n['datetime']).','.sql_getTime($n['datetime']).','.$n['ms'].',"'.sql_encodeValue($n['phone']).'",eingehend'."\n"); } else { fwrite($f,sql_getDate($n['datetime']).','.sql_getTime($n['datetime']).','.$n['ms'].',"'.sql_encodeValue($n['phone']).'",ausgehend'."\n"); } } fclose($f); ?>
				document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode($fn);?>&sid=<?echo $sid;?>";
<? } else if ($phpdataArr[0]=='edomiLive.archivCamData') { $fn=MAIN_PATH.'/www/data/tmp/Kameraarchiv_ID'.$phpdataArr[1].'.tar'; $fn_csv=MAIN_PATH.'/www/data/tmp/Kameraarchiv_ID'.$phpdataArr[1].'.csv'; $fn_tmp=MAIN_PATH.'/www/data/tmp/Kameraarchiv_ID'.$phpdataArr[1].'.tmp'; $f=fopen($fn_csv,'w'); $f2=fopen($fn_tmp,'w'); $ss1=sql_call("SELECT * FROM ".$phpdataArr[0]." WHERE targetid=".$phpdataArr[1]." ORDER BY datetime ASC,ms ASC"); fwrite($f,'Datum,Uhrzeit,Mikrosekunden,Dateiname'."\n"); while ($n=sql_result($ss1)) { $tmp=getArchivCamFilename($n['targetid'],$n['camid'],$n['datetime'],$n['ms']).'.jpg'; fwrite($f,sql_getDate($n['datetime']).','.sql_getTime($n['datetime']).','.$n['ms'].',"'.sql_encodeValue($tmp).'"'."\n"); fwrite($f2,$tmp."\n"); } fclose($f2); fclose($f); exec('tar -cf "'.MAIN_PATH.'/www/data/tmp/Kameraarchiv_ID'.$phpdataArr[1].'.tar" -C "'.MAIN_PATH.'/www/data/tmp/" "Kameraarchiv_ID'.$phpdataArr[1].'.csv"'); exec('tar -rf "'.MAIN_PATH.'/www/data/tmp/Kameraarchiv_ID'.$phpdataArr[1].'.tar" -C "'.MAIN_PATH.'/www/data/liveproject/cam/archiv/" -T "'.$fn_tmp.'"'); ?>
				document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode(basename($fn));?>&sid=<?echo $sid;?>";
<? } } } if ($cmd=='menu2_delete') { if (is_numeric($phpdataArr[1])) { if ($phpdataArr[0]=='edomiLive.archivKoData') { $tmp=sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1]); ?>
				ajaxConfirmSecure("<b>Soll das Datenarchiv mit der ID <?echo $phpdataArr[1];?> wirklich geleert werden?</b><br><br>Alle <?echo $tmp;?> Einträge werden unwiederbringlich gelöscht!","menu2_delete2","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>"+AJAX_SEPARATOR1+"<?echo $phpdataArr[1];?>","","Archiv leeren");
<? } else if ($phpdataArr[0]=='edomiLive.archivMsgData') { $tmp=sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1]); ?>
				ajaxConfirmSecure("<b>Soll das Meldungsarchiv mit der ID <?echo $phpdataArr[1];?> wirklich geleert werden?</b><br><br>Alle <?echo $tmp;?> Einträge werden unwiederbringlich gelöscht!","menu2_delete2","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>"+AJAX_SEPARATOR1+"<?echo $phpdataArr[1];?>","","Archiv leeren");
<? } else if ($phpdataArr[0]=='edomiLive.archivPhoneData') { $tmp=sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1]); ?>
				ajaxConfirmSecure("<b>Soll das Anrufarchiv mit der ID <?echo $phpdataArr[1];?> wirklich geleert werden?</b><br><br>Alle <?echo $tmp;?> Einträge werden unwiederbringlich gelöscht!","menu2_delete2","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>"+AJAX_SEPARATOR1+"<?echo $phpdataArr[1];?>","","Archiv leeren");
<? } else if ($phpdataArr[0]=='edomiLive.archivCamData') { $tmp=sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1]); ?>
				ajaxConfirmSecure("<b>Soll das Kameraarchiv mit der ID <?echo $phpdataArr[1];?> wirklich geleert werden?</b><br><br>Alle <?echo $tmp;?> Einträge werden unwiederbringlich gelöscht!","menu2_delete2","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>"+AJAX_SEPARATOR1+"<?echo $phpdataArr[1];?>","","Archiv leeren");
<? } } } if ($cmd=='menu2_delete2') { if (is_numeric($phpdataArr[1])) { if ($phpdataArr[0]=='edomiLive.archivKoData') { $ss1=sql_call("SELECT outgaid FROM edomiLive.archivKo WHERE (id=".$phpdataArr[1].")"); if ($n=sql_result($ss1)) { sql_call("DELETE FROM ".$phpdataArr[0]." WHERE (targetid=".$phpdataArr[1].")"); if ($n['outgaid']>0) {sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['outgaid'].",'".sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1])."')");} } sql_close($ss1); } else if ($phpdataArr[0]=='edomiLive.archivMsgData') { $ss1=sql_call("SELECT outgaid FROM edomiLive.archivMsg WHERE (id=".$phpdataArr[1].")"); if ($n=sql_result($ss1)) { sql_call("DELETE FROM ".$phpdataArr[0]." WHERE (targetid=".$phpdataArr[1].")"); if ($n['outgaid']>0) {sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['outgaid'].",'".sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1])."')");} } sql_close($ss1); } else if ($phpdataArr[0]=='edomiLive.archivPhoneData') { $ss1=sql_call("SELECT outgaid FROM edomiLive.archivPhone WHERE (id=".$phpdataArr[1].")"); if ($n=sql_result($ss1)) { sql_call("DELETE FROM ".$phpdataArr[0]." WHERE (targetid=".$phpdataArr[1].")"); if ($n['outgaid']>0) {sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['outgaid'].",'".sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1])."')");} } sql_close($ss1); } else if ($phpdataArr[0]=='edomiLive.archivCamData') { $ss1=sql_call("SELECT outgaid FROM edomiLive.archivCam WHERE (id=".$phpdataArr[1].")"); if ($n=sql_result($ss1)) { sql_call("DELETE FROM ".$phpdataArr[0]." WHERE (targetid=".$phpdataArr[1].")"); if ($n['outgaid']>0) {sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['outgaid'].",'".sql_getCount($phpdataArr[0],'targetid='.$phpdataArr[1])."')");} } sql_close($ss1); } } cmd('menu2'); } if ($cmd=='menu3') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Accounts</b></span><br><br>";
						n+="<div class='controlList' style='width:100%; height:auto; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+='<tr style="color:#a0a0a0;">';
									n+='<td>Typ</td>';
									n+='<td>Login</td>';
									n+='<td>Passwort</td>';
									n+='<td>letzte Aktion</td>';
									n+='<td>Login</td>';
									n+='<td>Logout</td>';
									n+='<td width="1">IP (Login)</td>';
								n+='</tr>';
<? $ss1=sql_call("SELECT * FROM edomiAdmin.user ORDER BY typ ASC,id ASC"); while ($n=sql_result($ss1)) { if ($n['typ']==0) {$n['typ']='Administration';} else if ($n['typ']==1) {$n['typ']='Status';} else if ($n['typ']==10) {$n['typ']='Fernzugriff';} ?>
								n+="<tr onClick='ajax(\"menu3_edit\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $n['id'];?>\");' class='controlListItem' style='display:table-row;'>";
									n+='<td><b><?echo $n['typ'];?></b></td>';
									n+='<td style="max-width:100px; overflow-x:hidden;"><?ajaxEcho($n['login']);?></td>';
									n+='<td style="max-width:100px; overflow-x:hidden;"><?ajaxEcho($n['pass']);?></td>';
									n+='<td><?echo sql_getDateTime($n['actiondate']);?></td>';
									n+='<td><?echo sql_getDateTime($n['logindate']);?></td>';
									n+='<td><?echo sql_getDateTime($n['logoutdate']);?></td>';
									n+='<td><?echo $n['loginip'];?></td>';
								n+='</tr>';
<? } sql_close($ss1); ?>
							n+='</table>';
						n+="</div>";

					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu3_edit') { $ss1=sql_call("SELECT * FROM edomiAdmin.user WHERE id=".$phpdataArr[0]); if ($n=sql_result($ss1)) { if ($n['typ']==0) {$n['typ']='Administration';} else if ($n['typ']==1) {$n['typ']='Status';} else if ($n['typ']==10) {$n['typ']='Fernzugriff';} ?>
			var n="<div id='<?echo $winId;?>-form1' class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td colspan='2' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Account: <?echo $n['typ'];?></b></span><br><br>";
							n+="<input type='hidden' id='<?echo $winId;?>-fd0' data-type='1' value='<?echo $n['id'];?>'></input>";
							n+="Login<br><input type='text' id='<?echo $winId;?>-fd1' data-type='1' value='' maxlength='30' class='control1' style='width:100%;'></input><br><br>";
							n+="Passwort<br><input type='text' id='<?echo $winId;?>-fd2' data-type='1' value='' maxlength='30' class='control1' style='width:100%;'></input><br><br>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>letzte Aktion:</td><td><b><?echo sql_getDateTime($n['actiondate']);?></b></td></tr>";
								n+="<tr><td align='right'>Login:</td><td><b><?echo sql_getDateTime($n['logindate']);?></b></td></tr>";
								n+="<tr><td align='right'>Logout:</td><td><b><?echo sql_getDateTime($n['logoutdate']);?></b></td></tr>";
								n+="<tr><td align='right'>IP (Login):</td><td><b><?echo $n['loginip'];?></b></td></tr>";
							n+="</table>";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton cmdButtonL' onClick='ajax(\"menu3\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Abbrechen</div>";
							n+="<div class='cmdButton cmdButtonR' onClick='ajax(\"menu3_save\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));'><b>Übernehmen</b></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;		
			document.getElementById("<?echo $winId;?>-fd1").value='<?ajaxValue($n['login']);?>';
			document.getElementById("<?echo $winId;?>-fd2").value='<?ajaxValue($n['pass']);?>';
			controlInitAll("<?echo $winId;?>-form1");
<? } sql_close($ss1); } if ($cmd=='menu3_save') { $ok=true; if (isEmpty($phpdataArr[1]) || isEmpty($phpdataArr[2])) { $ok=false; } else { $ss1=sql_call("SELECT * FROM edomiAdmin.user WHERE (id<>".$phpdataArr[0]." AND login='".sql_encodeValue($phpdataArr[1])."' AND pass='".sql_encodeValue($phpdataArr[2])."') LIMIT 0,1"); if (sql_result($ss1)) {$ok=false;} sql_close($ss1); } if ($ok) { sql_call("UPDATE edomiAdmin.user SET login='".sql_encodeValue($phpdataArr[1])."',pass='".sql_encodeValue($phpdataArr[2])."' WHERE (id=".$phpdataArr[0].")"); if ($n=checkAdminSid($sid,true)) { ?>
				desktopInfoAccount("<?ajaxEcho($n['login']);?>","<?ajaxEcho(global_serverIP);?>");
<? } cmd('menu3'); } else { ?>
			shakeObj("<?echo $winId;?>");
<? } } if ($cmd=='menu4') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $autoUpdate=''; deleteFiles(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt'); queueCmd(1,10,0); $timeout=0; while (!file_exists(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt') && $timeout<30) { sleep(1); $timeout++; } if (file_exists(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt')) { $updateInfo=readInfoFile(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt'); $autoUpdate=$updateInfo[0]; } ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Automatisches Update</b></span><br><br>";
<? if ($autoUpdate=='CHECKED') { ?>
						n+="Es ist ein Update auf die <b>Version <?echo $updateInfo[1];?></b> verfügbar. Das Update kann automatisch heruntergeladen und installiert werden.<br><br>";
						n+="Es wird dringend empfohlen, vor der Update-Installation eine Datensicherung vorzunehmen!";
<? } else if ($autoUpdate=='NOUPDATE') { ?>
						n+="Aktuell ist kein automatisches Update für die installierte EDOMI-Version <?echo global_version;?> auf dem Update-Server verfügbar.<br><br>Hinweis: Eventuell ist jedoch eine Update-Datei zur manuellen Installation verfügbar (z.B. wenn die installierte EDOMI-Version deutlich veraltet ist).";
<? } else if ($autoUpdate=='DISABLED') { ?>
						n+="Die automatische Update-Funktion ist deaktiviert (ggf. Basis-Konfiguration überprüfen).";
<? } else { ?>
						n+="Der Update-Server ist nicht erreichbar (ggf. Basis-Konfiguration überprüfen).";
<? } ?>
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
<? if ($autoUpdate=='CHECKED') { ?>
						n+="<div class='cmdButton' onClick='ajax(\"menu4_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%; border-color:#ff0000;'>Installieren (<?echo round($updateInfo[2]/(1024*1024),2);?> MB)</div>";
<? } else { ?>
						n+="<div class='cmdButton' onClick='window.open(\"http://undefineURL/#downloads\",\"_blank\");' style='width:70%; float:right;'>EDOMI-Homepage</div>";
<? } ?>
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		n+="<div class='controlEditInline' style='margin-top:5px;'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Manuelles Update</b></span><br><br>";
						n+="Eine lokale Updatedatei kann ausgewählt und hochgeladen werden, anschließend wird das Update installiert.<br><br>";
						n+="Es wird dringend empfohlen, vor der Update-Installation eine Datensicherung vorzunehmen!";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<form id='<?echo $winId;?>-formuploadUpdate' action='apps/app_upload.php?suffixes=edomiupdate;&ajaxok=menu4_install&ajaxappid=<?echo $appId;?>&ajaxwinid=<?echo $winId;?>&ajaxdata=<?echo $data;?>&sid=<?echo $sid;?>' target='<?echo $winId;?>-iframe' method='post' enctype='multipart/form-data'>";
							n+="<div class='cmdUpload' style='width:70%; border-color:#ff0000;'>Updatedatei hochladen<input type='file' name='file' id='file' onChange='openBusyWindow(); document.getElementById(\"<?echo $winId;?>-formuploadUpdate\").submit();'></div>";
						n+="</form>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
		
<? } if ($cmd=='menu4_download') { $autoUpdate=false; deleteFiles(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt'); queueCmd(1,10,1); $timeout=0; ?>
		var n="<div class='controlEditInline'>";
			n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Automatisches Update vorbereiten</b></span><br><br>";
			n+="Das Herunterladen wird vorbereitet...";
		n+="</div>";	
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
		
		ajax("menu4_downloading","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $timeout;?>");
<? } if ($cmd=='menu4_downloading') { $timeout=$phpdataArr[0]; if (!file_exists(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt') && $timeout<300) { sleep(1); $timeout++; $tmp=readInfoFile(MAIN_PATH.'/www/data/tmp/autoupdatedownload.txt'); if ($tmp!==false && $tmp[0]>0) {$dlState=$tmp[1]*100/$tmp[0];} else {$dlState=0;} ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Die Updatedatei wird heruntergeladen...</b></span><br><br>";
				n+="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:0px 5px 0px 5px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $dlState;?>%,transparent <?echo $dlState;?>%,transparent 100%);'><?echo intVal($dlState);?>%</div>";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
			
			ajax("menu4_downloading","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $timeout;?>");
<? } else { ?>
			ajax("menu4_prepare","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","");
<? } } if ($cmd=='menu4_prepare') { $autoUpdate=false; if (file_exists(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt')) { $updateInfo=readInfoFile(MAIN_PATH.'/www/data/tmp/autoupdateinfo.txt'); if ($updateInfo[0]=='DOWNLOADED') {$autoUpdate=true;} } if ($autoUpdate) { ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Die Updatedatei wurde heruntergeladen</b></span><br><br>";
				n+="Update-Installation vorbereiten...";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
			
			ajax("menu4_install","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $updateInfo[3];?>");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Das Herunterladen der Updatedatei ist fehlgeschlagen</b></span><br><br>";
				n+="<span style='color:#ff0000;'>Beim Herunterladen der Updatedatei ist ein Fehler aufgetreten (es wurden keine Änderungen vorgenommen).</span>";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;			
<? } } if ($cmd=='menu4_install') { $fileName=$phpdataArr[0]; $suffix=pathinfo($fileName,PATHINFO_EXTENSION); if ($suffix!='edomiupdate') { ?>
			shakeObj('<?echo $winId;?>');
<? } else { deleteFiles(MAIN_PATH.'/www/data/tmp/updateready.txt'); queueCmd(1,9,$fileName); $timeout=0; while (!file_exists(MAIN_PATH.'/www/data/tmp/updateready.txt') && $timeout<30) { sleep(1); $timeout++; } if (file_exists(MAIN_PATH.'/www/data/tmp/updateready.txt')) { setSysInfo(2,24); ?>
				var n="<div class='controlEditInline'>";
					n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Die Update-Installation wurde vorbereitet</b></span><br><br>";
					n+="Der Server wird neu gestartet...";
				n+="</div>";	
				document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
				
				gotoDesktop(3);
<? } else { deleteFiles(MAIN_PATH.'/www/data/tmp/'.$fileName); ?>
				var n="<div class='controlEditInline'>";
					n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Die Vorbereitung der Update-Installation ist fehlgeschlagen</b></span><br><br>";
					n+="<span style='color:#ff0000;'>Beim Vorbereiten der Update-Installation ist ein Fehler aufgetreten (es wurden keine Änderungen vorgenommen).</span>";
				n+="</div>";	
				document.getElementById("<?echo $winId;?>-edit").innerHTML=n;							
<? } } } if ($cmd=='menu5') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Manuelle Datensicherung</b></span><br><br>";
						n+="Es wird ein komplettes Backup erstellt und heruntergeladen. Eine Kopie des Backups wird im Backup-Verzeichnis <b><?echo BACKUP_PATH;?></b> abgelegt.";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajax(\"menu5_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Backup herunterladen</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		n+="<div class='controlEditInline' style='margin-top:5px;'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Automatische Datensicherung</b></span><br><br>";
<? if (global_autoBackup) { ?>
						n+="Die automatische Datensicherung ist aktiviert.<br>Täglich um Mitternacht wird automatisch ein Backup erstellt und <?echo global_backupKeep;?> Tage vorgehalten.";
<? } else { ?>
						n+="<span style='color:#ff0000;'>Die automatische Datensicherung ist nicht aktiviert!</span><br>Es wird kein automatisches Backup erstellt.";
<? } ?>
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajax(\"menu6\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Basis-Konfiguration</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		n+="<div class='controlEditInline' style='margin-top:5px;'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Wiederherstellung</b></span><br><br>";
						n+="Eine verfügbare Backup-Datei kann bei Bedarf wiederhergestellt werden. Dabei werden sämtliche Daten, Einstellungen und EDOMI-Systemdateien überschrieben!<br><br>";
						n+="<b>Backup-Dateien in <?echo BACKUP_PATH;?></b>";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:250px; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+='<tr style="color:#a0a0a0;">';
									n+='<td>Dateiname</td>';
									n+='<td>Zeitstempel</td>';
									n+='<td>Typ</td>';
									n+='<td>Größe</td>';
								n+='</tr>';
<? $backups=array(); clearstatcache(); $files=glob(BACKUP_PATH.'/*.edomibackup',GLOB_NOSORT); foreach ($files as $n) { if (is_file($n)) { $fn=basename($n); $fdate=filectime($n); $fsize=filesize($n); if (strtolower(basename($n,'.edomibackup'))=='edomi-backup') {$ftyp='manuell';} else {$ftyp='automatisch/sonstiges';} $backups[]=date('Y-m-d-H-i-s',$fdate).';'.$fn.';'.$fsize.';'.date('d.m.Y / H:i:s',$fdate).';'.$ftyp; } } rsort($backups,SORT_STRING); foreach ($backups as $n) { $tmp=explode(';',$n); ?>
								n+="<tr onClick='ajaxConfirmSecure(\"<b><?echo $tmp[1];?> wirklich wiederherstellen?</b><br><br>ACHTUNG: Alle aktuellen Daten und Einstellungen gehen vollständig verloren! Die Wiederherstellung wird alle Daten, Einstellungen und Systemdateien ersetzen!\",\"menu5_restore\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $tmp[1];?>\");'' class='controlListItem' style='display:table-row;'>";
									n+='<td style="max-width:200px; overflow-x:hidden;"><?ajaxEcho($tmp[1]);?></td>';
									n+='<td><b><?echo $tmp[3];?></b> Uhr</b></td>';
									n+='<td><?ajaxEcho($tmp[4]);?></td>';
									n+='<td><?echo round($tmp[2]/1024/1024,2);?> MB</td>';
								n+='</tr>';
<? } ?>
							n+='</table>';
						n+="</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu5_download') { deleteFiles(MAIN_PATH.'/www/data/tmp/backupready.txt'); $fn='EDOMI-Backup.edomibackup'; queueCmd(1,1,$fn); $timeout=0; while (!file_exists(MAIN_PATH.'/www/data/tmp/backupready.txt') && $timeout<300) { sleep(1); $timeout++; } if (file_exists(MAIN_PATH.'/www/data/tmp/backupready.txt')) { ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Backup erstellt</b></span><br><br>";
				n+="Die Backupdatei <b><?echo $fn;?></b> wird heruntergeladen...";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
			document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode($fn);?>&sid=<?echo $sid;?>";
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0; color:#ff0000;'><b>Backup konnte nicht erstellt werden</b></span><br><br>";
				n+="Beim Erstellen der Backupdatei ist ein Fehler aufgetreten.";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;			
<? } } if ($cmd=='menu5_restore') { $fn=substr($phpdataArr[0],0,100); deleteFiles(MAIN_PATH.'/www/data/tmp/restoreready.txt'); queueCmd(1,7,$fn); $timeout=0; while (!file_exists(MAIN_PATH.'/www/data/tmp/restoreready.txt') && $timeout<30) { sleep(1); $timeout++; } if (file_exists(MAIN_PATH.'/www/data/tmp/restoreready.txt')) { setSysInfo(2,21); ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Wiederherstellung vorbereitet</b></span><br><br>";
				n+="Der Server wird in 3 Sekunden neu gestartet...";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
			
			gotoDesktop(3);
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<span class='formTitel' style='padding:5px 0 5px 0; color:#ff0000;'><b>Wiederherstellung gescheitert</b></span><br><br>";
				n+="Die Wiederherstellung der Datei <?echo $fn;?> ist gescheitert (es wurden keine Daten verändert).";
			n+="</div>";	
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu6') { $ini=app103_parseIni(); if ($ini!==false) { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var n="";
		n+='<table class="controlEditInline" style="width:100%; height:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
			n+="<tr height='1'><td><span class='formTitel' style='padding:5px 0 5px 0;'><b>Basis-Konfiguration (<?echo MAIN_PATH;?>/edomi.ini)</b></span><br><br></td></tr>";
			n+='<tr valign="top">';
				n+='<td>';
					n+="<div id='<?echo $winId;?>-form1' class='controlList' style='width:100%; height:450px; border:none;'>";
						n+='<table style="width:100%; table-layout:auto; white-space:normal; word-wrap:normal;" border="0" cellpadding="5" cellspacing="0">';
<? $fdId=0; for ($t=0;$t<count($ini);$t++) { if ($ini[$t][0]==1) { ?>
							n+="<tr class='trSpace' valign='top' style='line-height:1.5;'>";
								n+="<td colspan='2' style='font-size:12px; color:#ffffff; background:#9090ff;'><b><?echo $ini[$t][4];?></b></td>";
							n+="</tr>";
<? } else if ($ini[$t][0]==2) { ?>
							n+="<tr valign='top' style='line-height:1.5;'>";
								n+="<td colspan='2' style='font-size:11px; color:#0000ff; padding-top:10px;'><b><?ajaxEcho($ini[$t][4]);?></b></td>";
							n+="</tr>";
<? } else if ($ini[$t][0]==3) { ?>
							n+="<tr valign='top' style='line-height:1.5;'>";
								n+="<td width='70%' style='border-top:1px solid #e0e0e0;'><b><?ajaxEcho($ini[$t][4]);?></b>";
<? for ($tt=5;$tt<count($ini[$t]);$tt++) { ?>
									n+="<br><?ajaxEcho($ini[$t][$tt]);?>";
<? } ?>
								n+="</td>";
<? if ($ini[$t][3]==1 || $ini[$t][3]==3) { ?>
								n+="<td style='border-top:1px solid #e0e0e0;'><input type='text' id='<?echo $winId;?>-fd<?echo $fdId;?>' data-type='1' value='' data-info='<?echo $ini[$t][1];?>;<?echo $ini[$t][3];?>;' class='control1' style='width:100%;'></input><br><span style='color:#909090;'><?echo $ini[$t][1];?></span></td>";
<? } else if ($ini[$t][3]==2) { ?>
								n+="<td style='border-top:1px solid #e0e0e0;'><div id='<?echo $winId;?>-fd<?echo $fdId;?>' data-type='6' data-value='' data-info='<?echo $ini[$t][1];?>;<?echo $ini[$t][3];?>;' data-list='false|false;true|true;' class='control6' style='width:100%;'>&nbsp;</div><br><span style='color:#909090;'><?echo $ini[$t][1];?></span></td>";
<? } $fdId++; ?>
							n+="</tr>";
<? } } ?>
						n+="</table>";
					n+="</div>";
				n+='</td>';
			n+='</tr>';
			n+='<tr height="1" align="right">';
				n+='<td>';
					n+="<div class='cmdButton' onClick='ajax(\"menu6_save\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\",true));' style='border-color:#ff0000;'><b>Übernehmen</b></div>";
				n+='</td>';
			n+='</tr>';
		n+='</table>';

		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? $fdId=0; for ($t=0;$t<count($ini);$t++) { if ($ini[$t][0]==3) { if ($ini[$t][3]==1 || $ini[$t][3]==3) { ?>
					document.getElementById("<?echo $winId;?>-fd<?echo $fdId;?>").value='<?ajaxValue($ini[$t][2]);?>';
<? } else if ($ini[$t][3]==2) { ?>
					document.getElementById("<?echo $winId;?>-fd<?echo $fdId;?>").dataset.value='<?ajaxValue($ini[$t][2]);?>';
<? } $fdId++; } } ?>
		controlInitAll("<?echo $winId;?>-form1");
<? } } if ($cmd=='menu6_save') { $ini=file(MAIN_PATH.'/edomi.ini'); for ($t=0;$t<count($phpdataArr);$t++) { if (!isEmpty($phpdataArr[$t])) { $tmp=explode(';',$phpdataArr[$t],3); $tmp[2]=trim(str_replace("'",'',$tmp[2])); if ($tmp[1]==1) {$tmp[2]="'".$tmp[2]."'";} if ($tmp[1]==1 || ($tmp[1]==2 && ($tmp[2]=='false' || $tmp[2]=='true')) || ($tmp[1]==3 && is_numeric($tmp[2]))) { for ($tt=0; $tt<count($ini); $tt++) { $line=trim($ini[$tt]); if (!isEmpty($line) && substr($line,0,1)!='#') { $var=explode('=',$line,2); if (count($var)==2) { if ($var[0]==$tmp[0]) { $ini[$tt]=$tmp[0]."=".$tmp[2]."\n"; } } } } } } } $tmp=implode('',$ini); file_put_contents(MAIN_PATH.'/edomi.ini',$tmp); ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Basis-Konfiguration gespeichert</b></span><br><br>";
						n+="Die Änderungen werden erst nach einem Neustart des Servers (Reboot) wirksam.";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajax(\"menu6_reboot\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%; border-color:#ff0000;'>Server neustarten</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";

		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu6_reboot') { if (getEdomiStatus()==1 || getEdomiStatus()==3) { setSysInfo(2,13); ?>
			gotoDesktop(0);
<? } else { ?>
			jsConfirm("Der Server kann momentan nicht neugestartet werden. Ein Neustart ist nur möglich, wenn EDOMI pausiert ist oder läuft.","","none");
<? } } if ($cmd=='menu7') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var n="";
<? if (getEditProjektId()) { ?>
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt löschen</b></span><br><br>";
							n+="Das aktuelle Arbeitsprojekt wird vollständig gelöscht. Anschließend wird ein leeres Arbeitsprojekt angelegt.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajaxConfirmSecure(\"<b>Soll das aktuelle Arbeitsprojekt wirklich gelöscht werden?</b>\",\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\",\"\",\"Löschen\");' style='width:70%; border-color:#ff0000;'>Arbeitsprojekt löschen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
<? } if (getLiveProjektId()) { ?>
			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Live-Projekt löschen</b></span><br><br>";
							n+="Das aktuelle Live-Projekt wird einschließlich sämtlicher Daten (Archive, Schaltzeiten, etc.) gelöscht. Anschließend steht kein Live-Projekt mehr zu Verfügung und EDOMI wird pausiert.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajaxConfirmSecure(\"<b>Soll das aktuelle Live-Projekt mit allen zugehörigen Daten wirklich gelöscht werden?</b>\",\"menu7_deleteLiveProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\",\"\",\"Löschen\");' style='width:70%; border-color:#ff0000;'>Live-Projekt löschen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
<? } ?>
		n+="<div class='controlEditInline' style='margin-top:5px;'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Sämtliche Daten löschen (Werkseinstellungen)</b></span><br><br>";
						n+="Alle Einstellungen und Daten werden vollständig gelöscht! Alle Projekte (auch archivierte Projekte), das Live-Projekt, Logdateien, Archive, etc. werden gelöscht! Logikbausteine und die Basis-Konfiguration bleiben jedoch erhalten.";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajaxConfirmSecure(\"<b>Sollen wirklich sämtliche Daten gelöscht werden (Werkseinstellungen)?</b>\",\"menu7_deleteAll\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\",\"\",\"Löschen\");' style='width:70%; border-color:#ff0000;'>Sämtliche Daten löschen</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu7_deleteLiveProject') { setSysInfo(2,31); ?>
		gotoDesktop(3);
<? } if ($cmd=='menu7_deleteAll') { setSysInfo(2,33); ?>
		gotoDesktop(3);
<? } if ($cmd=='menu7_deleteEditProject') { $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt löschen</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/projectdelete_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/projectdelete_report.txt'); queueCmd(1,12,0); ?>
			ajax("menu7_deleteEditProjectDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt löschen</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Löschen nicht möglich</b></span><br><br>";
							n+="EDOMI ist nicht pausiert oder gestartet.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu7_deleteEditProjectDo') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/projectdelete_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/projectdelete_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("menu7_deleteEditProjectDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/projectdelete_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/projectdelete_report.txt'); if ($report[1]==0) { ?>
					n+="Das Arbeitsprojekt wurde erfolgreich gelöscht und ein leeres Arbeitsprojekt wurde erstellt.";
<? if ($report[0]>0) { ?>
						n+="<br><br><span style='color:#ff0000;'><?echo $report[0];?> Probleme/Fehler sind aufgetreten</span>";
<? } } else { ?>
					n+="<span style='color:#ff0000;'><?echo $report[1];?> fatale Fehler sind aufgetreten</span>";
<? } } else { ?>
				n+="<span style='color:#ff0000;'>Keine Rückmeldung erhalten (Timeout).</span>";
<? } ?>			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } showEditProjectName(); } if ($cmd=='menu16') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $vse=array(); $vseErr=''; $vseIds=array(); $tmp=glob(MAIN_PATH.'/www/admin/vse/*_vse.php'); foreach ($tmp as $pathFn) { if (is_file($pathFn)) { $id=explode('_',basename($pathFn)); if (isset($id[0]) && $id[0]>0) {$vseIds[]=$id[0];} } } if (getEditProjektId()) { $ss1=sql_call("SELECT DISTINCT(controltyp) FROM edomiProject.editVisuElement AS a WHERE a.controltyp>0 AND a.controltyp NOT IN (SELECT id FROM edomiProject.editVisuElementDef)"); while ($n=sql_result($ss1)) { if (file_exists(MAIN_PATH.'/www/admin/vse/'.$n['controltyp'].'_vse.php')) { $vseErr.="<span class='id' style='background:#e09000;'>".$n['controltyp']."</span> &gt; Datei ist verfügbar und wird vom Arbeitsprojekt benötigt, ist jedoch nicht eingelesen<br>"; } else { $vseErr.="<span class='id' style='background:#ff0000;'>".$n['controltyp']."</span> &gt; Datei fehlt, wird jedoch vom Arbeitsprojekt benötigt<br>"; } } sql_close($ss1); } if (getEditProjektId()) { $ss1=sql_call("SELECT id FROM edomiProject.editVisuElementDef"); while ($n=sql_result($ss1)) { if (!in_array($n['id'],$vseIds)) { $dbId=sql_getValue('edomiProject.editVisuElement','id','controltyp='.$n['id']); if (isEmpty($dbId)) { $vseErr.="<span class='id'>".$n['id']."</span> &gt; Visuelement ist eingelesen, jedoch fehlt die Datei<br>"; } else { $vseErr.="<span class='id' style='background:#ff0000;'>".$n['id']."</span> &gt; Visuelement ist eingelesen und wird vom Arbeitsprojekt benötigt, jedoch fehlt die Datei<br>"; } } } sql_close($ss1); } sort($vseIds,SORT_NUMERIC); for ($t=0;$t<count($vseIds);$t++) { $arrId=(($vseIds[$t]>=1000)?1:0); if (getEditProjektId()) { $tmp=sql_getValues('edomiProject.editVisuElementDef','id,name,errcount,errmsg','id='.$vseIds[$t]); if ($tmp===false) { $vse[$arrId].="<span class='id' style='background:#e09000;'>".$vseIds[$t]."</span> &gt; Datei ist verfügbar, jedoch nicht eingelesen<br>"; } else { if ($tmp['errcount']>0) { $vse[$arrId].="<span class='id' style='background:#ff0000;'>".$vseIds[$t]."</span> &gt; Visuelement enthält ".$tmp['errcount']." Fehler<br>"; $vseErr.="<span class='id' style='background:#ff0000;'>".$vseIds[$t]."</span> &gt; Visuelement enthält ".$tmp['errcount']." Fehler<br>"; } else { $vse[$arrId].="<span class='id'>".$vseIds[$t]."</span> <div style='display:inline-block; max-width:250px; overflow-x:hidden; color:#a0a0a0;'>".ajaxValueHTML($tmp['name'])."</div><br>"; } } } else { $vse[$arrId].="<span class='id' style='background:#e09000;'>".$vseIds[$t]."</span> &gt; Datei ist verfügbar, jedoch nicht eingelesen<br>"; } } ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Installierte Visuelemente</b></span><br><br>";
						n+="Die folgenden Visuelement-Dateien sind auf diesem System verfügbar:";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:180px; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr style='color:#a0a0a0;'>";
									n+="<td width='50%'>EDOMI-Visuelemente</td>";
									n+="<td>Eigene Visuelemente</td>";
								n+="</tr>";
								n+="<tr valign='top' style='line-height:1.5;'>";
									n+="<td><?echo $vse[0];?></td>";
									n+="<td><?echo $vse[1];?></td>";
								n+="</tr>";
							n+="</table>";
						n+="</div>";
<? if (getEditProjektId() && !isEmpty($vseErr)) { ?>
						n+="<span style='color:#ff0000;'><br><br><b>Folgende Probleme sind aufgetreten (ein erneutes Einlesen der Visuelemente kann einige Probleme ggf. beheben):</b></span>";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:110px; border-color:#ff0000;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr valign='top' style='line-height:1.5;'>";
									n+="<td><span style='white-space:normal;'><?echo $vseErr;?></span></td>";
								n+="</tr>";
							n+="</table>";
						n+="</div>";
<? } ?>
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
<? if (getEditProjektId()) { ?>
			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuelemente neu einlesen</b></span><br><br>";
							n+="Visuelemente im Verzeichnis <b><?echo MAIN_PATH;?>/www/admin/vse</b> werden erneut eingelesen und analysiert. Dies ist z.B. nach einer Bearbeitung eines Visuelements in einem externen Editor erforderlich.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"importVse\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"-1\");' style='width:70%;'>Alle VSE einlesen</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"importVse\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"-2\");' style='width:70%;'>Eigene VSE einlesen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";

			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuelement importieren</b></span><br><br>";
							n+="Importiert ein Visuelement. Die Datei muss als <b>PHP-Datei</b> vorliegen.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<form id='<?echo $winId;?>-formuploadVse' action='apps/app_upload.php?suffixes=php;&ajaxok=menu16_vseUploadCheck&ajaxappid=<?echo $appId;?>&ajaxwinid=<?echo $winId;?>&ajaxdata=<?echo $data;?>&sid=<?echo $sid;?>' target='<?echo $winId;?>-iframe' method='post' enctype='multipart/form-data'>";
								n+="<div class='cmdUpload' style='width:70%;'>Visuelement hochladen<input type='file' name='file' id='file' onChange='openBusyWindow(); document.getElementById(\"<?echo $winId;?>-formuploadVse\").submit();'></div>";
							n+="</form>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } else { ?>
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt erstellt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu16_vseUploadCheck') { $fileName=$phpdataArr[0]; $suffix=pathinfo($fileName,PATHINFO_EXTENSION); $tmp=explode('_',basename($fileName)); $vseID=$tmp[0]; cmd('menu16'); if (isset($tmp[0]) && isset($tmp[1]) && $suffix=='php' && is_numeric($vseID) && $tmp[1]=='vse.php' && $vseID>=1000 && $vseID<=99999999) { if (file_exists(MAIN_PATH.'/www/admin/vse/'.$fileName)) { ?>
				ajaxConfirmSecure("<b>Es existiert bereits ein Visuelement mit der ID <?echo $vseID;?>.</b><br><br>Soll das vorhandene Visuelement durch die hochgeladene Datei ersetzt werden?","menu16_vseUploadConfirmed","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>","","Ersetzen");
<? } else { cmd('menu16_vseUploadConfirmed'); } } else { deleteFiles(MAIN_PATH.'/www/data/tmp/'.$fileName); ?>
			shakeObj('<?echo $winId;?>');
<? } } if ($cmd=='menu16_vseUploadConfirmed') { $fileName=$phpdataArr[0]; exec('mv "'.MAIN_PATH.'/www/data/tmp/'.$fileName.'" "'.MAIN_PATH.'/www/admin/vse"'); $tmp=explode('_',basename($fileName)); $vseID=$tmp[0]; ?>
		ajax("importVse","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $vseID;?>");
<? } if ($cmd=='importVse') { $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuelmente einlesen</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/importvse_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/importvse_report.txt'); queueCmd(1,20,$phpdataArr[0]); ?>
			ajax("importVseDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuelmente einlesen</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Einlesen nicht möglich</b></span><br><br>";
							n+="EDOMI ist nicht pausiert oder gestartet.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='importVseDo') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/importvse_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/importvse_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("importVseDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/importvse_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/importvse_report.txt'); if ($report[1]==0) { ?>
					n+="Visuelemente wurden erfolgreich eingelesen.";
<? if ($report[0]>0) { ?>
						n+="<br><br><span style='color:#ff0000;'><?echo $report[0];?> Probleme/Fehler sind aufgetreten</span>";
<? } } else { ?>
					n+="<span style='color:#ff0000;'><?echo $report[1];?> fatale Fehler sind aufgetreten</span>";
<? } } else { ?>
				n+="<span style='color:#ff0000;'>Keine Rückmeldung erhalten (Timeout).</span>";
<? } ?>			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } } if ($cmd=='menu8') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $lbs=array(); $lbsErr=''; $lbsIds=array(); $tmp=glob(MAIN_PATH.'/www/admin/lbs/????????_lbs.php'); foreach ($tmp as $pathFn) { if (is_file($pathFn)) {$lbsIds[]=substr(basename($pathFn),0,8);} } if (getEditProjektId()) { $ss1=sql_call("SELECT DISTINCT(functionid) FROM edomiProject.editLogicElement AS a WHERE a.functionid NOT IN (SELECT id FROM edomiProject.editLogicElementDef)"); while ($n=sql_result($ss1)) { if (file_exists(MAIN_PATH.'/www/admin/lbs/'.$n['functionid'].'_lbs.php')) { $lbsErr.="<span class='id' style='background:#e09000;'>".$n['functionid']."</span> &gt; Datei ist verfügbar und wird vom Arbeitsprojekt benötigt, ist jedoch nicht eingelesen<br>"; } else { $lbsErr.="<span class='id' style='background:#ff0000;'>".$n['functionid']."</span> &gt; Datei fehlt, wird jedoch vom Arbeitsprojekt benötigt<br>"; } } sql_close($ss1); } if (getEditProjektId()) { $ss1=sql_call("SELECT id FROM edomiProject.editLogicElementDef"); while ($n=sql_result($ss1)) { if (!in_array($n['id'],$lbsIds)) { $dbId=sql_getValue('edomiProject.editLogicElement','id','functionid='.$n['id']); if (isEmpty($dbId)) { $lbsErr.="<span class='id'>".$n['id']."</span> &gt; Logikbaustein ist eingelesen, jedoch fehlt die Datei<br>"; } else { $lbsErr.="<span class='id' style='background:#ff0000;'>".$n['id']."</span> &gt; Logikbaustein ist eingelesen und wird vom Arbeitsprojekt benötigt, jedoch fehlt die Datei<br>"; } } } sql_close($ss1); } sort($lbsIds,SORT_NUMERIC); for ($t=0;$t<count($lbsIds);$t++) { $arrId=((substr($lbsIds[$t],0,2)==19)?1:0); if (getEditProjektId()) { $tmp=sql_getValues('edomiProject.editLogicElementDef','id,name,errcount','id='.$lbsIds[$t]); if ($tmp===false) { $lbs[$arrId].="<span class='id' style='background:#e09000;'>".$lbsIds[$t]."</span> &gt; Datei ist verfügbar, jedoch nicht eingelesen<br>"; } else { if ($tmp['errcount']>0) { $lbs[$arrId].="<span class='id' style='background:#ff0000;'>".$lbsIds[$t]."</span> &gt; Logikbaustein enthält ".$tmp['errcount']." Fehler<br>"; $lbsErr.="<span class='id' style='background:#ff0000;'>".$lbsIds[$t]."</span> &gt; Logikbaustein enthält ".$tmp['errcount']." Fehler<br>"; } else { $lbs[$arrId].="<span class='id'>".$lbsIds[$t]."</span> <div style='display:inline-block; max-width:250px; overflow-x:hidden; color:#a0a0a0;'>".ajaxValueHTML($tmp['name'])."</div><br>"; } } } else { $lbs[$arrId].="<span class='id' style='background:#e09000;'>".$lbsIds[$t]."</span> &gt; Datei ist verfügbar, jedoch nicht eingelesen<br>"; } } ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Installierte Logikbausteine</b></span><br><br>";
						n+="Die folgenden Logikbaustein-Dateien sind auf diesem System verfügbar:";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:180px; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr style='color:#a0a0a0;'>";
									n+="<td width='50%'>EDOMI-Logikbausteine</td>";
									n+="<td>Eigene Logikbausteine</td>";
								n+="</tr>";
								n+="<tr valign='top' style='line-height:1.5;'>";
									n+="<td><?echo $lbs[0];?></td>";
									n+="<td><?echo $lbs[1];?></td>";
								n+="</tr>";
							n+="</table>";
						n+="</div>";
<? if (getEditProjektId() && !isEmpty($lbsErr)) { ?>
						n+="<span style='color:#ff0000;'><br><br><b>Folgende Probleme sind aufgetreten (ein erneutes Einlesen aller Logikbausteine kann einige Probleme ggf. beheben):</b></span>";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:80px; border-color:#ff0000;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr valign='top' style='line-height:1.5;'>";
									n+="<td><span style='white-space:normal;'><?echo $lbsErr;?></span></td>";
								n+="</tr>";
							n+="</table>";
						n+="</div>";
<? } ?>
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
<? if (getEditProjektId()) { ?>
			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Logikbausteine neu einlesen</b></span><br><br>";
							n+="Logikbausteine im Verzeichnis <b><?echo MAIN_PATH;?>/www/admin/lbs</b> werden erneut eingelesen und analysiert. Dies ist z.B. nach einer Bearbeitung eines Logikbausteins in einem externen Editor erforderlich.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"importLbs\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"-1\");' style='width:70%;'>Alle LBS einlesen</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"importLbs\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"-2\");' style='width:70%;'>Eigene LBS einlesen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";

			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Logikbaustein importieren</b></span><br><br>";
							n+="Importiert einen Logikbaustein in 'Eigene Logikbausteine (19)'. Die Datei muss als <b>PHP-Datei</b> vorliegen.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<form id='<?echo $winId;?>-formuploadLbs' action='apps/app_upload.php?suffixes=php;&ajaxok=menu8_lbsUploadCheck&ajaxappid=<?echo $appId;?>&ajaxwinid=<?echo $winId;?>&ajaxdata=<?echo $data;?>&sid=<?echo $sid;?>' target='<?echo $winId;?>-iframe' method='post' enctype='multipart/form-data'>";
								n+="<div class='cmdUpload' style='width:70%;'>Logikbaustein hochladen<input type='file' name='file' id='file' onChange='openBusyWindow(); document.getElementById(\"<?echo $winId;?>-formuploadLbs\").submit();'></div>";
							n+="</form>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } else { ?>
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt erstellt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu8_lbsUploadCheck') { $fileName=$phpdataArr[0]; $suffix=pathinfo($fileName,PATHINFO_EXTENSION); $lbsID=substr(basename($fileName),0,8); $lbsFolderID=substr($lbsID,0,2); cmd('menu8'); if ($suffix!='php' || !is_numeric(substr($fileName,0,8)) || !(substr($fileName,8,8)=='_lbs.php') || !($lbsID>=12000000 && $lbsID<=19999999)) { deleteFiles(MAIN_PATH.'/www/data/tmp/'.$fileName); ?>
			shakeObj('<?echo $winId;?>');
<? } else { if (file_exists(MAIN_PATH.'/www/admin/lbs/'.$fileName)) { ?>
				ajaxConfirmSecure("<b>Es existiert bereits ein Logikbaustein mit der ID <?echo $lbsID;?>.</b><br><br>Soll der vorhandene Logikbaustein durch die hochgeladene Datei ersetzt werden?","menu8_lbsUploadConfirmed","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>","","Ersetzen");
<? } else { cmd('menu8_lbsUploadConfirmed'); } } } if ($cmd=='menu8_lbsUploadConfirmed') { $fileName=$phpdataArr[0]; exec('mv "'.MAIN_PATH.'/www/data/tmp/'.$fileName.'" "'.MAIN_PATH.'/www/admin/lbs"'); ?>
		ajax("importLbs","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo substr($fileName,0,8);?>");
<? } if ($cmd=='importLbs') { $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Logikbausteine einlesen</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/importlbs_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/importlbs_report.txt'); queueCmd(1,21,$phpdataArr[0]); ?>
			ajax("importLbsDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Logikbausteine einlesen</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Einlesen nicht möglich</b></span><br><br>";
							n+="EDOMI ist nicht pausiert oder gestartet.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='importLbsDo') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/importlbs_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/importlbs_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("importLbsDo","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/importlbs_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/importlbs_report.txt'); if ($report[1]==0) { ?>
					n+="Logikbausteine wurden erfolgreich eingelesen.";
<? if ($report[0]>0) { ?>
						n+="<br><br><span style='color:#ff0000;'><?echo $report[0];?> Probleme/Fehler sind aufgetreten</span>";
<? } } else { ?>
					n+="<span style='color:#ff0000;'><?echo $report[1];?> fatale Fehler sind aufgetreten</span>";
<? } } else { ?>
				n+="<span style='color:#ff0000;'>Keine Rückmeldung erhalten (Timeout).</span>";
<? } ?>			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } } if ($cmd=='menu9') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? if (getEditProjektId()) { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Gruppenadressen importieren</b></span><br><br>";
							n+="Importiert eine ESF-Datei (aus einem ETS-Export) und legt entsprechende Kommunikationsobjekte (KNX-Gruppenadressen) an.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<form id='<?echo $winId;?>-formupload' action='apps/app_upload.php?filename=ets-import.txt&suffixes=esf;&ajaxok=menu9_import&ajaxappid=<?echo $appId;?>&ajaxwinid=<?echo $winId;?>&ajaxdata=<?echo $data;?>&sid=<?echo $sid;?>' target='<?echo $winId;?>-iframe' method='post' enctype='multipart/form-data'>";
								n+="<div class='cmdUpload' style='width:70%;'>ESF-Datei hochladen<input type='file' name='file' id='file' onChange='openBusyWindow(); document.getElementById(\"<?echo $winId;?>-formupload\").submit();'></div>";
							n+="</form>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>ETS-Import</b></span><br><br>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt erstellt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu9_import') { ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Gruppenadressen importieren</b></span><br><br>";
						n+="<div class='controlList' style='width:100%; height:auto; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+='<tr style="color:#a0a0a0;">';
									n+='<td width="1">GA</td>';
									n+='<td>Hauptgruppe</td>';
									n+='<td>Mittelgruppe</td>';
									n+='<td>Name</td>';
									n+='<td>DPT</td>';
									n+='<td>Status</td>';
								n+='</tr>';
<? $errCount=0; $changeCount=0; $existsCount=0; $addCount=0; $esfRaw=file(MAIN_PATH.'/www/data/tmp/ets-import.txt'); array_shift($esfRaw); $esfParsed=array(); foreach ($esfRaw as $tmp) { $tmp=utf8_encode($tmp); $err=0; $nA=explode("\t",$tmp); $nB=explode('.',$nA[0]); $nC=explode('/',$nB[2]); if (!((!isEmpty($nC[0]) && is_numeric($nC[0]) && $nC[0]>=0 && $nC[0]<=31) && (!isEmpty($nC[1]) && is_numeric($nC[1]) && $nC[1]>=0 && $nC[1]<=7) && (!isEmpty($nC[2]) && is_numeric($nC[2]) && $nC[2]>=0 && $nC[2]<=255))) {$err+=1;} $dpt=0; if (strpos($nA[2],'(1 Bit)')!==false) {$dpt=1;} if (strpos($nA[2],'(2 Bit)')!==false) {$dpt=2;} if (strpos($nA[2],'(4 Bit)')!==false) {$dpt=3;} if (strpos($nA[2],'(1 Byte)')!==false) {$dpt=5;} if (strpos($nA[2],'(2 Byte)')!==false) {$dpt=9;} if (strpos($nA[2],'(3 Byte)')!==false) {$dpt=232;} if (strpos($nA[2],'(4 Byte)')!==false) {$dpt=13;} if (strpos($nA[2],'(14 Byte)')!==false) {$dpt=16;} if ($dpt==0) {$err+=2;} $esfParsed[]=sprintf("%03d",$nC[0]).sprintf("%03d",$nC[1]).sprintf("%03d",$nC[2]).SEPARATOR1.$nC[0].SEPARATOR1.$nC[1].SEPARATOR1.$nC[2].SEPARATOR1.$nB[0].SEPARATOR1.$nB[1].SEPARATOR1.$nA[1].SEPARATOR1.$dpt.SEPARATOR1.$err; } sort($esfParsed,SORT_STRING); foreach ($esfParsed as $esf) { $errFolder=false; $errMsg=''; $n=explode(SEPARATOR1,$esf); $ga=$n[1].'/'.$n[2].'/'.$n[3]; if ($n[8]==0) { $ko=sql_getValues('edomiProject.editKo','id,name',"ga='".$ga."' AND gatyp=1"); if ($ko!==false) { if (sql_encodeValue($n[6])!=$ko['name']) { sql_call("UPDATE edomiProject.editKo SET name='".sql_encodeValue($n[6])."' WHERE id=".$ko['id']); ?>
								n+="<tr style='color:#898989;'>";
									n+="<td><span class='idGa1' style='background:#898989;'><?ajaxEcho($ga);?></span></td>";
									n+="<td><?ajaxEcho($n[4]);?></td>";
									n+="<td><?ajaxEcho($n[5]);?></td>";
									n+="<td><?ajaxEcho($n[6]);?></td>";
									n+="<td><?echo $n[7];?></td>";
									n+="<td>Name abgeändert</td>";
								n+="</tr>";
<? $changeCount++; } else { $existsCount++; } } else { $folderId1=sql_getValue('edomiProject.editRoot','id',"parentid=32 AND name='".sql_encodeValue($n[4])."' ORDER BY id ASC LIMIT 0,1"); if (!($folderId1>0)) { $folderId1=db_itemSave('editRoot',array( 1 => -1, 2 => $n[4], 3 => 32 )); } if ($folderId1>0) { $folderId2=sql_getValue('edomiProject.editRoot','id',"parentid=".$folderId1." AND name='".sql_encodeValue($n[5])."' ORDER BY id ASC LIMIT 0,1"); if (!($folderId2>0)) { $folderId2=db_itemSave('editRoot',array( 1 => -1, 2 => $n[5], 3 => $folderId1 )); } } if ($folderId1>0 && $folderId2>0) { $dbid=db_itemSave('editKo',array( 1 => -1, 2 => $folderId2, 3 => $ga, 4 => 1, 5 => $n[7], 16 => $n[6] )); ?>
								n+="<tr>";
									n+="<td><span class='idGa1'><?ajaxEcho($ga);?></span></td>";
									n+="<td><?ajaxEcho($n[4]);?></td>";
									n+="<td><?ajaxEcho($n[5]);?></td>";
									n+="<td><?ajaxEcho($n[6]);?></td>";
									n+="<td><?echo $n[7];?></td>";
									n+="<td style='color:#009000;'>Hinzugefügt (<?echo $dbid;?>)</td>";
								n+="</tr>";
<? $addCount++; } else { $errFolder=true; } } } if (!($n[8]==0) || $errFolder) { if ($errFolder) {$errMsg.='Ordner nicht erstellbar, ';} if ($n[8]&1) {$errMsg.='fehlerhafte GA, ';} if ($n[8]&2) {$errMsg.='unbekannter DPT, ';} $errMsg=rtrim(trim($errMsg),','); ?>
								n+="<tr style='color:#ff0000;'>";
									n+="<td><span class='idGa1' style='background:#ff0000;'><?ajaxEcho($ga);?></span></td>";
									n+="<td><?ajaxEcho($n[4]);?></td>";
									n+="<td><?ajaxEcho($n[5]);?></td>";
									n+="<td><?ajaxEcho($n[6]);?></td>";
									n+="<td><?echo $n[7];?></td>";
									n+="<td>Fehler: <?echo $errMsg;?></td>";
								n+="</tr>";
<? $errCount++; } } ?>
							n+="</table>";	
						n+="</div>";	
					n+="</td>";
				n+="</tr>";
				n+="<tr>";
					n+="<td>";
<? if ($errCount>0) { ?> n+="<span style='color:#ff0000;'><b>Es <?echo (($errCount==1)?'ist':'sind');?> <?echo $errCount;?> Fehler aufgetreten!</span></b><br>"; <? } if ($addCount>0) { ?> n+="<span style='color:#000000;'><b><?echo $addCount;?> Einträge wurden hinzugefügt</span></b><br>"; <? } if ($changeCount>0) { ?> n+="<span style='color:#000000;'><b>Name von <?echo $changeCount;?> existierenden Einträgen geändert</span></b><br>"; <? } if ($existsCount>0) { ?> n+="<span style='color:#000000;'><b><?echo $existsCount;?> existierende Einträge wurden nicht verändert</span></b><br>"; <? } ?>
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";	
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;		
		scrollToEnd("<?echo $winId;?>-edit");
<? deleteFiles(MAIN_PATH.'/www/data/tmp/ets-import.txt'); } if ($cmd=='menu11') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? if (getEditProjektId()) { ?>
			document.getElementById("<?echo $winId;?>-edit").innerHTML="### Coming soon... ;-)";
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Import & Export</b></span><br><br>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt erstellt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu12') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? cmd('menu12_refresh'); } if ($cmd=='menu12_refresh') { $prj=sql_getValues('edomiAdmin.project','*','edit=1'); if ($prj!==false) { ?>
			var n="<div id='<?echo $winId;?>-form1' class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-prjname' style='width:450px; word-wrap:break-word;'>";
									n+="<div class='controlEditInline' onClick='ajax(\"menu12_renameProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='background:#ffffff; cursor:pointer;'><?ajaxEcho($prj['name']);?> <span class='id'><?echo $prj['id'];?></span> <?echo (($prj['live']==1)?"<span style='color:#ffffff; border-radius:3px; background:#80e000;'>&nbsp;LIVE&nbsp;</span>":'');?></div>";
							n+="</div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo date('d.m.Y / H:i:s',strtotime($prj['createdate']));?></b></td></tr>";
								n+="<tr><td align='right'>Projektaktivierung:</td><td><b><?echo ((isEmpty($prj['livedate']))?'niemals':sql_getDateTime($prj['livedate']));?></b></td></tr>";
								n+="<tr><td align='right'>Archivierung:</td><td><b><?echo ((isEmpty($prj['savedate']))?'niemals':sql_getDateTime($prj['savedate']));?></b></td></tr>";
								n+="<tr><td align='right'>&gt;</td><td><span class='link' onClick='ajax(\"menu12\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"menu12_showDetails\",\"\");'><b>Weitere Informationen anzeigen</b></span></td></tr>";
							n+="</table>";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu13\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Projektaktivierung</div><br><br>";
							n+="<div class='cmdButton' onClick='ajax(\"menu12_saveProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='width:70%;'>Archivieren</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"menu12_saveProjectCopy\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='width:70%;'>Duplikat archivieren</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"menu12_downloadProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Herunterladen</div><br>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt löschen</div>";
						n+="</td>";
					n+="</tr>";
<? if ($dataArr[0]=='menu12_showDetails') { ?>
					n+="<tr>";
						n+="<td width='100%' colspan='2' valign='top'>";
							n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; border:none;'>";
<? app103_projectStatisticsJS(0); ?>
							n+="</div>";
						n+="</td>";
					n+="</tr>";
<? } ?>
				n+="</table>";
			n+="</div>";
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt</b></span><br><br>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt erstellt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
<? } ?>
		n+="<div class='controlEditInline' style='margin-top:5px;'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Archivierte Projekte</b></span><br><br>";
						n+="<div class='controlList' style='width:100%; height:auto; max-height:300px; border:none;'>";

							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr style='color:#a0a0a0;'>";
									n+="<td>Name</td>";
									n+="<td width='1'>Archivierung</td>";
								n+="</tr>";
<? $ss2=sql_call("SELECT * FROM edomiAdmin.project WHERE (savedate IS NOT NULL) ORDER BY savedate DESC"); while ($nn=sql_result($ss2)) { ?>
								n+="<tr onMouseDown='app103_clickArchiv(\"<?echo $winId;?>\",\"<?echo $nn['id'];?>\");' class='controlListItem' style='display:table-row; <?echo (($nn['edit']==1)?"background:#80e000;":"");?>'>";
									n+="<td style='max-width:200px; overflow-x:hidden;'><div id='<?echo $winId;?>-prj-<?echo $nn['id'];?>-name'><?ajaxEcho($nn['name']);?> <span class='id'><?echo $nn['id'];?></span> <?echo (($nn['live']==1)?"<span style='color:#ffffff; border-radius:3px; background:#80e000;'>&nbsp;LIVE&nbsp;</span>":"&nbsp;");?></div></td>";
									n+="<td><?echo sql_getDateTime($nn['savedate']);?></td>";
								n+="</tr>";
<? } sql_close($ss2); ?>
							n+='</table>';
						n+="</div>";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajax(\"menu12_addToArchiv\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Hinzufügen</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
	
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
		if (document.getElementById("<?echo $winId;?>-fd0")) {document.getElementById("<?echo $winId;?>-fd0").value='<?ajaxValue($prj['name']);?>';}
		controlInitAll("<?echo $winId;?>-form1");
<? } if ($cmd=='menu12_renameProject') { $prj=sql_getValues('edomiAdmin.project','*','edit=1'); if ($prj!==false) { ?>
			var n="<div id='<?echo $winId;?>-form2' style='width:100%;'>";
				n+="<input type='text' id='<?echo $winId;?>_form2-fd0' data-type='1' value='' maxlength='200' autofocus class='control1' onMouseDown='clickCancel();' onkeydown='if (event.keyCode==13){this.blur();}' onBlur='ajax(\"menu12_renameProjectSave\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form2\"));' style='width:100%; margin:2px;'></input>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-prjname").innerHTML=n;
			document.getElementById("<?echo $winId;?>_form2-fd0").value='<?ajaxValue($prj['name']);?>';
			appAll_setAutofocus("<?echo $winId;?>-form2");
<? } } if ($cmd=='menu12_renameProjectSave') { $prj=sql_getValues('edomiAdmin.project','*','edit=1'); $prj['name']=$phpdataArr[0]; if ($prj!==false && !isEmpty($prj['name'])) { sql_call("UPDATE edomiAdmin.project SET name='".sql_encodeValue($prj['name'])."' WHERE (id=".$prj['id'].")"); } showEditProjectName(); cmd('menu12_refresh'); } if ($cmd=='menu12_saveProject' || $cmd=='menu12_saveProjectCopy' || $cmd=='menu12_downloadProject') { $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt archivieren oder herunterladen</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/projectsave_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/projectsave_report.txt'); if ($cmd=='menu12_saveProject') { queueCmd(1,5,1); } else if ($cmd=='menu12_saveProjectCopy') { queueCmd(1,5,2); } else if ($cmd=='menu12_downloadProject') { queueCmd(1,5,3); } ?>
			ajax("menu12_saveProjectWait","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Arbeitsprojekt archivieren oder herunterladen</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Archivierung nicht möglich</b></span><br><br>";
							n+="EDOMI ist nicht pausiert oder gestartet.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu12_saveProjectWait') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/projectsave_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/projectsave_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("menu12_saveProjectWait","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/projectsave_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/projectsave_report.txt'); if ($report[1]==0) { ?>
					n+="Das Arbeitsprojekt wurde erfolgreich archiviert bzw. zum Herunterladen vorbereitet.";
<? if ($report[0]>0) { ?>
						n+="<br><br><span style='color:#ff0000;'><?echo $report[0];?> Probleme/Fehler sind aufgetreten</span>";
<? } if (!isEmpty($report[2])) { ?>
						document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode($report[2]);?>&sid=<?echo $sid;?>";
<? } } else { ?>
					n+="<span style='color:#ff0000;'><?echo $report[1];?> fatale Fehler sind aufgetreten</span>";
<? } } else { ?>
				n+="<span style='color:#ff0000;'>Keine Rückmeldung erhalten (Timeout).</span>";
<? } ?>
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } showEditProjectName(); } if ($cmd=='menu12_addToArchiv') { ?>
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='70%' colspan='2' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Projektdatei zu den archivierten Projekten hinzufügen</b></span><br><br>";
						n+="Projektdateien im Verzeichnis <b><?echo MAIN_PATH;?>/www/data/tmp</b> können zu den archivierten Projekten hinzugefügt werden. Alternativ kann eine lokale Projektdatei hochgeladen und hinzugefügt werden.<br><br><br>";
					n+="</td>";
				n+="</tr>";
				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<b>Projektdateien in <?echo MAIN_PATH;?>/www/data/tmp</b>";
						n+="<div class='controlList' style='margin-top:10px; width:100%; height:auto; max-height:200px; border:none;'>";

							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+="<tr style='color:#a0a0a0;'>";
									n+="<td>Dateiname</td>";
									n+="<td>Zeitstempel</td>";
								n+="</tr>";
<? $n=glob(MAIN_PATH.'/www/data/tmp/*.edomiproject',GLOB_NOSORT); natcasesort($n); foreach ($n as $pathFn) { if (is_file($pathFn)) { $fn=basename($pathFn); ?>
								n+="<tr onClick='ajax(\"menu12_addToArchivSave\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $fn;?>\");' class='controlListItem' style='display:table-row;'>";
									n+="<td style='max-width:200px; overflow-x:hidden;'><?ajaxEcho($fn);?></td>";
									n+="<td><?echo date("d.m.Y/H:i:s",filectime($pathFn));?></td>";
								n+="</tr>";
<? } } ?>
							n+='</table>';
						n+="</div>";

					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<form id='<?echo $winId;?>-formupload' action='apps/app_upload.php?suffixes=edomiproject;&ajaxok=menu12_addToArchivSave&ajaxappid=<?echo $appId;?>&ajaxwinid=<?echo $winId;?>&ajaxdata=<?echo $data;?>&sid=<?echo $sid;?>' target='<?echo $winId;?>-iframe' method='post' enctype='multipart/form-data'>";
							n+="<div class='cmdUpload' style='width:70%;'>Projektdatei hochladen<input type='file' name='file' id='file' onChange='openBusyWindow(); document.getElementById(\"<?echo $winId;?>-formupload\").submit();'></div>";
						n+="</form>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } if ($cmd=='menu12_addToArchivSave') { $fileName=$phpdataArr[0]; $prjName=pathinfo($fileName,PATHINFO_FILENAME); $suffix=pathinfo($fileName,PATHINFO_EXTENSION); if ($suffix!='edomiproject') { ?>
			shakeObj('<?echo $winId;?>');
<? } else { sql_call("INSERT INTO edomiAdmin.project (name,createdate,savedate,edit,live) VALUES ('".$prjName."',".sql_getNow().",".sql_getNow().",0,0)"); if ($id=sql_insertId()) { exec('mv "'.MAIN_PATH.'/www/data/tmp/'.$fileName.'" "'.MAIN_PATH.'/www/data/projectarchiv/prj-'.$id.'.edomiproject"'); cmd('menu12_refresh'); } else { ?>
				var n="<div class='controlEditInline'>";
					n+="<span class='formTitel' style='padding:5px 0 5px 0; color:#ff0000;'><b>Hinzufügen der Projektdatei fehlgeschlagen</b></span><br><br>";
					n+="Die Projektdatei konnte nicht zu den archivierten Projekten hinzugefügt werden.";
				n+="</div>";	
				document.getElementById("<?echo $winId;?>-edit").innerHTML=n;		
<? } } } if ($cmd=='menu12_loadArchiv') { $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Archiviertes Arbeitsprojekt öffnen</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/projectload_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/projectload_report.txt'); queueCmd(1,6,$phpdataArr[0]); ?>
			ajax("menu12_loadArchivWait","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Archiviertes Arbeitsprojekt öffnen</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Öffnen nicht möglich</b></span><br><br>";
							n+="EDOMI ist nicht pausiert oder gestartet.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu12_loadArchivWait') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/projectload_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/projectload_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("menu12_loadArchivWait","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/projectload_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/projectload_report.txt'); if ($report[1]==0) { ?>
					n+="Das Projekt wurde erfolgreich geöffnet.";
<? if ($report[0]>0) { ?>
						n+="<br><br><span style='color:#ff0000;'><?echo $report[0];?> Probleme/Fehler sind aufgetreten</span>";
<? } } else { ?>
					n+="<span style='color:#ff0000;'><?echo $report[1];?> fatale Fehler sind aufgetreten</span>";
<? } } else { ?>
				n+="<span style='color:#ff0000;'>Keine Rückmeldung erhalten (Timeout).</span>";
<? } ?>
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } showEditProjectName(); } if ($cmd=='menu12_renameArchiv') { $prjId=$phpdataArr[0]; $ss1=sql_call("SELECT * FROM edomiAdmin.project WHERE (id=".$prjId.")"); if ($prj=sql_result($ss1)) { ?>
			var n="<div id='<?echo $winId;?>-form2' style='width:100%;'>";
				n+="<input type='text' id='<?echo $winId;?>_form2-fd0' data-type='1' value='' maxlength='200' autofocus class='control1' onMouseDown='clickCancel();' onkeydown='if (event.keyCode==13){this.blur();}' onBlur='ajax(\"menu12_renameArchivSave\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"<?echo $prjId;?><?echo AJAX_SEPARATOR1;?>\"+controlGetFormData(\"<?echo $winId;?>-form2\"));' style='width:100%; margin:2px;'></input>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-prj-<?echo $prjId;?>-name").innerHTML=n;
			document.getElementById("<?echo $winId;?>_form2-fd0").value='<?ajaxValue($prj['name']);?>';
			appAll_setAutofocus("<?echo $winId;?>-form2");
<? } } if ($cmd=='menu12_renameArchivSave') { $prjId=$phpdataArr[0]; $prjName=$phpdataArr[1]; if ($prjId>0 && !isEmpty($prjName)) { sql_call("UPDATE edomiAdmin.project SET name='".sql_encodeValue($prjName)."' WHERE (id=".$prjId.")"); } showEditProjectName(); cmd('menu12_refresh'); } if ($cmd=='menu12_deleteArchiv') { $prjId=$phpdataArr[0]; $ss1=sql_call("SELECT * FROM edomiAdmin.project WHERE (id=".$prjId.")"); if ($prj=sql_result($ss1)) { sql_call("DELETE FROM edomiAdmin.project WHERE (id=".$prj['id']." AND edit=0)"); sql_call("UPDATE edomiAdmin.project SET savedate=NULL WHERE (id=".$prj['id'].")"); deleteFiles('"'.MAIN_PATH.'/www/data/projectarchiv/prj-'.$prj['id'].'.edomiproject'.'"'); } cmd('menu12_refresh'); } if ($cmd=='menu13') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $prj=sql_getValues('edomiAdmin.project','*','edit=1'); if ($prj!==false) { ?>
			var n="<div id='<?echo $winId;?>-form1' class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Projektaktivierung</b></span><br><br>";
							n+="Das aktuelle Arbeitsprojekt wird aktiviert und somit als Live-Projekt verfügbar gemacht.";
<? if ($prj['live']==1) { ?>
							n+="<br><br><br><b>Remanentdaten des aktuellen Live-Projekts löschen</b>";
							n+="<table width='100%' border='0' cellpadding='3' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr>";
									n+="<td colspan='2'><hr></td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td width='50%'><div id='<?echo $winId;?>-fd1' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Kommunikationsobjekte</td>";
									n+="<td width='50%'><div id='<?echo $winId;?>-fd5' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Datenarchive</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td><div id='<?echo $winId;?>-fd2' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Szenen</td>";
									n+="<td><div id='<?echo $winId;?>-fd4' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Meldungsarchive</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td><div id='<?echo $winId;?>-fd3' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Anwesenheitssimulationen</td>";
									n+="<td><div id='<?echo $winId;?>-fd6' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Kameraarchive</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td><div id='<?echo $winId;?>-fd7' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Zeitschaltuhren</td>";
									n+="<td><div id='<?echo $winId;?>-fd8' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Anrufarchive</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td><div id='<?echo $winId;?>-fd9' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Terminschaltuhren</td>";
									n+="<td><div id='<?echo $winId;?>-fd10' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Aufzeichnungen des digitalen Videorekorders</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td><div id='<?echo $winId;?>-fd11' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> Logikbaustein-Variablen</td>";
									n+="<td>&nbsp;</td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td colspan='2'><hr></td>";
								n+="</tr>";
								n+="<tr>";
									n+="<td colspan='2'><div id='<?echo $winId;?>-fd0' data-type='4' data-value='0' data-list='|X|' class='control5small' style='width:15px; border-radius:15px;'></div> sämtliche Remantendaten löschen (das Live-Projekt wird vollständig ersetzt)</td>";
								n+="</tr>";
							n+="</table>";
<? } ?>
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
<? if ($prj['live']==1) { ?>
							n+="<div class='cmdButton' onClick='ajaxConfirm(\"<b>Soll das aktuelle Arbeitsprojekt wirklich aktiviert werden?</b><br><br>Je nach Auswahl werden ggf. Remanentdaten gelöscht!\",\"menu13_activateProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='width:70%; min-width:auto; border-color:#ff0000;'>Projekt aktivieren</div>";
<? } else { if (getLiveProjektId()===false) { ?>
							n+="<div class='cmdButton' onClick='ajaxConfirm(\"<b>Soll das aktuelle Arbeitsprojekt wirklich aktiviert werden?</b>\",\"menu13_activateProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='width:70%; min-width:auto; border-color:#ff0000;'>Projekt aktivieren</div>";
							n+="<input type='hidden' id='<?echo $winId;?>-fd0' data-type='1' value='0'></input>";
<? } else { ?>
							n+="<div class='cmdButton' onClick='ajaxConfirmSecure(\"<b>Soll das aktuelle Arbeitsprojekt wirklich aktiviert werden?</b><br><br>Das aktuelle Live-Projekt (mit sämtlichen Remanentdaten) wird vollständig gelöscht!\",\"menu13_activateProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",controlGetFormData(\"<?echo $winId;?>-form1\"));' style='width:70%; min-width:auto; border-color:#ff0000;'>Projekt aktivieren</div>";
							n+="<input type='hidden' id='<?echo $winId;?>-fd0' data-type='1' value='1'></input>";
<? } } ?>
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";

			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td class='controlEditInline' width='50%' valign='top'>";
							n+="<span style='padding:5px 0 5px 0;'><b>Aktuelles Arbeitsprojekt</b></span><br><br>";
							n+="<div class='controlEditInline' style='word-wrap:break-word; background:#ffffff;'><?ajaxEcho($prj['name']);?> <span class='id'><?echo $prj['id'];?></span> <?echo (($prj['live']==1)?"<span style='color:#ffffff; border-radius:3px; background:#80e000;'>&nbsp;LIVE&nbsp;</span>":'');?></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Projektaktivierung:</td><td><b><?echo ((isEmpty($prj['livedate']))?'niemals':sql_getDateTime($prj['livedate']));?></b></td></tr>";
							n+="</table><br>";
						n+="</td>";

						n+="<td style='border-right:1px solid #a0a0a0;'><div style='width:1px;'></div></td>";

						n+="<td class='controlEditInline' width='50%' valign='top'>";
							n+="<span style='padding:5px 0 5px 0;'><b>Aktuelles Live-Projekt</b></span><br><br>";
<? if ($prj['live']==1) { ?>
							n+="<div class='controlEditInline' style='word-wrap:break-word; background:#ffffff;'><?ajaxEcho($prj['name']);?> <span class='id'><?echo $prj['id'];?></span></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo ((isEmpty($prj['livedate']))?'niemals':sql_getDateTime($prj['livedate']));?></b></td></tr>";
							n+="</table><br>";	
<? } else { $tmp=getLiveProjektData(); if ($tmp===false) { ?>
							n+="<div class='controlEditInline' style='background:#d9d9d9;'>kein Live-Projekt vorhanden</div><br>";
<? } else { ?>
							n+="<div class='controlEditInline' style='word-wrap:break-word; background:#ffffff;'><?ajaxEcho($tmp['name']);?> <span class='id'><?echo $tmp['id'];?></span></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo ((isEmpty($tmp['livedate']))?'niemals':sql_getDateTime($tmp['livedate']));?></b></td></tr>";
							n+="</table><br>";
							n+="<div class='controlEditInline' style='color:#ff0000;'><b>Achtung</b><br>Das aktuelle Live-Projekt wird bei einer Aktivierung des Arbeitsprojekts vollständig gelöscht, sämtliche Einstellungen und Remanentdaten gehen verloren!</div>";
<? } } ?>
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
			controlInitAll("<?echo $winId;?>-form1");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Projektaktivierung</b></span><br><br>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt angelegt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu13_activateProject') { ?>
		var n="";
		n+="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
				n+="<tr>";
					n+="<td width='100%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Projektaktivierung vorbereiten</b></span><br><br>";
						n+="<div id='<?echo $winId;?>-status'></div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? $edomiStatus=getEdomiStatus(); if ($edomiStatus==1 || $edomiStatus==3) { clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/activation_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/activation_report.txt'); createInfoFile(MAIN_PATH.'/www/data/tmp/activation_options.txt',$phpdataArr+array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)); if ($phpdataArr[0]==1) {setSysInfo(2,15);} else {setSysInfo(2,14);} ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#e0e000 0%,#e0e000 100%,transparent 100%,transparent 100%);'>EDOMI neustarten...</div>";
			ajax("menu13_activateProjectRestarting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<span style='color:#ff0000;'><b>EDOMI ist nicht bereit zur Projektaktivierung &gt; EDOMI muss pausiert oder gestartet sein</b></span>";
<? } } if ($cmd=='menu13_activateProjectRestarting') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/activation_status.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $tmp=100-intval((100/300)*$phpdataArr[0]); ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#e0e000 0%,#e0e000 <?echo $tmp;?>%,transparent <?echo $tmp;?>%,transparent 100%);'>EDOMI neustarten...</div>";
			ajax("menu13_activateProjectRestarting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo $phpdataArr[0];?>");
<? } else { if (file_exists(MAIN_PATH.'/www/data/tmp/activation_status.txt')) { ?>
				var n="";
				n+="<div class='controlEditInline'>";
					n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
						n+="<tr>";
							n+="<td width='100%' valign='top'>";
								n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Projektaktivierung</b></span><br><br>";
								n+="<div id='<?echo $winId;?>-status'></div>";
							n+="</td>";
						n+="</tr>";
						n+="<tr>";
							n+="<td width='100%' valign='top'>";
								n+="<br><br><b>Details</b><br><br>";
								n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
							n+="</td>";
						n+="</tr>";
					n+="</table>";
				n+="</div>";
				document.getElementById("<?echo $winId;?>-edit").innerHTML=n;

				document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
				ajax("menu13_activateProjectWaiting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0"+AJAX_SEPARATOR1+"0");
<? } else { ?>
				document.getElementById("<?echo $winId;?>-status").innerHTML="<span style='color:#ff0000;'><b>EDOMI reagiert nicht auf die Anforderung zur Projektaktivierung (innerhalb des erwarteten Zeitraums)</b></span>";
<? } } } if ($cmd=='menu13_activateProjectWaiting') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/activation_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/activation_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("menu13_activateProjectWaiting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/activation_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/activation_report.txt'); if ($report[0]==-1 && $report[1]==-1) { ?>
					n+="<span style='color:#ff0000;'><b>Es ist kein Arbeitsprojekt verfügbar, daher konnte die Projektaktivierung nicht durchgeführt werden.</b></span><br><br>";
					n+="Das aktuelle Live-Projekt bleibt unverändert bestehen &gt; EDOMI ist pausiert";
<? } else if ($report[1]>0) { ?>
					n+="<span style='color:#ff0000;'><b>Es sind <?echo $report[1];?> Fehler aufgetreten, das Arbeitsprojekt kann nicht aktiviert werden.</b></span><br><br>";
					n+="<span style='color:#ff0000;'>Es ist kein Live-Projekt mehr verfügbar &gt; EDOMI ist pausiert</span>";
<? } else if ($report[1]==0) { ?>
					n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
						n+="<tr>";
							n+="<td width='70%' valign='top'>";
<? if ($report[0]>0) { ?>
								n+="<span style='color:#ff0000;'><b><?echo $report[0];?> Probleme sind aufgetreten &gt; EDOMI ist pausiert</b></span><br><br>";
								n+="EDOMI kann dennoch gestartet werden, jedoch können unerwartete Probleme während des Betriebs auftreten.";
<? } else { ?>
								n+="<b>Die Projektaktivierung wurde erfolgreich abgeschlossen &gt; EDOMI ist pausiert</b><br><br>";
								n+="Zum Fortfahren muss EDOMI gestartet oder der Server neugestartet werden.";
<? } ?>
							n+="</td>";
							n+="<td align='right' valign='bottom'>";
								n+="<div class='cmdButton' onClick='ajax(\"menu13_edomiStart\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>EDOMI starten</div><br>";
								n+="<div class='cmdButton' onClick='ajax(\"menu13_serverReboot\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Server neustarten</div>";
							n+="</td>";
						n+="</tr>";
					n+="</table>";
<? if ($report[0]==0) { if (global_liveAutoReboot) { ?>
							ajaxConfirm("Server wird in <?echo global_liveAutostart;?> Sekunden neugestartet...</b><br><br><div class='pbAnim' style='display:block; -webkit-transform-origin: 0 0; width:100%; height:2px; background:#e0e000; -webkit-animation-duration:<?echo global_liveAutostart;?>s;'></div>","menu13_autostartCancel","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","","none","Abbrechen");
							document.getElementById("<?echo $winId;?>").dataset.autostart=window.setTimeout(function(){ajax("menu13_serverReboot","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","");},<?echo (global_liveAutostart*1000);?>);
<? } else { ?>
							ajaxConfirm("EDOMI wird in <?echo global_liveAutostart;?> Sekunden gestartet...</b><br><br><div class='pbAnim' style='display:block; -webkit-transform-origin: 0 0; width:100%; height:2px; background:#e0e000; -webkit-animation-duration:<?echo global_liveAutostart;?>s;'></div>","menu13_autostartCancel","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","","none","Abbrechen");
							document.getElementById("<?echo $winId;?>").dataset.autostart=window.setTimeout(function(){ajax("menu13_edomiStart","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","");},<?echo (global_liveAutostart*1000);?>);
<? } } } showEditProjectName(); } else { ?>
				n+="<span style='color:#ff0000;'><b>Projektaktivierung gescheitert</b></span><br><br>";
				n+="Keine Rückmeldung des Aktivierungsprozesses erhalten (Timeout).";
<? } ?>
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } } if ($cmd=='menu13_autostartCancel') { ?>
		if (document.getElementById("<?echo $winId;?>").dataset.autostart) {clearTimeout(document.getElementById("<?echo $winId;?>").dataset.autostart);}
<? } if ($cmd=='menu13_edomiStart') { if (getEdomiStatus()==1) { setSysInfo(2,11); ?>
			gotoDesktop(0);
<? } } if ($cmd=='menu13_serverReboot') { if (getEdomiStatus()==1) { setSysInfo(2,13); ?>
			gotoDesktop(0);
<? } } if ($cmd=='menu15') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");
<? $prj=sql_getValues('edomiAdmin.project','*','edit=1'); if ($prj!==false) { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuaktivierung</b></span><br><br>";
							n+="Sämtliche Visualisierungen des aktuellen Arbeitsprojekts werden im laufenden Betrieb als Vorschau aktiviert.";
						n+="</td>";
<? if ($prj['live']==1) { ?>
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu15_activate\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%; min-width:auto; border-color:#ff0000;'>Visualisierungen aktivieren</div>";
						n+="</td>";
<? } ?>
					n+="</tr>";
				n+="</table>";
			n+="</div>";

			n+="<div class='controlEditInline' style='margin-top:5px;'>";
				n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td class='controlEditInline' width='50%' valign='top'>";
							n+="<span style='padding:5px 0 5px 0;'><b>Aktuelles Arbeitsprojekt</b></span><br><br>";
							n+="<div class='controlEditInline' style='word-wrap:break-word; background:#ffffff;'><?ajaxEcho($prj['name']);?> <span class='id'><?echo $prj['id'];?></span> <?echo (($prj['live']==1)?"<span style='color:#ffffff; border-radius:3px; background:#80e000;'>&nbsp;LIVE&nbsp;</span>":'');?></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Projektaktivierung:</td><td><b><?echo ((isEmpty($prj['livedate']))?'niemals':sql_getDateTime($prj['livedate']));?></b></td></tr>";
								n+="<tr><td align='right'>Visualisierungen:</td><td><b><?echo sql_getCount('edomiProject.editVisu','1=1');?></b></td></tr>";
							n+="</table>";
						n+="</td>";

						n+="<td style='border-right:1px solid #a0a0a0;'><div style='width:1px;'></div></td>";

						n+="<td class='controlEditInline' width='50%' valign='top'>";
							n+="<span style='padding:5px 0 5px 0;'><b>Aktuelles Live-Projekt</b></span><br><br>";
<? if ($prj['live']==1) { ?>
							n+="<div class='controlEditInline' style='word-wrap:break-word; background:#ffffff;'><?ajaxEcho($prj['name']);?> <span class='id'><?echo $prj['id'];?></span></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo ((isEmpty($prj['livedate']))?'niemals':sql_getDateTime($prj['livedate']));?></b></td></tr>";
								n+="<tr><td align='right'>Visualisierungen:</td><td><b><?echo sql_getCount('edomiLive.visu','1=1');?></b></td></tr>";
								n+="<tr><td align='right'>davon als Vorschau:</td><td><b><?echo sql_getCount('edomiLive.visu','preview=1');?></b></td></tr>";
							n+="</table>";	
<? } else { $tmp=getLiveProjektData(); if ($tmp===false) { ?>
							n+="<div class='controlEditInline' style='width:100%; background:#d9d9d9;'>kein Live-Projekt vorhanden</div><br>";
							n+="<div class='controlEditInline' style='color:#ff0000;'>Zunächst ist eine vollständige Projektaktivierung erforderlich.</div>";
<? } else { ?>
							n+="<div class='controlEditInline' style='width:100%; word-wrap:break-word; background:#ffffff;'><?ajaxEcho($tmp['name']);?> <span class='id'><?echo $tmp['id'];?></span></div><br>";
							n+="<table border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
								n+="<tr><td align='right'>Erstellt am:</td><td><b><?echo ((isEmpty($tmp['livedate']))?'niemals':sql_getDateTime($tmp['livedate']));?></b></td></tr>";
							n+="</table><br>";
							n+="<div class='controlEditInline' style='color:#ff0000;'>Das aktuelle Live-Projekt ist nicht mit dem aktuellen Arbeitsprojekt referenziert, daher ist zunächst eine vollständige Projektaktivierung erforderlich.</div>";
<? } } ?>
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuaktivierung</b></span><br><br>";
							n+="Es ist kein Arbeitsprojekt vorhanden. Zunächst muss ein neues Arbeitsprojekt angelegt oder ein bestehendes Arbeitsprojekt importiert und geöffnet werden.";
						n+="</td>";
						n+="<td align='right' valign='bottom'>";
							n+="<div class='cmdButton' onClick='ajax(\"menu7_deleteEditProject\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");' style='width:70%;'>Arbeitsprojekt erstellen</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu15_activate') { if (getEdomiStatus()==3 && checkLiveProjectData()) { ?>
			var n="";
			n+="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuaktivierung</b></span><br><br>";
							n+="<div id='<?echo $winId;?>-status'></div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr>";
						n+="<td width='100%' valign='top'>";
							n+="<br><br><b>Details</b><br><br>";
							n+="<div id='<?echo $winId;?>-log' class='controlList' style='padding:2px; width:100%; height:350px; line-height:1.5; border:none;'></div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
	
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 0%,transparent 0%,transparent 100%);'>0%</div>";
<? clearTmpLog(); deleteFiles(MAIN_PATH.'/www/data/tmp/activation_status.txt'); deleteFiles(MAIN_PATH.'/www/data/tmp/activation_report.txt'); createInfoFile(MAIN_PATH.'/www/data/tmp/activation_options.txt',$phpdataArr+array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)); queueCmd(1,11,0); ?>
			ajax("menu15_activateWaiting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","0");
<? } else { ?>
			var n="<div class='controlEditInline'>";
				n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";
					n+="<tr>";
						n+="<td width='70%' valign='top'>";
							n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Visuaktivierung</b></span><br><br>";
							n+="<span style='color:#ff0000;'><b>Aktivierung nicht möglich</b></span><br><br>";
							n+="EDOMI ist pausiert bzw. nicht gestartet, oder das aktuelle Arbeitsprojekt ist nicht als Live-Projekt aktiviert.";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
			n+="</div>";
			document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
<? } } if ($cmd=='menu15_activateWaiting') { if ((!file_exists(MAIN_PATH.'/www/data/tmp/activation_report.txt')) && $phpdataArr[0]<300) { sleep(1); $phpdataArr[0]++; $status=readInfoFile(MAIN_PATH.'/www/data/tmp/activation_status.txt'); if ($phpdataArr[1]!=$status[0]) { $phpdataArr[0]=0; $phpdataArr[1]=$status[0]; } ?>
			document.getElementById("<?echo $winId;?>-status").innerHTML="<div style='box-sizing:border-box; width:100%; text-align:center; display:inline-block; padding:3px; border:1px solid #a0a0a0; border-radius:2px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $status[0];?>%,transparent <?echo $status[0];?>%,transparent 100%);'><?echo $status[0];?>%</div>";
			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			scrollToEnd("<?echo $winId;?>-log");

			ajax("menu15_activateWaiting","<?echo $appId;?>","<?echo $winId;?>","<?echo $data;?>","<?echo implode(AJAX_SEPARATOR1,$phpdataArr);?>");
<? } else { ?>
			var n="";
<? if (file_exists(MAIN_PATH.'/www/data/tmp/activation_report.txt')) { $report=readInfoFile(MAIN_PATH.'/www/data/tmp/activation_report.txt'); if ($report[0]==-1 && $report[1]==-1) { ?>
					n+="<span style='color:#ff0000;'><b>Aktivierung nicht möglich</b></span><br><br>";
					n+="Es ist kein Arbeitsprojekt verfügbar, keine Visualisierung im aktuellen Live-Projekt vorhanden oder der Visu-Prozess reagiert nicht wie erwartet.";
<? } else { if (($report[0]+$report[1])>0) { ?>
						n+="<span style='color:#ff0000;'><b><?echo ($report[0]+$report[1]);?> Probleme/Fehler sind aufgetreten</b></span><br><br>";
						n+="Es können unerwartete Probleme während der Nutzung der Visualisierungen auftreten.";
<? } else { ?>
						n+="<b>Aktivierung erfolgreich abgeschlossen</b><br><br>";
						n+="Sämtliche Visualisierungen im Live-Projekt wurden als Vorschau aktiviert und neu gestartet.";
<? } } } else { ?>
					n+="<span style='color:#ff0000;'><b>Aktivierung gescheitert</b></span><br><br>";
					n+="Keine Rückmeldung des Aktivierungsprozesses erhalten (Timeout).";
<? } ?>			document.getElementById("<?echo $winId;?>-log").innerHTML="<?echoTmpLog();?>";
			document.getElementById("<?echo $winId;?>-status").innerHTML=n;
			scrollToEnd("<?echo $winId;?>-log");
<? } } if ($cmd=='menu14') { ?>
		appAll_menuSelect("<?echo $winId;?>","<?echo $winId;?>-<?echo $cmd;?>");
		document.getElementById("<?echo $winId;?>-help").dataset.helpid="<?echo $appId;?>-<?echo ltrim($cmd,'menu');?>";
		scrollToTop("<?echo $winId;?>-edit");

		var status="";
		var n="<div class='controlEditInline'>";
			n+="<table width='100%' border='0' cellpadding='2' cellspacing='0' style='table-layout:auto;'>";

				n+="<tr>";
					n+="<td width='70%' valign='top'>";
						n+="<span class='formTitel' style='padding:5px 0 5px 0;'><b>Datenbanken</b></span><br><br>";
						n+="<span id='<?echo $winId;?>-status'></span>";
					n+="</td>";
					n+="<td align='right' valign='bottom'>";
						n+="<div class='cmdButton' onClick='ajaxConfirmSecure(\"Eine automatische Reparatur kann beschädigte Datenbanken u.U. erfolgreich wiederherstellen, jedoch könnten vereinzelte Datensätze ggf. nicht mehr konsistent sein.<br><br>Durch die Reparatur im laufenden Betrieb von EDOMI können zudem Probleme auftreten, daher wird eine Reparatur beim Start von EDOMI empfohlen (dies kann in der Basis-Konfiguration aktiviert werden).\",\"menu14_repair\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\",\"\",\"Reparieren\");' style='width:70%;'>Reparieren & Optimieren</div>";
					n+="</td>";
				n+="</tr>";

				n+="<tr>";
					n+="<td colspan='2' width='100%' valign='top' style='padding-top:10px;'>";
						n+="<div class='controlList' style='width:100%; height:auto; border:none;'>";
							n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
								n+='<tr style="color:#a0a0a0;">';
									n+='<td>Datenbank</td>';
									n+='<td>Einträge</td>';
									n+='<td>Daten</td>';
									n+='<td>Index</td>';
									n+='<td>Aktualisierung</td>';
								n+='</tr>';
<? $tableCount=0; $tableErr=0; $ss1=sql_call("SHOW DATABASES LIKE 'edomi%'"); while ($db=sql_result($ss1)) { $nameDb=$db[key($db)]; $ss2=sql_call("SHOW TABLE STATUS IN ".$nameDb); while ($table=sql_result($ss2)) { if (strtoupper($table['Engine'])!='MEMORY') { $ss3=sql_call("CHECK TABLE ".$nameDb.".".$table['Name']); if ($status=sql_result($ss3)) { $tableCount++; } else { $status['Msg_text']='Table not found'; $tableErr++; } sql_close($ss3); if (strtoupper($status['Msg_text'])=='OK') { $ss3=sql_call("SHOW TABLE STATUS FROM ".$nameDb." WHERE name='".$table['Name']."'"); if ($tableInfo=sql_result($ss3)) { ?>
							n+="<tr>";
								n+='<td><?echo $nameDb.'.'.$table['Name'];?></td>';
								n+='<td><?echo $tableInfo['Rows'];?></td>';
								n+='<td><?printf("%01.3f",$tableInfo['Data_length']/1024/1024);?> MB</td>';
								n+='<td><?printf("%01.3f",$tableInfo['Index_length']/1024/1024);?> MB</td>';
								n+='<td><?echo sql_getDateTime($tableInfo['Update_time']);?></td>';
							n+='</tr>';
<? } sql_close($ss3); } else { ?>
						n+="<tr style='color:#ff0000;'>";
							n+='<td colspan="5"><b><?echo $nameDb.'.'.$table['Name'];?></b>: <?ajaxEcho($status['Msg_text']);?></td>';
						n+='</tr>';
<? $tableErr++; } } } sql_close($ss2); } sql_close($ss1); if ($tableErr>0) { ?>
			status="<span style='color:#ff0000;'><?echo $tableErr;?> von <?echo $tableCount;?> Datenbanken sind fehlerhaft!</span><br>Unter Umständen kann eine Reparatur das Problem beheben.";
<? } else { ?>
			status="Es wurden keine Probleme bei <?echo $tableCount;?> Datenbanken festgestellt.";
<? } ?>
							n+='</table>';
						n+="</div>";

					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-edit").innerHTML=n;
		document.getElementById("<?echo $winId;?>-status").innerHTML=status;
<? } if ($cmd=='menu14_repair') { $ss1=sql_call("SHOW DATABASES LIKE 'edomi%'"); while ($db=sql_result($ss1)) { $nameDb=$db[key($db)]; $ss2=sql_call("SHOW TABLE STATUS IN ".$nameDb); while ($table=sql_result($ss2)) { if (strtoupper($table['Engine'])!='MEMORY') { $ss3=sql_call("CHECK TABLE ".$nameDb.".".$table['Name']); if (!($status=sql_result($ss3))) {$status['Msg_text']='Err';} sql_close($ss3); if (strtoupper($status['Msg_text'])!='OK') { sql_call("REPAIR TABLE ".$nameDb.".".$table['Name']); } sql_call("OPTIMIZE TABLE ".$nameDb.".".$table['Name']); } } sql_close($ss2); } sql_close($ss1); cmd('menu14'); } } sql_disconnect(); function app103_projectStatisticsJS($mode) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; ?>
	n+='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="3" cellspacing="0">';
<? $anz1=sql_getCount(db_convertTableName($mode,'logicElement'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td><b>Logikbausteine</b></td>';
		n+='<td colspan="3"><b><?echo $anz1;?> Logikbausteine insgesamt</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'visu'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'visuPage'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Visualisierungen</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Seiten in <?echo $anz1;?> Visualisierungen</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.visuid) AS anz1 FROM ".db_convertTableName($mode,'visu')." AS a LEFT JOIN ".db_convertTableName($mode,'visuPage')." AS b ON b.visuid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td colspan="2"><?echo $n['anz1'];?></td>';
	n+='</tr>';
<? } sql_close($ss1); if ($mode==0) { $tmp=getFilesCount(MAIN_PATH."/www/data/project/visu/img/img-*.*"); } else { $tmp=getFilesCount(MAIN_PATH."/www/data/liveproject/visu/img/img-*.*"); } ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Visualisierungen: Bilddateien</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $tmp;?> Bilddateien insgesamt</b></td>';
	n+='</tr>';
<? if ($mode==0) { $tmp=getFilesCount(MAIN_PATH."/www/data/project/visu/etc/snd-*.*"); } else { $tmp=getFilesCount(MAIN_PATH."/www/data/liveproject/visu/etc/snd-*.*"); } ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Visualisierungen: Tondateien</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $tmp;?> Tondateien insgesamt</b></td>';
	n+='</tr>';
<? if ($mode==0) { $tmp=getFilesCount(MAIN_PATH."/www/data/project/visu/etc/font-*.*"); } else { $tmp=getFilesCount(MAIN_PATH."/www/data/liveproject/visu/etc/font-*.*"); } ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Visualisierungen: Schriftartdateien</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $tmp;?> Schriftartdateien insgesamt</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'ko'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'ko'),'gatyp=1'); $anz3=sql_getCount(db_convertTableName($mode,'ko'),'gatyp=2 AND id>=100'); $anz4=sql_getCount(db_convertTableName($mode,'ko'),'id<100'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Kommunikationsobjekte</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Kommunikationsobjekte insgesamt</b></td>';
	n+='</tr>';
	n+='<tr style="color:#797979;">';
		n+='<td>KNX</td>';
		n+='<td><?echo $anz2;?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
	n+='<tr style="color:#797979;">';
		n+='<td>Intern</td>';
		n+='<td><?echo $anz3;?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
	n+='<tr style="color:#797979;">';
		n+='<td>System</td>';
		n+='<td><?echo $anz4;?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'scene'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'sceneList'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Szenen</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Einträge in <?echo $anz1;?> Szenen</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'scene')." AS a LEFT JOIN ".db_convertTableName($mode,'sceneList')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'archivKo'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'archivKoData'),'1=1'); if ($mode==0) { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Datenarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Archive</b></td>';
		n+='</tr>';
<? } else { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Datenarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Einträge in <?echo $anz1;?> Archiven</b></td>';
		n+='</tr>';
<? } $ss1=sql_call("SELECT a.id,a.name,MIN(b.datetime) AS d1,MAX(b.datetime) AS d2,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'archivKo')." AS a LEFT JOIN ".db_convertTableName($mode,'archivKoData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo (($mode==1)?$n['anz1']:'-');?></td>';
<? if ($mode==1 && $n['anz1']>0) { ?>
			n+='<td><?echo date('d.m.Y/H:i:s',strtotime($n['d1']));?> - <?echo date('d.m.Y/H:i:s',strtotime($n['d2']));?></td>';
			n+="<td align='right'><div class='cmdButtonSmall' onClick='ajax(\"menu2_delete\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivKoData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Leeren</div>&nbsp;<div class='cmdButtonSmall' onClick='ajax(\"menu2_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivKoData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Download</div></td>";
<? } else { ?>
			n+='<td>&nbsp;</td>';
			n+='<td>&nbsp;</td>';
<? } ?>
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'archivMsg'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'archivMsgData'),'1=1'); if ($mode==0) { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Meldungsarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Archive</b></td>';
		n+='</tr>';
<? } else { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Meldungsarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Einträge in <?echo $anz1;?> Archiven</b></td>';
		n+='</tr>';
<? } $ss1=sql_call("SELECT a.id,a.name,MIN(b.datetime) AS d1,MAX(b.datetime) AS d2,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'archivMsg')." AS a LEFT JOIN ".db_convertTableName($mode,'archivMsgData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo (($mode==1)?$n['anz1']:'-');?></td>';
<? if ($mode==1 && $n['anz1']>0) { ?>
		n+='<td><?echo date('d.m.Y/H:i:s',strtotime($n['d1']));?> - <?echo date('d.m.Y/H:i:s',strtotime($n['d2']));?></td>';
		n+="<td align='right'><div class='cmdButtonSmall' onClick='ajax(\"menu2_delete\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivMsgData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Leeren</div>&nbsp;<div class='cmdButtonSmall' onClick='ajax(\"menu2_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivMsgData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Download</div></td>";
<? } else { ?>
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
<? } ?>
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'ip'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>HTTP/UDP/SHELL</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Befehle</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'ir'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>IR-Trans</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> IR-Befehle</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'cam'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Kameras</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Kameras</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'archivCam'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'archivCamData'),'1=1'); if ($mode==0) { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Kameraarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Archive</b></td>';
		n+='</tr>';
<? } else { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Kameraarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Bilder in <?echo $anz1;?> Archiven</b></td>';
		n+='</tr>';
<? } $ss1=sql_call("SELECT a.id,a.name,MIN(b.datetime) AS d1,MAX(b.datetime) AS d2,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'archivCam')." AS a LEFT JOIN ".db_convertTableName($mode,'archivCamData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo (($mode==1)?$n['anz1']:'-');?></td>';
<? if ($mode==1 && $n['anz1']>0) { ?>
		n+='<td><?echo date('d.m.Y/H:i:s',strtotime($n['d1']));?> - <?echo date('d.m.Y/H:i:s',strtotime($n['d2']));?></td>';
		n+="<td align='right'><div class='cmdButtonSmall' onClick='ajax(\"menu2_delete\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivCamData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Leeren</div>&nbsp;<div class='cmdButtonSmall' onClick='ajax(\"menu2_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivCamData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Download</div></td>";
<? } else { ?>
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
<? } ?>
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'sequence'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'sequenceCmdList'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Sequenzen</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Befehle in <?echo $anz1;?> Sequenzen</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'sequence')." AS a LEFT JOIN ".db_convertTableName($mode,'sequenceCmdList')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'macro'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'macroCmdList'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Makros</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Befehle in <?echo $anz1;?> Makros</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'macro')." AS a LEFT JOIN ".db_convertTableName($mode,'macroCmdList')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'timer'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'timerData'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Zeitschaltuhren</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> <?echo (($mode==0)?'vorgegebene':'');?> Schaltzeiten in <?echo $anz1;?> Zeitschaltuhren</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'timer')." AS a LEFT JOIN ".db_convertTableName($mode,'timerData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'agenda'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'agendaData'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Terminschaltuhren</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> <?echo (($mode==0)?'vorgegebene':'');?> Termine in <?echo $anz1;?> Terminschaltuhren</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'agenda')." AS a LEFT JOIN ".db_convertTableName($mode,'agendaData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'aws'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'awsData'),'1=1'); if ($mode==0) { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Anwesenheitssimulationen</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Anwesenheitssimulationen</b></td>';
		n+='</tr>';
<? } else { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Anwesenheitssimulationen</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Aufzeichnungen in <?echo $anz1;?> Anwesenheitssimulationen</b></td>';
		n+='</tr>';
<? } $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'aws')." AS a LEFT JOIN ".db_convertTableName($mode,'awsData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo (($mode==1)?$n['anz1']:'-');?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'phoneBook'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Telefonbuch</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Einträge</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'phoneCall'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Anruftrigger</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Anruftrigger</b></td>';
	n+='</tr>';
<? $anz1=sql_getCount(db_convertTableName($mode,'archivPhone'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'archivPhoneData'),'1=1'); if ($mode==0) { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Anrufarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Archive</b></td>';
		n+='</tr>';
<? } else { ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Anrufarchive</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Einträge in <?echo $anz1;?> Archiven</b></td>';
		n+='</tr>';
<? } $ss1=sql_call("SELECT a.id,a.name,MIN(b.datetime) AS d1,MAX(b.datetime) AS d2,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'archivPhone')." AS a LEFT JOIN ".db_convertTableName($mode,'archivPhoneData')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo (($mode==1)?$n['anz1']:'-');?></td>';
<? if ($mode==1 && $n['anz1']>0) { ?>
		n+='<td><?echo date('d.m.Y/H:i:s',strtotime($n['d1']));?> - <?echo date('d.m.Y/H:i:s',strtotime($n['d2']));?></td>';
		n+="<td align='right'><div class='cmdButtonSmall' onClick='ajax(\"menu2_delete\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivPhoneData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Leeren</div>&nbsp;<div class='cmdButtonSmall' onClick='ajax(\"menu2_download\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"edomiLive.archivPhoneData<?echo AJAX_SEPARATOR1;?><?echo $n['id'];?>\");'>Download</div></td>";
<? } else { ?>
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
<? } ?>
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'chart'),'1=1'); $anz2=sql_getCount(db_convertTableName($mode,'chartList'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Diagramme</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz2;?> Datenquellen in <?echo $anz1;?> Diagrammen</b></td>';
	n+='</tr>';
<? $ss1=sql_call("SELECT a.id,a.name,COUNT(b.targetid) AS anz1 FROM ".db_convertTableName($mode,'chart')." AS a LEFT JOIN ".db_convertTableName($mode,'chartList')." AS b ON b.targetid=a.id GROUP BY a.id ORDER by a.id"); while ($n=sql_result($ss1)) { ?>
	n+='<tr style="color:#797979;">';
		n+='<td style="max-width:250px; overflow-x:hidden;"><div><?ajaxEcho($n['name']);?> <span class="id"><?echo $n['id'];?></span></div></td>';
		n+='<td><?echo $n['anz1'];?></td>';
		n+='<td>&nbsp;</td>';
		n+='<td>&nbsp;</td>';
	n+='</tr>';
<? } sql_close($ss1); $anz1=sql_getCount(db_convertTableName($mode,'httpKo'),'1=1'); ?>
	n+='<tr class="trSpace">';
		n+='<td style="border-top:1px solid #e0e0e0;"><b>Fernzugriff</b></td>';
		n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?echo $anz1;?> Freigaben</b></td>';
	n+='</tr>';

<? if (!isEmpty(global_dvrPath) ) { $anz1=getFilesCount(global_dvrPath.'/cam-*-1.edomidvr'); $anz2=getFolderSize(global_dvrPath.'/cam-*-2.edomidvr')/(1024*1024*1024); $anz3=sql_getCount(db_convertTableName($mode,'cam'),'dvr=1'); ?>
		n+='<tr class="trSpace">';
			n+='<td style="border-top:1px solid #e0e0e0;"><b>Digitaler Videorekorder (DVR)</b></td>';
			n+='<td style="border-top:1px solid #e0e0e0;" colspan="3"><b><?printf("%01.2f",$anz2);?> GB Bilddaten (ca. <?echo $anz1;?> Stunden)</b></td>';
		n+='</tr>';
		n+='<tr style="color:#797979;">';
			n+='<td style="max-width:250px; overflow-x:hidden;">für DVR aktivierte Kameras</td>';
			n+='<td><?echo $anz3;?> Kameras</td>';
			n+='<td>&nbsp;</td>';
			n+='<td>&nbsp;</td>';
		n+='</tr>';
<? } ?>

n+='</table>';
<? } function app103_parseIni() { $data=array(); $blockData=false; $n=file(MAIN_PATH.'/edomi.ini'); for ($t=0; $t<count($n); $t++) { $line=trim($n[$t]); if (!isEmpty($line)) { if (substr($line,0,1)=='#') { $line=trim(ltrim($line,'#')); if (substr($line,0,5)=='=====') { if ($blockData===false) { $blockData=array(0,null,null,null); } else { $blockData=false; } } else if (substr($line,0,5)=='/////') { if ($blockData===false) { $blockData=array(1,null,null,null); } else { $data[]=$blockData; $blockData=false; } } else if (substr($line,0,5)=='-----') { if ($blockData===false) { $blockData=array(2,null,null,null); } else { $data[]=$blockData; $blockData=false; } } else { if ($blockData===false) { $blockData=array(3,null,null,null,$line); } else { $blockData[]=$line; } } } else { if ($blockData!==false && $blockData[0]==3) { $var=explode('=',$line,2); if (count($var)==2) { $blockData[1]=trim($var[0]); $blockData[2]=trim($var[1]); if (substr($blockData[2],0,1)=="'") { $blockData[3]=1; $blockData[2]=ltrim($blockData[2],"'"); $blockData[2]=rtrim($blockData[2],"'"); } else if (strtolower($blockData[2])=='true' || strtolower($blockData[2])=='false') { $blockData[3]=2; $blockData[2]=strtolower($blockData[2]); } else { $blockData[3]=3; } $data[]=$blockData; } } $blockData=false; } } } for ($t=0;$t<count($data);$t++) { if ($data[$t][1]=='global_version') {$data[$t]=array(null,null,null,null,null);} } if (count($data)==0) {$data=false;} return $data; } ?>