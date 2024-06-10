/* 
*/ 
function app1_init(winId) {

	//--- ggf. anpassen ---
	app1_zoomFaktor=1;												//Default-Zoomfaktor (beliebig definierbar)
	app1_el_colorLine="#f0f0f0";									//Linienfarbe
	app1_el_lineWidth=2;											//Liniendicke
	app1_el_shadowSelected="0px 0px 0px 5px "+apps_colorSelected;	//Schatten eines Elements, wenn markiert
	app1_grid=5;													//Raster für LBS-Positionierung (X und Y)
	app1_el_rowHeight=15;											//Höhe eines Ein-/Ausgangs (korrespondierend zum CSS!)
	//---------------------

	app1_winId=winId;
	app1_pageId=0;
	app1_liveEnabled=0;						//0=keine Live-Werte verfügbar, 1=verfügbar
	app1_liveMode=0;						//0=keine Live-Werte anzeigen, 1=Live-Werte anzeigen, 2=dto. aber mit KO-Tabelle

	app1_bufferSelected=null;				//Zwischenspeicher für Selektion
	app1_elementSelectRectData={mode:0,x1:0,y1:0,x2:0,y2:0};	//Auswahlrahmen

	app1_curPosX=0;							//X-Position der Maus beim Einfügen eines LBS per Rechtsklick
	app1_curPosY=0;							//Y-Position der Maus beim Einfügen eines LBS per Rechtsklick

	//GUI-Ereignisse
	app1_GUImode=[false,false,false]; 		//Element wird gerade verschoben, Element-Verbindung wird gerade erstellt, Element wird gerade umbenannt

	//Verschieben eines Elements
	app1_dragElements=new Array();
	app1_dragElement=null; 					//Puffer für das angeklickte Element: elementIDindex, Select-Status vor dem Draggen

	//Verbindung erstellen
	app1_objConnect=[null,null]; 			//obj1,obj2 (Ausgang,Eingang)

	//Verbindungen zw. den Elementen
	app1_rD=new Array();

	//Default-Zoom einstellen
	app1_zoom(app1_zoomFaktor);

	app1_layerMode=0;						//aktueller Layer-Modus
}

function app1_setPageSize() {
	//Dummy-DIV zur Erweiterung des Arbeitsbereichs einfügen
	var page=document.getElementById(app1_winId+"-page");
	var d=document.getElementById(app1_winId+"-scaledummy");
	if (!d) {var d=app1_newDiv(page,app1_winId+"-scaledummy");}

	var tmp=page.getBoundingClientRect();
	var offsetX=tmp.left*(1/app1_zoomFaktor)-parseInt(page.scrollLeft);
	var offsetY=tmp.top*(1/app1_zoomFaktor)-parseInt(page.scrollTop);
	var maxX=0;
	var maxY=0;
	var elements=page.querySelectorAll("[data-elementselected]");
	for (var t=0; t<elements.length; t++) {
		var tmp=elements[t].getBoundingClientRect();
		var x=(tmp.right*(1/app1_zoomFaktor))-offsetX;
		var y=(tmp.bottom*(1/app1_zoomFaktor))-offsetY;
		if (x>maxX) {maxX=parseInt(x);}
		if (y>maxY) {maxY=parseInt(y);}
	}
	if (maxX==0 && maxY==0) {
		page.removeChild(d);
	} else {
		d.style.left=(maxX)+"px";
		d.style.top=(maxY)+"px";	
		d.style.width="50px";
		d.style.height="50px";
		d.style.borderLeft="1px solid #909090";
		d.style.borderTop="1px solid #909090";
		d.innerHTML="<div style='color:#909090; padding:2px;'>"+maxX+"<br>"+maxY+"</div>";
	}
}

function app1_zoom(n) {
	app1_zoomFaktor=n;
	var d=document.getElementById(app1_winId+"-page");
	d.style.webkitTransform="scale("+app1_zoomFaktor+")";
	d.style.width=((1/app1_zoomFaktor)*100)+"%";
	d.style.height=((1/app1_zoomFaktor)*100)+"%";
	document.getElementById(app1_winId+"-zoom").innerHTML=parseInt(n*100)+"%";
}

function app1_quit(winId) {
	app1_winId=null;
	app1_GUImode.length=0;
	app1_dragElements.length=0;
	app1_objConnect.length=0;
	app1_rD.length=0;
	closeWindow(winId);
}

function app1_refreshAll(pageId,list,disableBusy) {
	//Alle Elemente in "page" komplett löschen (wird i.d.R. von app1.php aufgerufen)
	//wird bei JEDEM Neuaufbau der Elemente aufgerufen (z.B. nach dem Löschen/Erstellen/Verbinden/...)
	//pageId: 0=aktuelle Seite refreshen, 1..x=diese Seite aufrufen
	//list: String mit den IDs der Elemente, die beim Aufbau markiert sein sollen (für Duplizierung): z.B. "1;2;3;4;5;"

	if (pageId==0) {
		pageId=app1_pageId;
		app1_saveState();
	} else {
		app1_bufferSelected=new Array();
	}

	if (!list) {list="";}

	app1_GUImode=[false,false,false];
	app1_dragElements=new Array();
	app1_dragElement=null;
	app1_objConnect=[null,null];
	app1_rD=new Array();

	app1_curPosX=0;
	app1_curPosY=0;

	app1_pageId=pageId;
	
	ajax("start",1,app1_winId,pageId+AJAX_SEPARATOR1+app1_liveMode,list,disableBusy);
}

function app1_saveState() {
	app1_bufferSelected=new Array();
	if (app1_pageId>0) {
		var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {app1_bufferSelected.push(element[t].id);}
	}
}

function app1_restoreState(list) {
	app1_elementSelectNone();
	if (list) {
		var element=list.split(";");
		for (var t=0; t<element.length; t++) {app1_elementSelect(app1_winId+"-element-"+element[t],1);}
	} else {
		for (var t=0; t<app1_bufferSelected.length; t++) {app1_elementSelect(app1_bufferSelected[t],1);}
	}
	app1_bufferSelected=new Array();
}


function app1_liveSwitch(enabled) {
	if (enabled) {
		document.getElementById(app1_winId+"-btnLive0").style.display="inline-block";
		document.getElementById(app1_winId+"-btnLive1").style.display="inline-block";
		document.getElementById(app1_winId+"-btnLive2").style.display="inline-block";
		app1_liveEnabled=true;
	} else {
		document.getElementById(app1_winId+"-btnLive0").style.display="none";
		document.getElementById(app1_winId+"-btnLive1").style.display="none";
		document.getElementById(app1_winId+"-btnLive2").style.display="none";
		app1_liveEnabled=false;
	}
}

function app1_live(mode,clickmode) {
	if (clickmode==1) {
		app1_info.play=true;
		app1_liveDo(mode,true);
	} else {
		app1_info.play=false;
	}
}

function app1_liveDo(mode,play) {
	if (mode==2) {
		if (play) {
			document.getElementById(app1_winId+"-btnLive2").style.background="#ff8000";
		} else {
			document.getElementById(app1_winId+"-btnLive2").style.background="#e8e800";
		}
		document.getElementById(app1_winId+"-btnLive1").style.background="";
		document.getElementById(app1_winId+"-btnLive0").style.background="";
		app1_liveMode=2;
		if (play) {app1_refreshAll(0,"",true);}
	} else if (mode==1) {
		document.getElementById(app1_winId+"-btnLive2").style.background="";
		if (play) {
			document.getElementById(app1_winId+"-btnLive1").style.background="#ff8000";
		} else {
			document.getElementById(app1_winId+"-btnLive1").style.background="#e8e800";
		}
		document.getElementById(app1_winId+"-btnLive0").style.background="";
		app1_liveMode=1;
		if (play) {app1_refreshAll(0,"",true);}
	} else {
		document.getElementById(app1_winId+"-btnLive2").style.background="";
		document.getElementById(app1_winId+"-btnLive1").style.background="";
		document.getElementById(app1_winId+"-btnLive0").style.background="#80e000";
		app1_liveMode=0;
		app1_info.play=false;
		if (play) {app1_refreshAll(0);}
	}
}


/*
============================================================================
GUI-Events
============================================================================
*/

//---------------------------------------------
//Element verschieben
//---------------------------------------------
function app1_elementDragStart(objId) {
	var event=window.event;
	if (!app1_GUImode[1] && !app1_GUImode[2]) {
		app1_GUImode[0]=true;

		app1_saveState();
		app1_elementSelect(objId,1); //Angeklicktes Element (temporär) markieren

		//Mehrfach-Auswahl
		var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {
			var el=element[t].id;
			if (objId==el) {app1_dragElement=t;}
			app1_dragElements[t]=[null,null,false,0,0]; //obj, objId, wasDragged, offsetX, offsetY
			app1_dragElements[t][0]=document.getElementById(el);
			app1_dragElements[t][1]=el;
			app1_dragElements[t][2]=false;
			app1_dragElements[t][3]=(event.pageX*(1/app1_zoomFaktor))-app1_dragElements[t][0].offsetLeft;
			app1_dragElements[t][4]=(event.pageY*(1/app1_zoomFaktor))-app1_dragElements[t][0].offsetTop;
		}

		app1_CrosshairX=app1_newDiv(document.getElementById(app1_winId+"-page"),app1_winId+"-crosshairX");
		app1_CrosshairX.className="app1_CrosshairX";
		app1_CrosshairX.style.top=app1_dragElements[app1_dragElement][0].style.top;
		app1_CrosshairX.style.height=app1_dragElements[app1_dragElement][0].offsetHeight+"px";
		app1_CrosshairX.style.width=(document.getElementById(app1_winId+"-page").scrollWidth-12)+"px";
		app1_CrosshairY=app1_newDiv(document.getElementById(app1_winId+"-page"),app1_winId+"-crosshairY");
		app1_CrosshairY.className="app1_CrosshairY";
		app1_CrosshairY.style.left=app1_dragElements[app1_dragElement][0].style.left;
		app1_CrosshairY.style.width=app1_dragElements[app1_dragElement][0].offsetWidth+"px";
		app1_CrosshairY.style.height=(document.getElementById(app1_winId+"-page").scrollHeight-12)+"px";

		window.addEventListener("mousemove",app1_elementDragMove,false);
		window.addEventListener("mouseup",app1_elementDragEnd,false);
	}
}

