/* 
*/ 
function app2_init(winId) {

	//--- ggf. anpassen ---
	app2_zoomFaktor=1;												//Default-Zoomfaktor (beliebig definierbar)
	app2_el_shadowSelected="0px 0px 0px 5px "+apps_colorSelected;	//Schatten eines Elements, wenn markiert
	app2_linkLineWidth=2;											//Liniendicke (und Markergröße) bei verknüpften Visuelementen
	app2_previewValue='';											//Default: KO-Wert1 für Vorschau
	app2_previewValue3='';											//Default: KO-Wert3 für Vorschau
	//---------------------

	app2_winId=winId;
	app2_pageId=0;
	app2_bufferSelected=null;			//Zwischenspeicher für Selektion
	app2_bufferGroups=null;				//Zwischenspeicher für Gruppen-Zustand
	app2_elementSelectRectData={mode:0,x1:0,y1:0,x2:0,y2:0};	//Auswahlrahmen

	app2_elementOutlineColor=apps_colorSelected;	//Rahmenfarbe/Linkfarbe für Elemente (wird in app2.php definiert)

	//Verschieben eines Elements
	app2_dragElements=null;				//Drag-Elemente: Array[0..oo] = object{obj,objId,left,top,width,height,offsetX,offsetY}
	app2_dragElement=null;				//das "angefasste" Element beim Draggen
	app2_dragMoved=false;				//true=beim Draggen wurden Elemente bewegt, false=es wurde nur geklickt
	app2_dragKeyboardX=0;				//Position (Mausersatz)
	app2_dragKeyboardY=0;				//Position (Mausersatz)

	//WinId beim Verschieben per Tastatur
	app2_elementsKeyboardMoveWinId='';

	app2_curPosX=0;						//X-Position der Maus beim Einfügen eines VE per Rechtsklick
	app2_curPosY=0;						//Y-Position der Maus beim Einfügen eines VE per Rechtsklick

	//Raster
	app2_gridXmode=true;
	app2_gridYmode=true;

	//Achsensperrung/Seitengrenzensperrung
	app2_unlockXmode=true;
	app2_unlockYmode=true;
	app2_pageboundMode=true;

	//Default-Zoom einstellen
	app2_zoom(app2_zoomFaktor);

	//Vorschau
	app2_previewMode=false;		

	//Doppelklick-Erkennung
	app2_dblClickTimeout=false;
	app2_dblClickObjid="";

	//Verknüpfungen
	app2_links=new Array();
	
	app2_layerMode=0;						//aktueller Layer-Modus
}

function app2_quit(winId) {
	app2_winId=null;
	app2_links.length=0;
	closeWindow(winId);
}

function app2_zoom(n) {
	app2_zoomFaktor=n;
	var d=document.getElementById(app2_winId+"-page");
	d.style.webkitTransform="scale("+app2_zoomFaktor+")";
	d.style.width=((1/app2_zoomFaktor)*100)+"%";
	d.style.height=((1/app2_zoomFaktor)*100)+"%";
	document.getElementById(app2_winId+"-zoom").innerHTML=parseInt(n*100)+"%";
}

function app2_refreshIncludeJS() {
	//Visuelemente erneut inkludieren und app2_refreshAll(0) aufrufen
	//(nach Schließen von "Konfiguration" oder "Visuaktivierung" aus app2 heraus => VSE könnten sich geändert haben)
	includeVisuelements('vse/vse_include_admin.js',function(){app2_refreshAll(0);},true);
}

function app2_refreshAll(pageId,list) {
	//Alle Elemente in "page" komplett löschen (wird von app2.php aufgerufen)
	//wird bei JEDEM Neuaufbau der Elemente aufgerufen (z.B. nach dem Löschen/Erstellen/Verbinden/...)
	//pageId: 0=aktuelle Seite refreshen, 1..x=diese Seite aufrufen
	//list: (optinal) String mit den IDs der Elemente, die beim Aufbau markiert sein sollen (für Duplizierung): z.B. "1;2;3;4;5;"
	if (pageId==0) {
		pageId=app2_pageId;
		app2_saveState();
	} else {
		app2_bufferSelected=new Array();
		app2_bufferGroups=new Array();
	}

	if (!list) {list="";}

	app2_curPosX=0;
	app2_curPosY=0;

	app2_links=new Array();

	app2_pageId=pageId;
	ajax("start",2,app2_winId,pageId+AJAX_SEPARATOR1+((app2_previewMode)?1:0)+AJAX_SEPARATOR1+app2_previewValue+AJAX_SEPARATOR1+app2_previewValue3,list);
}

function app2_saveState() {
	app2_bufferSelected=new Array();
	app2_bufferGroups=new Array();
	var visupage=document.getElementById(app2_winId+"-visupage");
	if (visupage && app2_pageId>0) {
		var element=visupage.querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {app2_bufferSelected.push(element[t].id);}
		var element=document.getElementById(app2_winId+"-menu").querySelectorAll("[data-groupexpanded='1']");
		for (var t=0; t<element.length; t++) {app2_bufferGroups.push(element[t].id);}
	}
}

function app2_restoreState(list) {
	app2_elementSelectNone();
	if (list) {
		var element=list.split(";");
		for (var t=0; t<element.length; t++) {app2_elementSelect(app2_winId+"-element-"+element[t],1);}
	} else {
		for (var t=0; t<app2_bufferSelected.length; t++) {app2_elementSelect(app2_bufferSelected[t],1);}
	}
	for (var t=0; t<app2_bufferGroups.length; t++) {app2_groupCollapse(app2_bufferGroups[t],1);}
	app2_bufferSelected=new Array();
	app2_bufferGroups=new Array();
}


/*
============================================================================
GUI-Events
============================================================================
*/

//---------------------------------------------
//Element skalieren
//---------------------------------------------

function app2_elementDragScaleStart(objId) {
	var event=window.event;

	app2_dragElements=new Array();
	app2_dragMoved=false;
	var visuPage=document.getElementById(app2_winId+"-visupage");

	var mouseX=event.pageX*(1/app2_zoomFaktor);
	var mouseY=event.pageY*(1/app2_zoomFaktor);

	//Anfasser-Element merken
	app2_dragElement=document.getElementById(objId);
	
	//Anfasser temporär selektieren
	app2_saveState();
	app2_elementSelect(objId,1);	//Anfasser-Element markieren (ggf. auch das Gruppenelement selbst)
	if (app2_dragElement.dataset.controltyp==0) {
		app2_groupSelect(objId,1); 	//Anfasser-Gruppe komplett markieren
	}

	//Boundingbox der selektierten Elemente ermitteln und merken
	var bBox=app2_selectionBoundingBox();
	app2_dragElements.push({
		obj:null,
		x:bBox.x,
		y:bBox.y,
		w:bBox.width,
		h:bBox.height,
		offsetX:parseInt(mouseX),
		offsetY:parseInt(mouseY)
	});

	//Selektierte Elemente merken (einschl. des Anfassers)
	var elements=visuPage.querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<elements.length; t++) {
		var obj=document.getElementById(elements[t].id);
		app2_dragElements.push({
			obj:obj,
			x:parseInt(obj.offsetLeft)-app2_dragElements[0].x,
			y:parseInt(obj.offsetTop)-app2_dragElements[0].y,
			w:parseInt(obj.offsetWidth),
			h:parseInt(obj.offsetHeight),
			left:parseInt(obj.offsetLeft),
			top:parseInt(obj.offsetTop),
			width:parseInt(obj.offsetWidth),
			height:parseInt(obj.offsetHeight)
		}); 
	}

	//Fadenkreuz (Boundingbox) und Größe anzeigen
//### in Prozent:	document.getElementById(app2_winId+"-position").innerHTML="100% / 100%";
	document.getElementById(app2_winId+"-position").innerHTML="<span style='color:#0000f0;'>"+parseInt(bBox.width)+" / "+parseInt(bBox.height)+"</span>";
	app2_CrosshairX=app2_newDiv(visuPage,app2_winId+"-crosshairX");
	app2_CrosshairX.className="app2_CrosshairX";
	app2_CrosshairX.style.top=app2_dragElements[0].y+"px";
	app2_CrosshairX.style.height=app2_dragElements[0].h+"px";
	app2_CrosshairX.style.width=visuPage.scrollWidth+"px";
	app2_CrosshairY=app2_newDiv(visuPage,app2_winId+"-crosshairY");
	app2_CrosshairY.className="app2_CrosshairY";
	app2_CrosshairY.style.left=app2_dragElements[0].x+"px";
	app2_CrosshairY.style.width=app2_dragElements[0].w+"px";
	app2_CrosshairY.style.height=visuPage.scrollHeight+"px";
	
	window.addEventListener("mousemove",app2_elementDragScaleMove,false);
	window.addEventListener("mouseup",app2_elementDragScaleEnd,false);
}

