/* 
*/ 
/*
============================================================================
Initialisierung
============================================================================
*/

function firstinit(newSid,newTyp,version) {
	//dies wird NUR EINMALIG beim allerersten Start aufgerufen (also nach dem Laden der index.php)

	//--- ggf. anpassen ---
	displayPixelRatio=2;			//Faktor für die virtuelle Auflösung eines CANVAS (z.B. für Retina-Displays sollte der Faktor mindestens 2 betragen)
									//benötigt für: Charts-Widget (Statistik)

	apps_colorSelected="rgba(255,0,255,0.75)";	//Farbe: Selektion von Elementen

	app0_widgetCount=7;				//Widgets: Anzahl der Widgets (1..oo), ggf. anpassen
	app0_widgetLogo=3;				//Widgets: ID des Logo-Widgets, ggf. anpassen

	app0_widgetChartCount=16; 		//Widget Chart: Anzahl der Charts (1..oo), ggf. anpassen
	app0_widgetChartMaxValues=100; 	//Widget Chart: max. Anzahl an Daten pro Chart (2..oo): pro Refresh wird 1 Wert gespeichert, ggf. anpassen
	//---------------------

	sid=newSid;
	accountTyp=newTyp;	
	edomiVersion=version;

	app0_widgetCurrentId=app0_widgetLogo;
		
	app0_widgetChart=new Array();
	desktopWidgetChartReset(false);

	app1000_history={};					//letzte Auswahl in app1000 merken
	app1000_visibleFolders=new Array();	//temporärer Puffer für Collapse-Zustand aller Ordner

	app9999_history=new Array("start");	//Hilfe-Historie (app9999)

	app1_info={};						//Logikeditor: Live-Ansicht

	app104_info={};						//Kameraaufzeichnungen
	app104_views={};						
	app104_files=new Array();
	app104_meta=new Array();
	app104_drag={mode:0,x:0,y:0,v1:0,v2:0,a2:0};

	apps_dragWindow={};					//verschiebbare Fenster
	apps_dragWindow.appId=false;
	apps_dragWindow.winId=false;
	apps_dragWindow.x=0;
	apps_dragWindow.y=0;
	apps_dragWindow.offsetX=0;
	apps_dragWindow.offsetY=0;
	apps_dragWindow.moved=false;
	apps_dragWindow.history={};

	camView=null;						//globale Klasseninstanz für Kamera-Ansichten

	apps_contextMenu=null;				//Kontextmenü: globale Klasseninstanz
	
	initAdmin();
}

function initAdmin() {
	busyID=0; 				//Busy-Fenster (Instanz-Zähler)

	errorTimer=null;  		//Timer für Fehlerbehandlung (ajaxDesktop)

	errorDesktop();
	toggleDesktop(true);
}


/*
============================================================================
Hilfe
============================================================================
*/

function openDesktopHelp() {
	var w=parseInt(window.innerWidth/3);
	document.getElementById("desktopHelpContent").style.width=w+"px";
	document.getElementById("windowContainer").style.right=w+"px";
	document.getElementById('desktopHelp').style.display="block";
	return "desktopHelpContent";
}

function closeDesktopHelp() {
	document.getElementById('desktopHelp').style.display="none";
	document.getElementById("windowContainer").style.right="0px";
	app9999_history=new Array("start");
}


/*
============================================================================
Desktop-Notes (Notizen)
============================================================================
*/

function enableDesktopNotes() {
	document.getElementById('desktopNotesButton').style.display='block';
	document.getElementById("windowContainer").style.bottom="0px";
}

function disableDesktopNotes() {
	document.getElementById('desktopNotesButton').style.display='none';
	closeDesktopNotes();
}

function openDesktopNotes() {
	var h=parseInt(window.innerHeight/3);
	document.getElementById("windowContainer").style.bottom=h+"px";
	document.getElementById("desktopNotesContent").style.height=h+"px";
	document.getElementById('desktopNotes').style.display='block';
	document.getElementById('desktopHelp').style.bottom=h+"px";
	ajax('desktopNotesOpen','0','','','');
}

function saveDesktopNotes() {
	ajax("desktopNotesSave",0,"","",stringCleanup(document.getElementById('desktopNotesContent2').value));
}