function app1_elementDragMove() {
	var event=window.event;
	if(app1_GUImode[0]) {
		app1_dragElements[app1_dragElement][2]=true; //Objekt wurde bewegt (und nicht nur angeklickt)

		var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {
			var newX=((event.pageX*(1/app1_zoomFaktor))-app1_dragElements[t][3]);
			var newY=((event.pageY*(1/app1_zoomFaktor))-app1_dragElements[t][4]);
			newX=Math.floor(newX/app1_grid)*app1_grid;
			newY=Math.floor(newY/app1_grid)*app1_grid;
			if (newX<0) {newX=0;}
			if (newY<0) {newY=0;}
			app1_dragElements[t][0].style.left=newX+"px";
			app1_dragElements[t][0].style.top=newY+"px";
		}

		app1_CrosshairX.style.top=app1_dragElements[app1_dragElement][0].style.top;
		app1_CrosshairX.style.width=(document.getElementById(app1_winId+"-page").scrollWidth-12)+"px";
		app1_CrosshairY.style.left=app1_dragElements[app1_dragElement][0].style.left;
		app1_CrosshairY.style.height=(document.getElementById(app1_winId+"-page").scrollHeight-12)+"px";
		app1_drawConnections();
	}
}

function app1_elementDragEnd() {
	var event=window.event;
	if (app1_dragElements[app1_dragElement][2]) {
		//Element(e) wurden bewegt
		var data="";
		var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {
			data+=app1_getMeta(app1_dragElements[t][1]).id+",";
			data+=app1_dragElements[t][0].style.left.replace("px","")+",";
			data+=app1_dragElements[t][0].style.top.replace("px","")+";";
		}
		ajax("saveElementsPosition",1,app1_winId,app1_pageId,data);
		app1_restoreState();
	} else {
		//Element wurde nur angeklickt
		if (app1_GUImode[0] && (!app1_GUImode[1])) {
			app1_restoreState();
		}
	}

	app1_GUImode[0]=false;

	app1_dragElements.length=0;

	window.removeEventListener("mousemove",app1_elementDragMove,false);
	window.removeEventListener("mouseup",app1_elementDragEnd,false);

	app1_CrosshairX.parentNode.removeChild(app1_CrosshairX);
	app1_CrosshairY.parentNode.removeChild(app1_CrosshairY);
}

//---------------------------------------------
//Element angeklickt
//---------------------------------------------
function app1_elementClick(objId) {
	var event=window.event;
	if (event.button==0) {app1_elementClickLeft(objId);}
	if (event.button==2) {app1_elementClickRight(objId);}
	clickCancel();
}
function app1_elementClickLeft(objId) {
	if (app1_GUImode[1]) {return;}
	var element=document.getElementById(objId);
	if (event.shiftKey) {
		app1_elementSelect(objId,2);
	} else {
		app1_elementDragStart(objId);
	}
}
function app1_elementClickRight(objId) {
	if (app1_GUImode[1]) {return;}	
	apps_contextMenu=new class_contextMenu(app1_winId);
	apps_contextMenu.addItem("Logikbaustein bearbeiten","app1_elementEditLbs('"+objId+"');");
	apps_contextMenu.addItem("Logikbaustein austauschen","app1_elementSwapLbs('"+objId+"');");
	apps_contextMenu.addItem("Logikbaustein duplizieren","app1_elementDuplicate('"+objId+"');");
	apps_contextMenu.addItem("Logikbaustein löschen","jsConfirm('Soll dieser Logikbaustein wirklich gelöscht werden?','app1_elementDelete($"+objId+"$);','','Löschen');");
	apps_contextMenu.addHr();
	apps_contextMenu.addItem("Hilfe zu diesem Logikbaustein","app1_elementShowHelp('"+objId+"');");
	app1_contextMenuSelection();
	apps_contextMenu.show();
}

function app1_contextMenuSelection() {
	var selectedCount=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']").length;
	var bufferCount=document.getElementById(app1_winId+"-global").dataset.copybuffer.split(";").length-1;
	apps_contextMenu.addHr();
	apps_contextMenu.addItem("Alles auswählen","app1_elementSelectAll();");
	if (selectedCount>0) {
		apps_contextMenu.addItem("Auswahl umkehren","app1_elementSelectInvert();");
		apps_contextMenu.addItem("Auswahl aufheben","app1_elementSelectNone();");

		apps_contextMenu.addHr();
		apps_contextMenu.addText("Ausgewählte Logikbausteine ("+selectedCount+"):");
		apps_contextMenu.addItem("&gt; Merken","app1_elementSelectedToBuffer();");

		apps_contextMenu.addItem("&gt; Schützen","app1_elementLayerSelected(1);",1);
		apps_contextMenu.addVr();
		apps_contextMenu.addItem("Freigeben","app1_elementLayerSelected(0);",1);

		apps_contextMenu.addItem("&gt; Duplizieren","app1_elementDuplicateSelected();");
		apps_contextMenu.addItem("&gt; Löschen","jsConfirm('Sollen wirklich alle markierten Logikbausteine ("+selectedCount+") gelöscht werden?','app1_elementDeleteSelected();','','Löschen');");
	}
	if (bufferCount>0) {
		apps_contextMenu.addHr();
		apps_contextMenu.addText("Gemerkte Logikbausteine ("+bufferCount+"):");
		apps_contextMenu.addItem("&gt; Auf diese Seite duplizieren","app1_elementPasteBuffered(0);");
		apps_contextMenu.addItem("&gt; Auf diese Seite verschieben","app1_elementPasteBuffered(1);");
	}
}


//---------------------------------------------
//Ausgang angeklickt
//---------------------------------------------
function app1_itemAusgangClick(objId) {
	var event=window.event;
	if (event.button==0) {
		if (app1_liveMode==0) {app1_itemAusgangClickLeft(objId);} else {app1_itemAusgangClickLeftLive(objId);}
	}
	if (event.button==2 && app1_liveMode==0) {app1_itemAusgangClickRight(objId);}
	clickCancel();
}
function app1_itemAusgangClickLeft(objId) {
	var obj=document.getElementById(objId);
	//Verbindung erstellen, Schritt 1
	if (app1_objConnect[0]!=null) { //es wurde schon ein Ausgang angeklickt
		if (app1_objConnect[0]==obj) { //Klick auf den schon gewählten Ausgang => alles zurücksetzen
			app1_ConnectionLine.parentNode.removeChild(app1_ConnectionLine);
			app1_objConnect[0].style.background="";
			var tmp=document.getElementById(app1_winId+"-element-"+app1_getMeta(app1_objConnect[0].id).id+"-20c-"+app1_getMeta(app1_objConnect[0].id).itemid);
			if (tmp) {tmp.style.background="";}
			app1_objConnect[0]=null;
			app1_GUImode[1]=false;
		}
	} else {
		app1_itemHoverOut(objId);
		app1_GUImode[1]=true;
		app1_objConnect[0]=obj;
		app1_objConnect[0].style.background=apps_colorSelected;

		var meta=app1_getMeta(objId);	

		var tmp=document.getElementById(app1_winId+"-element-"+meta.id+"-20c-"+meta.itemid);
		if (tmp) {tmp.style.background=apps_colorSelected;}

		var objTmp=document.getElementById(app1_winId+"-element-"+meta.id);
		app1_ConnectionLine=app1_newDiv(document.getElementById(app1_winId+"-page"),app1_winId+"-ConnectionLine");
		app1_ConnectionLine.style.position="absolute";
		app1_ConnectionLine.style.zIndex="3";
		app1_ConnectionLine.style.webkitTransformOrigin="0 "+(app1_el_lineWidth/2)+"px";
		app1_ConnectionLine.style.pointerEvents="none";
		app1_ConnectionLine.style.background=apps_colorSelected;
		app1_ConnectionLine.style.left=(parseInt(objTmp.style.left.replace("px",""))+parseInt(obj.offsetLeft)+parseInt(obj.offsetWidth))+"px";
		app1_ConnectionLine.style.top=(parseInt(objTmp.style.top.replace("px",""))+parseInt(obj.offsetTop)+parseInt(obj.offsetHeight/2)-(app1_el_lineWidth/2))+"px";
		app1_ConnectionLine.style.height=app1_el_lineWidth+"px";
	}
}
function app1_itemAusgangClickLeftLive(objId) {
	if (app1_GUImode[1]) {return;}
	var obj=document.getElementById(objId);
	if (app1_liveEnabled && app1_liveMode>0) {
		if (obj.dataset.liveea==1) {
			app1_setLiveValue(objId,1);
		}
	}
}
function app1_itemAusgangClickRight(objId) {
	if (app1_GUImode[1]) {return;}
	var obj=document.getElementById(objId);
	apps_contextMenu=new class_contextMenu(app1_winId);
	apps_contextMenu.addItem("Zurücksetzen","app1_elementDeleteAllConnections('"+objId+"');");
	apps_contextMenu.show();
}