function app2_elementDragScaleMove(event,dX,dY) {
	var event=window.event;

	app2_dragMoved=true;

	var visuPage=document.getElementById(app2_winId+"-visupage");
	var visupageWidth=visuPage.style.width.replace("px","");
	var visupageHeight=visuPage.style.height.replace("px","");
	
	//Raster einstellen
	var rasterX=1;
	var rasterY=1;
	if (app2_gridXmode) {rasterX=parseInt(document.getElementById(app2_winId+"-rasterx").value);}
	if (app2_gridYmode) {rasterY=parseInt(document.getElementById(app2_winId+"-rastery").value);}

	var mouseX=event.pageX*(1/app2_zoomFaktor);
	var mouseY=event.pageY*(1/app2_zoomFaktor);

	//neue Größe (relativ) der Boundingbox
	var newX=parseInt(mouseX-app2_dragElements[0].offsetX);
	var newY=parseInt(mouseY-app2_dragElements[0].offsetY);

	//BBOX-Größe um newX/Y skalieren (da hinein müssen die Elemente neu skaliert/positioniert werden)
	var bx=app2_dragElements[0].x;
	var by=app2_dragElements[0].y;
	var bw=app2_dragElements[0].w+newX;
	var bh=app2_dragElements[0].h+newY;
	bx=Math.round(bx/rasterX)*rasterX;
	by=Math.round(by/rasterY)*rasterY;
	bw=Math.round(bw/rasterX)*rasterX;
	bh=Math.round(bh/rasterY)*rasterY;

	//Grenzen prüfen (Boundingbox soll nicht über Seitenränder hinaus gehen)
	if (app2_pageboundMode) {
		if (bx<visupageWidth && (bx+bw)>visupageWidth) {bw=visupageWidth-app2_dragElements[0].x;}
		if (by<visupageHeight && (by+bh)>visupageHeight) {bh=visupageHeight-app2_dragElements[0].y;}
	}

	//BBOX muss >=1px groß sein
	if (bw<1) {bw=1;}
	if (bh<1) {bh=1;}

	//Elemente relativ zur neuen Boundingbox skalieren und positionieren(!)
	for (var t=1; t<app2_dragElements.length; t++) {	//t=1 (ohne Boundingbox!)
		var x=Math.round(app2_dragElements[t].x*(bw/app2_dragElements[0].w)+bx);
		var y=Math.round(app2_dragElements[t].y*(bh/app2_dragElements[0].h)+by);
		var w=Math.round(app2_dragElements[t].w*(bw/app2_dragElements[0].w));
		var h=Math.round(app2_dragElements[t].h*(bh/app2_dragElements[0].h));

		//Elemente müssen >=1px groß sein (abzüglich evtl. Design-Skalierung!)
		if (app2_unlockXmode) {
			if (w-visuElement_getScale(app2_dragElements[t].obj,0)<1) {w=1+visuElement_getScale(app2_dragElements[t].obj,0);}
			app2_dragElements[t].left=x;
			app2_dragElements[t].width=w;
			app2_dragElements[t].obj.style.left=x+"px";
			app2_dragElements[t].obj.style.width=w+"px";
		}

		if (app2_unlockYmode) {
			if (h-visuElement_getScale(app2_dragElements[t].obj,1)<1) {h=1+visuElement_getScale(app2_dragElements[t].obj,1);}
			app2_dragElements[t].top=y;
			app2_dragElements[t].height=h;
			app2_dragElements[t].obj.style.top=y+"px";
			app2_dragElements[t].obj.style.height=h+"px";
		}
	}

	//Fadenkreuz (Boundingbox) und Größe anzeigen (Boundingbox)
	var bBox=app2_selectionBoundingBox();
//### in Prozent:	document.getElementById(app2_winId+"-position").innerHTML=Math.round(bBox.width/app2_dragElements[0].w*100)+"% / "+Math.round(bBox.height/app2_dragElements[0].h*100)+"%";
	document.getElementById(app2_winId+"-position").innerHTML="<span style='color:#0000f0;'>"+parseInt(bBox.width)+" / "+parseInt(bBox.height)+"</span>";
	app2_CrosshairX.style.height=bBox.height+"px";
	app2_CrosshairY.style.width=bBox.width+"px";
	app2_CrosshairY.style.left=bBox.x+"px";
	app2_CrosshairY.style.height=visuPage.scrollHeight+"px";
	app2_CrosshairX.style.top=bBox.y+"px";
	app2_CrosshairX.style.width=visuPage.scrollWidth+"px";

	app2_groupBoundingBoxes();
	app2_drawLinks();
}

function app2_elementDragScaleEnd(event) {
	var event=window.event;

	if (app2_dragMoved) {
		//Positionen und Größen speichern
		var data="";
		for (var t=1; t<app2_dragElements.length; t++) {	//t=1 (ohne Boundingbox!)
			data+=app2_dragElements[t].obj.dataset.elementid+",";
			data+=parseInt(app2_dragElements[t].left-visuElement_getOffset(app2_dragElements[t].obj,0))+",";
			data+=parseInt(app2_dragElements[t].top-visuElement_getOffset(app2_dragElements[t].obj,1))+",";
			data+=parseInt(app2_dragElements[t].width-visuElement_getScale(app2_dragElements[t].obj,0))+",";
			data+=parseInt(app2_dragElements[t].height-visuElement_getScale(app2_dragElements[t].obj,1))+";";
		}
		ajax("saveElementsPositionAndSize",2,app2_winId,app2_pageId,data);
		app2_groupBoundingBoxes();
	}

	app2_restoreState();

	//Aufräumen
	app2_dragElements.length=0;
	window.removeEventListener("mousemove",app2_elementDragScaleMove,false);
	window.removeEventListener("mouseup",app2_elementDragScaleEnd,false);
	app2_CrosshairX.parentNode.removeChild(app2_CrosshairX);
	app2_CrosshairY.parentNode.removeChild(app2_CrosshairY);
}



//---------------------------------------------
//Element(e) verschieben
//---------------------------------------------

function app2_elementDragStart(objId,keyMode) {
	var event=window.event;

	app2_dragElements=new Array();
	app2_dragMoved=false;
	var visuPage=document.getElementById(app2_winId+"-visupage");

	if (!keyMode) {
		var mouseX=event.pageX*(1/app2_zoomFaktor);
		var mouseY=event.pageY*(1/app2_zoomFaktor);
	} else {
		var mouseX=0;
		var mouseY=0;	
		app2_dragKeyboardX=0;
		app2_dragKeyboardY=0;
	}

	//Anfasser-Element merken
	app2_dragElement=document.getElementById(objId);
	
	if (!keyMode) {
		//Anfasser temporär selektieren
		app2_saveState();
		app2_elementSelect(objId,1);	//Anfasser-Element markieren (ggf. auch das Gruppenelement selbst)
		if (app2_dragElement.dataset.controltyp==0) {
			app2_groupSelect(objId,1); 	//Anfasser-Gruppe komplett markieren
		}
	}

	//Boundingbox der selektierten Elemente ermitteln und merken
	var bBox=app2_selectionBoundingBox();
	app2_dragElements.push({
		obj:null,
		left:bBox.x,
		top:bBox.y,
		width:bBox.width,
		height:bBox.height,
		offsetX:parseInt(mouseX-bBox.x),
		offsetY:parseInt(mouseY-bBox.y)
	});

	//Selektierte Elemente merken (einschl. des Anfassers): offsetX/Y=Position relativ zur Boundingbox(!)
	var elements=visuPage.querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<elements.length; t++) {
		var obj=document.getElementById(elements[t].id);
		app2_dragElements.push({
			obj:obj,
			left:obj.offsetLeft,
			top:obj.offsetTop,
			offsetX:parseInt(obj.offsetLeft-bBox.x),
			offsetY:parseInt(obj.offsetTop-bBox.y)
		}); 
	}

	//Koordinaten anzeigen (Boundingbox)
	document.getElementById(app2_winId+"-position").innerHTML=parseInt(app2_dragElements[0].left)+" / "+parseInt(app2_dragElements[0].top);

	//Fadenkreuz (Boundingbox)
	app2_CrosshairX=app2_newDiv(visuPage,app2_winId+"-crosshairX");
	app2_CrosshairX.className="app2_CrosshairX";
	app2_CrosshairX.style.top=app2_dragElements[0].top+"px";
	app2_CrosshairX.style.height=app2_dragElements[0].height+"px";
	app2_CrosshairX.style.width=visuPage.scrollWidth+"px";
	app2_CrosshairY=app2_newDiv(visuPage,app2_winId+"-crosshairY");
	app2_CrosshairY.className="app2_CrosshairY";
	app2_CrosshairY.style.left=app2_dragElements[0].left+"px";
	app2_CrosshairY.style.width=app2_dragElements[0].width+"px";
	app2_CrosshairY.style.height=visuPage.scrollHeight+"px";

	if (!keyMode) {
		window.addEventListener("mousemove",app2_elementDragMove,false);
		window.addEventListener("mouseup",app2_elementDragEnd,false);
	}
}