function closeDesktopNotes() {
	document.getElementById("windowContainer").style.bottom="0px";
	document.getElementById('desktopNotes').style.display='none';
	document.getElementById('desktopHelp').style.bottom="0px";
}

function deleteDesktopNotes(mode) {
	if (mode==1 || mode==3) {document.getElementById('desktopNotesContent1').value="";}
	if (mode==2 || mode==3) {document.getElementById('desktopNotesContent2').value="";}
}

function pushDesktopNotes(mode,n) {
	if (mode==1 || mode==3) {
		var obj=document.getElementById('desktopNotesContent1');
		if (n!="" && obj.value!="") {n="\n"+n;}
		obj.value+=n.replace(/<br>/g,"\n");
	}
	if (mode==2 || mode==3) {
		var obj=document.getElementById('desktopNotesContent2');
		if (n!="" && obj.value!="") {n="\n"+n;}
		obj.value+=n.replace(/<br>/g,"\n");
	}
}


/*
============================================================================
Desktop (app0)
============================================================================
*/

function desktopLogoClick() {
	if (accountTyp=="0") {toggleDesktop();} else {shakeObj("desktopLogo");}
}

function desktopClick() {
	var obj=document.getElementById("desktopDisc");
	if (accountTyp=="0") {
		if (obj.dataset.inited=="0") {toggleDesktop();}
	} else {
		shakeObj("desktopLogo");
	}
}

function toggleDesktop(forceInit) {
	var obj=document.getElementById("desktopDisc");
	if (accountTyp=="0" && !forceInit && obj.dataset.inited=="0" && obj.dataset.locked=="0") {
		obj.dataset.inited="1";
		if (obj.dataset.timer) {clearTimeout(obj.dataset.timer);}

		obj.style.webkitTransform="scale(0.1)";
		obj.style.opacity="0.25";
		document.getElementById("desktopTable").style.background="transparent";
		desktopShowStatus(-2);

		document.getElementById("desktopContent").style.display="none";
		document.getElementById("desktopWidgets").style.display="none";
		enableDesktopNotes();
		desktopShowWidget(app0_widgetLogo);
		desktopInfoHelp("0-1");

	} else if (forceInit || obj.dataset.inited=="1") {
		obj.dataset.inited="0";
		ajaxDesktop("refreshAll","0","",app0_widgetLogo,"");

		obj.style.webkitTransform="scale(1)";
		obj.style.opacity="1";
		document.getElementById("desktopTable").style.background="rgba(0,0,0,0.7)";
		desktopShowStatus(-2);

		document.getElementById("desktopButtonRestart").style.webkitAnimation="none";
		document.getElementById("desktopButtonStart").style.webkitAnimation="none";
		document.getElementById("desktopButtonPause").style.webkitAnimation="none";
		disableDesktopNotes();
		desktopInfoHelp("0-0");
	}
}

function desktopShowStatus(status) {
	if (status==3) {
		document.getElementById("desktopDisc").style.boxShadow="0 0 10px 3px #80e000";
	} else if (status==2) {
		document.getElementById("desktopDisc").style.boxShadow="0 0 10px 3px #c9e000";
	} else if (status==1) {
		document.getElementById("desktopDisc").style.boxShadow="0 0 10px 3px #f0e000";
	} else if (status==0) {
		document.getElementById("desktopDisc").style.boxShadow="0 0 10px 3px #ffa000";
	} else if (status==-1) {
		document.getElementById("desktopDisc").style.boxShadow="0 0 10px 3px #ff0000";
	} else {
		document.getElementById("desktopDisc").style.boxShadow="none";
	}
}

function desktopInfoShow() {
	document.getElementById("desktopInfo").style.display="inline-block";
}

function desktopInfoHide() {
	document.getElementById("desktopInfo").style.display="none";
}

function desktopInfoAccount(login,ip) {
	if (document.getElementById("desktopInfo1").dataset.buffer!==(login+"/"+ip)) {
		document.getElementById("desktopInfo1").innerHTML="<span style='padding:3px;'>"+login+"</span> &middot; <span style='padding:3px;'>"+ip+"</span>";
		document.getElementById("desktopInfo1").dataset.buffer=(login+"/"+ip);
	}
}

