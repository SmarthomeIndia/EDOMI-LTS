<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); require(MAIN_PATH."/www/admin/include/php/incl_items.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; $pageId=$dataArr[0]; if (!is_numeric($pageId)) {$pageId=0;} if ($cmd=='initApp') { if (getEditProjektId()===false) { ?>
			closeWindow("<?echo $winId;?>");
			jsConfirm("Es ist kein Arbeitsprojekt vorhanden.","","none");
<? return; } ?>
		var n="<div id='<?echo $winId;?>-global' class='appWindowFullscreen' onMouseUp='app1_itemPageUnclick();' data-copybuffer=''>";
			n+="<div class='appTitel'>Logikeditor<div class='cmdClose' onClick='app1_quit(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main'></div>";
			n+="<div id='<?echo $winId;?>-menu' class='controlEditInline' style='position:absolute; background:#ffffff; margin:0 5px 5px 5px; padding:0px; left:0px; width:230px; top:80px; bottom:0px; border-radius:0; border:none;'></div>";
			n+="<div id='<?echo $winId;?>-pagecontainer' class='app1_pageContainer' style='left:242px;'>";
				n+="<div id='<?echo $winId;?>-page' class='app1_page' onMouseDown='app1_itemPageClick();' onMouseMove='app1_itemPageMouseMove();'></div>";
			n+="</div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;
<? ?>
		var n="<div class='appMenu'>";
			n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='white-space: nowrap; table-layout:auto;'>";
				n+="<tr>";
					n+="<td>";
						n+="<div id='<?echo $winId;?>-fd1' data-type='1000' data-root='11' data-value='<?echo $pageId;?>' data-options='typ=1;reset=0;caption=Logikseite öffnen' data-callback='app1_pickPage_callback(\"<?echo $winId;?>-fd1\");' data-callback2='app1_pickPage_callback(\"<?echo $winId;?>-fd1\");' class='cmdButton' style='min-width:100px; max-width:500px; white-space:nowrap; overflow:hidden; vertical-align:bottom;'>&nbsp;</div>";
						n+="&nbsp;&nbsp;&nbsp;";
						n+="<div id='<?echo $winId;?>-btnLive0' class='cmdButton' onClick='app1_live(0,1);' style='display:none; background:#80e000; border-radius:3px 0 0 3px; margin-right:0; border-right:none;'>Normale Ansicht</div>";
						n+="<div id='<?echo $winId;?>-btnLive1' class='cmdButton' onMousedown='app1_live(1,1);' onMouseup='app1_live(1,0);' onMouseout='app1_live(1,-1);' style='display:none; border-radius:0; margin-right:0; border-right:none;'>&gt; Liveansicht 1</div>";
						n+="<div id='<?echo $winId;?>-btnLive2' class='cmdButton' onMousedown='app1_live(2,1);' onMouseup='app1_live(2,0);' onMouseout='app1_live(2,-1);' style='display:none; border-radius:0 3px 3px 0; margin-right:0;'>&gt; Liveansicht 2</div>";
						n+="<div id='<?echo $winId;?>-fd2' data-type='1000' data-root='12' data-value='0' data-options='typ=1;reset=0' data-callback='app1_pickElement_callback(\"<?echo $winId;?>-fd2\");' data-callback2='app1_refreshAll(0);' style='display:none;'></div>";
						n+="<div id='<?echo $winId;?>-fd4' data-lbsid='0' data-type='1000' data-root='12' data-value='0' data-options='typ=4;reset=0;title=Logikbaustein austauschen' data-callback='app1_elementSwapLbs_callback(\"<?echo $winId;?>-fd4\");' data-callback2='' style='display:none;'></div>";
					n+="</td>";
					n+="<td align='right'>";
						n+="<div id='<?echo $winId;?>-zoom' style='display:inline;'>100%</div>&nbsp;";
						n+="<input type='range' class='controlSlider' value='1' min='0.10' max='1' step='0.05' onInput='app1_zoom(this.value);' onDblClick='app1_zoom(1); this.value=1;' style='width:120px; vertical-align:middle;'></input>";
						n+="&nbsp;&nbsp;&nbsp;";
						n+="<div id='<?echo $winId;?>-layer' class='cmdButton' onClick='app1_setLayerMode();'><img src='../shared/img/lock1.png' width='16' height='16' valign='middle' style='margin:0; padding-left:2px;' draggable='false'></div>";
						n+="&nbsp;&nbsp;&nbsp;";
						n+="<div id='<?echo $winId;?>-fd3' data-type='1000' data-root='10' data-value='0' data-options='typ=0;caption=Konfiguration' data-callback2='app1_refreshAll(0);' class='cmdButton'>&nbsp;</div>";
					n+="</td>";
				n+="</tr>";
			n+="</table>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;
		controlInitAll("<?echo $winId;?>-main");
		controlClickLeft("<?echo $winId;?>-fd1"); //Logikseite auswählen
		app1_info={play:false};
		app1_init("<?echo $winId;?>");