function app2_elementDragMove(event,keyMode,dX,dY) {

	var event=window.event;

	app2_dragMoved=true;

	var visuPage=document.getElementById(app2_winId+"-visupage");
	var visupageWidth=visuPage.style.width.replace("px","");
	var visupageHeight=visuPage.style.height.replace("px","");
	
	//Raster einstellen
	var rasterX=1;
	var rasterY=1;
	if (app2_gridXmode) {rasterX=parseInt(document.getElementById(app2_winId+"-rasterx").value);}
	if (app2_gridYmode) {rasterY=parseInt(document.getElementById(app2_winId+"-rastery").value);}

	if (!keyMode) {
		var mouseX=event.pageX*(1/app2_zoomFaktor);
		var mouseY=event.pageY*(1/app2_zoomFaktor);
	} else {
		app2_dragKeyboardX+=dX*rasterX;
		app2_dragKeyboardY+=dY*rasterY;
		var mouseX=app2_dragKeyboardX;
		var mouseY=app2_dragKeyboardY;	
	}

	//Boundingbox positionieren (mit Raster und Grenzen)
	//neue Koordinaten
	var newX=parseInt(mouseX-app2_dragElements[0].offsetX);
	var newY=parseInt(mouseY-app2_dragElements[0].offsetY);
	//Raster anwenden
	newX=Math.round(newX/rasterX)*rasterX;
	newY=Math.round(newY/rasterY)*rasterY;

	//Grenzen prüfen
	if (app2_pageboundMode) {
		if (newX<0) {newX=0;}
		if (newY<0) {newY=0;}
		if (newX>(visupageWidth-app2_dragElements[0].width)) {newX=visupageWidth-app2_dragElements[0].width;}
		if (newY>(visupageHeight-app2_dragElements[0].height)) {newY=visupageHeight-app2_dragElements[0].height;}
	}
	
	app2_dragElements[0].left=newX;
	app2_dragElements[0].top=newY;

	//Elemente neu positionieren (relativ zur Boundingbox)
	for (var t=1; t<app2_dragElements.length; t++) {	//t=1 (ohne Boundingbox!)
		if (app2_unlockXmode) {
			app2_dragElements[t].left=parseInt(newX+app2_dragElements[t].offsetX);
			app2_dragElements[t].obj.style.left=parseInt(newX+app2_dragElements[t].offsetX)+"px";
		}
		if (app2_unlockYmode) {
			app2_dragElements[t].top=parseInt(newY+app2_dragElements[t].offsetY);
			app2_dragElements[t].obj.style.top=parseInt(newY+app2_dragElements[t].offsetY)+"px";
		}
	}


	//Fadenkreuz (Boundingbox) und Position (in px) anzeigen (Boundingbox)
	var bBox=app2_selectionBoundingBox();
	document.getElementById(app2_winId+"-position").innerHTML=Math.round(bBox.x)+" / "+Math.round(bBox.y)+"";
	app2_CrosshairX.style.height=bBox.height+"px";
	app2_CrosshairY.style.width=bBox.width+"px";
	app2_CrosshairY.style.left=bBox.x+"px";
	app2_CrosshairY.style.height=visuPage.scrollHeight+"px";
	app2_CrosshairX.style.top=bBox.y+"px";
	app2_CrosshairX.style.width=visuPage.scrollWidth+"px";

	app2_groupBoundingBoxes();
	app2_drawLinks();
}

function app2_elementDragEnd(event,keyMode,keySave) {
	var event=window.event;
	if (app2_dragMoved && (!keyMode || keySave)) {
		//Positionen speichern
		var data="";
		for (var t=1; t<app2_dragElements.length; t++) {	//t=1 (ohne Boundingbox!)
			data+=app2_dragElements[t].obj.dataset.elementid+",";
			data+=parseInt(app2_dragElements[t].left-visuElement_getOffset(app2_dragElements[t].obj,0))+",";
			data+=parseInt(app2_dragElements[t].top-visuElement_getOffset(app2_dragElements[t].obj,1))+";";
		}
		ajax("saveElementsPosition",2,app2_winId,app2_pageId,data);
		app2_groupBoundingBoxes();
	}

	if (!keyMode) {
		app2_restoreState();
	}

	//Aufräumen
	app2_dragElements.length=0;

	if (!keyMode) {
		window.removeEventListener("mousemove",app2_elementDragMove,false);
		window.removeEventListener("mouseup",app2_elementDragEnd,false);
	}
	
	app2_CrosshairX.parentNode.removeChild(app2_CrosshairX);
	app2_CrosshairY.parentNode.removeChild(app2_CrosshairY);
}



//---------------------------------------------
//Element/Gruppe angeklickt
//---------------------------------------------
function app2_elementClick(objId,menu) {
	var event=window.event;
	if (event.button==0) {app2_elementClickLeft(objId,menu);}
	if (event.button==2) {app2_elementClickRight(objId,menu);}
	clickCancel();
}

function app2_elementClickLeft(objId,menu) {
	var event=window.event;
	var element=document.getElementById(objId);

	if (event.ctrlKey) {
		//Skalieren (nicht bei Klick auf Menu-Element)
		if (menu!==true) {app2_elementDragScaleStart(objId);}

	} else {
		//Doppelklick
		if (!event.shiftKey) {
			clearTimeout(app2_dblClickTimeout);
			if (app2_dblClickObjid==objId) {
				app2_dblClickObjid="";
				controlClickLeft(objId);
				return;
			} else {
				app2_dblClickTimeout=window.setTimeout(function(){app2_dblClickObjid="";},250)
				app2_dblClickObjid=objId;
			}
		}
		
		//Verschieben/Markieren
		if (element.dataset.controltyp==0) {
			//Gruppe
			//-------------------
			if (event.shiftKey) {	//alle Elemente dieser Gruppe selektieren/deselektieren
				app2_elementSelect(objId,2);
				if (app2_elementIsSelected(objId)) {
					app2_groupSelect(objId,1);
				} else {
					app2_groupSelect(objId,0);
				}
			} else {	
				app2_elementDragStart(objId);
			}
	
		} else {
			//Element
			//-------------------
			if (event.shiftKey) {
				app2_elementSelect(objId,2);
			} else {
				app2_elementDragStart(objId);
			}
	
		}
	
	}

}