function desktopInfoHelp(n) {
	if (document.getElementById("desktopInfo2").dataset.buffer!==n) {
		document.getElementById("desktopInfo2").innerHTML="<div onClick=\"openWindow(9999,'"+n+"');\" class='cmdHelp' style='float:none; color:#393930;'></div>";
		document.getElementById("desktopInfo2").dataset.buffer=n;
	}
}

function desktopMoveWidget(step) {
	var newId=app0_widgetCurrentId+step;
	if (newId>=0 && newId<app0_widgetCount) {desktopShowWidget(newId);}
}

function desktopShowWidget(id) {
	if (id<0) {id=app0_widgetLogo;}
	app0_widgetCurrentId=id;
	if (id==app0_widgetLogo) {
		document.getElementById("desktopLogo").style.visibility="visible";
		document.getElementById("desktopLogo").style.opacity="1";
		document.getElementById("desktopWidgets").innerHTML="";
		document.getElementById("desktopWidgets").style.display="none";
	} else {
		document.getElementById("desktopLogo").style.visibility="hidden";
		document.getElementById("desktopLogo").style.opacity="0";
		document.getElementById("desktopWidgets").innerHTML='<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto;"><tr valign="middle"><td align="center"><div class="desktopWidgetBusy"></div></td></tr></table>';
		document.getElementById("desktopWidgets").style.display="block";
		ajaxDesktop("refreshAll","0","",app0_widgetCurrentId,"");	//Widget sofort aufrufen
	}

	var n='';
	for (var t=0;t<app0_widgetCount;t++) {
		if (t==id) {
			if (t==app0_widgetLogo) {
				n+='<div onClick="desktopShowWidget('+t+');" style="display:inline-block; width:20px; height:16px; cursor:pointer;"><div style="margin-top:3px; width:8px; height:8px; border-radius:8px; border:1px solid #ffffff;"></div></div>';
			} else {
				n+='<div onClick="desktopShowWidget('+t+');" style="display:inline-block; width:15px; height:16px; cursor:pointer;"><div style="margin-top:5px; width:6px; height:6px; border-radius:6px; background:#ffffff;"></div></div>';
			}
		} else {
			if (t==app0_widgetLogo) {
				n+='<div onClick="desktopShowWidget('+t+');" style="display:inline-block; width:20px; height:16px; cursor:pointer;"><div style="margin-top:3px; width:8px; height:8px; border-radius:8px; border:1px solid #797970;"></div></div>';
			} else {
				n+='<div onClick="desktopShowWidget('+t+');" style="display:inline-block; width:15px; height:16px; cursor:pointer;"><div style="margin-top:5px; width:6px; height:6px; border-radius:6px; background:#797970;"></div></div>';
			}
		}
	}
	document.getElementById("desktopWidgetsControlLink").innerHTML=n;
}

function errorDesktop() {
	document.getElementById("desktopDisc").dataset.locked="1";
	desktopShowStatus(-2);
	document.getElementById("desktopContent").style.display="none";
	desktopShowWidget(app0_widgetLogo);
}

function gotoDesktop(delay) {
	//schließt (entfernt) alle Fenster und geht zur Startseite (Ring)
	//delay: 0=sofort, 1..oo=Verzögerung in Sekunden
	if (document.getElementById("desktopDisc").dataset.timer) {clearTimeout(document.getElementById("desktopDisc").dataset.timer);}
	if (delay>0) {
		openBusyWindow();
		//während der Wartezeit darf kein Ajax mehr verwendet werden, sonst verschwindet das BusyWindow wieder...
		window.setTimeout(function(){closeAllWindows();},(delay*1000));
	} else {
		closeAllWindows();
	}
}

function desktopWidgetChartReset(refresh) {
	for (var t=0;t<app0_widgetChartCount;t++) {
		app0_widgetChart[t]=new Array();
	}
	if (refresh) {ajaxDesktop("refreshAll","0","",app0_widgetCurrentId,"");}	//Widget sofort aktualisieren
}

