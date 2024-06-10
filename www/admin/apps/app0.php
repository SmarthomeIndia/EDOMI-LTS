<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); $adminAccount=checkAdminSid($sid,true); if ($cmd=='login' || $cmd=='refreshAll' || $adminAccount!==false) { cmd($cmd,$adminAccount); } else { ?>
	gotoDesktop(0);
<? } sql_disconnect(); function cmd($cmd,$adminAccount) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$clientDatetime,$sid,$vid; if ($cmd=='refreshAll') { if ($vid!=global_version) { ?>
			self.location.reload();
<? return; } if ($adminAccount===false) { $ss1=sql_call("SELECT id FROM edomiAdmin.user WHERE (id=1)"); if ($ss1===false) { ?>				
				ajaxDesktopError("refreshAll","0","",app0_widgetCurrentId,"");
<? } else { ?>
				if (document.getElementById("desktopDisc").dataset.timer) {clearTimeout(document.getElementById("desktopDisc").dataset.timer);}
				errorDesktop();
				document.getElementById("desktopMenu").style.display="none";
				document.getElementById("desktopDisc").style.display="none";
				jsLogin();
<? } } else { $procData1=false; $procData2=false; $procData3=false; $procData4=false; $procData5=false; $procData6=false; $procData7=false; $procData8=false; $edomiStatus=getEdomiStatus(); if ($edomiStatus>0) { $procData1=procStatus_getData(1); $procData2=procStatus_getData(2); $procData3=procStatus_getData(3); $procData4=procStatus_getData(4); $procData5=procStatus_getData(5); $procData6=procStatus_getData(6); $procData7=procStatus_getData(7); $procData8=procStatus_getData(8); } ?>
			document.getElementById("desktopDisc").dataset.locked="0";
			document.getElementById("desktopContent").style.display="block";
			document.getElementById("desktopControl").style.display="table-row";

			desktopInfoShow();
			desktopInfoAccount("<?ajaxEcho($adminAccount['login']);?>","<?ajaxEcho(global_serverIP);?>");
			desktopInfoHelp("0-0");

			if (accountTyp=="0") {
				document.getElementById("desktopMenu").style.display="block";
			} else {
				document.getElementById("desktopMenu").style.display="none";
			}
<? showStatus($adminAccount,$edomiStatus,$procData1,$procData2,$procData3,$procData4,$procData5,$procData6,$procData7,$procData8); updateWidgets($adminAccount,$dataArr[0],$edomiStatus,$procData1,$procData2,$procData3,$procData4,$procData5,$procData6,$procData7,$procData8); if ($adminAccount['typ']==0) {showEditProjectName();} ?>
			document.getElementById("desktopDisc").style.display="block";
			if (document.getElementById("desktopDisc").dataset.inited=="0") {
				if (document.getElementById("desktopDisc").dataset.timer) {clearTimeout(document.getElementById("desktopDisc").dataset.timer);}
				document.getElementById("desktopDisc").dataset.timer=window.setTimeout(function(){ajaxDesktop("refreshAll","0","",app0_widgetCurrentId,"");},<?echo global_adminRefresh;?>);
			} else {
				desktopShowStatus(-2);
				if (document.getElementById("desktopDisc").dataset.timer) {clearTimeout(document.getElementById("desktopDisc").dataset.timer);}
				document.getElementById("desktopContent").style.display="none";
				document.getElementById("desktopWidgets").style.display="none";
				desktopShowWidget(app0_widgetLogo);
			}
<? } } if ($cmd=='login') { if ($n=loginAdmin($phpdataArr[1],$phpdataArr[2])) { $sid=$n[0]; ?>
			sid="<?echo $n[0];?>";
			accountTyp="<?echo $n[1];?>";
			ajaxDesktop("refreshAll","0","",-1,"");
			closeWindow("<?echo $winId;?>");
<? } else { ?>
			document.getElementById("<?echo $winId;?>-fd1").value="";
			document.getElementById("<?echo $winId;?>-fd2").value="";
			document.getElementById("<?echo $winId;?>-fd1").focus();
			shakeObj("loginform");
<? } } if ($cmd=='logout') { logoutAdmin($sid); } if ($cmd=='visuLogout') { sql_call("UPDATE edomiLive.visuUserList SET logout=1 WHERE (id='".$phpdataArr[0]."' AND online=1)"); } if ($cmd=='restart') { if (getEdomiStatus()>=1) { ?>
			document.getElementById("desktopButtonRestart").style.webkitAnimation="animDesktopButtonRestart 0.5s infinite linear";
<? setSysInfo(2,12); createInfoFile(MAIN_PATH.'/www/data/tmp/restartadmin.txt',array('12')); } } if ($cmd=='start') { if (getEdomiStatus()==1 && getLiveProjektId() && checkLiveProjektValid()) { ?>
			document.getElementById("desktopButtonStart").style.webkitAnimation="animDesktopButtonStart 0.5s infinite linear";
<? setSysInfo(2,11); } } if ($cmd=='pause') { if (getEdomiStatus()>=2) { ?>
			document.getElementById("desktopButtonPause").style.webkitAnimation="animDesktopButtonPause 0.5s infinite linear";
<? setSysInfo(2,10); createInfoFile(MAIN_PATH.'/www/data/tmp/restartadmin.txt',array('10')); } } if ($cmd=='reboot') { if (getEdomiStatus()>0) {setSysInfo(2,13);} } if ($cmd=='stop') { if (getEdomiStatus()>0) {setSysInfo(2,22);} } if ($cmd=='shutdown') { if (getEdomiStatus()>0) {setSysInfo(2,23);} } if ($cmd=='resetErrors') { deleteFiles(MAIN_PATH.'/www/data/tmp/errorcount.txt'); } if ($cmd=='resetVisuPreview') { sql_call("UPDATE edomiLive.visu SET preview=0"); } if ($cmd=='desktopNotesOpen') { $ss1=sql_call("SELECT text FROM edomiProject.editProjectInfo WHERE (id=1)"); if ($n=sql_result($ss1)) { ?>
			document.getElementById('desktopNotesContent2').value="<?ajaxValue($n['text']);?>";
<? } else { ?>
			document.getElementById('desktopNotesContent2').value="";
<? } sql_close($ss1); } if ($cmd=='desktopNotesSave') { sql_call("UPDATE edomiProject.editProjectInfo SET text='".sql_encodeValue($phpdata)."' WHERE (id=1)"); ?>
		closeDesktopNotes();
<? } if ($cmd=='monsendgaWrite' || $cmd=='monsendgaRead') { if (checkLiveProjectData()) { ?>
			document.getElementById("monsendgaInfo").innerHTML="";
<? $monKo=$phpdataArr[1]; $monValue=$phpdataArr[2]; if ($monKo>0) { ?>
				document.getElementById("monsendgaInfo").style.display="inline-block";
<? $ss1=sql_call("SELECT id,name,ga,gatyp,value FROM edomiLive.RAMko WHERE (id='".$monKo."')"); if ($n=sql_result($ss1)) { if ($n['gatyp']==2) { if ($cmd=='monsendgaRead') { ?>
							document.getElementById("monsendgaInfo").innerHTML="<?ajaxEcho($n['name']);?> <span class='idGa<?echo $n['gatyp'];?>'><?echo $n['ga'];?></span> = <b><?ajaxEcho($n['value']);?></b>";
<? } else { sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['id'].",'".sql_encodeValue($monValue)."')"); ?>
							document.getElementById("monsendgaInfo").innerHTML="<?ajaxEcho($n['name']);?> <span class='idGa<?echo $n['gatyp'];?>'><?echo $n['ga'];?></span> &gt; <b><?ajaxEcho($monValue);?></b>";
<? } } else if ($n['gatyp']==1) { if ($cmd=='monsendgaRead') { sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid) VALUES (5,0,".$n['id'].")"); ?>
							document.getElementById("monsendgaInfo").innerHTML="<?ajaxEcho($n['name']);?> <span class='idGa<?echo $n['gatyp'];?>'><?echo $n['ga'];?></span> = <?ajaxEcho($n['value']);?> <span style='color:#000000; background:#e0e000;'>[Read-Request...]</span>";
<? } else { sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0,".$n['id'].",'".sql_encodeValue($monValue)."')"); ?>
							document.getElementById("monsendgaInfo").innerHTML="<?ajaxEcho($n['name']);?> <span class='idGa<?echo $n['gatyp'];?>'><?echo $n['ga'];?></span> &gt; <b><?ajaxEcho($monValue);?></b>";
<? } } } } } } } function showStatus($adminAccount,$edomiStatus,$procData1,$procData2,$procData3,$procData4,$procData5,$procData6,$procData7,$procData8) { global $global_procNames,$clientDatetime; if (checkClientServerDateTime($clientDatetime)==1) {$clockFG='#e00000';} else {$clockFG='#aaaaa0';} ?>
	document.getElementById("desktopStatus1").innerHTML='<div class="desktopStatus" style="color:<?echo $clockFG;?>;"><?echo date('d.m.Y');?> &middot; <?echo date('H:i:s');?></div>';
<? if ($edomiStatus>0) { ?>
		document.getElementById("desktopStatus0").innerHTML='<?echo getLiveProjectName();?>';
<? } else { ?>
		document.getElementById("desktopStatus0").innerHTML='';
<? } if ($edomiStatus>0 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false && $procData7!==false && $procData8!==false) { if ($procData2[0]>99) {$cpuColor='#e00000';} else {$cpuColor='#e0e000';} if ($procData2[1]>90) {$ramColor='#e00000';} else {$ramColor='#797970';} if ($procData2[2]>90) {$hddColor='#e00000';} else {$hddColor='#797970';} if ($edomiStatus>1 && $procData3[19]==2 && global_knxGatewayActive) { $showKnx=true; $knxSnd=$procData3[13]*100/global_knxMaxSendRate; $knxRcv=$procData3[15]*100/global_knxMaxSendRate; if ($knxSnd>90) {$knxSndColor='#e00000';} else if ($knxSnd>0) {$knxSndColor='#20e000';} else {$knxSndColor='#797970';} if ($knxRcv>90) {$knxRcvColor='#e00000';} else if ($knxRcv>0) {$knxRcvColor='#20e000';} else {$knxRcvColor='#797970';} } else { $showKnx=false; } ?>
		var n='<table border="0" cellspacing="2" cellpadding="0" style="table-layout:auto;">';
			n+='<tr align="center">';
				n+='<td>CPU</td>';
				n+='<td>RAM</td>';
				n+='<td>HDD</td>';
<? if ($showKnx) { ?>
				n+='<td style="color:#397900;">SND</td>';
				n+='<td style="color:#397900;">RCV</td>';
<? } ?>
			n+='</tr>';
			n+='<tr align="center">';
				n+='<td><div style="display:inline-block; width:25px; height:5px; border-radius:4px; margin-bottom:2px; background:-webkit-linear-gradient(left,<?echo $cpuColor;?> 0%,<?echo $cpuColor;?> <?echo intVal($procData2[0]);?>%,#393930 <?echo intVal($procData2[0]);?>%,#393930 100%);"></div></td>';
				n+='<td><div style="display:inline-block; width:25px; height:5px; border-radius:4px; margin-bottom:2px; background:-webkit-linear-gradient(left,<?echo $ramColor;?> 0%,<?echo $ramColor;?> <?echo $procData2[1];?>%,#393930 <?echo $procData2[1];?>%,#393930 100%);"></div></td>';
				n+='<td><div style="display:inline-block; width:25px; height:5px; border-radius:4px; margin-bottom:2px; background:-webkit-linear-gradient(left,<?echo $hddColor;?> 0%,<?echo $hddColor;?> <?echo $procData2[2];?>%,#393930 <?echo $procData2[2];?>%,#393930 100%);"></div></td>';
<? if ($showKnx) { ?>
				n+='<td><div style="display:inline-block; width:25px; height:5px; border-radius:4px; margin-bottom:2px; background:-webkit-linear-gradient(left,<?echo $knxSndColor;?> 0%,<?echo $knxSndColor;?> <?echo $knxSnd;?>%,#393930 <?echo $knxSnd;?>%,#393930 100%);"></div></td>';
				n+='<td><div style="display:inline-block; width:25px; height:5px; border-radius:4px; margin-bottom:2px; background:-webkit-linear-gradient(left,<?echo $knxRcvColor;?> 0%,<?echo $knxRcvColor;?> <?echo $knxRcv;?>%,#393930 <?echo $knxRcv;?>%,#393930 100%);"></div></td>';
<? } ?>
			n+='</tr>';
		n+='</table>';
		document.getElementById("desktopStatus3").innerHTML=n;
<? } else { ?>
		document.getElementById("desktopStatus3").innerHTML="";
<? } $tmp=getFileSize(MAIN_PATH.'/www/data/tmp/errorcount.txt'); if ($tmp>0) { ?>
		document.getElementById("desktopError1msg").innerHTML="<span style='color:#e00000;'><?echo $tmp;?> neue Fehler</span><br>im Fehler-Log";
		document.getElementById("desktopError1").style.display="inline-block";
<? if ($edomiStatus==3) { ?>
			document.getElementById("desktopError1btn").style.display="inline";
<? } else { ?>
			document.getElementById("desktopError1btn").style.display="none";
<? } } else { ?>
		document.getElementById("desktopError1").style.display="none";
<? } $tmp=sql_getCount('edomiLive.visu','preview=1'); if ($tmp>0) { ?>
		document.getElementById("desktopError2msg").innerHTML="<span style='color:#aaaaa0;'><?echo $tmp;?> Visualisierung<?echo (($tmp>1)?'en':'');?></span><br>als Vorschau";
		document.getElementById("desktopError2").style.display="inline-block";
<? if ($edomiStatus==3) { ?>
			document.getElementById("desktopError2btn").style.display="inline";
<? } else { ?>
			document.getElementById("desktopError2btn").style.display="none";
<? } } else { ?>
		document.getElementById("desktopError2").style.display="none";
<? } if ($edomiStatus>0 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false && $procData7!==false && $procData8!==false) { ?>
		var n='';
<? if ($edomiStatus==3) { if ($procData2[19]!=2) {$statusSysinfo='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[2].'</div>';} else {$statusSysinfo='';} if ($procData3[19]!=2 && global_knxGatewayActive) {$statusKnx ='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[3].'</div>';} else {$statusKnx ='';} if ($procData4[19]!=2) {$statusLogic='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[4].'</div>';} else {$statusLogic='';} if ($procData5[19]!=2) {$statusQueue='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[5].'</div>';} else {$statusQueue='';} if ($procData6[19]!=2 && global_phoneGatewayActive && global_phoneMonitorActive) {$statusPhone='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[6].'</div>';} else {$statusPhone='';} if ($procData7[19]!=2 && !isEmpty($procData7[19])) {$statusVisu ='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[7].'</div>';} else {$statusVisu ='';} if ($procData8[19]!=2 && !isEmpty($procData8[19])) {$statusDvr ='<div class="serverStatus0" style="width:60px; margin:2px;">'.$global_procNames[8].'</div>';} else {$statusDvr ='';} if ($statusKnx!='' || $statusLogic!='' || $statusQueue!='' || $statusPhone!='' || $statusSysinfo!='' || $statusVisu!='' || $statusDvr!='') { ?>
				n+='<div style="color:#797970; padding:3px;"><?echo $statusSysinfo?><?echo $statusKnx?><?echo $statusLogic?><?echo $statusQueue?><?echo $statusPhone?><?echo $statusVisu?><?echo $statusDvr?></div>';
<? } } if (getSysInfo(3)==1) { ?>
			n+='<div style="margin-bottom:1px; padding:5px; color:#ffffff; background:#e00000;">Unerwarteter Reboot &gt; Log-Dateien überprüfen!</div>';
<? } if (getSysInfo(4)>0) { ?>
			n+='<div style="margin-bottom:1px; padding:5px; color:#ffffff; background:#e00000;">FATALERROR in LBS <?echo getSysInfo(4);?> &gt; Fehler-Log überprüfen!</div>';
<? } if (global_logicLoopMax>0 && $procData4[10]>global_logicLoopMax) { ?>
			n+='<div style="margin-bottom:1px; padding:5px; color:#ffffff; background:#e00000;">max. LBS-Iterationen erreicht (<?echo global_logicLoopMax;?>) &gt; Logik-Schleife?</div>';
<? } if ($edomiStatus>=2 && global_logLogicEnabled>0) { require(MAIN_PATH."/main/include/php/logicmonitor_config.php"); if (defined('logicMonitor_enabled') && logicMonitor_enabled) { ?>
				n+='<div style="margin-bottom:1px; padding:5px;"><span style="margin-bottom:1px; border-radius:2px; padding:5px; color:#000000; background:#e0e000;">Logikmonitor ist aktiviert</span></div>';
<? } } ?>
		document.getElementById("desktopStatus").innerHTML=n;
<? } else { ?>
		document.getElementById("desktopStatus").innerHTML="";
<? } if (!file_exists(MAIN_PATH.'/www/data/tmp/restartadmin.txt')) { ?>
		document.getElementById("desktopButtonRestart").style.webkitAnimation="none";
		document.getElementById("desktopButtonPause").style.webkitAnimation="none";
<? } if ($edomiStatus==0) { ?>
		document.getElementById("desktopButtonRestart").style.webkitAnimation="none";
		document.getElementById("desktopButtonStart").style.webkitAnimation="none";
		document.getElementById("desktopButtonPause").style.webkitAnimation="none";
		document.getElementById("desktopDisc").dataset.locked="1";
		document.getElementById("desktopControl").style.display="none";
		desktopShowStatus(0);
		document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Projektaktivierung</span><br>Arbeitsprojekt wird aktiviert...";
<? } else if ($edomiStatus==1) { if (checkLiveProjektValid()) { ?>
			desktopShowStatus(1);
			document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Pausiert</span><br>Warten auf Startbefehl";
<? } else { ?>
			desktopShowStatus(1);
			document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Pausiert</span><br><span style='color:#f0e000;'>Projektaktivierung erforderlich!</span>";
<? } } else if ($edomiStatus==2) { ?>
		desktopShowStatus(2);
		document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Initialisierung</span><br>Start vorbereiten...";
<? } else if ($edomiStatus==3) { ?>
		document.getElementById("desktopButtonStart").style.webkitAnimation="none";
		desktopShowStatus(3);
		document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Gestartet</span><br>seit <?echo str_replace(' ',' &middot; ',$procData1[0]);?> Uhr";
<? } else { ?>
		document.getElementById("desktopButtonRestart").style.webkitAnimation="none";
		document.getElementById("desktopButtonStart").style.webkitAnimation="none";
		document.getElementById("desktopButtonPause").style.webkitAnimation="none";
		document.getElementById("desktopDisc").dataset.locked="1";
		document.getElementById("desktopControl").style.display="none";
		desktopShowStatus(-1);
		document.getElementById("desktopStatus2").innerHTML="<span style='color:#aaaaa0;'>Beendet</span><br>Kein Status verfügbar";
<? } } function updateWidgets($adminAccount,$widgetId,$edomiStatus,$procData1,$procData2,$procData3,$procData4,$procData5,$procData6,$procData7,$procData8) { global $global_procNames,$dataArr; if ($widgetId==0) { if ($edomiStatus>0 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false && $procData7!==false && $procData8!==false) { ?>
			if (!document.getElementById("desktopWidget_list0")) {
				var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
					n+='<tr valign="middle" height="1%">';
						n+="<td><div class='app0_cmdButton' onClick='desktopWidgetChartReset(true);' style='width:100%;'>Statistik zurücksetzen</div></td>";
					n+='</tr>';
					n+='<tr valign="middle">';
						n+='<td align="center" id="desktopWidget_list0">';
							n+='<table border="0" cellspacing="5" cellpadding="5" style="color:#a9a9a9;">';
								n+='<tr valign="middle">';
									n+='<td bgcolor="#292929">SYSINFO: CPU<br><canvas id="desktopWidgetChart-c0" style="width:100px; height:50px; color:#e0e000;"></canvas></td>';
									n+='<td bgcolor="#292929">SYSINFO: Load<br><canvas id="desktopWidgetChart-c11" style="width:100px; height:50px; color:#e0e000;"></canvas></td>';
									n+='<td bgcolor="#292929">SYSINFO: RAM<br><canvas id="desktopWidgetChart-c1" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">SYSINFO: PHP<br><canvas id="desktopWidgetChart-c2" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
								n+='</tr>';
							n+="<tr align='left'>";
								n+="<td colspan='4'><div style='width:1px; height:1px;'></div></td>";
							n+="</tr>";
								n+='<tr valign="middle">';
									n+='<td bgcolor="#292929">LOGIC: Queued<br><canvas id="desktopWidgetChart-c4" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">LOGIC: Running<br><canvas id="desktopWidgetChart-c6" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">QUEUE: Queued<br><canvas id="desktopWidgetChart-c8" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">QUEUE: Running<br><canvas id="desktopWidgetChart-c7" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
								n+='</tr>';
								n+='<tr valign="middle">';
									n+='<td bgcolor="#292929">KNX: Queued<br><canvas id="desktopWidgetChart-c5" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">KNX: Total/s<br><canvas id="desktopWidgetChart-c3" style="width:100px; height:50px; color:#80e000;"></canvas></td>';
									n+='<td bgcolor="#292929">KNX: Send/s<br><canvas id="desktopWidgetChart-c9" style="width:100px; height:50px; color:#80e000;"></canvas></td>';
									n+='<td bgcolor="#292929">KNX: Receive/s<br><canvas id="desktopWidgetChart-c10" style="width:100px; height:50px; color:#80e000;"></canvas></td>';
								n+='</tr>';
								n+='<tr valign="middle">';
									n+='<td bgcolor="#292929">VISU: Item/s<br><canvas id="desktopWidgetChart-c12" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">VISU: Trigger/s<br><canvas id="desktopWidgetChart-c13" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">VISU: Send kb/s<br><canvas id="desktopWidgetChart-c14" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
									n+='<td bgcolor="#292929">VISU: Receive kb/s<br><canvas id="desktopWidgetChart-c15" style="width:100px; height:50px; color:#e0e0e0;"></canvas></td>';
								n+='</tr>';
							n+='</table>';
						n+='</td>';
					n+='</tr>';
				n+='</table>';
				document.getElementById("desktopWidgets").innerHTML=n;
			}
<? } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } if ($edomiStatus>0 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false && $procData7!==false && $procData8!==false) { ?>
		desktopWidgetChartInsertValue(0,	<?if ($procData2[19]==2 && !isEmpty($procData2[0])) {echo $procData2[0];} else {echo 'null';}?>,"%",false);
		desktopWidgetChartInsertValue(11,	<?if ($procData2[19]==2 && !isEmpty($procData2[3])) {echo $procData2[3];} else {echo 'null';}?>,"",true);
		desktopWidgetChartInsertValue(1,	<?if ($procData2[19]==2 && !isEmpty($procData2[1])) {echo $procData2[1];} else {echo 'null';}?>,"%",false);
		desktopWidgetChartInsertValue(2,	<?if ($procData2[19]==2 && !isEmpty($procData2[6])) {echo $procData2[6];} else {echo 'null';}?>,"",true);

		desktopWidgetChartInsertValue(4,	<?if ($procData4[19]==2 && !isEmpty($procData4[2])) {echo $procData4[2];} else {echo 'null';}?>,"",true);
		desktopWidgetChartInsertValue(6,	<?if ($procData4[19]==2 && !isEmpty($procData4[4])) {echo $procData4[4];} else {echo 'null';}?>,"",true);
		desktopWidgetChartInsertValue(8,	<?if ($procData5[19]==2 && !isEmpty($procData5[0])) {echo $procData5[0];} else {echo 'null';}?>,"",true);
		desktopWidgetChartInsertValue(7,	<?if ($procData5[19]==2 && !isEmpty($procData5[1])) {echo $procData5[1];} else {echo 'null';}?>,"",true);

		desktopWidgetChartInsertValue(5,	<?if ($procData3[19]==2 && global_knxGatewayActive && !isEmpty($procData3[0])) {echo $procData3[0];} else {echo 'null';}?>,"",true);
		desktopWidgetChartInsertValue(3,	<?if ($procData3[19]==2 && global_knxGatewayActive && !isEmpty($procData3[13]) && !isEmpty($procData3[15])) {printf("%01.2f",$procData3[13]+$procData3[15]);} else {echo 'null';}?>,"/s",true);
		desktopWidgetChartInsertValue(9,	<?if ($procData3[19]==2 && global_knxGatewayActive && !isEmpty($procData3[13])) {printf("%01.2f",$procData3[13]);} else {echo 'null';}?>,"/s",true);
		desktopWidgetChartInsertValue(10,	<?if ($procData3[19]==2 && global_knxGatewayActive && !isEmpty($procData3[15])) {printf("%01.2f",$procData3[15]);} else {echo 'null';}?>,"/s",true);

		desktopWidgetChartInsertValue(12,	<?if ($procData7[19]==2) {echo $procData7[3];} else {echo 'null';}?>,"/s",true);
		desktopWidgetChartInsertValue(13,	<?if ($procData7[19]==2) {echo $procData7[4];} else {echo 'null';}?>,"/s",true);
		desktopWidgetChartInsertValue(14,	<?if ($procData7[19]==2) {printf("%01.2f",$procData7[6]/1024);} else {echo 'null';}?>,"/s",true);
		desktopWidgetChartInsertValue(15,	<?if ($procData7[19]==2) {printf("%01.2f",$procData7[7]/1024);} else {echo 'null';}?>,"/s",true);
<? } if ($widgetId==1) { if ($edomiStatus>0 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false && $procData7!==false && $procData8!==false) { if ($edomiStatus==3) {$status1="<div class='serverStatus2'>".$global_procNames[1]."</div>";} else if ($edomiStatus==2 || $edomiStatus==1) {$status1="<div class='serverStatus1'>".$global_procNames[1]."</div>";} else {$status1="<div class='serverStatus0'>".$global_procNames[1]."</div>";} if ($procData2[19]==2) {$status2="<div class='serverStatus2'>".$global_procNames[2]."</div>";} else if ($procData2[19]==1) {$status2="<div class='serverStatus1'>".$global_procNames[2]."</div>";} else {$status2="<div class='serverStatus0'>".$global_procNames[2]."</div>";} if ($procData3[19]==2) {$status3="<div class='serverStatus2'>".$global_procNames[3]."</div>";} else if ($procData3[19]==1) {$status3="<div class='serverStatus1'>".$global_procNames[3]."</div>";} else if (global_knxGatewayActive) {$status3="<div class='serverStatus0'>".$global_procNames[3]."</div>";} else {$status3="<div class='serverStatus00'>".$global_procNames[3]."</div>";} if ($procData4[19]==2) {$status4="<div class='serverStatus2'>".$global_procNames[4]."</div>";} else if ($procData4[19]==1 || $procData4[19]==3) {$status4="<div class='serverStatus1'>".$global_procNames[4]."</div>";} else {$status4="<div class='serverStatus0'>".$global_procNames[4]."</div>";} if ($procData5[19]==2) {$status5="<div class='serverStatus2'>".$global_procNames[5]."</div>";} else if ($procData5[19]==1) {$status5="<div class='serverStatus1'>".$global_procNames[5]."</div>";} else {$status5="<div class='serverStatus0'>".$global_procNames[5]."</div>";} if ($procData6[19]==2) {$status6="<div class='serverStatus2'>".$global_procNames[6]."</div>";} else if ($procData6[19]==1) {$status6="<div class='serverStatus1'>".$global_procNames[6]."</div>";} else if (global_phoneGatewayActive && global_phoneMonitorActive) {$status6="<div class='serverStatus0'>".$global_procNames[6]."</div>";} else {$status6="<div class='serverStatus00'>".$global_procNames[6]."</div>";} if ($procData7[19]==2) {$status7="<div class='serverStatus2'>".$global_procNames[7]."</div>";} else if ($procData7[19]==1) {$status7="<div class='serverStatus1'>".$global_procNames[7]."</div>";} else {$status7="<div class='serverStatus00'>".$global_procNames[7]."</div>";} if ($procData8[19]==2) {$status8="<div class='serverStatus2'>".$global_procNames[8]."</div>";} else if ($procData8[19]==1) {$status8="<div class='serverStatus1'>".$global_procNames[8]."</div>";} else {$status8="<div class='serverStatus00'>".$global_procNames[8]."</div>";} ?>
			var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
				n+="<tr align='center' height='1%'>";
					n+="<td><?echo $status1;?></td>";
				n+="</tr>";
				n+="<tr valign='middle'>";
					n+="<td align='center'>";

						n+="<table width='90%' border='0' cellspacing='5' cellpadding='2'>";
							n+="<tr align='center' valign='top' height='1%'>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status2;?></td></tr>";
<? if ($procData2[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData2[0]>99) {echo '1';}?>'>CPU&nbsp;<span style='float:right;'><?echo $procData2[0];?>% (<?echo $procData2[10];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData2[1]>90) {echo '1';}?>'>RAM&nbsp;<span style='float:right;'><?echo $procData2[1];?>% (<?echo $procData2[11];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData2[2]>90) {echo '1';}?>'>HDD&nbsp;<span style='float:right;'><?echo $procData2[2];?>% (<?echo $procData2[12];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Load&nbsp;<span style='float:right;'><?echo $procData2[3];?> (<?echo $procData2[13];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData2[6]>100) {echo '1';}?>'>PHP&nbsp;<span style='float:right;'><?echo $procData2[6];?> (<?echo $procData2[16];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData2[7]>100) {echo '1';}?>'>HTTP&nbsp;<span style='float:right;'><?echo $procData2[7];?> (<?echo $procData2[17];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>DB-Tables&nbsp;<span style='float:right;'><?echo $procData2[8];?> (<?echo $procData2[18];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData2[20];?> MB</span></td></tr>";
<? } ?>
									n+="</table>";
								n+="</td>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status4;?></td></tr>";
<? if ($procData4[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData4[2]>100) {echo '1';}?>'>Queued&nbsp;<span style='float:right;'><?echo $procData4[2];?> (<?echo $procData4[12];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Triggered&nbsp;<span style='float:right;'><?echo $procData4[0];?> (<?echo $procData4[10];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Running&nbsp;<span style='float:right;'><?echo $procData4[4];?> (<?echo $procData4[14];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData4[1]!=$procData4[11]) {echo '2';}?>'>Initialized&nbsp;<span style='float:right;'><?echo $procData4[1];?>/<?echo $procData4[11];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Exec-Scripts&nbsp;<span style='float:right;'><?echo $procData4[6];?> (<?echo $procData4[16];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Exec-Queue&nbsp;<span style='float:right;'><?echo $procData4[5];?> (<?echo $procData4[15];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData4[20];?> MB</span></td></tr>";
<? } ?>
									n+="</table>";
								n+="</td>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status5;?></td></tr>";
<? if ($procData5[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData5[0]>10) {echo '1';}?>'>Queued&nbsp;<span style='float:right;'><?echo $procData5[0];?> (<?echo $procData5[10];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Running&nbsp;<span style='float:right;'><?echo $procData5[1];?> (<?echo $procData5[11];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData5[20];?> MB</span></td></tr>";
<? } ?>
									n+="</table>";
								n+="</td>";
							n+="</tr>";
			
							n+="<tr align='center' valign='top' height='1%'>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status3;?></td></tr>";
<? if ($procData3[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData3[9]==0) {echo '1';}?>'>Connection&nbsp;<span style='float:right;'><?echo (($procData3[9]>0)?'ok':'not connected');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData3[8]>1) {echo '1';}?>'>Connections&nbsp;<span style='float:right;'><?echo $procData3[8];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData3[0]>100) {echo '1';}?>'>Queued&nbsp;<span style='float:right;'><?echo $procData3[0];?> (<?echo $procData3[10];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if (($procData3[13]*100/global_knxMaxSendRate)>90) {echo '1';}?>'>Send/s&nbsp;<span style='float:right;'><?printf("%01.2f",$procData3[13]);?>/s</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if (($procData3[15]*100/global_knxMaxSendRate)>90) {echo '1';}?>'>Receive/s&nbsp;<span style='float:right;'><?printf("%01.2f",$procData3[15]);?>/s</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Send&nbsp;<span style='float:right;'><?echo number_format($procData3[3],0,'','.');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Receive&nbsp;<span style='float:right;'><?echo number_format($procData3[5],0,'','.');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Send ok&nbsp;<span style='float:right;'><?echo number_format($procData3[4],0,'','.');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo<?if ($procData3[7]>0) {echo '1';}?>'>Error: Connect&nbsp;<span style='float:right;'><?echo $procData3[7];?></span></td></tr>";
<? if (global_knxUnknownGA & 1) { ?>
										n+="<tr align='left'><td class='serverInfo'>Error: GA&nbsp;<span style='float:right;'><?echo number_format($procData3[2],0,'','.');?></span></td></tr>";
<? } ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData3[6]>0) {echo '1';}?>'>Error: Snd/Rcv&nbsp;<span style='float:right;'><?echo $procData3[6];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Error: Data&nbsp;<span style='float:right;'><?echo $procData3[1];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData3[20];?> MB</span></td></tr>";
<? } ?>
									n+="</table>";
								n+="</td>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status7;?></td></tr>";
<? if ($procData7[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData7[0]==0) {echo '1';}?>'>Connection&nbsp;<span style='float:right;'><?echo (($procData7[0]>0)?'ok':'not connected');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Online&nbsp;<span style='float:right;'><?echo $procData7[1];?> (<?echo $procData7[11];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Queued&nbsp;<span style='float:right;'><?echo $procData7[8];?> (<?echo $procData7[18];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>KO&nbsp;<span style='float:right;'><?echo $procData7[9];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Page/s&nbsp;<span style='float:right;'><?echo $procData7[5];?>/s (<?echo $procData7[15];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Refresh/s&nbsp;<span style='float:right;'><?echo $procData7[2];?>/s (<?echo $procData7[12];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Item/s&nbsp;<span style='float:right;'><?echo $procData7[3];?>/s (<?echo $procData7[13];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Trigger/s&nbsp;<span style='float:right;'><?echo $procData7[4];?>/s (<?echo $procData7[14];?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Send kb/s&nbsp;<span style='float:right;'><?printf("%01.1f",$procData7[6]/1024);?>/s (<?printf("%01.1f",$procData7[16]/1024);?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Receive kb/s&nbsp;<span style='float:right;'><?printf("%01.1f",$procData7[7]/1024);?>/s (<?printf("%01.1f",$procData7[17]/1024);?>)</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData7[20];?> MB</span></td></tr>";
<? } ?>
									n+="</table>";
								n+="</td>";
								n+="<td width='33%' bgcolor='#292929'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr><td><?echo $status6;?></td></tr>";
<? if ($procData6[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData6[0]==0) {echo '1';}?>'>Connection&nbsp;<span style='float:right;'><?echo (($procData6[0]>0)?'ok':'not connected');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Incomming&nbsp;<span style='float:right;'><?echo $procData6[1];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Outgoing&nbsp;<span style='float:right;'><?echo $procData6[2];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData6[20];?> MB</span></td></tr>";
<? } ?>
										n+="<tr><td><?echo $status8;?></td></tr>";
<? if ($procData8[19]>0) { ?>
										n+="<tr align='left'><td class='serverInfo<?if ($procData8[0]==0) {echo '1';}?>'>Status&nbsp;<span style='float:right;'><?echo (($procData8[0]>0)?'ok':'not ready');?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Cameras&nbsp;<span style='float:right;'><?echo $procData8[2];?>/<?echo $procData8[1];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Cameras Error&nbsp;<span style='float:right;'><?echo $procData8[4];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Frames/h&nbsp;<span style='float:right;'><?echo $procData8[3];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>Frames Error&nbsp;<span style='float:right;'><?echo $procData8[5];?></span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>HDD&nbsp;<span style='float:right;'><?echo $procData8[6];?>%</span></td></tr>";
										n+="<tr align='left'><td class='serverInfo'>PROC-RAM&nbsp;<span style='float:right;'><?echo $procData8[20];?> MB</span></td></tr>";
<? } ?>

									n+="</table>";
								n+="</td>";
							n+="</tr>";
						n+="</table>";

					n+="</td>";
				n+="</tr>";
			n+="</table>";
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center">'+n+'</td></tr></table>';
<? } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } if ($widgetId==2) { if ($edomiStatus>1 && sql_tableExists('edomiLive.RAMlivemon') && checkLiveProjectData() && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false) { ?>
			if (!document.getElementById("desktopWidget_list2")) {
				var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
					n+="<tr id='monsendga' valign='middle' height='1%'>";
						n+="<td align='center'>";
								n+="<div id='monsendgaform'>";
									n+="<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
										n+="<tr>";
											n+="<td width='35%' style='padding-right:3px;'><div id='monsendgaform-fd1' data-type='1000' data-root='30' data-value='0' data-options='typ=4;reset=0' class='app0_cmdInput' style='width:100%; cursor:pointer; white-space:nowrap; overflow:hidden; line-height:18px; vertical-align:bottom;'>&nbsp;</div></td>";
											n+="<td width='15%' style='padding-right:3px;'><div class='app0_cmdButton' onClick='ajax(\"monsendgaRead\",0,\"\",\"\",controlGetFormData(\"monsendgaform\"));' style='width:100%;'>Lesen</div></td>";
											n+="<td width='35%' style='padding-right:3px;'><input type='text' id='monsendgaform-fd2' data-type='1' value='' placeholder='Wert' class='app0_cmdInput' style='width:100%;'></input></td>";
											n+="<td width='15%'><div class='app0_cmdButton' onClick='ajax(\"monsendgaWrite\",0,\"\",\"\",controlGetFormData(\"monsendgaform\"));' style='width:100%;'>Schreiben</div></td>";
										n+="</tr>";
									n+="</table>";
									n+="<div id='monsendgaInfo' style='display:none; box-sizing:border-box; width:100%; background:#c0c0c0; color:#000000; border-radius:3px; margin-bottom:1px; padding:2px; border:1px solid #343434;'></div>";
								n+="</div>";
						n+="</td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td align='center'>";
							n+="<div style='position:relative; height:100%;'>";
								n+="<div id='desktopWidget_list2' data-paused='0' onClick='desktopWidgetToggelPause(this);' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto; font-size:9px; color:#a0a0a0; cursor:pointer;'></div>";
							n+="</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
				document.getElementById("desktopWidgets").innerHTML=n;
				controlInitAll("monsendgaform");
			}
			
			var n="";
<? $ss1=sql_call("SELECT * FROM edomiLive.RAMlivemon WHERE (datetime IS NOT NULL) ORDER BY ts DESC"); while ($n=sql_result($ss1)) { $info=''; $n['ganame']=ajaxEncode($n['ganame']); $n['gavalue']=ajaxEncode($n['gavalue']); if ($n['gamode']==0) {$info="<span style='color:#e0e000;'>REQ ?</span>"; $n['gavalue']="<span style='color:#e0e000;'>(Read-Request)</span>";} if ($n['gamode']==1) {$info="<span style='color:#80e000;'>RES &gt;</span>";} if ($n['gamode']==2) {$info='WRITE';} if ($n['gamode']==-1) {$info='WRITE'; $n['gavalue']="<span style='color:#ffffff; background:#ff0000;'>Ungültiger Wert: ".$n['gavalue']."</span>";} if ($n['gaid']!=0) {$n['ganame'].=" (".$n['gaid'].")";} else {$n['ganame']="<span style='color:#ff0000;'>Unbekannte Gruppenadresse!</span>";} ?>
				n+="<tr align='left'>";
					n+="<td colspan='2'><?echo sql_getDateTime($n['datetime']);?></td>";
					n+="<td><?echo $n['ms'];?></td>";
					n+="<td style='max-width:300px; overflow:hidden;'><?echo $n['ganame'];?></td>";
				n+="</tr>";
				n+="<tr align='left'>";
					n+="<td><?echo $n['pa'];?></td>";
					n+="<td><span class='idGa1'><?ajaxEcho($n['ga']);?></span></td>";
					n+="<td><?echo $info;?></td>";
					n+="<td style='max-width:300px; overflow:hidden;'><?echo $n['gavalue'];?></td>";
				n+="</tr>";
				n+="<tr><td colspan='4'><div style='width:100%; height:1px; background:#494940;'></div></td></tr>";
<? } ?>
			if (n!="") {
				if (document.getElementById("desktopWidget_list2")) {
					if (document.getElementById("desktopWidget_list2").dataset.paused!="1") {
						document.getElementById("desktopWidget_list2").innerHTML="<table width='100%' border='0' cellspacing='0' cellpadding='2' style='color:#ffffff; table-layout:auto;'>"+n+"</table>";
					}
				}
			} else {
				document.getElementById("desktopWidget_list2").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">keine Daten verfügbar</div></td></tr></table>';
			}
<? if ($edomiStatus==3) { ?>
				if (document.getElementById("monsendga")) {document.getElementById("monsendga").style.display='table-row';}
<? } else { ?>
				if (document.getElementById("monsendga")) {document.getElementById("monsendga").style.display='none';}
<? } } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } if ($widgetId==4) { if ($edomiStatus>1 && sql_tableExists('edomiLive.RAMko') && checkLiveProjectData() && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false) { ?>
			if (!document.getElementById("desktopWidget_list4")) {
				var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
					n+="<tr valign='middle' height='1%'>";
						n+="<td align='center'>";
							n+="<table width='100%' border='0' cellspacing='0' cellpadding='3'>";
								n+="<tr style='color:#000000; background:#797979'>";
									n+='<td width="10%">Aktualität</td>';
									n+='<td width="10%">KO/GA</td>';
									n+='<td width="40%">Name</td>';
									n+='<td width="40%">Wert</td>';
								n+="</tr>";
							n+="</table>";
						n+="</td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td align='center'>";
							n+="<div style='position:relative; height:100%;'>";
								n+="<div id='desktopWidget_list4' data-paused='0' onClick='desktopWidgetToggelPause(this);' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto; font-size:9px; color:#a0a0a0; cursor:pointer;'></div>";
							n+="</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
				document.getElementById("desktopWidgets").innerHTML=n;
			}
			
			var n="";
<? $ts=getTimestampVisu(); $timeout=global_adminRefresh+1000; $ss1=sql_call("SELECT * FROM edomiLive.RAMko WHERE visuts>=".($ts-(61*1000000))." ORDER BY gatyp DESC,(id<100) DESC,id ASC"); while ($n=sql_result($ss1)) { if ($n['gatyp']==1) { $fgCol='#20e000'; } else if ($n['gatyp']==2) { if ($n['id']<100) { $fgCol='#a0a0a0'; } else { $fgCol='#ffffff'; } } $update=($ts-$n['visuts'])/1000; $delay=100-(($update/1000)*(100/60)); if ($delay>100) {$delay=100;} if ($delay<0) {$delay=0;} ?>
				n+="<tr align='left' style='color:<?echo $fgCol;?>; <?if ($update<$timeout) {echo 'background:#505050;';}?>'>";
					n+='<td width="10%"><div style="display:inline-block; width:40px; height:5px; border-radius:5px; background:-webkit-linear-gradient(left,#80e000 0%,#80e000 <?echo $delay;?>%,#595950 <?echo $delay;?>%,#595950 100%);"></div></td>';
					n+="<td width='10%'><span class='idGa<?echo $n['gatyp'];?>'><?echo $n['ga'];?></span></td>";
					n+="<td width='40%'style='max-width:300px; overflow:hidden;'><?ajaxEcho($n['name']);?></td>";
					n+="<td width='40%'style='max-width:300px; overflow:hidden;'><?ajaxEcho($n['value']);?></td>";
				n+="</tr>";
<? } ?>
			if (n!="") {
				if (document.getElementById("desktopWidget_list4")) {
					if (document.getElementById("desktopWidget_list4").dataset.paused!="1") {
						document.getElementById("desktopWidget_list4").innerHTML="<table width='100%' border='0' cellspacing='0' cellpadding='2' style='color:#ffffff; table-layout:auto;'>"+n+"</table>";
					}
				}
			} else {
				document.getElementById("desktopWidget_list4").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">keine Daten verfügbar</div></td></tr></table>';
			}
<? } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } if ($widgetId==5) { if ($edomiStatus==3 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false) { ?>
			if (!document.getElementById("desktopWidget_list5")) {
				var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
					n+="<tr valign='middle' height='1%'>";
						n+="<td align='center'>";
							n+="<table width='100%' border='0' cellspacing='0' cellpadding='3'>";
								n+="<tr style='color:#000000; background:#797979'>";
									n+='<td width="30%">Visualisierung</td>';
									n+='<td width="25%">Account</td>';
									n+='<td width="35%">IP-Adresse/Login</td>';
									n+='<td width="10%">&nbsp;</td>';
								n+="</tr>";
							n+="</table>";
						n+="</td>";
					n+="</tr>";
					n+="<tr valign='top'>";
						n+="<td align='center'>";
							n+="<div style='position:relative; height:100%;'>";
								n+="<div id='desktopWidget_list5' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto;'></div>";
							n+="</div>";
						n+="</td>";
					n+="</tr>";
				n+="</table>";
				document.getElementById("desktopWidgets").innerHTML=n;
			}
			
			var n="";
<? $ss1=sql_call("SELECT a.logindate,a.id,b.id AS userid,b.login AS username,a.visuid,c.name AS visuname,a.loginip FROM edomiLive.visuUserList AS a,edomiLive.visuUser AS b,edomiLive.visu AS c WHERE (a.targetid=b.id AND a.visuid=c.id AND a.online=1) ORDER BY a.visuid ASC,a.logindate ASC"); while ($n=sql_result($ss1)) { ?>
				n+='<tr align="left" style="color:#ffffff;">';
					n+='<td width="30%"><?ajaxEcho(substr($n['visuname'],0,30));?> <span class="id" style="color:#343434;"><?echo $n['visuid'];?></span></td>';
					n+='<td width="25%"><?ajaxEcho(substr($n['username'],0,20));?> <span class="id" style="color:#343434;"><?echo $n['userid'];?></span></td>';
					n+='<td width="35%"><?echo $n['loginip'];?> / <?echo sql_getDate($n['logindate']);?> <?echo sql_getTime($n['logindate']);?></td>';
					n+="<td width='10%' align='right'><div class='app0_cmdButton' onClick='ajax(\"visuLogout\",\"0\",\"\",\"\",\"<?echo $n['id'];?>\");' style='width:100%;'>Logout</div></td>";
				n+='</tr>';
<? } ?>
			if (n!="") {
				document.getElementById("desktopWidget_list5").innerHTML="<table width='100%' border='0' cellspacing='0' cellpadding='3'>"+n+"</table>";
			} else {
				document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">keine Visualisierung online</div></td></tr></table>';
			}
<? } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } if ($widgetId==6) { $console=getServerScreenshot(); if ($console || ($edomiStatus>=1 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false)) { ?>
			if (!document.getElementById("desktopWidget_list6")) {
				var n="<table width='516' height='416' border='0' cellspacing='3' cellpadding='0'>";
					n+='<tr id="desktopWidget_control6" valign="middle" height="1%">';
						n+="<td><div class='app0_cmdButton' onClick='ajaxConfirmSecure(\"Soll EDOMI wirklich beendet werden?<br><br>EDOMI kann nach dem Beenden nicht mehr bedient werden, der Server kann nur noch lokal oder z.B. per SSH kontrolliert werden!\",\"stop\",\"0\",\"\",\"\",\"\");' style='width:100%;'>EDOMI beenden</div></td>";
						n+="<td><div class='app0_cmdButton' onClick='ajaxConfirm(\"Soll der Server wirklich neugestartet werden?<br><br>EDOMI wird nach dem Server-Neustart automatisch gestartet.\",\"reboot\",\"0\",\"\",\"\",\"\");' style='width:100%;'>Server neustarten</div></td>";
						n+="<td><div class='app0_cmdButton' onClick='ajaxConfirmSecure(\"Soll der Server wirklich herunterfahren und ggf. ausgeschaltet werden?\",\"shutdown\",\"0\",\"\",\"\",\"\");' style='width:100%;'>Server herunterfahren</div></td>";
					n+='</tr>';
					n+='<tr valign="middle">';
						n+='<td colspan="3" align="center">';
							n+="<div style='position:relative; height:100%;'>";
								n+="<div style='position:absolute; top:0; left:0; right:0; bottom:0; overflow:auto;'>";
									n+="<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0' style='table-layout:auto;'>";
										n+="<tr valign='middle'><td align='center'><pre id='desktopWidget_list6' style='color:#f0f0f0; padding:0px; margin:0; font-size:9px; line-height:1.25; font-family:EDOMIfontMono,Menlo,Courier,monospace;'></pre></td></tr>";
									n+="</table>";
								n+="</div>";
							n+="</div>";
						n+='</td>';
					n+='</tr>';
				n+="</table>";
				document.getElementById("desktopWidgets").innerHTML=n;
			}
<? if ($edomiStatus>=1 && $procData1!==false && $procData2!==false && $procData3!==false && $procData4!==false && $procData5!==false && $procData6!==false) { ?>
				if (document.getElementById("desktopWidget_control6")) {document.getElementById("desktopWidget_control6").style.display='table-row';}
<? } else { ?>
				if (document.getElementById("desktopWidget_control6")) {document.getElementById("desktopWidget_control6").style.display='none';}
<? } if ($console) { ?>
				if (document.getElementById("desktopWidget_list6")) {document.getElementById("desktopWidget_list6").innerHTML="<?ajaxValue($console);?>";}
<? } else { ?>
				if (document.getElementById("desktopWidget_list6")) {document.getElementById("desktopWidget_list6").innerHTML='<div style="font-size:20px; color:#696969; font-family:<?echo global_adminFont;?>;">Konsoleninhalt nicht verfügbar</div>';}
<? } } else { ?>
			document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div style="font-size:20px; color:#696969;">nicht verfügbar</div></td></tr></table>';
<? } } } function getServerScreenshot() { $n=file_get_contents('/dev/vcsa'); if ($n!==false) { $w=intval(ord(substr($n,1,1))); $h=intval(ord(substr($n,0,1))); if ($w>0 && $h>0) { $r=''; for ($t=0;$t<(strlen($n)-4);$t+=2) { $char=substr($n,$t+4,1); if (ord($char)==132) {$char='Ä';} if (ord($char)==150) {$char='Ö';} if (ord($char)==156) {$char='Ü';} if (ord($char)==228) {$char='ä';} if (ord($char)==246) {$char='ö';} if (ord($char)==252) {$char='ü';} if (ord($char)==159) {$char='ß';} $r.=$char; if (($t+2)%($w*2)==0) {$r.='<br>';} } return $r; } } return false; } ?>