function app2_elementClickRight(objId) {
	var element=document.getElementById(objId);
	var selectedCount=app2_elementCountSelected();

	apps_contextMenu=new class_contextMenu(app2_winId);
	if (element.dataset.controltyp==0) {
		//Gruppe
		apps_contextMenu.addItem("Gruppe bearbeiten","controlClickLeft('"+objId+"');");
		apps_contextMenu.addItem("Gruppe auflösen","app2_uncreateGroup('"+objId+"');");
		apps_contextMenu.addItem("Gruppe duplizieren","app2_elementGroupDuplicate('"+objId+"');");
		apps_contextMenu.addItem("Gruppe löschen","jsConfirm('Soll diese Gruppe (einschließlich Inhalt) wirklich gelöscht werden?','app2_elementGroupDelete($"+objId+"$);','','Löschen');");
	} else {
		//Element
		apps_contextMenu.addItem("Visuelement bearbeiten","controlClickLeft('"+objId+"');");

		if (app2_elementIsSelected(objId)) {
			apps_contextMenu.addText("Visuelement verbinden",1);
			apps_contextMenu.addVr();
			apps_contextMenu.addItem("Trennen","app2_elementUnlink('"+objId+"');",1);
		} else {
			if (selectedCount==1) {
				var tmp=app2_elementGetFirstSelected();
				if (tmp) {
					apps_contextMenu.addItem("Visuelement verbinden mit <span class='id'>"+tmp.dataset.elementid+"</span>","app2_elementLink('"+objId+"');",1);
				} else {
					apps_contextMenu.addText("Visuelement verbinden",1);
				}
				apps_contextMenu.addVr();
				apps_contextMenu.addItem("Trennen","app2_elementUnlink('"+objId+"');",1);
			} else {
				apps_contextMenu.addText("Visuelement verbinden",1);
				apps_contextMenu.addVr();
				apps_contextMenu.addItem("Trennen","app2_elementUnlink('"+objId+"');",1);
			}
		}

		apps_contextMenu.addItem("Visuelement duplizieren","app2_elementDuplicate('"+objId+"');");
		apps_contextMenu.addItem("Visuelement löschen","jsConfirm('Soll dieses Visuelement wirklich gelöscht werden?','app2_elementDelete($"+objId+"$);','','Löschen');");
	}
	app2_contextMenuSelection();
	apps_contextMenu.show();
}

function app2_contextMenuSelection() {
	var selectedCount=app2_elementCountSelected();
	var bufferCount=document.getElementById(app2_winId+"-global").dataset.copybuffer.split(";").length-1;
	apps_contextMenu.addHr();
	apps_contextMenu.addItem("Alles auswählen","app2_elementSelectAll();");
	if (selectedCount>0) {
		apps_contextMenu.addItem("Auswahl umkehren","app2_elementSelectInvert();");
		apps_contextMenu.addItem("Auswahl aufheben","app2_elementSelectNone();");
		apps_contextMenu.addHr();
		apps_contextMenu.addText("Ausgewählte Visuelemente ("+selectedCount+"):");
		apps_contextMenu.addItem("&gt; Merken","app2_elementSelectedToBuffer();");
		apps_contextMenu.addItem("&gt; Bearbeiten","app2_elementBulkeditSelected(1);");
		apps_contextMenu.addItem("&gt; Gruppieren","app2_createGroup();");

		apps_contextMenu.addItem("&gt; Schützen","app2_elementLayerSelected(1);",1);
		apps_contextMenu.addVr();
		apps_contextMenu.addItem("Freigeben","app2_elementLayerSelected(0);",1);

		apps_contextMenu.addItem("&gt; Duplizieren","app2_elementDuplicateSelected();");
		apps_contextMenu.addItem("&gt; Löschen","jsConfirm('Sollen wirklich alle markierten Visuelemente ("+selectedCount+") gelöscht werden?','app2_elementDeleteSelected();','','Löschen');");
	}
	if (bufferCount>0) {
		apps_contextMenu.addHr();
		apps_contextMenu.addText("Gemerkte Visuelemente ("+bufferCount+"):");
		apps_contextMenu.addItem("&gt; Auf diese Seite duplizieren","app2_elementPasteBuffered(0);");
		apps_contextMenu.addItem("&gt; Auf diese Seite verschieben","app2_elementPasteBuffered(1);");
	}
}


//---------------------------------------------
//globale GUI-Events
//---------------------------------------------
function app2_itemPageClick() {
	var event=window.event;
	if (event.button==0) {
		if (event.shiftKey) {
			app2_elementSelectRect(2);
		} else {
			app2_elementSelectNone();
			app2_elementSelectRect(2);
		}
	}
	if (event.button==2) {
		if (app2_pageId>0) {
			var pos=app2_getMousePosition();
			apps_contextMenu=new class_contextMenu(app2_winId);
			apps_contextMenu.addItem("Visuelement hinzufügen","app2_pickElementAtCursor("+pos.x+","+pos.y+");");
			app2_contextMenuSelection();
			apps_contextMenu.show();
		}
	}
}

function app2_pickElementAtCursor(x,y) {
	var rasterX=1;
	var rasterY=1;
	if (app2_gridXmode) {rasterX=parseInt(document.getElementById(app2_winId+"-rasterx").value);}
	if (app2_gridYmode) {rasterY=parseInt(document.getElementById(app2_winId+"-rastery").value);}
	app2_curPosX=Math.round(x/rasterX)*rasterX;
	app2_curPosY=Math.round(y/rasterY)*rasterY;
	controlClickLeft(app2_winId+"-fd3");
}

function app2_itemPageMouseMove() {
	var event=window.event;
	if (app2_elementSelectRectData.mode>0) {
		app2_elementSelectRect(1);
	}
}

function app2_itemPageUnclick() {
	if (app2_elementSelectRectData.mode>0) {app2_elementSelectRect(0);}
}

function app2_getMousePosition() {
	var event=window.event;
	var objWindowContainer=document.getElementById("windowContainer");
	var objWindow=document.getElementById(app2_winId);
	var objPageContainer=document.getElementById(app2_winId+"-pagecontainer");
	var objPage=document.getElementById(app2_winId+"-page");
	var x=(event.pageX-parseInt(objPageContainer.offsetLeft)-parseInt(objWindow.offsetLeft)-parseInt(objWindowContainer.offsetLeft))*(1/app2_zoomFaktor)+parseInt(objPage.scrollLeft);
	var y=(event.pageY-parseInt(objPageContainer.offsetTop)-parseInt(objWindow.offsetTop)-parseInt(objWindowContainer.offsetTop))*(1/app2_zoomFaktor)+parseInt(objPage.scrollTop);
	x-=5;	//5px abziehen für "visupage"-Border
	y-=5;	//5px abziehen für "visupage"-Border
	return {x,y};
}

function app2_getElementMousePosition(obj) {
	//Mausposition relativ zum (bzw. "im") Element

	//absolute Position von visupage ermitteln (als Offset für getBoundingClientRect() notwendig)
	var tmp=document.getElementById(app2_winId+"-visupage").getBoundingClientRect();
	var offsetX=tmp.left*(1/app2_zoomFaktor)+5;	//5 Pixel für Border
	var offsetY=tmp.top*(1/app2_zoomFaktor)+5;	//5 Pixel für Border

	//Position des Elements relativ zu Visupage
	var tmp=obj.getBoundingClientRect();
	var x=(tmp.left*(1/app2_zoomFaktor))-offsetX;
	var y=(tmp.top*(1/app2_zoomFaktor))-offsetY;
	var w=tmp.width*(1/app2_zoomFaktor);
	var h=tmp.height*(1/app2_zoomFaktor);

	var mouse=app2_getMousePosition();
	return {
		x:Math.round(mouse.x-x),
		y:Math.round(mouse.y-y),
		w:w,
		h:h
	};
}


/*
============================================================================
Callbacks
============================================================================
*/

function app2_pickPage_callback(senderId) {
	var newPageId=document.getElementById(senderId).dataset.value;
	if (newPageId>0) {
		app2_refreshAll(newPageId);
		//Zum Ursprung scrollen
		document.getElementById(app2_winId+"-page").scrollTop=0;
		document.getElementById(app2_winId+"-page").scrollLeft=0;
	}
}

function app2_pickElement_callback(senderId) {
	var newElementId=document.getElementById(senderId).dataset.value;
	if (newElementId>0) {
		ajax("newVisuElement",2,app2_winId,app2_pageId,newElementId+AJAX_SEPARATOR1+app2_curPosX+AJAX_SEPARATOR1+app2_curPosY);
	}
	app2_curPosX=0;
	app2_curPosY=0;
}

function app2_editElement_callback(senderId) {
	if (document.getElementById(senderId).dataset.value>0) {
		app2_refreshAll(0);
	}
}


/*
============================================================================
Funktionalität
============================================================================
*/