function desktopWidgetChartInsertValue(chartId,value,legend,logScale) {
	var chartValue=value;
	if (chartValue>100) {chartValue=100};
	if (chartValue<0) {chartValue=0};
	if (logScale && chartValue!=null) {
		chartValue=parseInt(((Math.log(chartValue+1)/Math.log(10))*(100/(Math.log(100+1)/Math.log(10)))));
	}
	
	//Value einfügen
	if (app0_widgetChart[chartId].length>=app0_widgetChartMaxValues) {app0_widgetChart[chartId].pop();}
	app0_widgetChart[chartId].unshift(chartValue);

	//Chart zeichnen
	var canvas=document.getElementById("desktopWidgetChart-c"+chartId);
	if (canvas) {
		if (canvas.getContext) {
			var c=canvas.getContext("2d");

			var cWidth=canvas.style.width.replace("px","");		//Canvas-Größe (real, ohne Faktor)
			var cHeight=canvas.style.height.replace("px","");

			var leftMargin=2;	//Abstände des Charts zum Canvas (Platzhalter)
			var rightMargin=2;
			var topMargin=2;
			var bottomMargin=2;
			var chartColor=canvas.style.color;
			var axisColor="#696960";

			//Retina-Skalierung
			canvas.width=cWidth*displayPixelRatio;
			canvas.height=cHeight*displayPixelRatio;
			c.scale(displayPixelRatio,displayPixelRatio);

			c.clearRect(0,0,cWidth,cHeight);

			var xfak=(cWidth-leftMargin-rightMargin)/(app0_widgetChartMaxValues-1);
			var yfak=(cHeight-topMargin-bottomMargin)/100;
			var tmax=(app0_widgetChart[chartId].length-1);

			if (value==null) {
				c.globalAlpha=0.25;
			} else {
				c.globalAlpha=1;
			}
			c.strokeStyle=axisColor;
			c.lineWidth=1;
			c.lineCap="round";
			c.lineJoin="round";

			if (logScale) {
				c.beginPath();
				c.moveTo( leftMargin , cHeight-bottomMargin );
				c.lineTo( cWidth-rightMargin , cHeight-bottomMargin );
				c.stroke();
				c.setLineDash([4,4]);
				c.beginPath();
				c.moveTo( cWidth-rightMargin , cHeight-bottomMargin );
				c.lineTo( cWidth-rightMargin , cHeight/3 );
				c.stroke();
				c.setLineDash([]);
				c.setLineDash([1,2]);
				c.beginPath();
				c.moveTo( cWidth-rightMargin , cHeight/3 );
				c.lineTo( cWidth-rightMargin , topMargin );
				c.stroke();
				c.setLineDash([]);
			} else {
				c.beginPath();
				c.moveTo( leftMargin , cHeight-bottomMargin );
				c.lineTo( cWidth-rightMargin , cHeight-bottomMargin );
				c.lineTo( cWidth-rightMargin , topMargin );
				c.stroke();
			}

			c.strokeStyle=chartColor;
			c.lineWidth=2;

			if (tmax>0) {
				c.beginPath();
				c.moveTo( cWidth-rightMargin , (cHeight-bottomMargin)-(yfak*parseInt(app0_widgetChart[chartId][0])) );
				for (var t=1; t<=tmax; t++) {
					c.lineTo( (cWidth-rightMargin)-(t*xfak) , (cHeight-bottomMargin)-(yfak*parseInt(app0_widgetChart[chartId][t])) );
				}
				c.stroke();
			}

			c.textAlign="center";
			c.textBaseline="middle";
			if (value==null) {
				c.globalAlpha=1;
				c.font="10px EDOMIfont,Lucida Grande,Arial";
				c.fillStyle="#494949";
				c.fillText("keine Daten",cWidth/2,cHeight/2);
			} else {
				c.globalAlpha=0.5;
				c.font="16px EDOMIfont,Lucida Grande,Arial";
				c.fillStyle=chartColor;
				c.fillText(value+""+legend,cWidth/2,cHeight/2);
			}
		}
	}
}

function desktopWidgetToggelPause(obj) {
	if (obj.dataset.paused=="1") {
		obj.style.backgroundImage="none";
		obj.dataset.paused="0";
	} else {
		obj.style.backgroundImage="url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAAXNSR0IArs4c6QAAABZJREFUCB1jYEAD/18zGKMIUUMAaCAAM4kKkt8foVoAAAAASUVORK5CYII=')";
		obj.dataset.paused="1";
	}
}