<? } if ($cmd=='start') { if ($pageId>0) { ?>
			clearObject(app1_winId+"-page",0);
			clearObject(app1_winId+"-menu",0);
			document.getElementById(app1_winId+"-menu").style.background="#ffffff";
<? if (!is_numeric($dataArr[1])) {$dataArr[1]=0;} if (checkLiveProjectData()) { ?>
				app1_liveSwitch(true);
<? } else { ?>
				app1_liveSwitch(false);
<? $dataArr[1]=0; } $pageIsEmpty=true; $ss1=sql_call("SELECT * FROM edomiProject.editLogicElement WHERE (pageid=".$pageId.") ORDER BY id ASC"); while ($element=sql_result($ss1)) { $pageIsEmpty=false; ?>
				var lbs=new class_app1_LBS();
<? $ss2=sql_call("SELECT * FROM edomiProject.editLogicElementDef WHERE (id=".$element['functionid']." AND errcount=0)"); if ($elementDef=sql_result($ss2)) { if ($element['functionid']==12000000) { $lbsTyp=1; } else if ($element['functionid']>=12000001 && $element['functionid']<=12000005) { $lbsTyp=2; } else if ($element['functionid']>=12000010 && $element['functionid']<=12000019) { $lbsTyp=3; } else { $lbsTyp=0; } $tmp1=sql_getValues('edomiProject.editLogicElementDefIn','MAX(id) AS anz1','targetid='.$elementDef['id']); $tmp2=sql_getValues('edomiProject.editLogicElementDefOut','MAX(id) AS anz1','targetid='.$elementDef['id']); if ($tmp1!==false && $tmp2!==false ) { if (isEmpty($tmp1['anz1'])) {$tmp1['anz1']=0;} if (isEmpty($tmp2['anz1'])) {$tmp2['anz1']=0;} } else { $tmp1['anz1']=0; $tmp2['anz1']=0; } ?>
					lbs.createDef(<?echo $lbsTyp;?>,"<?echo $element['id'];?>","<?echo $element['functionid'];?>","<?ajaxValue($elementDef['name']);?>","<?ajaxValue($elementDef['title']);?>","<?echo $element['xpos'];?>","<?echo $element['ypos'];?>","<?echo $element['layer'];?>","<?echo $tmp1['anz1'];?>","<?echo $tmp2['anz1'];?>","<?ajaxValue($element['name']);?>","<?echo ajaxValueHTML($element['name']);?>","<?echo global_logicStyleOutbox;?>");
<? if ($lbsTyp!=1) { $ss3=sql_call("SELECT a.id,a.name,a.color,b.eingang,b.linktyp,b.linkid,b.value FROM edomiProject.editLogicElementDefIn AS a,edomiProject.editLogicLink AS b WHERE a.targetid=".$elementDef['id']." AND b.elementid=".$element['id']." AND a.id=b.eingang ORDER BY a.id ASC"); while ($nn=sql_result($ss3)) { if ($nn['linktyp']==0) { $tmp=explode(SEPARATOR1,getGaInfo(0,$nn['linkid'])); } else { $tmp=array('',''); } ?>
							lbs.elementDef.data[<?echo $nn['id'];?>].in={name:"<?ajaxValue($nn['name']);?>",color:"<?echo $nn['color'];?>",value:"<?ajaxEcho($nn['value']);?>",value2:"<?ajaxValue($nn['value']);?>",linktyp:"<?echo $nn['linktyp'];?>",linkid:"<?echo $nn['linkid'];?>",ga1:"<?echo $tmp[0];?>",ga2:"<?echo $tmp[1];?>"};
<? } sql_close($ss3); if ($lbsTyp==3) { $tmp=sql_getCount('edomiProject.editLogicCmdList','targetid='.$element['id']); ?>
							lbs.elementDef.data[1].out={name:"<?echo $tmp;?>"};
<? } else { $ss3=sql_call("SELECT * FROM edomiProject.editLogicElementDefOut WHERE targetid=".$elementDef['id']); while ($nn=sql_result($ss3)) { ?>
								lbs.elementDef.data[<?echo $nn['id'];?>].out={name:"<?ajaxValue($nn['name']);?>"};
<? } sql_close($ss3); } if ($lbsTyp!=2) { if ($dataArr[1]>0) { $ss3=sql_call("SELECT value,eingang FROM edomiLive.RAMlogicLink WHERE (elementid=".$element['id']." AND (value IS NOT NULL))"); while ($nn=sql_result($ss3)) { if (isEmpty($nn['value'])) {$nn['value']=' ';} ?>
									lbs.elementDef.live["<?echo $nn['eingang'];?>"]="<?ajaxEcho($nn['value']);?>";
<? } sql_close($ss3); } } } } else { ?>
					lbs.createDef(-1,"<?echo $element['id'];?>","<?echo $element['functionid'];?>","<span style='color:#ffffff; background:#ff0000;'>&nbsp;LBS <?echo $element['functionid'];?>&nbsp;</span>","","<?echo $element['xpos'];?>","<?echo $element['ypos'];?>","<?echo $element['layer'];?>","0","0","","","");
<? } sql_close($ss2); ?>
				lbs.createLbs();
<? } sql_close($ss1); if (!$pageIsEmpty && $dataArr[1]==2) { app2_getLiveKoValues($pageId); } if (!$pageIsEmpty && $dataArr[1]>0) { $ss1=sql_call("SELECT a.id,b.status,b.statusexec FROM edomiProject.editLogicElement AS a,edomiLive.RAMlogicElement AS b WHERE (a.pageid=".$pageId." AND a.id=b.id AND a.functionid=b.functionid)"); while ($n=sql_result($ss1)) { if ($n['status']>0 || $n['statusexec']>0) {$tmp=1;} else {$tmp=0;} ?>
					lbs.showLiveStatus("<?echo $n['id'];?>","<?echo $tmp;?>");
<? } sql_close($ss1); ?>
				lbs.hideNoLiveAll();
<? } ?>
			app1_rD=new Array();
<? $ss1=sql_call("SELECT id FROM edomiProject.editLogicElement WHERE (pageid=".$pageId.") ORDER BY id ASC"); while ($element=sql_result($ss1)) { $ss2=sql_call("SELECT eingang,linkid,ausgang FROM edomiProject.editLogicLink WHERE (elementid=".$element['id']." AND linktyp=1)"); while ($n=sql_result($ss2)) { ?>
					app1_addConnection("<?echo $element['id'];?>","<?echo $n['eingang'];?>","<?echo $n['linkid'];?>","<?echo $n['ausgang'];?>");
<? } sql_close($ss2); } sql_close($ss1); ?>
			app1_drawConnections();
			app1_restoreState("<?echo $phpdataArr[0];?>");
			app1_setPageSize();
			app1_liveDo(app1_liveMode,app1_info.play);
<? } } if ($cmd=='itemAusgangContextMenu') { $cmdCount=sql_getCount('edomiProject.editLogicCmdList','targetid='.$phpdataArr[0]); if ($cmdCount>0) { ?>
			apps_contextMenu.addHr();
<? $ss1=sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE targetid=".$phpdataArr[0]." ORDER BY id ASC LIMIT 0,10"); while ($n=sql_result($ss1)) { ?>
				apps_contextMenu.addText("&bull; <?echo getCommandData('editLogicCmdList',$n['id']);?>");
<? } sql_close($ss1); if ($cmdCount>10) { ?>
				apps_contextMenu.addText("&bull; ...");
<? } ?>
			apps_contextMenu.show();
<? } } if ($cmd=='editLbs') { if ($phpdataArr[0]>0) { $tmp=sql_getValue('edomiProject.editLogicElement','functionid','id='.$phpdataArr[0]); if (!isEmpty($tmp)) { ?>
				openWindow(1000,""+AJAX_SEPARATOR1+"12"+AJAX_SEPARATOR1+"<?echo $tmp;?>"+AJAX_SEPARATOR1+"typ=6"+AJAX_SEPARATOR1+"app1_refreshAll(0);"+AJAX_SEPARATOR1+"app1_refreshAll(0);");
<? } } ?>
		app1_setPageSize();
<? } if ($cmd=='saveElementsPosition') { $tmp=explode(';',$phpdataArr[0]); foreach ($tmp as $n) { $element=explode(',',$n); if ($element[0]>0) { sql_call("UPDATE edomiProject.editLogicElement SET xpos='".$element[1]."',ypos='".$element[2]."' WHERE (id=".$element[0].")"); } } ?>
		app1_setPageSize();
<? } if ($cmd=='layerElements') { $tmp=explode(';',$phpdataArr[0]); foreach ($tmp as $element) { if ($element>0) { sql_call("UPDATE edomiProject.editLogicElement SET layer='".$phpdataArr[1]."' WHERE id=".$element); } } ?>
		app1_refreshAll(0);
<? } if ($cmd=='setGa') { if ($phpdataArr[0]>0 && $phpdataArr[1]>0 && $phpdataArr[2]>0) { sql_call("UPDATE edomiProject.editLogicLink SET linktyp=0,linkid=".$phpdataArr[0].",ausgang=null WHERE (elementid=".$phpdataArr[1]." AND eingang=".$phpdataArr[2].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='setValue') { if ($phpdataArr[1]>0 && $phpdataArr[2]>0) { sql_call("UPDATE edomiProject.editLogicLink SET value=".sql_encodeValue($phpdataArr[0],true)." WHERE (elementid=".$phpdataArr[1]." AND eingang=".$phpdataArr[2].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='newElementConnection') { if ($phpdataArr[0]>0 && $phpdataArr[1]>0 && $phpdataArr[2]>0 && $phpdataArr[3]>0) { sql_call("UPDATE edomiProject.editLogicLink SET linktyp=1,linkid=".$phpdataArr[0].",ausgang=".$phpdataArr[1]." WHERE (elementid=".$phpdataArr[2]." AND eingang=".$phpdataArr[3].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='deleteElementConnection') { if ($phpdataArr[0]>0 && $phpdataArr[1]>0) { sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE (elementid=".$phpdataArr[0]." AND eingang=".$phpdataArr[1].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='deleteElementConnectionsAll') { if ($phpdataArr[0]>0 && $phpdataArr[1]>0) { sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE (linktyp=1 AND linkid=".$phpdataArr[0]." AND ausgang=".$phpdataArr[1].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='deleteElementCommands') { if ($phpdataArr[0]>0) { sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (targetid=".$phpdataArr[0].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='renameElement') { if ($phpdataArr[1]>0) { sql_call("UPDATE edomiProject.editLogicElement SET name='".sql_encodeValue($phpdataArr[0])."' WHERE (id=".$phpdataArr[1].")"); } ?>
		app1_refreshAll(0);
<? } if ($cmd=='pasteElements') { if ($dataArr[0]>0) { $tmp=explode(';',rtrim($phpdataArr[0],';')); $newElements=db_itemDuplicate_editLogicElement($dataArr[0],$tmp); ?>
			app1_refreshAll(0,"<?echo implode(';',$newElements);?>");
<? } } if ($cmd=='moveElements') { if ($dataArr[0]>0) { $newElements=array(); $tmp=explode(';',rtrim($phpdataArr[0],';')); foreach ($tmp as $element) { if ($element>0) { sql_call("UPDATE edomiProject.editLogicElement SET pageid='".$dataArr[0]."' WHERE (id=".$element.")"); $newElements[]=$element; } } if (count($tmp)>0) { $ids=join(',',$tmp); sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=NULL,ausgang=NULL WHERE (linktyp=1 AND ((linkid IN (".$ids.") AND elementid NOT IN (".$ids.")) OR (linkid NOT IN (".$ids.") AND elementid IN (".$ids."))))"); } ?>
			app1_refreshAll(0,"<?echo implode(';',$newElements);?>");
<? } } if ($cmd=='setLiveValue') { if (checkLiveProjectData()) { ?>
			openWindow(1011,"","<?echo $phpdataArr[0];?><?echo AJAX_SEPARATOR1;?><?echo $phpdataArr[1];?><?echo AJAX_SEPARATOR1;?><?echo $phpdataArr[2];?>");
<? } } if ($cmd=='setLiveKoValue') { if (checkLiveProjectData()) { $koLocal=0; $ss1=sql_call("SELECT linkid FROM edomiProject.editLogicLink WHERE (elementid='".$phpdataArr[0]."' AND eingang='".$phpdataArr[1]."' AND linktyp=0)"); if ($tmp=sql_result($ss1)) {$koLocal=$tmp['linkid'];} sql_close($ss1); $koLive=0; $ss1=sql_call("SELECT linkid FROM edomiLive.RAMlogicLink WHERE (elementid='".$phpdataArr[0]."' AND eingang='".$phpdataArr[1]."' AND linktyp=0)"); if ($tmp=sql_result($ss1)) {$koLive=$tmp['linkid'];} sql_close($ss1); $ss1=sql_call("SELECT id FROM edomiProject.editLogicElement WHERE (id='".$phpdataArr[0]."' AND functionid<=12000005)"); if ($tmp=sql_result($ss1)) { $koLive=$koLocal; $koLocal=-1; } sql_close($ss1); if ($koLive>0) { ?>
				openWindow(1010,"","<?echo $koLive;?><?echo AJAX_SEPARATOR1;?><?echo $koLocal;?>");
<? } else { ?>
				jsConfirm("Im Live-Projekt ist an diesem Eingang kein KO vorhanden (oder der Logikbaustein existiert nicht im Live-Projekt).","","none");
<? } } } if ($cmd=='newLogicElement') { if (sql_getValue('edomiProject.editLogicPage','id','id='.$dataArr[0])>0) { $dbId=db_itemSave('editLogicElement',array( 1 => -1, 2 => $dataArr[0], 3 => $phpdataArr[0], 4 => $phpdataArr[1], 5 => $phpdataArr[2], 6 => '' )); ?>
			app1_refreshAll(0);
<? } } if ($cmd=='deleteElements') { $tmp=explode(';',rtrim($phpdataArr[0],';')); foreach ($tmp as $element) { if ($element>0) { db_itemDelete('editLogicElement',$element); } } ?>
		app1_refreshAll(0);
<? } if ($cmd=='swapLbsSelect') { if ($phpdataArr[0]>0) { $tmp=sql_getValue('edomiProject.editLogicElement','functionid','id='.$phpdataArr[0]); if (!isEmpty($tmp)) { ?>
				document.getElementById(app1_winId+"-fd4").dataset.lbsid="<?echo $phpdataArr[0];?>";
				document.getElementById(app1_winId+"-fd4").dataset.value="<?echo $tmp;?>";
				controlClickLeft(app1_winId+"-fd4");
<? } } } if ($cmd=='swapLbs') { if ($phpdataArr[0]>0 && $phpdataArr[1]>0) { if (sql_getValue('edomiProject.editLogicPage','id','id='.$dataArr[0])>0) { $tmp=sql_getValues('edomiProject.editLogicElement','*','id='.$phpdataArr[0]); if ($tmp!==false) { if ($tmp['functionid']!=$phpdataArr[1]) { $dbId=db_itemSave('editLogicElement',array( 1 => $phpdataArr[0], 2 => $dataArr[0], 3 => $phpdataArr[1], 4 => $tmp['xpos'], 5 => $tmp['ypos'], 6 => $tmp['name'] )); ?>
						app1_refreshAll(0);
<? } } } } } } sql_disconnect(); function app2_getLiveKoValues($pageId) { global $winId; ?>
	var n='<table style="width:100%; white-space:nowrap; table-layout:auto;" border="0" cellpadding="2" cellspacing="0">';
		n+='<tr bgcolor="#bbbb00"><td colspan="4">KOs: Eingänge</td></tr>';
<? $ss1=sql_call("SELECT c.ga,c.name,c.value,c.gatyp FROM edomiProject.editLogicElement AS a, edomiLive.RAMlogicLink AS b1, edomiLive.RAMko AS c WHERE
		(a.pageid=".$pageId." AND a.id=b1.elementid AND b1.linktyp=0 AND b1.linkid>0 AND b1.linkid=c.id) GROUP BY c.id ORDER BY c.id ASC"); while ($live=sql_result($ss1)) { ?>
		n+='<tr>';
			n+='<td><span class="idGa<?echo $live['gatyp']?>"><?ajaxEcho($live['ga']);?></span></td>';
			n+='<td>=</td>';
			n+='<td><?ajaxEcho($live['value']);?></td>';
			n+='<td style="color:#909000;"><?ajaxEcho($live['name']);?></td>';
		n+='</tr>';
<? } sql_close($ss1); ?>
	n+='<tr bgcolor="#bbbb00"><td colspan="4">KOs: Befehle</td></tr>';
<? $ss1=sql_call("SELECT c.ga,c.name,c.value,c.gatyp FROM edomiProject.editLogicElement AS a, edomiLive.RAMlogicCmdList AS b2, edomiLive.RAMko AS c WHERE
		(a.pageid=".$pageId." AND a.id=b2.targetid AND b2.cmd<10 AND b2.cmdid1>0 AND b2.cmdid1=c.id) GROUP BY c.id ORDER BY c.id ASC"); while ($live=sql_result($ss1)) { ?>
		n+='<tr>';
			n+='<td><span class="idGa<?echo $live['gatyp']?>"><?ajaxEcho($live['ga']);?></span></td>';
			n+='<td>=</td>';
			n+='<td><?ajaxEcho($live['value']);?></td>';
			n+='<td style="color:#909000;"><?ajaxEcho($live['name']);?></td>';
		n+='</tr>';
<? } sql_close($ss1); ?>
	n+='</table>';
	document.getElementById("<?echo $winId;?>-menu").innerHTML=n;
	document.getElementById("<?echo $winId;?>-menu").style.background="#e8e800";
<? } ?>