function app2_newDiv(objParent,id) {
	//erzeugt neues Div und hängt es an parent
	//objParent: Parent-Objekt
	//id: gewünschte ID des neuen DIVs
	var div=document.createElement('div');
	objParent.appendChild(div);
	div.style.position="absolute";
	div.id=id;
	return div;
}

function app2_elementSelectRect(mode) {
	var visuPage=document.getElementById(app2_winId+"-visupage");
	if (visuPage) {
		var pos=app2_getMousePosition();
		if (mode==2) {
			app2_elementSelectRectData.mode=2;
			app2_elementSelectRectData.x1=pos.x;
			app2_elementSelectRectData.y1=pos.y;
			app2_elementSelectRectData.x2=pos.x;
			app2_elementSelectRectData.y2=pos.y;
			render();
		} else if (mode==1) {
			app2_elementSelectRectData.mode=1;
			app2_elementSelectRectData.x2=pos.x;
			app2_elementSelectRectData.y2=pos.y;
			getHits();
			render();			
		} else if (mode==0) {
			app2_elementSelectRectData.mode=0;
			getHits();
			render();
		}
	}

	function getHits() {
		var visuPage=document.getElementById(app2_winId+"-visupage");
		if (visuPage) {
			//absolute Position von visupage ermitteln
			var tmp=visuPage.getBoundingClientRect();
			var offsetX=tmp.left*(1/app2_zoomFaktor)+5;	//5 Pixel für Border
			var offsetY=tmp.top*(1/app2_zoomFaktor)+5;	//5 Pixel für Border
	
			//Box
			var x1 = Math.min(app2_elementSelectRectData.x1,app2_elementSelectRectData.x2);
			var x2 = Math.max(app2_elementSelectRectData.x1,app2_elementSelectRectData.x2);
			var y1 = Math.min(app2_elementSelectRectData.y1,app2_elementSelectRectData.y2);
			var y2 = Math.max(app2_elementSelectRectData.y1,app2_elementSelectRectData.y2);
	
			var elements=visuPage.querySelectorAll("[data-elementselected='0']");
			for (var t=0; t<elements.length; t++) {
				var tmp=elements[t].getBoundingClientRect();
				var x=(tmp.left*(1/app2_zoomFaktor))-offsetX;
				var y=(tmp.top*(1/app2_zoomFaktor))-offsetY;
				var w=tmp.width*(1/app2_zoomFaktor);
				var h=tmp.height*(1/app2_zoomFaktor);
	
				if ((x>=x1 && x<=x2 && y>=y1 && y<=y2) && ((x+w)>=x1 && (x+w)<=x2 && (y+h)>=y1 && (y+h)<=y2)) {
					if (app2_elementSelectRectData.mode==0) {
						app2_elementSelect(elements[t].id,1); 
					} else if (elements[t].dataset.layer==0 || elements[t].dataset.layer==app2_layerMode) {
						elements[t].style.boxShadow=app2_el_shadowSelected;
						document.getElementById(elements[t].id+"-list").style.background=apps_colorSelected;
					}
				} else {
					elements[t].style.boxShadow=elements[t].dataset.shadowbuffer;
					document.getElementById(elements[t].id+"-list").style.background="";
				}
	
			}
		}
	}

	function render() {
		var visuPage=document.getElementById(app2_winId+"-visupage");
		if (visuPage) {
			if (app2_elementSelectRectData.mode>0) {
				var tmp=document.getElementById(app2_winId+"-selectrect");
				if (!tmp) {
					tmp=app2_newDiv(visuPage,app2_winId+"-selectrect");
					tmp.className="app2_elementSelectRect";
				}		
				var x1 = Math.min(app2_elementSelectRectData.x1,app2_elementSelectRectData.x2);
				var x2 = Math.max(app2_elementSelectRectData.x1,app2_elementSelectRectData.x2);
				var y1 = Math.min(app2_elementSelectRectData.y1,app2_elementSelectRectData.y2);
				var y2 = Math.max(app2_elementSelectRectData.y1,app2_elementSelectRectData.y2);
				tmp.style.left=x1+"px";
				tmp.style.top=y1+"px";
				tmp.style.width=(x2-x1)+"px";
				tmp.style.height=(y2-y1)+"px";
			} else {
				var tmp=document.getElementById(app2_winId+"-selectrect");
				if (tmp) {tmp.parentNode.removeChild(tmp);}
			}
		}
	}
}

function app2_elementSelect(objId,mode) {
	var element=document.getElementById(objId);
	if (element) {
		if (element.dataset.layer==0 || element.dataset.layer==app2_layerMode) {
			if (mode==2) {
				if (element.dataset.elementselected==0) {mode=1;} else {mode=0;}
			}
			if (mode==1) {
				element.style.boxShadow=app2_el_shadowSelected;
				document.getElementById(objId+"-list").style.background=apps_colorSelected;
				element.dataset.elementselected=1;
			} else if (mode==0) {
				element.style.boxShadow=element.dataset.shadowbuffer;
				document.getElementById(objId+"-list").style.background="";
				element.dataset.elementselected=0;
			}
		}
	}
}

function app2_elementSelectAll() {
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='0']");
		for (var t=0; t<element.length; t++) {
			app2_elementSelect(element[t].id,1);
		}
	}
}

function app2_elementSelectInvert() {
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected]");
		for (var t=0; t<element.length; t++) {
			if (element[t].dataset.elementselected==1) {
				app2_elementSelect(element[t].id,0);
			} else {
				app2_elementSelect(element[t].id,1);
			}
		}
	}
}

function app2_elementSelectNone() {
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {
			app2_elementSelect(element[t].id,0);
		}
	}
}

function app2_elementIsSelected(objId) {
	var element=document.getElementById(objId);
	if (element) {
		if (element.dataset.elementselected==1) {return true;}
		if (element.dataset.elementselected==0) {return false;}
	}
}

function app2_elementCountSelected() {
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		return element.length;
	}
	return 0;
}

function app2_groupSelect(objId,mode) {
	var element=document.getElementById(objId);
	if (element) {
		if (element.dataset.controltyp=="0") {
			var elements=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-groupid='"+element.dataset.elementid+"']");
			for (var t=0; t<elements.length; t++) {
				app2_elementSelect(elements[t].id,mode);
			}
		}
	}
}

function app2_elementGetFirstSelected() {
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		if (element.length>0) {return element[0];}
	}
	return false;
}


function app2_elementAddBorder(obj,enabled) {
	if (enabled) {
		if (obj.dataset.layer==0 || obj.dataset.layer==app2_layerMode) {var color=app2_elementOutlineColor+" solid";} else {var color=app2_elementOutlineColor+" dotted";}
	} else {
		var color=app2_elementOutlineColor+" dotted";
	}
	if (!app2_previewMode) {
		obj.style.outline=color+" 1px";
		obj.style.outlineOffset="-1px";
	}
}

function app2_groupAddBorder(obj,enabled) {
	if (enabled) {
		if (obj.dataset.layer==0 || obj.dataset.layer==app2_layerMode) {var color=app2_elementOutlineColor+" dashed";} else {var color=app2_elementOutlineColor+" dotted";}
	} else {
		var color=app2_elementOutlineColor+" dotted";
	}
	if (!app2_previewMode) {
		obj.style.outline=color+" 1px";
		obj.style.outlineOffset="-1px";
	}
}