//---------------------------------------------
//Connector angeklickt
//---------------------------------------------
function app1_itemConnectorClick(objId) {
	var event=window.event;
	if (event.button==0 && app1_liveMode==0) {app1_itemConnectorClickLeft(objId);}
	if (event.button==2 && app1_liveMode==0) {app1_itemConnectorClickRight(objId);}
	clickCancel();
}
function app1_itemConnectorClickLeft(objId) {
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);
	if (!app1_rDgetMatches(meta.id,meta.item,meta.itemid)) { //Eingang noch frei?
		if (app1_GUImode[1]) {
			//Verbindung erstellen, Schritt 2
			if (app1_objConnect[0]!=null) { //Ausgang schon gewählt? (müsste ja eigentlich...)
				if ((meta.id!=app1_getMeta(app1_objConnect[0].id).id) && (!app1_rDgetMatches(meta.id,10,meta.itemid)) && (obj.dataset.gaconnected==0)) { //Eingang/Ausgang müssen von versch. Elementen stammen, Eingang darf nur 1 Verbindung haben, und darf nicht mit einer GA verbunden sein
					app1_ConnectionLine.parentNode.removeChild(app1_ConnectionLine);
					app1_objConnect[1]=obj;
					app1_elementCreateConnection(app1_objConnect[0].id,app1_objConnect[1].id);
				}
			}
		} else {
			//GA zuweisen (per Klick-Simulation auf das Control, Rest macht callback)
			controlClickLeft(objId);
		}
	}
}
function app1_itemConnectorClickRight(objId) {
	var obj=document.getElementById(objId);
	if (app1_GUImode[1]) {
		obj.style.background="";
		return;
	}
	apps_contextMenu=new class_contextMenu(app1_winId);
	apps_contextMenu.addItem("Zurücksetzen","app1_elementDeleteConnection('"+objId+"');");
	apps_contextMenu.show();
}


//---------------------------------------------
//GA angeklickt
//---------------------------------------------
function app1_itemGaClick(objId) {
	var event=window.event;
	if (event.button==0) {
		if (app1_liveMode==0) {app1_itemGaClickLeft(objId);} else {app1_itemGaClickLeftLive(objId);}
	}
	if (event.button==2 && app1_liveMode==0) {app1_itemGaClickRight(objId);}
	clickCancel();
}
function app1_itemGaClickLeft(objId) {
	if (app1_GUImode[1]) {return;}
	controlClickLeft(objId);
}
function app1_itemGaClickLeftLive(objId) {
	if (app1_GUImode[1]) {return;}
	var obj=document.getElementById(objId);
	if (app1_liveEnabled && app1_liveMode>0) {
		if (obj.dataset.liveko==1) {
			app1_setLiveKoValue(objId);
		}
	}
}
function app1_itemGaClickRight(objId) {
	if (app1_GUImode[1]) {return;}
	var obj=document.getElementById(objId);
	apps_contextMenu=new class_contextMenu(app1_winId);
	apps_contextMenu.addItem("Zurücksetzen","app1_elementDeleteConnection('"+objId+"');");
	apps_contextMenu.show();
}

//---------------------------------------------
//Tootipp (allgemein)
//---------------------------------------------
function app1_itemTooltipHover(objId,cssClass,setPos) {
	if (app1_GUImode[1]) {return;}
	if (!document.getElementById(app1_winId+"-divTooltip")) {
		var meta=app1_getMeta(objId);	
		var tmp=document.getElementById(app1_winId+"-element-"+meta.id);
		tmp.style.zIndex="3";

		var tmp=app1_newDiv(document.getElementById(objId),app1_winId+"-divTooltip");
		tmp.className=cssClass;
		tmp.style.pointerEvents="none";
		if (setPos) {
			var d=document.getElementById(objId);
			tmp.style.left=d.offsetLeft+"px";
			tmp.style.top=d.offsetTop+"px";
		}
		tmp.innerHTML=document.getElementById(objId).dataset.info;
	}
}
function app1_itemTooltipHoverOut(objId) {
	if (app1_GUImode[1]) {return;}
	if (document.getElementById(app1_winId+"-divTooltip")) {
		var meta=app1_getMeta(objId);	
		var tmp=document.getElementById(app1_winId+"-element-"+meta.id);
		tmp.style.zIndex="";

		var tmp=document.getElementById(app1_winId+"-divTooltip");
		tmp.parentNode.removeChild(tmp);
	}
}

//---------------------------------------------
//Initialwert angeklickt (nur in Liveansicht)
//---------------------------------------------
function app1_itemValueClick(objId) {
	var event=window.event;
	if (event.button==0) {app1_itemValueClickLeftLive(objId);}
	clickCancel();
}
function app1_itemValueClickLeftLive(objId) {
	if (app1_GUImode[1]) {return;}
	var obj=document.getElementById(objId);
	if (app1_liveEnabled && app1_liveMode>0) {
		if (obj.dataset.liveea==1) {
			app1_setLiveValue(objId,0);
		}
	}
}

//---------------------------------------------
//Befehl angeklickt (Ausgangsbox 12000010)
//---------------------------------------------
function app1_itemCmdClick(objId) {
	var event=window.event;
	if (event.button==0) {app1_itemCmdClickLeft(objId);}
	if (event.button==2) {app1_itemCmdClickRight(objId);}
	clickCancel();
}
function app1_itemCmdClickLeft(objId) {
	if (app1_GUImode[1]) {return;}
	controlClickLeft(objId);
}
function app1_itemCmdClickRight(objId) {
	if (app1_GUImode[1]) {return;}
	var meta=app1_getMeta(objId);
	if (meta && meta.id>0) {
		apps_contextMenu=new class_contextMenu(app1_winId);
		if (app1_liveMode==0) {apps_contextMenu.addItem("Zurücksetzen","jsConfirm('Sollen wirklich alle Befehle gelöscht werden?','app1_elementDeleteCommands($"+objId+"$);','','Löschen');");}
		ajax("itemAusgangContextMenu","1",app1_winId,"",meta.id);
	}
}

//---------------------------------------------
//Eingang/Connector/Value/Ausgang hover
//---------------------------------------------
function app1_itemHover(objId) {
	//objId: ObjektId
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);	

	if (app1_GUImode[1]) { //beim Erstellen einer Verbindung? => Nur Connector hovern, dann abbrechen
		if (meta.item==11 && obj.dataset.gaconnected==0 && (meta.id!=app1_getMeta(app1_objConnect[0].id).id) && (!app1_rDgetMatches(meta.id,meta.item,meta.itemid))) {
			obj.style.background=apps_colorSelected;
			var tmp=document.getElementById(app1_winId+"-element-"+meta.id+"-11c-"+meta.itemid);
			if (tmp) {tmp.style.background=apps_colorSelected;}
		}
		return;
	}
	if (!app1_GUImode[0]) { //nur Hovern, wenn Element nicht gerade bewegt wird
		var index=app1_rDgetMatches(meta.id,meta.item,meta.itemid);
		var d;
		//Wenn etwas in app1_rD gefunden wurde: Farben der Linien hervorheben
		for (var t=0;t<index.length;t++) {
			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-11-"+app1_rD[index[t]][1]);
			if (d) {d.style.background=apps_colorSelected;}
			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-11c-"+app1_rD[index[t]][1]);
			if (d) {d.style.background=apps_colorSelected;}
			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-20-"+app1_rD[index[t]][3]);
			if (d) {d.style.background=apps_colorSelected;}
			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-20c-"+app1_rD[index[t]][3]);
			if (d) {d.style.background=apps_colorSelected;}

			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-19-"+app1_rD[index[t]][1]);
			if (d) {d.style.background=apps_colorSelected;}
			d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-29-"+app1_rD[index[t]][3]);
			if (d) {d.style.background=apps_colorSelected;}

			if (!app1_rD[index[t]][4]) {
				for (var tt=5;tt<=7;tt++) {
					app1_rD[index[t]][tt].style.background=apps_colorSelected;
					app1_rD[index[t]][tt].style.zIndex=2;
				}
			}
		}
	}
}
function app1_itemHoverOut(objId) {
	//obj: this (Objektzeiger)
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);	
	
	if (app1_GUImode[1]) { //beim Erstellen einer Verbindung? => Nur Connector hovern, dann abbrechen
		if (meta.item==11) {obj.style.background="";}
		var tmp=document.getElementById(app1_winId+"-element-"+meta.id+"-11c-"+meta.itemid);
		if (tmp) {tmp.style.background="";}
		return;
	}

	var index=app1_rDgetMatches(meta.id,meta.item,meta.itemid);
	var d;
	//Wenn etwas in app1_rD gefunden wurde: Farben der Linien zurücksetzen
	for (var t=0;t<index.length;t++) {
		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-11-"+app1_rD[index[t]][1]);
		if (d) {d.style.background="";}
		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-11c-"+app1_rD[index[t]][1]);
		if (d) {d.style.background="";}
		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-20-"+app1_rD[index[t]][3]);
		if (d) {d.style.background="";}
		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-20c-"+app1_rD[index[t]][3]);
		if (d) {d.style.background="";}

		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][0]+"-19-"+app1_rD[index[t]][1]);
		if (d) {d.style.background="";}
		d=document.getElementById(app1_winId+"-element-"+app1_rD[index[t]][2]+"-29-"+app1_rD[index[t]][3]);
		if (d) {d.style.background="";}

		if (!app1_rD[index[t]][4]) {
			for (var tt=5;tt<=7;tt++) {
				app1_rD[index[t]][tt].style.background=app1_el_colorLine;
				app1_rD[index[t]][tt].style.zIndex=0;
			}
		}			
	}
}