function ajaxDesktop(cmd,appID,winID,data,phpdata) {
	var req;
	req=new XMLHttpRequest();
	req.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			try {
				hideBusyWindow();
				eval(this.responseText);
				//console.log(this.responseText);
			} catch(e) {
				hideBusyWindow();
				console.log("Ajax-Response fehlerhaft! Error-Msg: "+e.message+" / URL: "+decodeURIComponent(url)+" / Response: "+this.responseText);
			}
		}
		if (this.readyState==4 && this.status!=200) {
			console.log("Ajax-Abruf gescheitert! http-status: "+this.status+" / URL: "+decodeURIComponent(url));
			ajaxDesktopError(cmd,appID,winID,data,phpdata);
		}
	}
	//POST
	var url="apps/app"+appID+".php?cmd="+encodeURIComponent(cmd)+"&appid="+encodeURIComponent(appID)+"&winid="+encodeURIComponent(winID)+"&datetime="+encodeURIComponent(getClientDatetime(4))+"&sid="+encodeURIComponent(sid)+"&vid="+encodeURIComponent(edomiVersion);
	req.open("POST",url,true);
	req.timeout=10000; //nach 5s: Timeout
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send("data="+encodeURIComponent(data)+"&phpdata="+encodeURIComponent(phpdata));
}

function ajaxDesktopError(cmd,appID,winID,data,phpdata) {
	hideBusyWindow();
	openBusyWindow();
	clearTimeout(errorTimer);
	errorDesktop();
	errorTimer=window.setTimeout(function(){ajaxDesktop(cmd,appID,winID,data,phpdata);},3*1000);
}


/*
============================================================================
Fenster-Verwaltung
============================================================================
*/

function openWindow(appID,data,phpdata) {	
	if (appID==9999) {
		//Sonderfall: Hilfe-Fenster
		var winID=openDesktopHelp();
	} else {
		var winID=newWindow();
	}
	if (!phpdata) {phpdata='';}
	ajax("initApp",appID,winID,data,phpdata);
	return winID;
}

function closeWindow(windowID) {
	var div=document.getElementById(windowID);
	if (windowID && div) {
		div.parentNode.removeChild(div);
	}

	var childs=document.getElementById("windowContainer").childNodes;
	if (childs.length==0) {document.getElementById("windowContainer").style.display="none";}
}

function closeAllWindows() {
	clearObject("windowContainer",0);
	document.getElementById("windowContainer").style.display="none";
	initAdmin();
}

function newWindow() {
	if (document.getElementById("windowContainer").lastChild) {
		var newID=parseInt(document.getElementById("windowContainer").lastChild.id.replace("w",""))+1;
	} else {
		var newID=0;
	}
	var winID="w"+newID;
	var div=document.createElement("div");
	document.getElementById("windowContainer").appendChild(div);
	div.className="appWindow";
	div.id=winID;

	div.onclick=function() {flashWindow(this);}

	var childs=document.getElementById("windowContainer").childNodes;
	document.getElementById("windowContainer").style.display="block";
	return winID;
}

function flashWindow(obj) {
	var event=window.event;
	if (event.target===obj) {
		obj.style.webkitAnimation="none";
		obj.style.webkitAnimationPlayState="paused";
		setTimeout(function(){obj.style.webkitAnimation="animFlash 0.3s linear"; obj.style.webkitAnimationPlayState="running";},100);
	}
}

function openBusyWindow() {
	document.getElementById('busy').style.display="block";
	busyID++;
}

function hideBusyWindow() {
	if (busyID<=1) {
		document.getElementById('busy').style.display="none";
		busyID=0;
	} else {
		busyID--;
	}
}

function dragWindowStart(appId,winId) {
	var event=window.event;
	var obj=document.getElementById(winId);
	if (obj) {
		apps_dragWindow.appId=appId;
		apps_dragWindow.winId=winId;
		apps_dragWindow.offsetX=event.pageX-obj.offsetLeft;
		apps_dragWindow.offsetY=event.pageY-obj.offsetTop;
		apps_dragWindow.moved=false;
		window.addEventListener("mousemove",dragWindowMove,false);
		window.addEventListener("mouseup",dragWindowEnd,false);
	}
}