function app2_groupBoundingBoxes() {
	//Boundingboxen aller Gruppen ermitteln und anzeigen

	var xpos,ypos,xsize,ysize;

	//absolute Position von visupage ermitteln (als Offset für getBoundingClientRect() notwendig)
	var tmp=document.getElementById(app2_winId+"-visupage").getBoundingClientRect();
	var offsetX=tmp.left*(1/app2_zoomFaktor)+5;	//5 Pixel für Border
	var offsetY=tmp.top*(1/app2_zoomFaktor)+5;	//5 Pixel für Border

	//Gruppen suchen
	var group=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-controltyp='0']");
	for (var t=0; t<group.length; t++) {

		var x1=null;
		var y1=null;
		var x2=0;
		var y2=0;
		
		//Elemente der Gruppe suchen
		var item=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-groupid='"+group[t].dataset.elementid+"']");
		for (var tt=0; tt<item.length; tt++) {
			
			//min/max... und speichern! (dataset) => Dragging...
			xpos=parseInt(item[tt].style.left.replace("px",""));
			ypos=parseInt(item[tt].style.top.replace("px",""));
			xsize=parseInt(item[tt].offsetWidth);
			ysize=parseInt(item[tt].offsetHeight);

			tmp=item[tt].getBoundingClientRect();
			xpos=(tmp.left*(1/app2_zoomFaktor))-offsetX;
			ypos=(tmp.top*(1/app2_zoomFaktor))-offsetY;
			xsize=tmp.width*(1/app2_zoomFaktor);
			ysize=tmp.height*(1/app2_zoomFaktor);
			
			if (xpos<x1 || x1==null) {x1=xpos;}
			if (ypos<y1 || y1==null) {y1=ypos;}
			if ((xpos+xsize)>x2) {x2=xpos+xsize;}
			if ((ypos+ysize)>y2) {y2=ypos+ysize;}
			
		}

		if (item.length==0) {	//leere Gruppe
			group[t].style.left="0px";
			group[t].style.top="0px";
			group[t].style.width="0px";
			group[t].style.height="0px";
			group[t].style.display="none";			
		} else {
			group[t].style.left=Math.round(x1)+"px";
			group[t].style.top=Math.round(y1)+"px";
			group[t].style.width=Math.round(x2-x1)+"px";
			group[t].style.height=Math.round(y2-y1)+"px";
			group[t].style.display="block";			
		}
		
	}
}

function app2_selectionBoundingBox() {
	//Boundingbox der ausgewählten Elemente berechnen

	var xpos,ypos,xsize,ysize;

	var x1=null;
	var y1=null;
	var x2=0;
	var y2=0;

	//absolute Position von visupage ermitteln (als Offset für getBoundingClientRect() notwendig)
	var tmp=document.getElementById(app2_winId+"-visupage").getBoundingClientRect();
	var offsetX=tmp.left*(1/app2_zoomFaktor)+5;	//5 Pixel für Border
	var offsetY=tmp.top*(1/app2_zoomFaktor)+5;	//5 Pixel für Border

	var elements=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<elements.length; t++) {
		if (elements[t].dataset.controltyp!=0) { //keine Gruppenelemente (Gruppe könnte leer sein - dann stimmt die Position ja nicht)
			//min/max... und speichern! (dataset) => Dragging...
			xpos=parseInt(elements[t].style.left.replace("px",""));
			ypos=parseInt(elements[t].style.top.replace("px",""));
			xsize=parseInt(elements[t].offsetWidth);
			ysize=parseInt(elements[t].offsetHeight);

			tmp=elements[t].getBoundingClientRect();
			xpos=(tmp.left*(1/app2_zoomFaktor))-offsetX;
			ypos=(tmp.top*(1/app2_zoomFaktor))-offsetY;
			xsize=tmp.width*(1/app2_zoomFaktor);
			ysize=tmp.height*(1/app2_zoomFaktor);

			if (xpos<x1 || x1==null) {x1=xpos;}
			if (ypos<y1 || y1==null) {y1=ypos;}
			if ((xpos+xsize)>x2) {x2=xpos+xsize;}
			if ((ypos+ysize)>y2) {y2=ypos+ysize;}
		}
	}

	return {
		x:Math.round(x1),
		y:Math.round(y1),
		width:Math.round(x2-x1),
		height:Math.round(y2-y1)
	};
		
}

//---------------------------------------------
//diverse Funktionen
//---------------------------------------------

function app2_elementLink(objId) {
	var obj=document.getElementById(objId);
	var obj2=app2_elementGetFirstSelected();
	if (obj && obj2) {
		ajax("linkElement",2,app2_winId,app2_pageId,obj.dataset.elementid+";"+obj2.dataset.elementid);
	}
}

function app2_elementUnlink(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		ajax("unlinkElements",2,app2_winId,app2_pageId,obj.dataset.elementid);
	}
}

function app2_elementDuplicate(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		ajax("pasteElements",2,app2_winId,app2_pageId,obj.dataset.elementid);
	}
}
function app2_elementDelete(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		ajax("deleteElements",2,app2_winId,app2_pageId,obj.dataset.elementid);
	}
}
function app2_elementGroupDuplicate(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		data=obj.dataset.elementid+";";
		var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-groupid='"+obj.dataset.elementid+"']");
		for (var t=0; t<element.length; t++) {
			if (element[t].dataset.layer==0 || element[t].dataset.layer==app2_layerMode) {
				data+=element[t].dataset.elementid+";";
			}
		}
		if (data!="") {ajax("pasteElements",2,app2_winId,app2_pageId,data);}
	}
}
function app2_elementGroupDelete(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		data=obj.dataset.elementid+";";
		var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-groupid='"+obj.dataset.elementid+"']");
		for (var t=0; t<element.length; t++) {
			if (element[t].dataset.layer==0 || element[t].dataset.layer==app2_layerMode) {
				data+=element[t].dataset.elementid+";";
			}
		}
		if (data!="") {ajax("deleteElements",2,app2_winId,app2_pageId,data);}
	}
}

function app2_createGroup() {
	var data="";
	var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-elementselected='1']");
	if (element.length>0) {	//mindestens 1 Elemente selektiert?
		for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
		if (data!="") {
			ajax("createGroup",2,app2_winId,app2_pageId,data);
		}
	}
}

function app2_uncreateGroup(objId) {
	var element=document.getElementById(objId);
	if (element.dataset.controltyp==0) {
		ajax("uncreateGroup",2,app2_winId,app2_pageId,element.dataset.elementid);
	}
}

function app2_groupCollapse(objId,mode) {
	var element=document.getElementById(objId);
	if (element) {
		if (mode==2) {
			if (element.dataset.groupexpanded==0) {mode=1;} else {mode=0;}
		}
		if (mode==1) {
			element.style.display="block";
			document.getElementById(objId+"-img").src="../shared/img/folder1b.png";
			element.dataset.groupexpanded=1;
		} else if (mode==0) {
			element.style.display="none";
			document.getElementById(objId+"-img").src="../shared/img/folder1.png";
			element.dataset.groupexpanded=0;
		}
	}
}

function app2_moveElementsByKeyboard() {
	if (app2_pageId>0 && app2_elementCountSelected()>0) {
		app2_elementsKeyboardMoveWinId=openWindow(1013,AJAX_SEPARATOR1+"1013"+AJAX_SEPARATOR1+"-1"+AJAX_SEPARATOR1+AJAX_SEPARATOR1+app2_pageId);
		app2_elementDragStart(null,true);
	}
}

function app2_moveElementsByKeyboardKeyEvent() {
	var event=window.event;
	if (event.keyCode==37) {app2_elementDragMove(null,true,-1,0);} 	//Cursor links
	if (event.keyCode==38) {app2_elementDragMove(null,true,0,-1);} 	//Cursor hoch
	if (event.keyCode==39) {app2_elementDragMove(null,true,1,0);} 	//Cursor rechts
	if (event.keyCode==40) {app2_elementDragMove(null,true,0,1);} 	//Cursor runter
	if (event.keyCode==27) {app2_moveElementsByKeyboardCancel(app2_elementsKeyboardMoveWinId);}	//ESC
	if (event.keyCode==13) {app2_moveElementsByKeyboardSave(app2_elementsKeyboardMoveWinId);} 	//Enter
	event.cancelBubble=true;
	if (event.stopPropagation) {event.stopPropagation();}
	event.preventDefault();
	return false;
}

function app2_moveElementsByKeyboardCancel(winId) {
	app2_elementDragEnd(null,true,false);
	window.removeEventListener("keydown",app2_moveElementsByKeyboardKeyEvent,false);
	closeWindow(winId);
	app2_refreshAll(0);
}

function app2_moveElementsByKeyboardSave(winId) {
	app2_elementDragEnd(null,true,true);
	window.removeEventListener("keydown",app2_moveElementsByKeyboardKeyEvent,false);
	closeWindow(winId);
}

function app2_newElement(x,y) {
	if (app2_pageId>0) {
		openWindow(1002,AJAX_SEPARATOR1+"1002"+AJAX_SEPARATOR1+"-1"+AJAX_SEPARATOR1+AJAX_SEPARATOR1+app2_pageId+AJAX_SEPARATOR1+x+AJAX_SEPARATOR1+y);
	}
}