//---------------------------------------------
//Info angeklickt
//---------------------------------------------
function app1_itemInfoClick(objId) {
	var event=window.event;
	if (event.button==0) {app1_itemInfoClickLeft(objId);}
	//if (event.button==2) {}
	clickCancel();
}
function app1_itemInfoClickLeft(objId) {
	if (app1_GUImode[1]) {return;}
	app1_GUImode[2]=true;
	var obj=document.getElementById(objId);
	obj.innerHTML+="<div style='position:absolute; left:0; top:0; width:100%; height:100%; min-height:50px;'><textarea id='"+objId+"-tmp' maxlength='10000' rows='4' onBlur='app1_elementRename(\""+objId+"\");' class='app1_divTitelInfoEdit'></textarea></div>";
	document.getElementById(objId+"-tmp").value=obj.dataset.info;
	obj.setAttribute("onMouseDown","clickCancel();");
	window.setTimeout("if (document.getElementById('"+objId+"-tmp')) {document.getElementById('"+objId+"-tmp').focus();}",250);
}
function app1_elementRename(objId) {
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);	
	var newValue=document.getElementById(objId+"-tmp").value;
	ajax("renameElement",1,app1_winId,app1_pageId,stringCleanup(newValue)+AJAX_SEPARATOR1+meta.id);
	app1_GUImode[2]=false;
}


//---------------------------------------------
//Textbox-Inhalt angeklickt
//---------------------------------------------
function app1_itemTextboxClick(objId) {
	var event=window.event;
	if (event.button==0) {app1_itemTextboxClickLeft(objId);}
	//if (event.button==2) {}
	clickCancel();
}
function app1_itemTextboxClickLeft(objId) {
	if (app1_GUImode[1]) {return;}
	app1_GUImode[2]=true;
	var obj=document.getElementById(objId);
	obj.innerHTML+="<div style='position:absolute; left:0; top:0; width:100%; height:100%;'><textarea id='"+objId+"-tmp' maxlength='10000' rows='4' onBlur='app1_elementRename(\""+objId+"\");' class='app1_divTitelInfoEdit' style='padding:5px;'></textarea></div>";
	document.getElementById(objId+"-tmp").value=obj.dataset.info;
	obj.setAttribute("onMouseDown","clickCancel();");
	window.setTimeout("if (document.getElementById('"+objId+"-tmp')) {document.getElementById('"+objId+"-tmp').focus();}",250);
}


//---------------------------------------------
//globale GUI-Events
//---------------------------------------------
function app1_itemPageClick() {
	var event=window.event;
	if (event.button==0) {
		//beim Erstellen einer Verbindung? => Verbindungsmodus abbrechen
		if (app1_GUImode[1] && app1_objConnect[0]!=null) {
			app1_cancelNewConnection();
		} else if (!app1_GUImode[2]) {
			if (event.shiftKey) {
				app1_elementSelectRect(2);
			} else {
				app1_elementSelectNone();
				app1_elementSelectRect(2);
			}		
		}
	}
	if (event.button==2) {
		//beim Erstellen einer Verbindung? => Verbindungsmodus abbrechen
		if (app1_GUImode[1] && app1_objConnect[0]!=null) {
			app1_cancelNewConnection();
		} else if (app1_pageId>0) {
			var pos=app1_getMousePosition();
			apps_contextMenu=new class_contextMenu(app1_winId);
			apps_contextMenu.addItem("Logikbaustein hinzufügen","app1_pickElementAtCursor("+pos.x+","+pos.y+");");
			app1_contextMenuSelection();
			apps_contextMenu.show();
		}
	}
}

function app1_pickElementAtCursor(x,y) {
	app1_curPosX=Math.floor(x/app1_grid)*app1_grid;
	app1_curPosY=Math.floor(y/app1_grid)*app1_grid;
	controlClickLeft(app1_winId+"-fd2");
}


function app1_itemPageMouseMove() {
	var event=window.event;
	if (app1_GUImode[1] && app1_objConnect[0]!=null) {
		//beim Erstellen einer Verbindung? => Linie vom Ausgang zur Mausposition
		var pos=app1_getMousePosition();
		var x=parseInt(app1_ConnectionLine.style.left.replace("px",""));
		var y=parseInt(app1_ConnectionLine.style.top.replace("px",""))+(app1_el_lineWidth/2);
		var length=(Math.sqrt((x-pos.x)*(x-pos.x)+(y-pos.y)*(y-pos.y)));
		var angle=Math.atan2(pos.y-y,pos.x-x)*180/Math.PI;
		app1_ConnectionLine.style.width=length+"px";
		app1_ConnectionLine.style.webkitTransform="rotate("+angle+"deg)";
	} else {
		if (app1_elementSelectRectData.mode>0) {
			app1_elementSelectRect(1);
		}
	}
}

function app1_itemPageUnclick() {
	if (app1_elementSelectRectData.mode>0) {app1_elementSelectRect(0);}
}

function app1_getMousePosition() {
	var event=window.event;
	var objWindowContainer=document.getElementById("windowContainer");
	var objWindow=document.getElementById(app1_winId);
	var objPageContainer=document.getElementById(app1_winId+"-pagecontainer");
	var objPage=document.getElementById(app1_winId+"-page");
	var x=(event.pageX-parseInt(objPageContainer.offsetLeft)-parseInt(objWindow.offsetLeft)-parseInt(objWindowContainer.offsetLeft))*(1/app1_zoomFaktor)+parseInt(objPage.scrollLeft);
	var y=(event.pageY-parseInt(objPageContainer.offsetTop)-parseInt(objWindow.offsetTop)-parseInt(objWindowContainer.offsetTop))*(1/app1_zoomFaktor)+parseInt(objPage.scrollTop);
	x-=3*(1/app1_zoomFaktor);	//3px abziehen für "pagecontainer"-Border
	y-=3*(1/app1_zoomFaktor);	//3px abziehen für "pagecontainer"-Border
	return {x,y};
}


/*
============================================================================
Callbacks
============================================================================
*/

function app1_editGa_callback(objId) {
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);	
	if (obj.dataset.value>0) { //Eingang mit GA verbinden
		ajax("setGa",1,app1_winId,app1_pageId,obj.dataset.value+AJAX_SEPARATOR1+meta.id+AJAX_SEPARATOR1+meta.itemid);
	} else { //Eingang zurücksetzen (GaId=0)
		app1_elementDeleteConnection(objId);
	}
}

function app1_pickPage_callback(senderId) {
	var newPageId=document.getElementById(senderId).dataset.value;
	if (newPageId>0) {
		app1_refreshAll(newPageId);
		//Zum Ursprung scrollen
		document.getElementById(app1_winId+"-page").scrollTop=0;
		document.getElementById(app1_winId+"-page").scrollLeft=0;
	}
}

function app1_pickElement_callback(senderId) {
	var newElementId=document.getElementById(senderId).dataset.value;
	if (newElementId>0) {
		ajax("newLogicElement",1,app1_winId,app1_pageId,newElementId+AJAX_SEPARATOR1+app1_curPosX+AJAX_SEPARATOR1+app1_curPosY);
	}
	app1_curPosX=0;
	app1_curPosY=0;
}

function app1_elementSwapLbs_callback(senderId) {
	var newElementId=document.getElementById(senderId).dataset.value;
	if (newElementId>0) {
		var lbsId=document.getElementById(app1_winId+"-fd4").dataset.lbsid;
		ajax("swapLbs",1,app1_winId,app1_pageId,lbsId+AJAX_SEPARATOR1+newElementId);
	}
}

function app1_editValue_callback(objId) {
	var obj=document.getElementById(objId);
	var meta=app1_getMeta(objId);	
	ajax("setValue",1,app1_winId,app1_pageId,obj.dataset.value+AJAX_SEPARATOR1+meta.id+AJAX_SEPARATOR1+meta.itemid);
}

function app1_editCommandList_callback(objId) {
	app1_refreshAll(0); //nichts weiter machen - die CommandList wird ja vom Control verwaltet...
}



/*
============================================================================
Funktionalität
============================================================================
*/

function app1_getMeta(objId) {
	var r=objId.split("-");
	return {id:r[2],item:r[3],itemid:r[4]};
}

function app1_newDiv(objParent,id) {
	//erzeugt neues Div und hängt es an parent
	//objParent: Parent-Objekt
	//id: gewünschte ID des neuen DIVs
	var div=document.createElement('div');
	objParent.appendChild(div);
	div.style.position="absolute";
	div.id=id;
	return div;
}