function dragWindowMove() {
	var event=window.event;
	var obj=document.getElementById(apps_dragWindow.winId);
	if (obj) {
		apps_dragWindow.x=(event.pageX-apps_dragWindow.offsetX);
		apps_dragWindow.y=(event.pageY-apps_dragWindow.offsetY);
		if ((apps_dragWindow.x+obj.offsetWidth)<100) {apps_dragWindow.x=100-obj.offsetWidth;}
		if (apps_dragWindow.y<0) {apps_dragWindow.y=0;}
		obj.style.position="absolute";
		obj.style.left=apps_dragWindow.x+"px";
		obj.style.top=apps_dragWindow.y+"px";
		apps_dragWindow.moved=true;
	}
}

function dragWindowEnd() {
	var obj=document.getElementById(apps_dragWindow.winId);
	if (obj && apps_dragWindow.moved) {
		apps_dragWindow.history["app"+apps_dragWindow.appId]=[apps_dragWindow.x,apps_dragWindow.y];
	}
	window.removeEventListener("mousemove",dragWindowMove,false);
	window.removeEventListener("mouseup",dragWindowEnd,false);
	apps_dragWindow.appId=false;
	apps_dragWindow.winId=false;
}

function dragWindowRestore(appId,winId) {
	var obj=document.getElementById(winId);
	if (obj && apps_dragWindow.history["app"+appId]) {
		obj.style.position="absolute";
		obj.style.left=apps_dragWindow.history["app"+appId][0]+"px";
		obj.style.top=apps_dragWindow.history["app"+appId][1]+"px";
	}
}


/*
============================================================================
Hilfsfunktionen
============================================================================
*/

function getClientDatetime(mode) {
	//mode: 1=Datum, 2=Uhrzeit, 3=Beides mit " / " getrennt, 4=Beides mit " " getrennt
	var d=new Date(); 
	var d1=("0"+d.getDate()).slice(-2)+"."+("0"+(d.getMonth()+1)).slice(-2)+"."+("0000"+d.getFullYear()).slice(-4);
	var d2=("0"+d.getHours()).slice(-2)+":"+("0"+d.getMinutes()).slice(-2)+":"+("0"+d.getSeconds()).slice(-2);
	if (mode==1) {return d1;}
	if (mode==2) {return d2;}
	if (mode==3) {return d1+" / "+d2;}
	if (mode==4) {return d1+" "+d2;}
}

function createNewDiv(parentId,id) {
	//erzeugt neues Div und hängt es an parent
	//objParent: Parent-Objekt
	//id: gewünschte ID des neuen DIVs
	var div=document.createElement('div');
	document.getElementById(parentId).appendChild(div);
	if (id) {div.id=id;}
	return div;
}

function clickCancel() {
	//verhindert bei verschachtelten DIVs, dass das Click-Event vom Parent bearbeitet wird, wenn auf das Child geklickt wird
	var event=window.event;
	event.cancelBubble=true;
	if (event.stopPropagation) {event.stopPropagation();}
}

function clearObject(objId,mode) {
	//Leert ein Objekt (entspricht quasi obj.innerHTML="")
	//mode: 1=setzt obj.style.display auf "none"
	var obj=document.getElementById(objId);
	if (mode==1) {obj.style.display="none";}
	while (obj.firstChild) {obj.removeChild(obj.firstChild);}
}

function scrollToObject(containerId,objId,force) {
	//scrollt den Container (containerId) zum Objekt (objId), so dass das Objekt mittig im Container zu sehen ist (falls möglich)
	//force: false=nur scrollen wenn erforderlich (das Objekt also sonst nicht sichtbar wäre), true=immer scrollen
	var container=document.getElementById(containerId);
	var obj=document.getElementById(objId);
	if (obj && container) {
		var containerBound=container.getBoundingClientRect();
		var objBound=obj.getBoundingClientRect();		
		var objY=obj.offsetTop; 					//Position des Objects relativ zum Container
		var objH=objBound.height;					//Höhe des Objects
		var containerH=containerBound.height;		//sichtbare Höhe des Containers
		var containerY=container.scrollTop;			//Scrollposition des Containers
		if (force || (objY+objH)>(containerY+containerH) || objY<containerY) {
			var scrollY=parseInt(objY+objH-(containerH/2));
			container.scrollTop=((scrollY>0)?scrollY:0);
		}
	}
}

function shakeObj(objId) {
	var obj=document.getElementById(objId);
	obj.style.webkitAnimation="none";
	obj.style.webkitAnimationPlayState="paused";
	setTimeout(function(){document.getElementById(objId).style.webkitAnimation="animShake 0.3s linear"; document.getElementById(objId).style.webkitAnimationPlayState="running";},100);
}


