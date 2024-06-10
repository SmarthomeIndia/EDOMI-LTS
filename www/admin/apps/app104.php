<?
/* 
*/ 
?><? ?><? require("../../shared/php/config.php"); require(MAIN_PATH."/www/shared/php/base.php"); require(MAIN_PATH."/www/shared/php/incl_http.php"); require(MAIN_PATH."/www/admin/include/php/config.php"); require(MAIN_PATH."/www/admin/include/php/base.php"); sql_connect(); if (checkAdmin($sid)) {cmd($cmd);} function cmd($cmd) { global $appId,$winId,$data,$dataArr,$phpdata,$phpdataArr,$sid; if ($cmd=='initApp') { if (getLiveProjektId()===false) { ?>
			closeWindow("<?echo $winId;?>");
			jsConfirm("Es ist kein Live-Projekt vorhanden.","","none");
<? return; } ?>
		var n="<div class='appWindowFullscreen' onMouseMove='app104_mouseMove(\"<?echo $winId;?>\");' onMouseUp='app104_mouseUp(\"<?echo $winId;?>\");'>";
			n+="<div class='appTitel'>Kameraaufnahmen<div class='cmdClose' onClick='closeWindow(\"<?echo $winId;?>\");'></div><div class='cmdHelp' onClick='openWindow(9999,\"<?echo $appId;?>\");'></div></div>";
			n+="<div id='<?echo $winId;?>-main'></div>";

			n+="<div style='position:absolute; left:0px; top:75px; right:0px; height:120px; border-bottom:1px solid #808080; padding:0; overflow:hidden; color:#ffffff; background:#505050;'>";
				n+="<div id='<?echo $winId;?>-control'>";
					n+="<table width='100%' height='120' border='0' cellpadding='3' cellspacing='0' style='table-layout:auto;'>";
						n+="<tr>";
							n+="<td id='<?echo $winId;?>-cap' class='formTitel' align='center' style='background:#808080; color:#ffffff; white-space:nowrap;'>&nbsp;</td>";
						n+="</tr>";

						n+="<tr>";
							n+="<td>";
								n+="<div style='position:relative; width:100%; height:40px; overflow:hidden;'>";
									n+="<div id='<?echo $winId;?>-stat1' style='position:absolute; left:0; top:0; width:100%; height:30px; border-left:7px solid transparent; border-right:7px solid transparent; box-sizing:border-box; overflow:hidden;'></div>";
									n+="<input type='range' id='<?echo $winId;?>-slider1' class='controlSlider' value='0' min='0' max='0' step='1' onInput='app104_filesLoadImage(\"<?echo $winId;?>\",this.value);' onMouseUp='app104_metaLoadArray(\"<?echo $winId;?>\",this.value);' style='position:absolute; left:0; top:30px; width:100%;'></input>";
									n+="<div id='<?echo $winId;?>-event1' style='position:absolute; left:0; top:30px; width:100%; height:30px; border-left:7px solid transparent; border-right:7px solid transparent; box-sizing:border-box; overflow:hidden; pointer-events:none;'></div>";
								n+="</div>";
							n+="</td>";
						n+="</tr>";

						n+="<tr>";
							n+="<td>";
								n+="<div id='<?echo $winId;?>-timeline' style='position:relative; width:100%; height:25px; overflow:hidden;'>";
									n+="<div id='<?echo $winId;?>-stat2' style='position:absolute; left:0; top:0; width:100%; height:15px; border-left:7px solid transparent; border-right:7px solid transparent; box-sizing:border-box; overflow:hidden;'></div>";
									n+="<input type='range' id='<?echo $winId;?>-slider2' class='controlSlider' value='0' min='0' max='0' step='1' onInput='app104_metaLoadImage(\"<?echo $winId;?>\",this.value);' style='position:absolute; left:0; top:15px; width:100%;'></input>";
									n+="<div id='<?echo $winId;?>-event2' style='position:absolute; left:0; top:15px; width:100%; height:5px; border-left:7px solid transparent; border-right:7px solid transparent; box-sizing:border-box; overflow:hidden; pointer-events:none;'></div>";
								n+="</div>";
							n+="</td>";
						n+="</tr>";
					n+="</table>";	
				n+="</div>";
			n+="</div>";

			n+="<div style='position:absolute; left:0px; width:243px; top:196px; height:35px; background:#343434; border-radius:0; border:none;'>";
				n+="<table width='100%' height='35' border='0' cellpadding='0' cellspacing='0' style='background:#343434; table-layout:auto;'>";
					n+="<tr valign='middle'>";
<? if (!isEmpty(global_dvrPath)) { ?>
						n+="<td align='center' width='50%' id='<?echo $winId;?>-mode1' onClick='app104_setMode(\"<?echo $winId;?>\",1);' class='app104_modeA' style='border-right:1px solid #a0a0a0;'>Archive</td>";
						n+="<td align='center' width='50%' id='<?echo $winId;?>-mode0' onClick='app104_setMode(\"<?echo $winId;?>\",0);' class='app104_modeB'>DVR</td>";
<? } else { ?>
						n+="<td align='center' width='50%' id='<?echo $winId;?>-mode1' onClick='app104_setMode(\"<?echo $winId;?>\",1);' class='app104_modeA' style='border-right:1px solid #a0a0a0;'>Archive</td>";
						n+="<td align='center' width='50%' id='<?echo $winId;?>-mode0' class='app104_modeB' style='color:#a0a0a0;'>DVR<br>(nicht konfiguriert)</td>";
<? } ?>
					n+="</tr>";
				n+="</table>";	
			n+="</div>";
			n+="<div id='<?echo $winId;?>-list' class='controlEditInline' style='position:absolute; left:0px; width:237px; top:231px; bottom:0px; background:#343434; padding:3px; border-radius:0; border:none;'></div>";
			n+="<div id='<?echo $winId;?>-viewport' onMouseDown='app104_mouseDown(\"<?echo $winId;?>\");' style='position:absolute; left:243px; top:196px; right:0; bottom:0; border-left:1px solid #808080; border-radius:0; overflow:auto; background:#343434;'>";
				n+="<img id='<?echo $winId;?>-img' data-loaded='1' draggable='false' style='display:none; width:auto; height:100%; -webkit-transform-origin:0 0; pointer-events:none;' onload='app104_imgOnLoad(\"<?echo $winId;?>\");' onerror='app104_imgOnLoad(\"<?echo $winId;?>\");'></img>";
				n+="<canvas id='<?echo $winId;?>-cnv' data-loaded='1' style='display:none; width:auto; height:100%; -webkit-transform-origin:0 0; pointer-events:none;'></canvas>";
			n+="</div>";
		n+="</div>";
		document.getElementById("<?echo $winId;?>").innerHTML=n;

		var n="<div class='appMenu'>";
			n+="<div style='width:0; height:0; display:none;'><iframe id='<?echo $winId;?>-iframe'></iframe></div>";

			n+="<table width='100%' border='0' cellpadding='0' cellspacing='0' style='table-layout:auto; white-space:nowrap;'>";
				n+="<tr valign='top'>";

					n+="<td width='33%'>";
						n+="<div id='<?echo $winId;?>-buffsave' data-active='0' class='cmdButton cmdButtonL' onClick='app104_saveImage(\"<?echo $winId;?>\");'><b>Bildkopie ablegen</b></div>";
						n+="<div class='cmdButton cmdButtonM' onClick='ajax(\"downloadImages\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Download (<span id='<?echo $winId;?>-buffinfo'></span>)</div>";	
						n+="<div class='cmdButton cmdButtonR' onClick='ajaxConfirm(\"<b>Sollen wirklich alle abgelegten Bildkopien gelöscht werden?</b>\",\"deleteImages\",\"<?echo $appId;?>\",\"<?echo $winId;?>\",\"<?echo $data;?>\",\"\");'>Löschen</div>";	
					n+="</td>";

					n+="<td id='<?echo $winId;?>-nav1' align='center' style='display:none;'>";
						n+="<div class='cmdButton cmdButtonL' onClick='app104_addStep1(\"<?echo $winId;?>\",-1);'>&lt;&lt;</div>";
						n+="<div class='cmdButton cmdButtonM' onClick='app104_addStep3(\"<?echo $winId;?>\",-1);' style='min-width:30px;'>|&lt;</div>";
						n+="<div class='cmdButton cmdButtonM' onMousedown='app104_play2(\"<?echo $winId;?>\",-1,1);' onMouseup='app104_play2(\"<?echo $winId;?>\",-1,0);' onMouseout='app104_play2(\"<?echo $winId;?>\",-1,-1);'>&lt;</div>";
							n+="<div class='cmdButton cmdButtonM' onClick='app104_refresh(\"<?echo $winId;?>\");' style='min-width:30px; width:30px;'>&sext;</div>";	
						n+="<div class='cmdButton cmdButtonM' onMousedown='app104_play2(\"<?echo $winId;?>\",1,1);' onMouseup='app104_play2(\"<?echo $winId;?>\",1,0);' onMouseout='app104_play2(\"<?echo $winId;?>\",1,-1);'>&gt;</div>";
						n+="<div class='cmdButton cmdButtonM' onClick='app104_addStep3(\"<?echo $winId;?>\",1);' style='min-width:30px;'>&gt;|</div>";
						n+="<div class='cmdButton cmdButtonR' onClick='app104_addStep1(\"<?echo $winId;?>\",1);'>&gt;&gt;</div>";
					n+="</td>";

					n+="<td id='<?echo $winId;?>-nav2' width='33%' style='display:none;'>";
						n+="<div style='vertical-align:middle; float:right;'>";
							n+="<div id='<?echo $winId;?>-srcscaption' style='display:inline;'>50%</div>&nbsp;";
							n+="<input type='range' id='<?echo $winId;?>-srcs' class='controlSlider' data-type='1' value='50' min='10' max='100' step='5' onChange='app104_setSrcs(\"<?echo $winId;?>\",this.value);' style='display:none; width:120px; vertical-align:middle;'></input>&nbsp;&nbsp;&nbsp;";
							n+="<div id='<?echo $winId;?>-view' data-type='6' data-value='0' data-list='' onChange='app104_changeView(\"<?echo $winId;?>\");' class='control6' style='min-width:150px; width:150px; height:27px;'>&nbsp;</div>";
						n+="</div>";			
					n+="</td>";
				n+="</tr>";
				
			n+="</table>";	

		n+="</div>";
		document.getElementById("<?echo $winId;?>-main").innerHTML=n;

		app104_info={ts:-1,mode:0,id:0,lastId:[0,0],timer:null,buffer:false,load:true,camviewInited:false,camViewId:0,play:false};
		app104_views={};
		app104_files=new Array();
		app104_meta=new Array();
<? app104_updateCopyInfo(); $phpdataArr[1]=1; cmd('start'); } if ($cmd=='start') { $id=app104_list($phpdataArr[0],$phpdataArr[1]); if ($phpdataArr[1]==1) {$camId=sql_getValue('edomiLive.archivCam','camid','id='.$id);} else {$camId=$id;} $tmp='0|ohne Ansicht (Originalbild);'; $ss1=sql_call("SELECT id,name FROM edomiLive.camView WHERE camId=".$camId." ORDER BY id ASC"); while ($n=sql_result($ss1)) { $n['name']=preg_replace("/[\|\;]/",'',$n['name']); $tmp.=$n['id'].'|'.$n['name'].' ('.$n['id'].');'; } if (!$phpdataArr[2]>=1) {$phpdataArr[2]=0;} $n=sql_getValues('edomiLive.camView','*','id='.$phpdataArr[2].' AND srctyp>0'); if ($n!==false) { ?>
				app104_info.camViewId="<?echo $n['id'];?>";
				app104_info.camviewInited=false;
				camView=new class_camView();	

				//PTZ-Funktion:
				app104_drag.zoom=parseFloat("<?echo ($n['zoom']/5);?>");
				app104_drag.srctyp=parseInt("<?echo $n['srctyp'];?>");
				app104_drag.a2=parseFloat("<?echo $n['a2'];?>");
				if (app104_drag.srctyp==1) {
					app104_drag.v1=parseFloat("<?echo $n['x'];?>");
					app104_drag.v2=parseFloat("<?echo $n['y'];?>");
				} else {
					app104_drag.v1=parseFloat("<?echo $n['a1'];?>");
					app104_drag.v2=parseFloat("<?echo $n['a2'];?>");
				}

				camView.setProperty("srccanvas",false);		
				camView.setProperty("dstcanvas",document.getElementById("<?echo $winId;?>-cnv"));		
				camView.setProperty("srctyp",parseInt("<?echo $n['srctyp'];?>"));		
				camView.setProperty("db_zoom",parseInt("<?echo $n['zoom'];?>"));
				camView.setProperty("db_a1",parseInt("<?echo (($n['srctyp']==1)?0:$n['a1']);?>"));
				camView.setProperty("db_a2",parseInt("<?echo $n['a2'];?>"));
				camView.setProperty("db_x",parseInt("<?echo $n['x'];?>"));
				camView.setProperty("db_y",parseInt("<?echo $n['y'];?>"));

				var w=document.getElementById("<?echo $winId;?>-viewport").offsetWidth;
				var h=w/<?echo ($n['dstw']/$n['dsth']);?>;
				camView.setProperty("db_dstw",parseInt(w));		
				camView.setProperty("db_dsth",parseInt(h));

				camView.setProperty("db_srcr",parseInt("<?echo (($n['srctyp']==1)?0:$n['srcr']);?>"));
				camView.setProperty("db_srcd",parseInt("<?echo (($n['srctyp']==1)?0:$n['srcd']);?>"));
				camView.setProperty("db_srcs",parseInt(document.getElementById("<?echo $winId;?>-srcs").value));
				
				document.getElementById("<?echo $winId;?>-srcscaption").style.display="inline-block";
				document.getElementById("<?echo $winId;?>-srcs").style.display="inline-block";
<? } else { ?>
			app104_info.camViewId="0";
			camView=null;					
			document.getElementById("<?echo $winId;?>-srcscaption").style.display="none";
			document.getElementById("<?echo $winId;?>-srcs").style.display="none";
<? } ?>
		document.getElementById("<?echo $winId;?>-view").dataset.value="<?echo $phpdataArr[2];?>";
		document.getElementById("<?echo $winId;?>-view").dataset.list='<?ajaxValue($tmp);?>';
		controlInit("<?echo $winId;?>-view");

		app104_info.lastId["<?echo $phpdataArr[1];?>"]="<?echo $id;?>";
		app104_info.id="<?echo $id;?>";
		app104_info.mode="<?echo $phpdataArr[1];?>";
		app104_info.load=true;
		app104_info.buffer=false;
		app104_files=new Array();
		var sl1=document.getElementById("<?echo $winId;?>-slider1");
		var sl2=document.getElementById("<?echo $winId;?>-slider2");
<? app104_getFileArray($id,$phpdataArr[1]); $anz1=sql_getCount('edomiLive.archivCamData','1=1'); ?>
		document.getElementById("<?echo $winId;?>-mode1").innerHTML="Archive<br><span style='color:#c0c0c0;'><?echo $anz1;?> Bilder</span>";
<? if (!isEmpty(global_dvrPath)) { $anz1=getFilesCount(global_dvrPath.'/cam-*-1.edomidvr'); $anz2=getFolderSize(global_dvrPath.'/cam-*-2.edomidvr')/(1024*1024*1024); ?>
			document.getElementById("<?echo $winId;?>-mode0").innerHTML="DVR<br><span style='color:#c0c0c0;'><?printf("%01.2f",$anz2);?> GB (&asymp;<?echo $anz1;?> h)</span>";
<? } ?>
		if (app104_files.length>0) {
			var n="";
			var preValue="";			
			var tmpDate=new Array();
			for (var t=0;t<(app104_files.length);t++) {
				var tmp=app104_files[t][4].split("/");
				if (tmp[1]!=preValue) {
					tmpDate.push(t);
					preValue=tmp[1];
				}
			}
			var loopStep=parseInt((tmpDate.length*70)/document.getElementById("<?echo $winId;?>-viewport").offsetWidth);
			if (loopStep<1) {loopStep=1;}
			for (var t=loopStep;t<(tmpDate.length);t+=loopStep) {
				var pos=(tmpDate[t]/(app104_files.length-1))*100;
				var tmp=app104_files[tmpDate[t]][4].split("/");
				n+="<div class='app104_stat1' style='left:"+pos+"%;'>"+tmp[0]+"<br>"+tmp[1]+"</div>";
			}
			var tmp=app104_files[0][4].split("/");
			n+="<div class='app104_stat1' style='left:0; color:#ffff00; border-left:1px solid #ffff00;'><span style='background:#505050;'>"+tmp[0]+"&nbsp;<br>"+tmp[1]+"&nbsp;</span></div>";
			var tmp=app104_files[app104_files.length-1][4].split("/");
			n+="<div class='app104_stat1' style='color:#ffff00; text-align:right; right:0; border-left:none; border-right:1px solid #ffff00;'><span style='background:#505050;'>&nbsp;"+tmp[0]+"<br>&nbsp;"+tmp[1]+"</span></div>";
			document.getElementById("<?echo $winId;?>-stat1").innerHTML=n;

			var n="";
			if (app104_info.mode==0) {
				var p1=false;
				var p2=false;
				for (var t=0;t < app104_files.length;t++) {
					if (app104_files[t][5]>0) {
						if (p1===false) {
							p1=(t/(app104_files.length-1))*100;
							if (isNaN(p1)) {p1=0;}
						}
					} else if (p1!==false) {
						p2=(t/(app104_files.length-1))*100;
					}
					if (p1!==false && p2!==false) {
						n+="<div class='app104_event' style='left:"+p1+"%; width:"+(p2-p1)+"%;'></div>";
						p1=false;
						p2=false;
					}
				}
				if (p1!==false && p2===false) {
					n+="<div class='app104_event' style='left:"+p1+"%; width:"+(100-p1)+"%;'></div>";
				}
			}
			document.getElementById("<?echo $winId;?>-event1").innerHTML=n;

			var newPos=app104_files.length-1;
			if (app104_info.ts>=0) {
				if (app104_info.mode==1) {
					var tmp=app104_getDay(app104_info.ts);
				} else {
					var tmp=app104_getHour(app104_info.ts);
				}
				for (var t=0;t<(app104_files.length);t++) {
					if (app104_info.mode==1) {
						if (app104_getDay(app104_files[t][0])>=tmp) {
							newPos=t;
							break;
						}
					} else {
						if (app104_getHour(app104_files[t][0])>=tmp) {
							newPos=t;
							break;
						}
					}
				}
			}
			sl1.max=app104_files.length-1;
			sl1.value=newPos;

			var tmp=app104_files[0][4].split("/");
			var tmp=app104_files[app104_files.length-1][4].split("/");

			app104_metaLoadArray("<?echo $winId;?>",newPos);

			document.getElementById("<?echo $winId;?>-control").style.display="block";
			document.getElementById("<?echo $winId;?>-buffsave").dataset.active="1";
			document.getElementById("<?echo $winId;?>-nav1").style.display="table-cell";
			document.getElementById("<?echo $winId;?>-nav2").style.display="table-cell";
		} else {
			document.getElementById("<?echo $winId;?>-img").style.display="none";
			document.getElementById("<?echo $winId;?>-cnv").style.display="none";
			document.getElementById("<?echo $winId;?>-control").style.display="none";
			document.getElementById("<?echo $winId;?>-buffsave").dataset.active="0";
			document.getElementById("<?echo $winId;?>-nav1").style.display="none";
			document.getElementById("<?echo $winId;?>-nav2").style.display="none";
		}
<? } if ($cmd=='metaLoadArray') { ?>
		app104_info.load=true;
		app104_info.buffer=false;
		app104_meta=new Array();
<? app104_getMetaArray($phpdataArr[0],$phpdataArr[2],$phpdataArr[1]); ?>
		if (app104_meta.length>0) {
			var n="";
			var loopStep=parseInt((app104_meta.length*70)/document.getElementById("<?echo $winId;?>-viewport").offsetWidth);
			if (loopStep<1) {loopStep=1;}
			for (var t=loopStep;t<(app104_meta.length-1);t+=loopStep) {
				var pos=(t/(app104_meta.length-1))*100;
				var tmp=app104_meta[t][4].split("/");
				n+="<div class='app104_stat2' style='left:"+pos+"%;'>"+tmp[2]+"</div>";
			}
			var tmp=app104_meta[0][4].split("/");
			n+="<div class='app104_stat2' style='left:0; color:#ffff00; border-left:1px solid #ffff00;'><span style='background:#505050;'>"+tmp[2]+"&nbsp;</span></div>";
			var tmp=app104_meta[app104_meta.length-1][4].split("/");
			n+="<div class='app104_stat2' style='color:#ffff00; text-align:right; right:0; border-left:none; border-right:1px solid #ffff00;'><span style='background:#505050;'>&nbsp;"+tmp[2]+"</span></div>";
			document.getElementById("<?echo $winId;?>-stat2").innerHTML=n;

			var n="";			
			if (app104_info.mode==0) {
				var p1=false;
				var p2=false;
				for (var t=0;t < app104_meta.length;t++) {
					if (app104_meta[t][6]!=0) {
						if (p1===false) {
							p1=(t/(app104_meta.length-1))*100;
							if (isNaN(p1)) {p1=0;}
						}
					} else if (p1!==false) {
						p2=(t/(app104_meta.length-1))*100;
					}
					if (p1!==false && p2!==false) {
						n+="<div class='app104_event' style='left:"+p1+"%; width:"+(p2-p1)+"%;'></div>";
						p1=false;
						p2=false;
					}
				}
				if (p1!==false && p2===false) {
					n+="<div class='app104_event' style='left:"+p1+"%; width:"+(100-p1)+"%;'></div>";
				}
			}
			document.getElementById("<?echo $winId;?>-event2").innerHTML=n;

			var newPos=app104_meta.length-1;
			if (app104_info.ts>=0) {
				for (var t=0;t<(app104_meta.length);t++) {
					if (app104_meta[t][0]>=app104_info.ts) {
						newPos=t;
						if (t>0 && (app104_info.ts-app104_meta[t-1][0])<(app104_meta[t][0]-app104_info.ts)) {newPos--;}
						break;
					}
				}
			}
			var sl=document.getElementById("<?echo $winId;?>-slider2");
			sl.max=app104_meta.length-1;
			sl.value=newPos;
<? if ($phpdataArr[3]!=0) { ?>
				var tmp=app104_seekEvent("<?echo $winId;?>",newPos,<?echo $phpdataArr[3];?>);
				if (tmp!==false) {
					newPos=tmp;
					sl.value=newPos;
				}
<? } ?>
			app104_metaLoadImage("<?echo $winId;?>",newPos);
		}
<? } if ($cmd=='saveImage') { $err=true; if (!isEmpty($phpdataArr[0])) { if (isEmpty($data)) { $img=app104_loadImage($phpdataArr[1],$phpdataArr[2],$phpdataArr[3],$phpdataArr[7]); } else { $img=base64_decode($data); if (isEmpty($img)) {$img=false;} } if ($img!==false) { if ($phpdataArr[7]==1) { $phpdataArr[3]=sprintf("%06d",$phpdataArr[3]); } else { $phpdataArr[3]='000000'; $phpdataArr[6]='0'; } if (app104_saveImage(MAIN_PATH.'/www/data/tmp/camimg-'.$phpdataArr[6].'-'.$phpdataArr[5].'-'.$phpdataArr[8].'-'.date('Ymd-His',$phpdataArr[0]).'-'.$phpdataArr[3].'-img.jpg',$img)) {$err=false;} app104_updateCopyInfo(); } } if ($err) { ?>
			jsConfirm("Beim Ablegen der Bildkopie ist ein Problem aufgetreten.","","none");
<? } } if ($cmd=='deleteImages') { deleteFiles(MAIN_PATH.'/www/data/tmp/camimg-*.jpg'); app104_updateCopyInfo(); } if ($cmd=='downloadImages') { $tmp=getFilesCount(MAIN_PATH.'/www/data/tmp/camimg-*.jpg'); if ($tmp>0) { $fn=MAIN_PATH.'/www/data/tmp/Kamerabilder-'.date('Ymd-His').'.tar'; $fn_csv=MAIN_PATH.'/www/data/tmp/Kamerabilder.csv'; $fn_tmp=MAIN_PATH.'/www/data/tmp/Kamerabilder.tmp'; $f1=fopen($fn_csv,'w'); $f2=fopen($fn_tmp,'w'); fwrite($f1,'Archiv-ID,Kamera-ID,Ansicht-ID,Datum,Uhrzeit,Mikrosekunden,Dateiname'."\n"); $n=glob(MAIN_PATH.'/www/data/tmp/camimg-*.jpg'); foreach ($n as $pathFn) { if (is_file($pathFn)) { $filename=basename($pathFn); $tmp=explode('-',$filename); if ($tmp[1]==0) {$tmp[1]='DVR';} if ($tmp[3]==0) {$tmp[3]='-';} $d1=substr($tmp[4],6,2).'.'.substr($tmp[4],4,2).'.'.substr($tmp[4],0,4); $d2=substr($tmp[5],0,2).':'.substr($tmp[5],2,2).':'.substr($tmp[5],4,2); fwrite($f1,$tmp[1].','.$tmp[2].','.$tmp[3].','.$d1.','.$d2.','.$tmp[6].','.$filename."\n"); fwrite($f2,$filename."\n"); } } fclose($f1); fclose($f2); exec('tar -cf "'.$fn.'" -C "'.MAIN_PATH.'/www/data/tmp/" "Kamerabilder.csv"'); exec('tar -rf "'.$fn.'" -C "'.MAIN_PATH.'/www/data/tmp/" -T "'.$fn_tmp.'"'); ?>
			document.getElementById("<?echo $winId;?>-iframe").src="apps/app_download.php?filename=<?echo urlencode(basename($fn));?>&sid=<?echo $sid;?>";
	<? } } } sql_disconnect(); function app104_list($defaultId,$mode) { global $winId; ?>
	clearObject("<?echo $winId;?>-list",0);
<? if ($mode==1) { $old_folderId=-1; $ss1=sql_call("SELECT * FROM edomiLive.archivCam ORDER BY folderid ASC,id ASC"); while ($n=sql_result($ss1)) { if ($old_folderId!=$n['folderid']) { if ($n['folderid']>=1000) { $tmp=sql_getValue('edomiLive.root','name','id='.$n['folderid']); ?>
					app104_addFolder("<?echo $winId;?>","<?echo $n['id'];?>","<?ajaxEcho($tmp);?>");
<? } $old_folderId=$n['folderid']; } $anz1=sql_getCount('edomiLive.archivCamData','targetid='.$n['id']); if (!$defaultId>0 && $anz1>0) {$defaultId=$n['id'];} ?>
			if (!app104_views["arc<?echo $n['id'];?>"]) {app104_views["arc<?echo $n['id'];?>"]=0;}
			app104_addItem("<?echo $winId;?>","<?echo $n['id'];?>","<?ajaxValue($n['name']);?>",1,<?echo (($n['id']==$defaultId)?'true':'false');?>,<?echo (($anz1==0)?'true':'false');?>);
<? } sql_close($ss1); } else { $old_folderId=-1; $ss1=sql_call("SELECT id,folderid,name FROM edomiLive.cam WHERE (dvr=1) ORDER BY folderid ASC,id ASC"); while ($n=sql_result($ss1)) { if ($old_folderId!=$n['folderid']) { if ($n['folderid']>=1000) { $tmp=sql_getValue('edomiLive.root','name','id='.$n['folderid']); ?>
					app104_addFolder("<?echo $winId;?>","<?echo $n['id'];?>","<?ajaxEcho($tmp);?>");
<? } $old_folderId=$n['folderid']; } $anz1=count(glob(global_dvrPath.'/cam-'.$n['id'].'-*-1.edomidvr',GLOB_NOSORT)); if (!$defaultId>0 && $anz1>0) {$defaultId=$n['id'];} ?>
			if (!app104_views["dvr<?echo $n['id'];?>"]) {app104_views["dvr<?echo $n['id'];?>"]=0;}
			app104_addItem("<?echo $winId;?>","<?echo $n['id'];?>","<?ajaxValue($n['name']);?>",0,<?echo (($n['id']==$defaultId)?'true':'false');?>,<?echo (($anz1==0)?'true':'false');?>);
<? } sql_close($ss1); } if (!($defaultId>=1)) {return 0;} return $defaultId; } function app104_getFileArray($id,$mode) { global $global_weekdays; if ($mode==1) { $ss1=sql_call("SELECT targetid,camid,MIN(CONCAT(datetime,';',ms)) AS tsmin FROM edomiLive.archivCamData WHERE (targetid=".$id.") GROUP BY DATE(datetime) ORDER BY datetime ASC,ms ASC"); while ($n=sql_result($ss1)) { $tmp=explode(';',$n['tsmin']); $tmp0=strtotime($tmp[0]); $fn=getArchivCamFilename($n['targetid'],$n['camid'],$tmp[0],$tmp[1]); ?>
			app104_files.push(["<?echo $tmp0;?>","<?echo $fn;?>","<?echo $tmp[1];?>","","<?echo $global_weekdays[date('N',$tmp0)-1].'/'.date('d.m.Y/H:i:s',$tmp0).'/'.sprintf("%06d",$tmp[1]);?>"]);
<? } sql_close($ss1); } else { $n=glob(global_dvrPath.'/cam-'.$id.'-*-1.edomidvr',GLOB_NOSORT); foreach ($n as $pathFn) { if (is_file($pathFn)) { $f=fopen($pathFn,'r'); $meta=fgets($f); fclose($f); if ($meta!==false) { $tmp=explode(';',trim($meta)); $eventCount=0; $tmpFn=str_replace('-1.edomidvr','-0.edomidvr',$pathFn); $metaLast=file_get_contents($tmpFn); $tmpLast=explode(';',trim($metaLast)); if (isset($tmpLast[2]) && $tmpLast[2]>0) {$eventCount=intVal($tmpLast[2]);} ?>
					app104_files.push(["<?echo $tmp[0];?>","<?echo $tmp[1].'-'.date('Ymd',$tmp[0]).'-'.date('H',$tmp[0]);?>","<?echo $tmp[2];?>","<?echo $tmp[3];?>","<?echo $global_weekdays[date('N',$tmp[0])-1].'/'.date('d.m.Y/H:i:s',$tmp[0]);?>","<?echo $eventCount;?>"]);
<? } } } ?>
		app104_files.sort(function (a,b) {if (a[0]<b[0]) {return -1;} else if (a[0]>b[0]) {return 1;} return 0;});
<? } } function app104_getMetaArray($id,$ts,$mode) { global $global_weekdays; if ($mode==1) { $ts0=date('Y-m-d 00:00:00',$ts); $ss1=sql_call("SELECT targetid,camid,datetime,ms FROM edomiLive.archivCamData WHERE targetid=".$id." AND datetime>='".$ts0."' AND datetime<DATE_ADD('".$ts0."',INTERVAL 1 DAY) ORDER BY datetime ASC,ms ASC"); while ($n=sql_result($ss1)) { $tmp0=strtotime($n['datetime']); $fn=getArchivCamFilename($n['targetid'],$n['camid'],$n['datetime'],$n['ms']); ?>
			app104_meta.push(["<?echo $tmp0;?>","<?echo $fn;?>","<?echo $n['datetime'];?>","<?echo $n['ms'];?>","<?echo $global_weekdays[date('N',$tmp0)-1].'/'.date('d.m.Y/H:i:s',$tmp0).'/'.sprintf("%06d",$n['ms']);?>","<?echo $n['camid'];?>","<?echo $n['targetid'];?>"]);
<? } sql_close($ss1); } else { $fName=$id.'-'.date('Ymd',$ts).'-'.date('H',$ts); $n=glob(global_dvrPath.'/cam-'.$fName.'-1.edomidvr',GLOB_NOSORT); foreach ($n as $pathFn) { if (is_file($pathFn)) { $meta=file($pathFn); for ($t=0;$t<count($meta);$t++) { $tmp=explode(';',trim($meta[$t])); ?>
					app104_meta.push(["<?echo $tmp[0];?>","<?echo $tmp[1].'-'.date('Ymd',$tmp[0]).'-'.date('H',$tmp[0]);?>","<?echo $tmp[2];?>","<?echo $tmp[3];?>","<?echo $global_weekdays[date('N',$tmp[0])-1].'/'.date('d.m.Y/H:i:s',$tmp[0]);?>","<?echo $tmp[1];?>","<?echo (($tmp[4]!=0)?1:0);?>"]);
<? } } } ?>
		app104_meta.sort(function (a,b) {if (a[0]<b[0]) {return -1;} else if (a[0]>b[0]) {return 1;} return 0;});
<? } } function app104_loadImage($fname,$pos,$len,$mode) { $img=false; if ($mode==1) { $img=file_get_contents(MAIN_PATH.'/www/data/liveproject/cam/archiv/'.$fname.'.jpg'); } else { $f1=fopen(global_dvrPath.'/cam-'.$fname.'-2.edomidvr','rb'); if ($f1) { fseek($f1,$pos); $img=fread($f1,$len); } fclose($f1); } return $img; } function app104_saveImage($fname,$img) { $r=false; $f=fopen($fname,'w'); if (fwrite($f,$img)) {$r=true;} fclose($f); return $r; } function app104_updateCopyInfo() { global $winId; $tmp=getFilesCount(MAIN_PATH.'/www/data/tmp/camimg-*.jpg'); if ($tmp>0) { ?>
		document.getElementById("<?echo $winId;?>-buffinfo").innerHTML="<?echo $tmp;?>";
<? } else { ?>
		document.getElementById("<?echo $winId;?>-buffinfo").innerHTML="0";
<? } } ?>