function app1_elementSelectRect(mode) {
	var page=document.getElementById(app1_winId+"-page");
	if (page) {
		var pos=app1_getMousePosition();
		if (mode==2) {
			app1_elementSelectRectData.mode=2;
			app1_elementSelectRectData.x1=pos.x;
			app1_elementSelectRectData.y1=pos.y;
			app1_elementSelectRectData.x2=pos.x;
			app1_elementSelectRectData.y2=pos.y;
			render();
		} else if (mode==1) {
			app1_elementSelectRectData.mode=1;
			app1_elementSelectRectData.x2=pos.x;
			app1_elementSelectRectData.y2=pos.y;
			getHits();
			render();			
		} else if (mode==0) {
			app1_elementSelectRectData.mode=0;
			getHits();
			render();
		}
	}

	function getHits() {
		var page=document.getElementById(app1_winId+"-page");
		if (page) {
			//absolute Position von page ermitteln
			var tmp=page.getBoundingClientRect();
			var offsetX=tmp.left*(1/app1_zoomFaktor)-parseInt(page.scrollLeft);
			var offsetY=tmp.top*(1/app1_zoomFaktor)-parseInt(page.scrollTop);
	
			//Box
			var x1 = Math.min(app1_elementSelectRectData.x1,app1_elementSelectRectData.x2);
			var x2 = Math.max(app1_elementSelectRectData.x1,app1_elementSelectRectData.x2);
			var y1 = Math.min(app1_elementSelectRectData.y1,app1_elementSelectRectData.y2);
			var y2 = Math.max(app1_elementSelectRectData.y1,app1_elementSelectRectData.y2);
	
			var elements=page.querySelectorAll("[data-elementselected='0']");
			for (var t=0; t<elements.length; t++) {
				var tmp=elements[t].getBoundingClientRect();
				var x=(tmp.left*(1/app1_zoomFaktor))-offsetX;
				var y=(tmp.top*(1/app1_zoomFaktor))-offsetY;
				var w=tmp.width*(1/app1_zoomFaktor);
				var h=tmp.height*(1/app1_zoomFaktor);
	
				if ((x>=x1 && x<=x2 && y>=y1 && y<=y2) && ((x+w)>=x1 && (x+w)<=x2 && (y+h)>=y1 && (y+h)<=y2)) {
					if (app1_elementSelectRectData.mode==0) {
						app1_elementSelect(elements[t].id,1); 
					} else if (elements[t].dataset.layer==0 || elements[t].dataset.layer==app1_layerMode) {
						elements[t].style.boxShadow=app1_el_shadowSelected;
						var tmpList=document.getElementById(elements[t].id+"-list");
						if (tmpList) {tmpList.style.background=apps_colorSelected;}
					}
				} else {
					elements[t].style.boxShadow="";
					var tmpList=document.getElementById(elements[t].id+"-list");
					if (tmpList) {tmpList.style.background="";}
				}
	
			}
		}
	}

	function render() {
		var page=document.getElementById(app1_winId+"-page");
		if (page) {
			if (app1_elementSelectRectData.mode>0) {
				var tmp=document.getElementById(app1_winId+"-selectrect");
				if (!tmp) {
					tmp=app1_newDiv(page,app1_winId+"-selectrect");
					tmp.className="app1_elementSelectRect";
				}		
				var x1 = Math.min(app1_elementSelectRectData.x1,app1_elementSelectRectData.x2);
				var x2 = Math.max(app1_elementSelectRectData.x1,app1_elementSelectRectData.x2);
				var y1 = Math.min(app1_elementSelectRectData.y1,app1_elementSelectRectData.y2);
				var y2 = Math.max(app1_elementSelectRectData.y1,app1_elementSelectRectData.y2);
				tmp.style.left=x1+"px";
				tmp.style.top=y1+"px";
				tmp.style.width=(x2-x1)+"px";
				tmp.style.height=(y2-y1)+"px";
			} else {
				var tmp=document.getElementById(app1_winId+"-selectrect");
				if (tmp) {tmp.parentNode.removeChild(tmp);}
			}
		}
	}
}

function app1_elementSelect(objId,mode) {
	var element=document.getElementById(objId);
	if (element) {
		if (element.dataset.layer==0 || element.dataset.layer==app1_layerMode) {
			if (mode==2) {
				if (element.dataset.elementselected==0) {mode=1;} else {mode=0;}
			}
			if (mode==1) {
				element.style.boxShadow=app1_el_shadowSelected;
				var tmpList=document.getElementById(objId+"-list");
				if (tmpList) {tmpList.style.background=apps_colorSelected;}
				element.dataset.elementselected=1;
				app1_elementShowConnections(objId,1);
			} else if (mode==0) {
				element.style.boxShadow="";
				var tmpList=document.getElementById(objId+"-list");
				if (tmpList) {tmpList.style.background="";}
				element.dataset.elementselected=0;
				app1_elementShowConnections(objId,0);
			}
		}
	}
}

function app1_elementSelectAll() {
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='0']");
	for (var t=0; t<element.length; t++) {
		app1_elementSelect(element[t].id,1);
	}
}

function app1_elementSelectInvert() {
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected]");
	for (var t=0; t<element.length; t++) {
		if (element[t].dataset.elementselected==1) {
			app1_elementSelect(element[t].id,0);
		} else {
			app1_elementSelect(element[t].id,1);
		}
	}
}
function app1_elementSelectNone() {
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {
		app1_elementSelect(element[t].id,0);
	}
}

function app1_elementIsSelected(objId) {
	var element=document.getElementById(objId);
	if (element) {
		if (element.dataset.elementselected==1) {return true;}
	}
	return false;
}


//---------------------------------------------
//diverse Funktionen
//---------------------------------------------

function app1_elementDuplicate(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var meta=app1_getMeta(objId);	
		ajax("pasteElements",1,app1_winId,app1_pageId,meta.id);
	}
}

function app1_elementDelete(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var meta=app1_getMeta(objId);	
		ajax("deleteElements",1,app1_winId,app1_pageId,meta.id);
	}
}

function app1_setLiveValue(objId,mode) {
	//mode: 0=Eingang, 1=Ausgang
	var obj=document.getElementById(objId);
	if (obj) {
		var meta=app1_getMeta(objId);	
		ajax("setLiveValue",1,app1_winId,app1_pageId,meta.id+AJAX_SEPARATOR1+meta.itemid+AJAX_SEPARATOR1+mode);
	}
}

function app1_setLiveKoValue(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var meta=app1_getMeta(objId);	
		ajax("setLiveKoValue",1,app1_winId,app1_pageId,meta.id+AJAX_SEPARATOR1+meta.itemid);
	}
}

function app1_elementCreateConnection(obj1Id,obj2Id) {
	//Erstellt eine neue Verbindung
	//obj1Id: Ausgang
	//obj2Id: Eingang
	var meta1=app1_getMeta(obj1Id);	
	var meta2=app1_getMeta(obj2Id);	
	ajax("newElementConnection",1,app1_winId,app1_pageId,meta1.id+AJAX_SEPARATOR1+meta1.itemid+AJAX_SEPARATOR1+meta2.id+AJAX_SEPARATOR1+meta2.itemid);
}

function app1_elementDeleteConnection(objId) {
	//Löscht eine bestehende Verbindung (GA oder Ausgang) an einem Eingang(!)
	var meta=app1_getMeta(objId);	
	ajax("deleteElementConnection",1,app1_winId,app1_pageId,meta.id+AJAX_SEPARATOR1+meta.itemid);
}

function app1_elementDeleteAllConnections(objId) {
	//Löscht alle bestehenden Verbindungen an einem Ausgang(!)
	var meta=app1_getMeta(objId);	
	ajax("deleteElementConnectionsAll",1,app1_winId,app1_pageId,meta.id+AJAX_SEPARATOR1+meta.itemid);
}

function app1_elementDeleteCommands(objId) {
	//Löscht alle Befehle an einer Ausgangsbox
	var meta=app1_getMeta(objId);	
	ajax("deleteElementCommands",1,app1_winId,app1_pageId,meta.id);
}

function app1_elementShowHelp(objId) {
	var meta=app1_getMeta(objId);	
	openWindow(9999,"",meta.id);
	clickCancel();
}

function app1_elementEditLbs(objId) {
	var meta=app1_getMeta(objId);	
	ajax("editLbs",1,app1_winId,app1_pageId,meta.id)
}

function app1_elementSwapLbs(objId) {
	var meta=app1_getMeta(objId);	
	ajax("swapLbsSelect",1,app1_winId,app1_pageId,meta.id)
}

function app1_cancelNewConnection() {
	app1_ConnectionLine.parentNode.removeChild(app1_ConnectionLine);

	var meta=app1_getMeta(app1_objConnect[0].id);	
	var tmp=document.getElementById(app1_winId+"-element-"+meta.id+"-20c-"+meta.itemid);
	if (tmp) {tmp.style.background="";}

	app1_objConnect[0].style.background="";
	app1_objConnect[0]=null;
	app1_GUImode[1]=false;
}

function app1_setLayerMode() {
	//(aktuell nur toggeln zw. 0 und 1)
	if (app1_layerMode==0) {
		app1_layerMode=1;
		document.getElementById(app1_winId+"-layer").style.background="#80e000";
		document.getElementById(app1_winId+"-layer").innerHTML="<img src='../shared/img/lock1b.png' width='16' height='16' valign='middle' style='margin:0; padding-left:2px;' draggable='false'>";
	} else {
		app1_layerMode=0;
		document.getElementById(app1_winId+"-layer").style.background="";
		document.getElementById(app1_winId+"-layer").innerHTML="<img src='../shared/img/lock1.png' width='16' height='16' valign='middle' style='margin:0; padding-left:2px;' draggable='false'>";
	}
	app1_refreshAll(0);
}


//---------------------------------------------
//Selektion und Copy/Paste
//---------------------------------------------

function app1_elementLayerSelected(mode) {
	//markierte Objekte: layer zuweisen
	var data="";
	var page=document.getElementById(app1_winId+"-page");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {
			var meta=app1_getMeta(element[t].id);	
			data+=meta.id+";";
		}
		if (data!="") {
			ajax("layerElements",1,app1_winId,app1_pageId,data+AJAX_SEPARATOR1+mode);
		}
	}
}

function app1_elementDuplicateSelected() {
	//markierte Elemente auf aktueller Seite duplizieren
	var data="";
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {
		var meta=app1_getMeta(element[t].id);	
		data+=meta.id+";";
	}
	if (data!="") {ajax("pasteElements",1,app1_winId,app1_pageId,data);}
}

function app1_elementDeleteSelected() {
	//markierte Elemente löschen
	var data="";
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {
		var meta=app1_getMeta(element[t].id);	
		data+=meta.id+";";
	}
	if (data!="") {
		ajax("deleteElements",1,app1_winId,app1_pageId,data);
	}
}

function app1_elementSelectedToBuffer() {
	//markierte Elemente merken
	var data="";
	var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {
		var meta=app1_getMeta(element[t].id);	
		data+=meta.id+";";
	}
	document.getElementById(app1_winId+"-global").dataset.copybuffer=data;
}

function app1_elementPasteBuffered(mode) {
	//gemerkte Elemente auf aktuelle Seite einfügen
	var data=document.getElementById(app1_winId+"-global").dataset.copybuffer;
	if (data!="") {
		if (mode==1) {
			ajax("moveElements",1,app1_winId,app1_pageId,data);
		} else {
			ajax("pasteElements",1,app1_winId,app1_pageId,data);
		}
	}
	document.getElementById(app1_winId+"-global").dataset.copybuffer="";
}