/*
============================================================================
Ajax, Login/Logout und Alert-/Confirm-Dialoge
============================================================================
*/

function ajax(cmd,appID,winID,data,phpdata,disableBusy) {
	if (disableBusy!==true) {openBusyWindow();}
	var req;
	req=new XMLHttpRequest();
	req.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			try {
				eval(this.responseText);
				//console.log(this.responseText);
			} catch(e) {
				console.log("Ajax-Response fehlerhaft! Error-Msg: "+e.message+" / URL: "+decodeURIComponent(url)+" / Response: "+this.responseText);
			}
			if (disableBusy!==true) {hideBusyWindow();}
		}
		if (this.readyState==4 && this.status!=200) {
			console.log("Ajax-Abruf gescheitert! http-status: "+this.status+" / URL: "+decodeURIComponent(url));
		}
	}
	//POST
	var url="apps/app"+appID+".php?cmd="+encodeURIComponent(cmd)+"&appid="+encodeURIComponent(appID)+"&winid="+encodeURIComponent(winID)+"&sid="+encodeURIComponent(sid)+"&vid="+encodeURIComponent(edomiVersion);
	req.open("POST",url,true);
	req.timeout=300000; //nach 5 Minuten: Timeout
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send("data="+encodeURIComponent(data)+"&phpdata="+encodeURIComponent(phpdata));
}

function jsLogin() {
	//Logindialog (modal)
	if (!document.getElementById("loginform")) {
		var loginWinId=newWindow();
		var n="<div id='loginform' class='appWindowConfirm' onkeydown='if (event.keyCode==13) {ajax(\"login\",0,\""+loginWinId+"\",\"\",controlGetFormData(\"loginform\"));}' style='width:200px;'>";
			n+="<div style='margin-bottom:10px;'><b>Login</b></div>";
			n+="<div style='margin-bottom:15px; color:#a0a0a0;'>Administration</div>";
			n+="<input type='text' id='"+loginWinId+"-fd1' data-type='1' value='' maxlength='30' class='control1' autofocus style='width:100%; height:25px; padding:5px; border-color:transparent;'></input><br>";
			n+="<input type='password' id='"+loginWinId+"-fd2' data-type='1' value='' maxlength='30' class='control1' style='width:100%; height:25px; padding:5px; border-color:transparent;'></input><br>";
			n+="<br>";
			n+="<div class='cmdButton' onClick='ajax(\"login\",0,\""+loginWinId+"\",\"\",controlGetFormData(\"loginform\"));' style='float:right; border-color:transparent;'>Ok</div>";
		n+="</div>";
		document.getElementById(loginWinId).innerHTML=n;
		document.getElementById(loginWinId).style.background="url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAAXNSR0IArs4c6QAAAChJREFUCB1jZGBgMAZiZODDhMwDsn2AeAuyIFgApAgmCBeACaIIgAQBzpEEiaAV3YQAAAAASUVORK5CYII=')";
		deleteDesktopNotes(3);
	}
	closeDesktopHelp();
	desktopInfoHide();
}

function jsLogout() {
	sid=false;
	closeDesktopHelp();
	gotoDesktop(0);
}

function ajaxConfirm(msg,cmd,appID,winID,data,phpdata,btnLeft,btnRight) {
	//Confirmdialog (modal): führt bei "Ok" einen Ajax-Befehl aus
	//msg: Dialogtext
	//cmd,...: Ajax-Daten
	//btnLeft,btnRight: OPTIONAL! Wenn angegeben, werden die Buttons entsprechend bezeichnet. "none" blendet den Button aus!
	if (!btnLeft) {btnLeft="Abbrechen";}
	if (!btnRight) {btnRight="Ok";}
	var askWinId=newWindow();
	var n="<div class='appWindowConfirm' style='width:400px;'>";
		n+="<div style='margin-bottom:15px; max-height:300px; overflow:auto;'>"+msg+"</div>";
		if (btnLeft!="none") {n+="<div class='cmdButton' onClick='closeWindow(\""+askWinId+"\");' style='min-width:100px; float:left; border-color:transparent;'>"+btnLeft+"</div>";}
		if (btnRight!="none") {n+="<div class='cmdButton' id='"+askWinId+"-buttonok' data-enabled='1' onClick='ajaxConfirmYes(\""+askWinId+"\",\""+cmd+"\",\""+appID+"\",\""+winID+"\",\""+data+"\",\""+phpdata+"\");' style='min-width:100px; float:right; border-color:transparent;'>"+btnRight+"</div>";}
	n+="</div>";
	document.getElementById(askWinId).innerHTML=n;
}