function app2_setLayerMode() {
	//(aktuell nur toggeln zw. 0 und 1)
	if (app2_layerMode==0) {
		app2_layerMode=1;
		document.getElementById(app2_winId+"-layer").style.background="#80e000";
		document.getElementById(app2_winId+"-layer").innerHTML="<img src='../shared/img/lock1b.png' width='16' height='16' valign='middle' style='margin:0; padding-left:2px;' draggable='false'>";
	} else {
		app2_layerMode=0;
		document.getElementById(app2_winId+"-layer").style.background="";
		document.getElementById(app2_winId+"-layer").innerHTML="<img src='../shared/img/lock1.png' width='16' height='16' valign='middle' style='margin:0; padding-left:2px;' draggable='false'>";
	}
	app2_refreshAll(0);
}

function app2_previewModeToggle() {
	if (app2_previewMode) {
		app2_previewMode=false;
		document.getElementById(app2_winId+"-preview").style.background="";
	} else {
		app2_previewMode=true;
		document.getElementById(app2_winId+"-preview").style.background="#80e000";
	}
	app2_refreshAll(0);
}

function app2_previewChangeValue(obj) {
	app2_previewValue=stringCleanup(obj.value);
	if (app2_previewMode) {
		app2_refreshAll(0);
	}
}
function app2_previewChangeValue3(obj) {
	app2_previewValue3=stringCleanup(obj.value);
	if (app2_previewMode) {
		app2_refreshAll(0);
	}
}

function app2_gridXtoggle() {
	if (app2_gridXmode) {
		app2_gridXmode=false;
		document.getElementById(app2_winId+"-rasterx").style.color="#a0a0a0";
		document.getElementById(app2_winId+"-rasterx").style.background="";
	} else {
		app2_gridXmode=true;
		document.getElementById(app2_winId+"-rasterx").style.color="#000000";
		document.getElementById(app2_winId+"-rasterx").style.background="#80e000";
	}
}

function app2_gridYtoggle() {
	if (app2_gridYmode) {
		app2_gridYmode=false;
		document.getElementById(app2_winId+"-rastery").style.color="#a0a0a0";
		document.getElementById(app2_winId+"-rastery").style.background="";
	} else {
		app2_gridYmode=true;
		document.getElementById(app2_winId+"-rastery").style.color="#000000";
		document.getElementById(app2_winId+"-rastery").style.background="#80e000";
	}
}

function app2_unlockXtoggle() {
	if (app2_unlockXmode) {
		app2_unlockXmode=false;
		document.getElementById(app2_winId+"-unlockx").style.color="#a0a0a0";
		document.getElementById(app2_winId+"-unlockx").style.background="";
	} else {
		app2_unlockXmode=true;
		document.getElementById(app2_winId+"-unlockx").style.color="#000000";
		document.getElementById(app2_winId+"-unlockx").style.background="#80e000";
	}
}

function app2_unlockYtoggle() {
	if (app2_unlockYmode) {
		app2_unlockYmode=false;
		document.getElementById(app2_winId+"-unlocky").style.color="#a0a0a0";
		document.getElementById(app2_winId+"-unlocky").style.background="";
	} else {
		app2_unlockYmode=true;
		document.getElementById(app2_winId+"-unlocky").style.color="#000000";
		document.getElementById(app2_winId+"-unlocky").style.background="#80e000";
	}
}

function app2_pageboundToggle() {
	if (app2_pageboundMode) {
		app2_pageboundMode=false;
		document.getElementById(app2_winId+"-pagebound").style.color="#a0a0a0";
		document.getElementById(app2_winId+"-pagebound").style.background="";
	} else {
		app2_pageboundMode=true;
		document.getElementById(app2_winId+"-pagebound").style.color="#000000";
		document.getElementById(app2_winId+"-pagebound").style.background="#80e000";
	}
}


//---------------------------------------------
//Selektion und Copy/Paste
//---------------------------------------------

function app2_elementLayerSelected(mode) {
	//markierte Objekte: layer zuweisen
	var data="";
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
		if (data!="") {
			ajax("layerElements",2,app2_winId,app2_pageId,data+AJAX_SEPARATOR1+mode);
		}
	}
}

function app2_elementDuplicateSelected() {
	//markierte Elemente auf aktueller Seite duplizieren
	var data="";
	var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
	if (data!="") {ajax("pasteElements",2,app2_winId,app2_pageId,data);}
}

function app2_elementBulkeditSelected(mode) {
	//Bulk-Bearbeitung aller markierten Objekte
	var data="";
	var page=document.getElementById(app2_winId+"-visupage");
	if (page) {
		var element=page.querySelectorAll("[data-elementselected='1']");
		for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
		if (data!="") {openWindow(1004,app2_winId+AJAX_SEPARATOR1+app2_pageId,data);}
	}
}

function app2_elementDeleteSelected() {
	//markierte Elemente löschen
	var data="";
	var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
	if (data!="") {
		ajax("deleteElements",2,app2_winId,app2_pageId,data);
	}
}

function app2_elementSelectedToBuffer() {
	//markierte Elemente merken
	var data="";
	var element=document.getElementById(app2_winId+"-visupage").querySelectorAll("[data-elementselected='1']");
	for (var t=0; t<element.length; t++) {data+=element[t].dataset.elementid+";";}
	document.getElementById(app2_winId+"-global").dataset.copybuffer=data;
}

function app2_elementPasteBuffered(mode) {
	//gemerkte Elemente auf aktuelle Seite einfügen
	var data=document.getElementById(app2_winId+"-global").dataset.copybuffer;
	if (data!="") {
		if (mode==1) {
			ajax("moveElements",2,app2_winId,app2_pageId,data);
		} else {
			ajax("pasteElements",2,app2_winId,app2_pageId,data);
		}
	}
	document.getElementById(app2_winId+"-global").dataset.copybuffer="";
}


/*
============================================================================
Visuelement erstellen
============================================================================
*/

function app2_addLink(elementId,linkId) {
	app2_links.push([elementId,linkId,newLine()]);
	
	function newLine() {
		var line=document.createElement('div');
		document.getElementById(app2_winId+"-page").appendChild(line);
		line.style.position="absolute";
		line.style.left="0px";
		line.style.width="0px";
		line.style.zIndex=999999999;
		line.style.pointerEvents="none";
		line.style.display="none";
		line.style.height=app2_linkLineWidth+"px";
		line.style.background=app2_elementOutlineColor;
		line.style.webkitTransformOrigin="0px "+(app2_linkLineWidth/2)+"px";

		var circle=document.createElement('div');
		line.appendChild(circle);
		circle.style.position="absolute";
		circle.style.left="-"+(app2_linkLineWidth*4)+"px";
		circle.style.top="-"+(app2_linkLineWidth*4-app2_linkLineWidth/2)+"px";
		circle.style.zIndex=999999999;
		circle.style.pointerEvents="none";
		circle.style.width=(app2_linkLineWidth*8)+"px";
		circle.style.height=(app2_linkLineWidth*8)+"px";
		circle.style.borderRadius="100%";
		circle.style.borderWidth=app2_linkLineWidth+"px";
		circle.style.borderStyle="solid";
		circle.style.borderColor=app2_elementOutlineColor;
		circle.style.boxSizing="border-box";
		return line;
	}
}

function app2_drawLinks() {
	if (app2_previewMode) {
		for (var t=0;t<app2_links.length;t++) {
			app2_links[t][2].style.display="none";
		}
	} else {
		for (var t=0;t<app2_links.length;t++) {
			var obj1=document.getElementById(app2_winId+"-element-"+app2_links[t][0]);
			var obj2=document.getElementById(app2_winId+"-element-"+app2_links[t][1]);
			if (obj1 && obj2) {
				//Zentrum, +5 Pixel für Border
				var obj1X=parseFloat(obj1.offsetLeft+obj1.offsetWidth/2+5);
				var obj1Y=parseFloat(obj1.offsetTop+obj1.offsetHeight/2+5-app2_linkLineWidth/2);
				var obj2X=parseFloat(obj2.offsetLeft+obj2.offsetWidth/2+5);
				var obj2Y=parseFloat(obj2.offsetTop+obj2.offsetHeight/2+5-app2_linkLineWidth/2);
		
				var length=(Math.sqrt((obj1X-obj2X)*(obj1X-obj2X)+(obj1Y-obj2Y)*(obj1Y-obj2Y)));
				var angle=Math.atan2(obj2Y-obj1Y,obj2X-obj1X)*180/Math.PI;
		
				app2_links[t][2].style.left=obj1X+"px";
				app2_links[t][2].style.top=obj1Y+"px";
				app2_links[t][2].style.width=length+"px";
				app2_links[t][2].style.webkitTransform="rotate("+angle+"deg)";
				app2_links[t][2].style.display="block";
			}
		}
	}
}