//---------------------------------------------
//Connections berechnen/zeichnen
//---------------------------------------------

function app1_addConnection(elementId,linkIn,linkId,linkOut) {	
	//app1_rD[id][0]: elementID des Eingangs (elementId)
	//app1_rD[id][1]: EingangNr (linkIn)
	//app1_rD[id][2]: elementID des Ausgangs (linkId)
	//app1_rD[id][3]: AusgangNr (linkOut)
	//app1_rD[id][4]: LBS ist ausgewählt (true/false)
	//app1_rD[id][5..7]: DIV-Objekte (für Verbindungslinien)
	var n=[elementId,linkIn,linkId,linkOut,false,app1_newLine(),app1_newLine(),app1_newLine()];
	app1_rD.push(n);

	//Pins erstellen
	//Eingang
	var d=document.getElementById(app1_winId+"-element-"+elementId+"-19-"+linkIn);
	if (!d) {
		var pElement=document.getElementById(app1_winId+"-element-"+elementId);
		var tmp=document.getElementById(app1_winId+"-element-"+elementId+"-11-"+linkIn);	//Eingang (Connector)
		if (tmp) {
			var d=app1_newDiv(pElement,app1_winId+"-element-"+elementId+"-19-"+linkIn);		//Pin
			d.className="app1_elInputPin";
			d.style.top=(tmp.offsetTop+(app1_el_rowHeight/2)-d.offsetHeight/2-1)+"px";
			d.style.left=(tmp.offsetLeft-d.offsetWidth)+"px";
		}
	}

	//Ausgang
	var d=document.getElementById(app1_winId+"-element-"+linkId+"-29-"+linkOut);
	if (!d) {
		var pElement=document.getElementById(app1_winId+"-element-"+linkId);
		var tmp=document.getElementById(app1_winId+"-element-"+linkId+"-20-"+linkOut);	//Ausgang
		if (tmp) {
			var d=app1_newDiv(pElement,app1_winId+"-element-"+linkId+"-29-"+linkOut);	//Pin
			d.className="app1_elOutputPin";
			d.style.top=(tmp.offsetTop+(app1_el_rowHeight/2)-d.offsetHeight/2-1)+"px";
			d.style.left=(tmp.offsetLeft+tmp.offsetWidth)+"px";
		}
	}
}

function app1_drawConnections() {
	//Verbindungslinien einzeichnen
	for (var t=0;t<app1_rD.length;t++) {
		for (var tt=5;tt<=7;tt++) {app1_rD[t][tt].style.display="none";}

		var elIn=document.getElementById(app1_winId+"-element-"+app1_rD[t][0]+"-11-"+app1_rD[t][1]);
		var elInTmp=document.getElementById(app1_winId+"-element-"+app1_rD[t][0]);
		var elOut=document.getElementById(app1_winId+"-element-"+app1_rD[t][2]+"-20-"+app1_rD[t][3]);
		var elOutTmp=document.getElementById(app1_winId+"-element-"+app1_rD[t][2]);
		if (elIn && elInTmp && elOut && elOutTmp) {
			var elInX=parseInt(elInTmp.style.left.replace("px",""));
			var elInY=parseInt(elInTmp.style.top.replace("px",""));
			var inX=elInX-15;
			var inY=elInY+elIn.offsetTop+(app1_el_rowHeight/2);
			
			var elOutX=parseInt(elOutTmp.style.left.replace("px",""));
			var elOutY=parseInt(elOutTmp.style.top.replace("px",""));
			var outX=elOut.offsetLeft+elOut.offsetWidth+elOutX+15;
			var outY=elOutY+elOut.offsetTop+(app1_el_rowHeight/2);
	
			var length=(Math.sqrt((inX-outX)*(inX-outX)+(inY-outY)*(inY-outY)));
			var angle=Math.atan2(outY-inY,outX-inX)*180/Math.PI;
	
			app1_rD[t][5].style.left=inX+"px";
			app1_rD[t][5].style.top=(inY-(app1_el_lineWidth/2+1))+"px";
			app1_rD[t][5].style.width="14px";
			if (!app1_rD[t][5].style.background) {app1_rD[t][5].style.background=app1_el_colorLine;}
			app1_rD[t][5].style.display="block";
	
			app1_rD[t][6].style.left=(inX+0)+"px";
			app1_rD[t][6].style.top=(inY-(app1_el_lineWidth/2+1))+"px";
			app1_rD[t][6].style.width=length+"px";
			app1_rD[t][6].style.webkitTransform="rotate("+angle+"deg)";
			if (!app1_rD[t][6].style.background) {app1_rD[t][6].style.background=app1_el_colorLine;}
			app1_rD[t][6].style.display="block";
			app1_rD[t][6].style.webkitTransformOrigin="0px "+(app1_el_lineWidth/2)+"px";
	
			app1_rD[t][7].style.left=(outX-14)+"px";
			app1_rD[t][7].style.top=(outY-(app1_el_lineWidth/2+1))+"px";
			app1_rD[t][7].style.width="14px";
			if (!app1_rD[t][7].style.background) {app1_rD[t][7].style.background=app1_el_colorLine;}
			app1_rD[t][7].style.display="block";
		}
	}
}

function app1_newLine() {
	//generiert 1 "Linie" (DIV)
	//return: Div-Objekt
	var line=document.createElement('div');
	document.getElementById(app1_winId+"-page").appendChild(line);
	line.style.position="absolute";
	line.style.left="0px";
	line.style.width="0px";
	line.style.height=app1_el_lineWidth+"px";
	line.style.zIndex=0;
	line.style.display="none";
	line.style.pointerEvents="none";
	return line;
}

function app1_rDgetMatches(elementId,item,itemNr) {
	//durchsucht app1_rD[] nach Verbindungen an einem Eingang/Ausgang:
	//elementId: db.logicElement.id (bzw. index des DIVs)
	//item: 10=Eingang, 20=Ausgang
	//itemNr: die Nummer des Ein-/Ausgangs
	var index=new Array();
	var i=0;
	//id in app1_rD[] suchen, die zu dem gehoverten Item gehört
	for (var t=0;t<app1_rD.length;t++) {
		if ((item>=10) && (item<=12) && (app1_rD[t][0]==elementId) && (app1_rD[t][1]==itemNr)) {
			index[i]=t;
			i++; //eigentlich kann es ja nur i=0 geben, da ein Eingang max. mit 1 Ausgang verbunden sein darf
		}
		if ((item==20) && (app1_rD[t][2]==elementId) && (app1_rD[t][3]==itemNr)) {
			index[i]=t;
			i++;
		}
	}
	if (i>0) {
		return index;
	} else {
		return false; //nichts gefunden
	}
}

function app1_elementShowConnections(objId,mode) {
	var obj=document.getElementById(objId);
	if (obj) {
		var meta=app1_getMeta(objId);
		for (var t=0;t<app1_rD.length;t++) {
			if (app1_rD[t][0]==meta.id || app1_rD[t][2]==meta.id) {
				if (mode==1) {
					app1_rD[t][4]=true;
					for (var tt=5;tt<=7;tt++) {
						app1_rD[t][tt].style.background=apps_colorSelected;
						app1_rD[t][tt].style.zIndex=2;
					}
				} else {
					app1_rD[t][4]=false;
					for (var tt=5;tt<=7;tt++) {
						app1_rD[t][tt].style.background=app1_el_colorLine;
						app1_rD[t][tt].style.zIndex=0;
					}
				}			
			}
		}
	}
}


/*
============================================================================
LBS erstellen
============================================================================
*/