function ajaxConfirmSecure(msg,cmd,appID,winID,data,phpdata,btnLeft,btnRight) {
	//Confirmdialog (modal), besonders sicher: führt bei "Ok" einen Ajax-Befehl aus. "Ok" muss zuvor freigeschaltet werden.
	//msg: Dialogtext
	//cmd,...: Ajax-Daten
	//btnLeft,btnRight: OPTIONAL! Wenn angegeben, werden die Buttons entsprechend bezeichnet.
	var askWinId=newWindow();
	var n="<div class='appWindowConfirm' style='width:400px;'>";
		n+="<div style='margin-bottom:15px; max-height:300px; overflow:auto;'>"+msg+"</div>";
		n+="<div class='cmdButton' onClick='closeWindow(\""+askWinId+"\");' style='min-width:100px; float:left; border-color:transparent;'>"+((btnLeft)?btnLeft:"Abbrechen")+"</div>";
		n+="<div class='cmdButton' onClick='ajaxConfirmSecureEnable(\""+askWinId+"\");' style='min-width:30px; float:right; border-color:transparent;'>&lt;</div>";
		n+="<div class='cmdButton' id='"+askWinId+"-buttonok' data-enabled='0' onClick='ajaxConfirmYes(\""+askWinId+"\",\""+cmd+"\",\""+appID+"\",\""+winID+"\",\""+data+"\",\""+phpdata+"\");' style='opacity:0.3; min-width:100px; float:right; border-color:transparent;'>"+((btnRight)?btnRight:"Ok")+"</div>";
	n+="</div>";
	document.getElementById(askWinId).innerHTML=n;
}
function ajaxConfirmSecureEnable(askWinId) {
	document.getElementById(askWinId+"-buttonok").style.opacity="1";
	document.getElementById(askWinId+"-buttonok").dataset.enabled=1;
}
function ajaxConfirmYes(askWinId,cmd,appID,winID,data,phpdata) {
	if (document.getElementById(askWinId+"-buttonok").dataset.enabled==1) {
		closeWindow(askWinId);
		ajax(cmd,appID,winID,data,phpdata);
	}
}

function jsConfirm(msg,cmd,btnLeft,btnRight,width) {
	//Confirmdialog (modal): führt bei "Ok" einen JS-Befehl aus
	//msg: Dialogtext
	//cmd: JS-Funktion (als String), z.B. "deleteElement();"
	//		Wichtig: String als Parameter in der JS-Funktion müssen speziell markiert werden: statt "deleteElement("bla");" => "deleteElement($bla$);"
	//btnLeft,btnRight: OPTIONAL! Wenn angegeben, werden die Buttons entsprechend bezeichnet. "none" blendet den Button aus!
	//width: OPTIONAL! Breite in px oder "auto" - Default ist 400px
	if (!btnLeft) {btnLeft="Abbrechen";}
	if (!btnRight) {btnRight="Ok";}
	if (!width) {width="400px";}
	var askWinId=newWindow();
	var n="<div class='appWindowConfirm' style='width:"+width+";'>";
		n+="<div style='margin-bottom:15px; max-height:300px; overflow:auto;'>"+msg+"</div>";
		if (btnLeft!="none") {n+="<div class='cmdButton' onClick='closeWindow(\""+askWinId+"\");' style='min-width:100px; float:left; border-color:transparent;'>"+btnLeft+"</div>";}
		if (btnRight!="none") {n+="<div class='cmdButton' onClick='jsConfirmYes(\""+askWinId+"\",\""+cmd+"\");' style='min-width:100px; float:right; border-color:transparent;'>"+btnRight+"</div>";}
	n+="</div>";
	document.getElementById(askWinId).innerHTML=n;
}
function jsConfirmYes(askWinId,cmd) {
	closeWindow(askWinId);
	cmd=cmd.replace(/\$/g,"'");
	eval(cmd);
}