function class_app2_VE() {

	var that=this;
	this.element;
	this.item;

	var visupage=document.getElementById(app2_winId+"-visupage");
	var menuItem=null;
	var menuItemSuffix="";

	this.addGroup=function() {
		if (that.element.pageid==app2_pageId) {	//Element der aktuellen Seite
			//Visuelement (Gruppenelement)
			that.item=app2_newDiv(visupage,app2_winId+"-element-"+that.element.id);
			that.item.dataset.elementid=that.element.id;
			that.item.dataset.controltyp=that.element.controltyp;
			that.item.dataset.groupid="0";
			that.item.dataset.shadowbuffer="none";
			that.item.dataset.elementselected=0;		
			that.item.dataset.layer=that.element.layer;
			if (that.element.layer==0 || that.element.layer==app2_layerMode) {
				that.item.className="app2_element";
				that.item.setAttribute("onMouseDown","app2_elementClick('"+that.item.id+"');");
				//Control 1002
				that.item.dataset.type=1002;
				that.item.dataset.value=that.element.id;
				that.item.dataset.callback="app2_editElement_callback('"+that.item.id+"');";
			} else {
				that.item.style.cursor="not-allowed";
			}	
			app2_groupAddBorder(that.item,true);
	
			//Menuelement (Gruppenelement)
			menuItem=createNewDiv(app2_winId+"-menu",app2_winId+"-element-"+that.element.id+"-list");
			menuItem.className="controlListItem";
			menuItem.style.display="block";
			menuItem.style.color="#0000ff";
			menuItem.innerHTML="<img id='"+app2_winId+"-element-"+that.element.id+"-group-img' src='../shared/img/folder1.png' width='12' height='12' onClick='app2_groupCollapse(\""+app2_winId+"-element-"+that.element.id+"-group\",2);' valign='middle' style='margin:0; padding-left:2px;' draggable='false'> <b>"+that.element.name+"</b> <span class='id' style='background:#8080ff;'>"+that.element.id+"</span>";		
			if (that.element.layer==0 || that.element.layer==app2_layerMode) {
				menuItem.setAttribute("onMouseDown","app2_elementClick('"+app2_winId+"-element-"+that.element.id+"',true);");
			} else {
				menuItem.style.opacity="0.5";
				menuItem.style.cursor="not-allowed";
			}
		
			//Menuelement (Container für Elemente dieser Gruppe)
			var groupItem=createNewDiv(app2_winId+"-menu",app2_winId+"-element-"+that.element.id+"-group");
			groupItem.style.display="none";
			groupItem.dataset.groupexpanded=0;
		}
	}
	
	this.addElement=function(itemCss,itemText,property,vars) {
		if (that.element.pageid==app2_pageId) {
			//Element der aktuellen Seite
			that.item=app2_newDiv(visupage,app2_winId+"-element-"+that.element.id);
			that.item.style.cssText=itemCss;
			that.item.dataset.elementid=that.element.id;
			that.item.dataset.controltyp=that.element.controltyp;
			that.item.dataset.groupid=that.element.groupid;
			that.item.dataset.shadowbuffer="none";
			that.item.dataset.elementselected=0;
			that.item.dataset.layer=that.element.layer;
			if (that.element.layer==0 || that.element.layer==app2_layerMode) {
				that.item.setAttribute("onMouseDown","app2_elementClick('"+that.item.id+"');");
				that.item.className="app2_element";
				//Control 1002
				that.item.dataset.type=1002;
				that.item.dataset.value=that.element.id;
				that.item.dataset.callback="app2_editElement_callback('"+that.item.id+"');";
			} else {
				that.item.style.pointerEvents="none";
			}
			app2_elementAddBorder(that.item,true);

			//Menuelement
			if (that.element.groupid>0) {
				//Element einer Gruppe -> in den Gruppencontainer einhängen
				menuItem=createNewDiv(app2_winId+"-element-"+that.element.groupid+"-group",app2_winId+"-element-"+that.element.id+"-list");
				menuItem.className="controlListItem";
				menuItem.style.display="block";
				menuItem.style.paddingLeft="17px";
				if (that.element.layer==0 || that.element.layer==app2_layerMode) {
					menuItem.setAttribute("onMouseDown","app2_elementClick('"+that.item.id+"',true);");
				} else {
					menuItem.style.opacity="0.5";
					menuItem.style.cursor="not-allowed";
				}
			} else {
				//Element ohne Gruppe
				menuItem=createNewDiv(app2_winId+"-menu",app2_winId+"-element-"+that.element.id+"-list");
				menuItem.className="controlListItem";
				menuItem.style.display="block";
				if (that.element.layer==0 || that.element.layer==app2_layerMode) {
					menuItem.setAttribute("onMouseDown","app2_elementClick('"+that.item.id+"',true);");
				} else {
					menuItem.style.opacity="0.5";
					menuItem.style.cursor="not-allowed";
				}
			}
		} else {
			//Inkludieres Element
			that.item=app2_newDiv(visupage,app2_winId+"-element-"+that.element.id);
			that.item.style.cssText=itemCss;
			app2_elementAddBorder(that.item,false);
			that.item.style.pointerEvents="none";
		}		


		var menuItemName="<b>"+that.element.name+"</b> <span class='id'>"+that.element.id+"</span>";
		var menuControlName=that.element.defname+" <span class='id' style='background:#a0a0a0;'>"+that.element.controltyp+"</span>";

		that.item.dataset.var1=vars.var1;
		that.item.dataset.var2=vars.var2;
		that.item.dataset.var3=vars.var3;
		that.item.dataset.var4=vars.var4;
		that.item.dataset.var5=vars.var5;
		that.item.dataset.var6=vars.var6;
		that.item.dataset.var7=vars.var7;
		that.item.dataset.var8=vars.var8;
		that.item.dataset.var9=vars.var9;
		that.item.dataset.var10=vars.var10;
		that.item.dataset.var11=vars.var11;
		that.item.dataset.var12=vars.var12;
		that.item.dataset.var13=vars.var13;
		that.item.dataset.var14=vars.var14;
		that.item.dataset.var15=vars.var15;
		that.item.dataset.var16=vars.var16;
		that.item.dataset.var17=vars.var17;
		that.item.dataset.var18=vars.var18;
		that.item.dataset.var19=vars.var19;
		that.item.dataset.var20=vars.var20;
					
		if (that.element.error==1) {
			that.item.style.background="rgba(255,0,0,0.25)";
			var n="<svg style='position:absolute; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'>";	
				n+="<line x1='0' y1='0' x2='100%' y2='100%' stroke='#ff0000' stroke-width='2' vector-effect='non-scaling-stroke' fill='none'/>";
				n+="<line x1='0' y1='100%' x2='100%' y2='0' stroke='#ff0000' stroke-width='2' vector-effect='non-scaling-stroke' fill='none'/>";
			n+="</svg>";
			that.item.innerHTML=n;

			if (menuItem) {
				menuItem.innerHTML="<span class='app2_elementMenu' style='color:#ffffff; background:#ff0000;'>"+menuItemName+" &gt; "+menuControlName+"</span>";
			}

		} else {
			var n=window["VSE_"+that.element.controltyp](that.item.dataset.elementid,that.item,{itemText:itemText},property,app2_previewMode,((app2_previewMode)?app2_previewValue:""));
			if (menuItem) {
				if (n===true) {n=that.element.menutext;}
				else if (n===false || n===undefined) {n="";}
				menuItem.innerHTML=menuItemName+" &gt; "+menuControlName+((n!="")?" &gt; "+n:"")+menuItemSuffix;
			}			
		}

		that.item.dataset.shadowbuffer=that.item.style.boxShadow;
	}
	
	this.addMenuItemSuffix=function(n) {
		if (n!="") {menuItemSuffix+=" &gt; "+String(n);}
	}
}