function class_app1_LBS() {

	var that=this;
	this.elementDef;

	var pElement;
	var menuItem;

	this.createDef=function(typ,id,functionid,name,title,xpos,ypos,layer,inCount,outCount,info,info2,style) {
		if (title=="") {title=name;}
		that.elementDef={typ:typ,id:id,functionid:functionid,name:name,title:title,xpos:xpos,ypos:ypos,layer:layer,inCount:inCount,outCount:outCount,info:info,info2:info2,style:style,data:new Array(),live:new Array()};
		if (parseInt(inCount)>=parseInt(outCount)) {var rows=parseInt(inCount);} else {var rows=parseInt(outCount);}
		for (var t=1;t<=rows;t++) {
			that.elementDef.data[t]={in:null,out:null};
		}
	}

	this.createLbs=function() {
		if (that.elementDef.typ<0) {
			createLbs_error();

		} else if (that.elementDef.typ==1) {
			createLbs_12000000();

		} else {
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				createTable("app1_elContainer");
			} else {
				createTable("app1_elContainerLayer1");
			}
	
			if (parseInt(that.elementDef.inCount)>=parseInt(that.elementDef.outCount)) {var rows=parseInt(that.elementDef.inCount);} else {var rows=parseInt(that.elementDef.outCount);}
			for (var id=1;id<=rows;id++) {
				var row=pElement.insertRow(-1);
	
				if (that.elementDef.typ==2) {
					setInput_ko(row,id);
				} else {
					setInput(row,id);
				}
					
				if (that.elementDef.typ==2) {
					setOutput(row,id,0);
				} else if (that.elementDef.typ==3) {
					setOutput_cmd(row,id);
				} else {
					setOutput(row,id,1);
				}
			}

			setTitle();
			setInfo();			
		}

		addMenuItem();
		customizeBorders();
	}

	this.showLiveStatus=function(id,status) {
		var obj=document.getElementById(app1_winId+"-element-"+id);
		if (obj) {
			obj.dataset.live="1";

			var d=document.getElementById(app1_winId+"-element-"+id+"-0");
			if (status!=0) {
				d.style.color="#000000";
				d.style.background="#ff8000";
				d.style.webkitAnimation="app1_animElementStatus 1s infinite linear";
			} else {
				d.style.color="#000000";
				d.style.background="#e8e800";
			}
		}
	}	

	this.hideNoLiveAll=function() {
		var element=document.getElementById(app1_winId+"-page").querySelectorAll("[data-live='0']");
		for (var t=0; t<element.length; t++) {
			element[t].style.opacity="0.5";
		}
	}	

	function createTable(cssClass) {	
		pElement=document.createElement('table');
		var tmp=document.getElementById(app1_winId+"-page");
		tmp.appendChild(pElement);
		pElement.id=app1_winId+"-element-"+that.elementDef.id;
		pElement.className=cssClass;
		pElement.cellPadding="1";
		pElement.cellSpacing="0";
		pElement.dataset.elementselected=0;
		pElement.dataset.layer=that.elementDef.layer;
		pElement.dataset.live="0";
		pElement.style.left=that.elementDef.xpos+"px";
		pElement.style.top=that.elementDef.ypos+"px";
		pElement.setAttribute("onMouseDown","clickCancel();");	//verhindert die Anzeige des allgemeinen Kontextmenüs beim Klick auf einen freien Bereich des LBS
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
		} else {
			pElement.style.cursor="not-allowed";
		}
	}

	function customizeBorders() {
		//Border-Radius: erste Zeile
		var cl=pElement.rows[0].cells.length-1;		
		pElement.rows[0].cells[0].style.borderTopLeftRadius="3px";
		pElement.rows[0].cells[cl].style.borderTopRightRadius="3px";	
		//Border-Radius: letzte Zeile
		var rl=pElement.rows.length-1;
		var cl=pElement.rows[rl].cells.length-1;
		pElement.rows[rl].cells[0].style.borderBottomLeftRadius="3px";
		pElement.rows[rl].cells[cl].style.borderBottomRightRadius="3px";	
		//Border der letzten Zeile löschen
		for (var t=0;t<=cl;t++) {pElement.rows[rl].cells[t].style.borderBottom="none";}
	}
	
	function addMenuItem() {
		//LBS im Menu auflisten
		if (app1_liveMode!=2) {
			menuItem=createNewDiv(app1_winId+"-menu",app1_winId+"-element-"+that.elementDef.id+"-list");
			menuItem.className="controlListItem";
			menuItem.style.display="block";
			menuItem.innerHTML=that.elementDef.name+" <span class='id'>"+that.elementDef.id+"</span>";
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				menuItem.setAttribute("onMouseDown","app1_elementClick('"+pElement.id+"');");
			} else {
				menuItem.style.opacity="0.5";
				menuItem.style.cursor="not-allowed";
			}
		}
	}

	function setTitle() {
		//Titel	
		if (that.elementDef.typ==3 && that.elementDef.style&1 && that.elementDef.inCount==1) {
			//einzeilige Ausgangsboxen			
			if (that.elementDef.style&8) {
				//Titel rechts
				var row=pElement.rows[0];
				var title=row.insertCell(-1);
			} else {
				//Titel mittig
				var row=pElement.rows[0];
				var title=row.insertCell(((that.elementDef.data[1].in.name)?3:2));
			}
			if (that.elementDef.style&4) {
				title.innerHTML="<div class='app1_cellContent'><span class='app1_elId'>"+that.elementDef.id+"</span><span class='app1_elHelp' onMouseDown='app1_elementShowHelp(\""+pElement.id+"\");'>?</span></div>";
			} else {
				title.innerHTML="<div class='app1_cellContent' style='width:100%;'><table border='0' cellpadding='0' cellspacing='0' style='width:100%; table-layout:auto;'><tr><td>"+that.elementDef.title+"</td><td align='right'><span class='app1_elId'>"+that.elementDef.id+"</span><span class='app1_elHelp' onMouseDown='app1_elementShowHelp(\""+pElement.id+"\");'>?</span></td></tr></table></div>";
			}
		} else {
			var row=pElement.insertRow(0);
			var title=row.insertCell(-1);
			title.colSpan="5";
			title.innerHTML="<div class='app1_cellContent' style='width:100%;'><table border='0' cellpadding='0' cellspacing='0' style='width:100%; table-layout:auto;'><tr><td>"+that.elementDef.title+"</td><td align='right'><span class='app1_elId'>"+that.elementDef.id+"</span><span class='app1_elHelp' onMouseDown='app1_elementShowHelp(\""+pElement.id+"\");'>?</span></td></tr></table></div>";
		}
			
		title.id=app1_winId+"-element-"+that.elementDef.id+"-0";
		title.className="app1_elTitel";
		title.dataset.item=0;
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			title.setAttribute("onMouseDown","app1_elementClick('"+pElement.id+"');");
		} else {
			title.style.opacity="0.5";
			title.style.cursor="not-allowed";
		}
	}

	function setInfo() {
		//Bemerkung
		var info=app1_newDiv(pElement,app1_winId+"-element-"+that.elementDef.id+"-1");

		if (that.elementDef.typ==3 && that.elementDef.style&1 && that.elementDef.inCount==1) {
			//einzeilige Ausgangsboxen
			if (that.elementDef.style&2) {info.className="app1_divTitelInfo2";} else {info.className="app1_divTitelInfo";}
		} else {
			info.className="app1_divTitelInfo";
		}

		info.dataset.info=that.elementDef.info;
		info.innerHTML=that.elementDef.info2;
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			info.setAttribute("onMouseDown","app1_itemInfoClick('"+info.id+"');");
		} else {
			info.style.cursor="not-allowed";
		}
	}

	function setInput(row,id) {
		if (that.elementDef.data[id].in) {
	
			//Kürzel
			var d=row.insertCell(-1);
			if (!that.elementDef.data[id].in.name) {d.colSpan="2";}
			d.id=app1_winId+"-element-"+that.elementDef.id+"-11-"+id;
			d.className="app1_elInputNum col"+that.elementDef.data[id].in.color;
			d.dataset.liveea=1;
	
			//Control:
			d.dataset.type=1000;
			d.dataset.root=30;
			d.dataset.options='typ=1;reset=0;refresh=0';
			if (that.elementDef.data[id].in.linktyp==0) {
				d.dataset.gaconnected=1;
				d.dataset.value=that.elementDef.data[id].in.linkid;
			} else {
				d.dataset.gaconnected=0;
				d.dataset.value=0;
			}
			d.dataset.callback="app1_editGa_callback('"+d.id+"');";
			d.setAttribute("onMouseOver","app1_itemHover('"+d.id+"');");
			d.setAttribute("onMouseOut","app1_itemHoverOut('"+d.id+"');");
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				d.setAttribute("onMouseDown","app1_itemConnectorClick('"+d.id+"');");
			} else {
				d.style.cursor="not-allowed";
			}
			d.innerHTML="<div class='app1_cellContent'><div id='"+app1_winId+"-element-"+that.elementDef.id+"-ko-"+id+"'></div>&#x25B8;&nbsp;E"+id+"</div>";
	
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			} else {
				d.style.cursor="not-allowed";
			}
		
			//KO-Fähnchen generieren
			if (that.elementDef.data[id].in.linktyp==0) { 
				var tmp=document.getElementById(app1_winId+"-element-"+that.elementDef.id+"-ko-"+id);
				var d=app1_newDiv(tmp,app1_winId+"-element-"+that.elementDef.id+"-13-"+id);
				d.className="app1_elInputKo";
				d.dataset.info=that.elementDef.data[id].in.ga2;
				d.dataset.liveko=1;
				d.setAttribute("onMouseOver","app1_itemTooltipHover('"+d.id+"','app1_divKoTooltip',false);");
				d.setAttribute("onMouseOut","app1_itemTooltipHoverOut('"+d.id+"');");
				d.innerHTML="<div style='pointer-events:none;'>"+that.elementDef.data[id].in.ga1+"&nbsp;</div>";			
	
				//Control:
				d.dataset.type=1000;
				d.dataset.root=30;
				d.dataset.options='typ=1;reset=0;refresh=0';
				d.dataset.value=that.elementDef.data[id].in.linkid;
				d.dataset.callback="app1_editGa_callback('"+d.id+"');";
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","app1_itemGaClick('"+d.id+"');");
				} else {
					d.style.cursor="not-allowed";
				}
			}
	
	
			//Bezeichnung
			if (that.elementDef.data[id].in.name) {
				var d=row.insertCell(-1);
				d.id=app1_winId+"-element-"+that.elementDef.id+"-11c-"+id;
				d.className="app1_elInput";		
				//Control (Verweis auf -11):
				d.setAttribute("onMouseOver","app1_itemHover('"+app1_winId+"-element-"+that.elementDef.id+"-11-"+id+"');");
				d.setAttribute("onMouseOut","app1_itemHoverOut('"+app1_winId+"-element-"+that.elementDef.id+"-11-"+id+"');");
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","app1_itemConnectorClick('"+app1_winId+"-element-"+that.elementDef.id+"-11-"+id+"');");
				} else {
					d.style.cursor="not-allowed";
				}
				d.innerHTML="<div class='app1_cellContent'><div id='"+app1_winId+"-element-"+that.elementDef.id+"-ko-"+id+"'></div>"+that.elementDef.data[id].in.name+"</div>";
				
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				} else {
					d.style.cursor="not-allowed";
				}
			}
	
	
			//Initwert/Livewert
			var d=row.insertCell(-1);
			d.id=app1_winId+"-element-"+that.elementDef.id+"-12-"+id;
			d.className="app1_elInputValue";
	
			d.setAttribute("onMouseOver","app1_itemTooltipHover('"+d.id+"','app1_divValueTooltip',true);");
			d.setAttribute("onMouseOut","app1_itemTooltipHoverOut('"+d.id+"');");
	
			if (app1_liveMode==0) {
				d.dataset.info=that.elementDef.data[id].in.value;
				d.innerHTML="<div class='app1_cellContent'>"+that.elementDef.data[id].in.value+"</div>";
	
				//Control:
				d.dataset.type=1005;
				d.dataset.value=that.elementDef.data[id].in.value2;
				d.dataset.callback="app1_editValue_callback('"+d.id+"');";
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","if (!app1_GUImode[1]){controlClickLeft('"+d.id+"'); clickCancel();}");
				} else {
					d.style.cursor="not-allowed";
				}
			} else {
				d.dataset.liveea=1;
				if (that.elementDef.live[id]) {
					d.dataset.info="<span class='app1_liveValue'>"+that.elementDef.live[id]+"</span>";
					d.innerHTML="<div class='app1_cellContent'><span class='app1_liveValue'>"+that.elementDef.live[id]+"</span></div>";
					if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
						d.setAttribute("onMouseDown","app1_itemValueClick('"+d.id+"');");
					} else {
						d.style.cursor="not-allowed";
					}
				} else {
					d.dataset.info=that.elementDef.data[id].in.value;
					d.innerHTML="<div class='app1_cellContent'>"+that.elementDef.data[id].in.value+"</div>";
					if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
						d.setAttribute("onMouseDown","app1_itemValueClick('"+d.id+"');");
					} else {
						d.style.cursor="not-allowed";
					}
				}
			}

		} else if (that.elementDef.inCount>0) {
			var d=row.insertCell(-1);
			d.className="app1_elDefault";
			d.colSpan="3";
		}
	}	

	function setInput_ko(row,id) {
		if (that.elementDef.data[id].in) {
	
			//KO-Auswahl
			var d=row.insertCell(-1);
			d.id=app1_winId+"-element-"+that.elementDef.id+"-10-"+id;
			if (that.elementDef.data[id].in.linkid>0) {
				d.className="app1_elEingangsboxInputKo";
				d.innerHTML="<div class='app1_cellContent'><table border='0' cellpadding='0' cellspacing='0'><tr><td align='right' style='min-width:60px;'>"+that.elementDef.data[id].in.ga1+"&nbsp;</td><td align='left'><div style='overflow:hidden; white-space:nowrap;'>"+that.elementDef.data[id].in.ga2+"</div></td></tr></table></div>";
				d.dataset.info="<table border='0' cellpadding='0' cellspacing='0'><tr><td align='right' style='min-width:60px;'>"+that.elementDef.data[id].in.ga1+"&nbsp;</td><td align='left'><div style='overflow:hidden; white-space:nowrap;'>"+that.elementDef.data[id].in.ga2+"</div></td></tr></table>";
				d.setAttribute("onMouseOver","app1_itemTooltipHover('"+d.id+"','app1_divEingangsboxTooltip',true);");
				d.setAttribute("onMouseOut","app1_itemTooltipHoverOut('"+d.id+"');");
				d.dataset.value=that.elementDef.data[id].in.linkid;
				d.dataset.liveko=1;
			} else {
				d.className="app1_elEingangsboxInput";
				d.innerHTML="<div class='app1_cellContent'>"+that.elementDef.data[id].in.name+"</div>";
				d.dataset.value=0;
				d.dataset.liveko=0;
			}
			d.dataset.liveea=0;
	
			//Control:
			d.dataset.type=1000;
			d.dataset.root=30;
			d.dataset.options='typ=1;reset=0;refresh=0';
			d.dataset.callback="app1_editGa_callback('"+d.id+"');";
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				d.setAttribute("onMouseDown","app1_itemGaClick('"+d.id+"');");
			} else {
				d.style.cursor="not-allowed";
			}
	
		} else if (that.elementDef.inCount>0) {
			var d=row.insertCell(-1);
			d.className="app1_elDefault";
		}
	}	

	function setOutput(row,id,flagLive) {
		if (that.elementDef.data[id].out) {
	
			//Bezeichnung
			if (that.elementDef.data[id].out.name) {
				var d=row.insertCell(-1);
				d.id=app1_winId+"-element-"+that.elementDef.id+"-20c-"+id;
				d.className="app1_elOutput";
				d.setAttribute("onMouseOver","app1_itemHover('"+app1_winId+"-element-"+that.elementDef.id+"-20-"+id+"');");
				d.setAttribute("onMouseOut","app1_itemHoverOut('"+app1_winId+"-element-"+that.elementDef.id+"-20-"+id+"');");
				d.innerHTML="<div class='app1_cellContent'>"+that.elementDef.data[id].out.name+"</div>";
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","app1_itemAusgangClick('"+app1_winId+"-element-"+that.elementDef.id+"-20-"+id+"');");
				} else {
					d.style.cursor="not-allowed";
				}
			}
			
			
			//Kürzel
			var d=row.insertCell(-1);
			if (!that.elementDef.data[id].out.name) {d.colSpan="2";}
			d.id=app1_winId+"-element-"+that.elementDef.id+"-20-"+id;
			d.className="app1_elOutputNum";
			d.dataset.liveea=flagLive;
			d.setAttribute("onMouseOver","app1_itemHover('"+d.id+"');");
			d.setAttribute("onMouseOut","app1_itemHoverOut('"+d.id+"');");
			d.innerHTML="<div class='app1_cellContent'>A"+id+"&nbsp;&#x25B8;</div>";
			if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
				d.setAttribute("onMouseDown","app1_itemAusgangClick('"+d.id+"');");
			} else {
				d.style.cursor="not-allowed";
			}	
	
		} else if (that.elementDef.outCount>0) {
			var d=row.insertCell(-1);
			d.className="app1_elDefault";
			d.colSpan="2";
		}
	}	

	function setOutput_cmd(row,id) {
		if (that.elementDef.data[id].out) {

			//Befehle
			var d=row.insertCell(-1);
			d.id=app1_winId+"-element-"+that.elementDef.id+"-2";
			d.dataset.item=1;
			if (that.elementDef.data[id].out.name==0) {
				d.className="app1_elAusgangsboxOutput0";
			} else {
				d.className="app1_elAusgangsboxOutput1";
			}
			d.innerHTML="<div class='app1_cellContent'>"+that.elementDef.data[id].out.name+"&nbsp;Befehl"+((that.elementDef.data[id].out.name!=1)?"e":"")+"</div>";
	
			//Control:
			if (app1_liveMode==0) {
				d.dataset.type=1006;
				d.dataset.db="editLogicCmdList";
				d.dataset.value=that.elementDef.id;
				d.dataset.callback="app1_editCommandList_callback('"+d.id+"');";
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","app1_itemCmdClick('"+d.id+"');");
				} else {
					d.style.cursor="not-allowed";
				}
			} else {
				if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
					d.setAttribute("onMouseDown","app1_itemCmdClickRight('"+d.id+"');");
				} else {
					d.style.cursor="not-allowed";
				}
			}
			
		} else {
			var d=row.insertCell(-1);
			d.className="app1_elDefault";
		}
	}	

	function createLbs_12000000() {
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			createTable("app1_elTextboxContainer");
		} else {
			createTable("app1_elTextboxContainerLayer1");
		}

		//Titel
		var row=pElement.insertRow(-1);
		var d=row.insertCell(-1);
		d.id=app1_winId+"-element-"+that.elementDef.id+"-0";
		d.className="app1_elTextboxTitel";
		d.style.position="relative";
		d.innerHTML="<div class='app1_cellContent' style='width:100%;'><table border='0' cellpadding='0' cellspacing='0' style='width:100%; table-layout:auto;'><tr><td>"+that.elementDef.title+"</td><td align='right'><span class='app1_elId'>"+that.elementDef.id+"</span><span class='app1_elHelp' onMouseDown='app1_elementShowHelp(\""+pElement.id+"\");'>?</span></td></tr></table></div>";
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			d.setAttribute("onMouseDown","app1_elementClick('"+pElement.id+"');");
		} else {
			d.style.opacity="0.5";
			d.style.cursor="not-allowed";
		}

		var row=pElement.insertRow(-1);
		var cell=row.insertCell(-1);
		var d=app1_newDiv(cell,app1_winId+"-element-"+that.elementDef.id+"-1");
		d.className="app1_divTextboxInfo";
		d.style.position="relative";
		d.dataset.info=that.elementDef.info;
		d.innerHTML=that.elementDef.info2;
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			d.setAttribute("onMouseDown","app1_itemTextboxClick('"+d.id+"');");
		} else {
			d.style.cursor="not-allowed";
		}
	}

	function createLbs_error() {
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			createTable("app1_elContainer");
		} else {
			createTable("app1_elContainerLayer1");
		}

		//Titel
		var row=pElement.insertRow(-1);
		var d=row.insertCell(-1);
		d.id=app1_winId+"-element-"+that.elementDef.id+"-0";
		d.className="app1_elTitel";
		d.dataset.item=0;
		d.innerHTML="<div class='app1_cellContent' style='width:100%;'><table border='0' cellpadding='0' cellspacing='0' style='width:100%; table-layout:auto;'><tr><td>LBS "+that.elementDef.functionid+"</td><td align='right'><span class='app1_elId'>"+that.elementDef.id+"</span></td></tr></table></div>";
		if (that.elementDef.layer==0 || that.elementDef.layer==app1_layerMode) {
			d.setAttribute("onMouseDown","app1_elementClick('"+pElement.id+"');");
		} else {
			d.style.opacity="0.5";
			d.style.cursor="not-allowed";
		}

		var row=pElement.insertRow(-1);
		var d=row.insertCell(-1);
		d.style.width="150px";
		d.style.minWidth="150px";
		d.style.maxWidth="150px";
		d.style.padding="5px";
		d.style.color="#ffffff";
		d.style.background="#ff0000";
		d.innerHTML="Dieser Logikbaustein ist fehlerhaft oder existiert nicht mehr.";
	}

}
