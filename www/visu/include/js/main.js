/* 
*/ 
/*
============================================================================
Initialisierung
============================================================================
*/

function firstinit(visuid,newSid,version) {
	//dies wird NUR EINMALIG beim allerersten Start aufgerufen (also nach dem Laden der index.php)
	
	//--- ggf. anpassen ---
	displayPixelRatio=2;			//Faktor für die virtuelle Auflösung eines CANVAS (z.B. für Retina-Displays sollte der Faktor mindestens 2 betragen)
									//benötigt für: Diagramme, Rundinstrumente

	visu_cssbuffer=true;			//true=CSS-Style von Visuelementen nur bei Änderung setzen (vermeidet u.U. flackern bei einigen Browsern)
	//---------------------

	sid=newSid;
	edomiVersion=version;

	//Mouse/Touch-Events
	clickEvent='mousedown';
	downEvent='mousedown';
	moveEvent='mousemove';
	upEvent='mouseup';
	cancelEvent=false;
	
	visu_dragEventlistener=new Array();	//Drag-Eventhandler (workaround für iOS/Android)

	visu_visuid=null;				//aktuelle VisuID (wird erst in app0.php/initVisu gesetzt)
	visu_preview=false;				//true=Vorschauaktivierung (wird erst in app0.php/initVisu gesetzt)
	visu_startPageId=null;			//Startseite
	visu_sspageid=null;				//Bildschirmschoner-Seiten-ID
	visu_sstimeout=null; 			//nach dieser Zeit (Minuten!) wird der Bilderschirmschoner aufgerufen

	visu_touchMode=false;			//true=Touchscreen

	visu_flagErrors=true;			//Warnungen/Fehler anzeigen?
	visu_flagSounds=true;			//Tonausgabe: "Aktivieren"-Button anzeigen?
	visu_longclickSize=0;			//Größe der Langclick-Animation (relativ zur VE-Größe in %)
	
	visu_sound=false;				//Audio-Objekt
	visu_soundEnabled=0;			//0=Sound wird nicht benötigt, 1=wird benötigt ist aber noch nicht initialisiert (iOS), 2=steht zu Verfügung und ist initialisiert
	visu_textToSpeech=false; 		//SpeechSynthesisUtterance-Objekt
	visu_textToSpeechEnabled=0;		//0=TextToSpeech wird nicht benötigt, 1=wird benötigt ist aber noch nicht initialisiert (iOS), 2=steht zu Verfügung und ist initialisiert

	preload_images_max=0;			//Preload-Bilder (Anzahl, nur temporär für den Preloadvorgang!)
	preload_imagesFile=new Array();	//Preload-Bilder (Dateinamen, nur temporär für den Preloadvorgang!)

	visu_socket=null;				//Websocket
	visu_interrupt={id:null,lastTs:null};	//Timer

	initVisu(visuid);
}

function initVisu(visuid) {
	//wird beim Start und Fehler (=Neustart) aufgerufen
	windowZindex=0; 				//Fenster-Verwaltung

	screensaverActive=false;		//Flag: Bildschirmschoner aktiv/inaktiv
	
	//interne Timeout-Funktion initialisieren
	visu_timer={};
	window.clearInterval(visu_interrupt.id);
	visu_interrupt={id:window.setInterval(function(){
		var ts=Date.now();
		var diff=ts-visu_interrupt.lastTs;
		for (var key in visu_timer) {
			if (visu_timer[key]!==undefined) {
				if ((ts-visu_timer[key].ts)>=visu_timer[key].timeout) {
					var tmp=visu_timer[key];
					delete visu_timer[key];
					if (tmp.ve) {
						tmp.func(tmp.veid,tmp.timerid);
					} else {
						tmp.func(tmp.id);
					}
				}
			}
		}	
		visu_interrupt.lastTs=ts;	
	},5),lastTs:Date.now()};

	element_linked={};
	element_globals={};

	drag_removeAllEventListeners();

	//Longclick
	element_longclick={veId:0,objId:null,obj:null,funcShort:false,visibleShort:false,funcLong:false,visibleLong:false,timer:null};

	//für Controls mit Drag-Funktion (Mausposition und KO-Intervall)
	element_drag={id:0,veobj:null,obj:null,interval:-1,initKovalue:false,koid:0,livekoid:0,kovalue:"",kovaluesbc:"",lastkovalue:"",koblock:0,startx:null,starty:null,valuex:null,valuey:null,lastcx:null,lastcy:null};

	visu_showWarningErr=0;			//Fehlerhinweis quittieren: Enthält die Anzahl der aktuell quittierten Fehler

	ajax(visuid,"initVisu",{visuId:visuid},null);
}

function setInputMode(touch,click,scroll) {
	if ((touch==2 || touch==4) || ((touch==0 || touch==3) && ('ontouchstart' in window))) {
		visu_touchMode=true;
		downEvent='touchstart';
		moveEvent='touchmove';
		upEvent='touchend';
		cancelEvent='touchcancel';
		clickEvent='touchstart';
	} else {
		visu_touchMode=false;
		downEvent='mousedown';
		moveEvent='mousemove';
		upEvent='mouseup';
		cancelEvent=false;
		clickEvent='mousedown';
	}

	if (click!=0) {
		clickEvent='click';
	}

	if (scroll==0) {
		document.body.style.position="fixed";
	} else if (scroll==1) {
		document.body.style.position="absolute";
	} else if (scroll==2) {
		if (!visu_touchMode) {document.body.style.position="absolute";} else {document.body.style.position="fixed";}
	} else if (scroll==3) {
		if (visu_touchMode) {document.body.style.position="absolute";} else {document.body.style.position="fixed";}
	}
	
	//Mauspfeil ggf. ausblenden
	if (visu_touchMode && (touch==3 || touch==4)) {document.body.style.cursor="none";}
}

function prepareVisu() {
	document.getElementById("waitanim").style.borderLeftColor="#80e000";
	document.getElementById("waitanim").style.borderRightColor="#80e000";

	if (visu_preview) {
		document.getElementById("preview").style.display="block";
	} else {
		document.getElementById("preview").style.display="none";
	}

	if (preload_imagesFile.length>0) {
		document.getElementById("preload").style.display="block";
		preload_images_max=preload_imagesFile.length;
		preloadImagesLoad();
	} else {
		startVisu();
	}
}

function preloadImagesLoad() {
	if (preload_imagesFile.length>0) {
		var id=preload_imagesFile[0].match(/visu\/img\/img-([0-9]*)./);
		if (id && id[1]>0) {
			var obj=new Image();			
			obj.onload=function(){preloadImagesReady();}
			obj.onerror=function(){preloadImagesReady();}
			obj.src=preload_imagesFile.shift();
		} else {
			preload_imagesFile.shift();
			preloadImagesReady();		
		}
	} else {
		startVisu();
	}
}

function preloadImagesReady() {
	var t=parseInt((preload_images_max-preload_imagesFile.length)*100/preload_images_max);
	document.getElementById("preload").style.background="-webkit-linear-gradient(left,#ffffff 0%,#ffffff "+t+"%,#606060 "+t+"%,#606060 100%)";
	preloadImagesLoad();
}

function startVisu() {
	document.getElementById("waitanim").style.borderLeftColor="#e00000";
	document.getElementById("waitanim").style.borderRightColor="#e00000";
	document.getElementById("wait").style.display="none";
	document.getElementById("preload").style.display="none";
	document.getElementById("windowContainer").style.display="block";
	initScreensaverTimer();
	openPage(visu_startPageId);
}

function jsLogin(visuid,visus) {
	//Logindialog (modal)	
	gotoStart("");
	var loginWinId="login";
	var n="<div id='loginform' class='appWindowLogin' onkeydown='if (event.keyCode==13) {ajax(0,\"login\",null,jsLogin_getFormArray(\"loginform\"));}' style='width:200px;'>";
		n+="<div style='margin-bottom:10px;'><b>Login</b></div>";
		n+="<select id='"+loginWinId+"-loginform-fd0' class='loginSelect' data-type='1' style='margin-bottom:15px;'>"+visus+"</select>";
		n+="<input type='text' id='"+loginWinId+"-loginform-fd1' data-type='1' value='' maxlength='30' class='loginInput' autofocus style='width:100%; height:25px; padding:5px; border-color:transparent;'></input><br>";
		n+="<input type='password' id='"+loginWinId+"-loginform-fd2' data-type='1' value='' maxlength='30' class='loginInput' style='width:100%; height:25px; padding:5px; border-color:transparent;'></input><br>";
		n+="<br>";
		n+="<div class='loginButton' onClick='ajax(0,\"login\",null,jsLogin_getFormArray(\"loginform\"));' style='float:right; border-color:transparent;'>Ok</div>";
	n+="</div>";
	document.getElementById(loginWinId).innerHTML=n;
	document.getElementById(loginWinId).style.display="block";
}

function jsLogin_getFormArray(formId) {
	var n=new Array();
	var tmp=document.getElementById(formId).querySelectorAll("[data-type]");
	for (var t=0; t<tmp.length; t++) {
		if (tmp[t].id) {
			var id=tmp[t].id.split("-")[2].replace("fd","");
			if (tmp[t].dataset.type==1) {n[id]=stringCleanup(tmp[t].value);}			//Input (Text/Hidden)
			if (tmp[t].dataset.type==4) {n[id]=stringCleanup(tmp[t].dataset.value);}	//checkmulti
			if (tmp[t].dataset.type==5) {n[id]=stringCleanup(tmp[t].dataset.value);}	//check
			if (tmp[t].dataset.type==6) {n[id]=stringCleanup(tmp[t].dataset.value);}	//select
		}
	}

	return n;
}


/*
============================================================================
Sound & Speech
============================================================================
*/

function visuSoundInit(obj) {
	if (visu_soundEnabled==1) {
		if (!visu_sound) {visu_sound=new Audio("../shared/etc/snd-empty.mp3");}	//Dummy
		visu_sound.type="audio/mpeg";
		visu_sound.preload="none";
		visu_sound.load();
		visu_sound.autoplay=false;
		visu_sound.loop=false;
		visu_sound.onplay=function(event){
			visu_soundEnabled=2;
			if (obj && visu_soundEnabled!=1 && visu_textToSpeechEnabled!=1) {obj.style.display="none";}
		}
		visu_sound.play();
	}
}

function visuSoundPlay(sndUrl,sndLoop) {
	if (visu_sound && visu_soundEnabled==2 && sndUrl!="") {
		visu_sound.src=sndUrl; 
		visu_sound.load();
		visu_sound.loop=sndLoop;
		visu_sound.play();
	}
}

function visuSoundStop() {
	if (visu_sound && visu_soundEnabled==2) {
		visu_sound.src="../shared/etc/snd-empty.mp3";
		visu_sound.load();
		visu_sound.pause();
	}
}

function visuTextToSpeechInit(obj) {
	if (visu_textToSpeechEnabled==1 && ("speechSynthesis" in window)) {
		if (!visu_textToSpeech) {visu_textToSpeech=new SpeechSynthesisUtterance();}
		visu_textToSpeech.onstart=function(event){
			visu_textToSpeechEnabled=2;
			if (obj && visu_soundEnabled!=1 && visu_textToSpeechEnabled!=1) {obj.style.display="none";}
		}
		visu_textToSpeech.text=" ";	//Dummy (darf nicht leer sein, muss bei Init ausgesprochen werden!)
		window.speechSynthesis.speak(visu_textToSpeech);
	}
}	

function visuTextToSpeechPlay(voice,language,rate,pitch,msg) {
	if (visu_textToSpeech && visu_textToSpeechEnabled==2 && msg!="") {
		visu_textToSpeech.text=msg;
		if (voice!="") 		{visu_textToSpeech.voice=voice;}	//### Voice wird noch nicht wirklich unterstützt...
		if (language!="") 	{visu_textToSpeech.lang=language;}
		if (rate!="") 		{visu_textToSpeech.rate=rate;}
		if (pitch!="") 		{visu_textToSpeech.pitch=pitch;}
		window.speechSynthesis.speak(visu_textToSpeech);
	}
}

function visuTextToSpeechStop() {
	if (visu_textToSpeech && visu_textToSpeechEnabled==2) {
		window.speechSynthesis.cancel();
	}
}


/*
============================================================================
Visuseiten & Popups
============================================================================
*/

function openPage(pageid,userClick) {
	if (userClick!==true) {userClick=false;}
	//Visuseite/Popup aufrufen
	if (visu_socket) {visu_socket.request_initPage(pageid,userClick);}
}

function reloadPage() {
	//### ungenutzt
	//aktuelle Visuseite (nicht Popup!) neu laden (KOs updaten)
	var obj=document.getElementById("w0");
	if (obj && obj.dataset.pageid>0) {
		openPage(obj.dataset.pageid);
		if (visu_socket) {visu_socket.request_initPage(pageid);}
	}
}

function focusPopup(pageid) {
	//existiert bereits ein Popup(!) mit dieser pageid, wird das Popup in den Vordergrund geholt
	//return: true=Popup ist bereits geöffnet, false=Popup existiert nicht
	var win=document.getElementById("windowContainer").childNodes;
	for (var t=0;t<win.length;t++) {
		if (win[t].dataset.pagetyp==1 && win[t].dataset.pageid==pageid) {
			focusWindow(win[t].id);
			return true;
		}
	}
	return false;
}

function closePopup(winId,userClick) {
	if (userClick!==true) {userClick=false;}
	if (winId!="w0") {
		controlCANCEL();
		var obj=document.getElementById(winId);
		if (obj) {
			if (visu_socket) {visu_socket.request_closePopup(obj.dataset.pageid,userClick);}
			closeWindow(winId);
		}
	}
}

function closePopupById(pageid,userClick) {
	var tmp=document.getElementById("windowContainer").childNodes;
	for (var t=0;t<tmp.length;t++) {
		if (tmp[t]!="w0" && tmp[t].dataset.pagetyp==1 && tmp[t].dataset.pageid==pageid) {
			controlCANCEL();
			if (visu_socket) {visu_socket.request_closePopup(tmp[t].dataset.pageid,userClick);}
			closeWindow(tmp[t].id);
			return true;
		}
	}
	return false;
}

function closeAllPopups() {
	var tmp=new Array();
	var win=document.getElementById("windowContainer").childNodes;
	for (var t=0;t<win.length;t++) {
		if (win[t].dataset.pagetyp==1) {tmp.push(win[t].id);}
	}
	
	//alle Popups schliessen (nur DOM)
	for (var t=0;t<tmp.length;t++) {
		if (tmp[t]!="w0") {
			closeWindow(tmp[t]);
		}
	}
}


/*
============================================================================
Bildschirmschoner
============================================================================
*/

function startScreensaver() {
	if (visu_sstimeout>0 && visu_sspageid>0) {
		screensaverActive=true;
		openPage(visu_sspageid);
	}
}

function initScreensaverTimer() {
	//wird bei jeder User-Aktion (Click) von checkClick() aufgerufen, und bei jedem Seitenwechsel (normal und Popup, damit ein Visualarm den Bildschirmschoner deaktiviert)
	screensaverActive=false;
	visu_clearTimeout("screensaver");
	if (visu_sstimeout>0 && visu_sspageid>0) {
		visu_setTimeout("screensaver",visu_sstimeout*60*1000,function(){startScreensaver();})
	}
}


/*
============================================================================
Warnungen & Fehlerbehandlung
============================================================================
*/

function showWarnings(arr) {
	var n="";
	if (visu_flagSounds) {
		if (visu_soundEnabled==1 || visu_textToSpeechEnabled==1) {n+="<div onClick='ackWarningSound(this);' class='visuWarning' style='color:#000000; background:#80e000; pointer-events:auto;'>&#9651;<br>Tonausgabe aktivieren</div>";}	//Achtung: onClick ist wichtig für iOS!
	}
	if (visu_flagErrors) {
		if (parseInt(arr[3])>visu_showWarningErr) {n+="<div onClick='ackWarningErr(this,"+parseInt(arr[3])+");' class='visuWarning' style='color:#ffffff; background:#ff4000; pointer-events:auto;'>&#9651;<br>"+arr[3]+" FEHLER</div>";}	//dto.
		if (arr[0]==1) {n+="<div class='visuWarning'>&#9651;<br>CPU</div>";}
		if (arr[1]==1) {n+="<div class='visuWarning'>&#9651;<br>RAM</div>";}
		if (arr[2]==1) {n+="<div class='visuWarning'>&#9651;<br>HDD</div>";}
	}
	if (n!="") {
		document.getElementById("warn").innerHTML=n;
		document.getElementById("warn").style.display="inline";
	} else {
		document.getElementById("warn").style.display="none";
	}	
}

function ackWarningErr(obj,n) {
	obj.style.display="none";
	visu_showWarningErr=n;
}

function ackWarningSound(obj) {
	visuSoundInit(obj);
	visuTextToSpeechInit(obj);
}

function ackWarningPreview() {
	document.getElementById("preview").style.display="none";
}

function showWarningError(n) {
	if (n!="") {
		document.getElementById("error").innerHTML="<div class='visuError'>&#9651;<br>"+n+"</div>";
		document.getElementById("error").style.display="block";
	} else {
		document.getElementById("error").style.display="none";
	}
}

function gotoStart(errMsg) {
	//alles auf Neustart

	//alle Timer löschen
	visu_timer={};

	if (visu_socket) {
		visu_socket.request_close();
		visu_socket=null;
	}
		
	//Sounds stoppen
	visuSoundStop();
	visuTextToSpeechStop();

	//alle Fenster entfernen
	clearObject("windowContainer",0);

	//alle Warnungen ausblenden
	document.getElementById("warn").style.display="none";
	ackWarningPreview();

	//wait-Animation einblenden
	document.getElementById("wait").style.display="block";

	//Viewport setzen
	document.getElementById("meta-viewport").setAttribute('content','user-scalable=no, width=device-width, initial-scale=1.0');

	//Mauspfeil (wieder) anzeigen
	document.body.style.cursor="default";
	
	showWarningError(errMsg);
}

function serverNotReady(visuid,errMsg) {
	gotoStart(errMsg);

	//kompletter Neustart nach 3 Sekunden
	visu_setTimeout("errortimer",3*1000,function(){initVisu(visuid);});
}


/*
============================================================================
Fenster-Verwaltung
============================================================================
*/

function openWindowPage(xsize,ysize) {
	//Visuseite
	var winID=newWindow(true,false,false);
	var n="<div id='"+winID+"-page' class='appWindowNormal' style='width:"+xsize+"px; height:"+ysize+"px; box-shadow:none; -webkit-animation:none;'></div>";
	document.getElementById(winID).innerHTML=n;
}

function openWindow(xsize,ysize,xpos,ypos,bgmodal,bganim,bgdark,bgshadow) {
	//Popups
	var winID=newWindow(bgmodal,bganim,bgdark);
	if (xpos!==undefined && ypos!==undefined) {
		var n="<div id='"+winID+"-page' class='appWindowNormal' style='position:absolute; left:"+xpos+"px; top:"+ypos+"px; width:"+xsize+"px; height:"+ysize+"px; "+((bganim===false)?"-webkit-animation:none;":"")+" "+((bgshadow===false)?"box-shadow:none;":"")+" border-radius:3px;'></div>";
	} else {
		var n="<table width='100%' height='100%' cellpadding='0' cellspacing='0' border='0'><tr valign='middle'><td>";
		n+="<div id='"+winID+"-page' class='appWindowNormal' style='width:"+xsize+"px; height:"+ysize+"px; "+((bganim===false)?"-webkit-animation:none;":"")+" "+((bgshadow===false)?"box-shadow:none;":"")+" border-radius:3px;'></div>";
		n+="</td></tr></table>";
	}
	document.getElementById(winID).innerHTML=n;
	return winID;
}

function focusWindow(winID) {
	//Popup on top bringen
	if (winID!=null) {
		var win=document.getElementById(winID);

		//neuen winID-zindex = ehemals größer zindex
		var maxZ=0;
		var wins=document.getElementById("windowContainer").childNodes;
		for (var t=0;t<wins.length;t++) {
			if (wins[t].style.zIndex>maxZ) {maxZ=parseInt(wins[t].style.zIndex);}
		}

		//alle zindex, die größer sind als win-zindex: dec()
		for (var t=0;t<wins.length;t++) {
			if (wins[t].style.zIndex>win.style.zIndex) {wins[t].style.zIndex=parseInt(wins[t].style.zIndex)-1;}
		}

		win.style.zIndex=maxZ;
	}
}

function closeWindow(winID) {
	if (winID!=null) {
		var win=document.getElementById(winID);

		//alle zindex, die größer sind als win-zindex: dec()
		var wins=document.getElementById("windowContainer").childNodes;
		for (var t=0;t<wins.length;t++) {
			if (wins[t].style.zIndex>win.style.zIndex) {wins[t].style.zIndex=parseInt(wins[t].style.zIndex)-1;}
		}
		windowZindex--;

		if (win) {win.parentNode.removeChild(win);}
		visupage_onChange(false,false);
	}
}

function newWindow(bgmodal,bganim,bgdark) {
	if (document.getElementById("windowContainer").lastChild) {
		var newID=parseInt(document.getElementById("windowContainer").lastChild.id.replace("w",""))+1;
	} else {
		var newID=0;
	}
	var winID="w"+newID;
	var div=document.createElement("div");
	document.getElementById("windowContainer").appendChild(div);
	div.className="appWindow";
	if (bgmodal===false) {div.style.pointerEvents="none";}
	if (bganim===false) {div.style.webkitAnimation="none";}
	if (bgdark===false) {div.style.background="none";}
	div.id=winID;
	windowZindex++;
	div.style.zIndex=windowZindex;
	return winID;
}


/*
============================================================================
Timer und Hilfsfunktionen
============================================================================
*/

function visu_setTimeout(id,timeout,func) {
	//setzt einen allgemeinen Timeout
	//id: eindeutige ID des Timers (z.B. "test" oder 123)
	//timeout: Zeit in ms
	//func: anonyme Funktion (ggf. mit Parametern): function(id){...}
	var ts=Date.now();
	visu_timer["v-"+id]={ve:false,id:id,timeout:timeout,func:func,ts:ts};
}

function visu_clearTimeout(id) {
	//löscht (d.h. Abbruch) einen allgemeinen Timeout
	//id: eindeutige ID des Timers (z.B. "test" oder 123)
	delete visu_timer["v-"+id];
}

function visuElement_deleteTimeouts(elementId) {
	//elementId: 
	//	>0 = alle Timer löschen, die einem vorhandenen(!) Visuelement mit der ID "elementId" zugeordnet sind 
	//	false = alle Timer löschen, die einem beliebigen nicht(!) mehr vorhandenen Visuelement zugeordnet sind
	if (elementId!==false) {
		for (var key in visu_timer) {
			if (visu_timer[key].ve && visu_timer[key].veid==elementId) {
				if (document.getElementById("e-"+visu_timer[key].veid)) {
					visuElement_clearTimeout(visu_timer[key].veid,visu_timer[key].timerid);
				}
			}
		}		

	} else {
		for (var key in visu_timer) {
			if (visu_timer[key].ve) {
				if (!document.getElementById("e-"+visu_timer[key].veid)) {
					visuElement_clearTimeout(visu_timer[key].veid,visu_timer[key].timerid);
				}
			}
		}		
	}
}

function checkClick() {
	//prüft, ob (z.B. auf iOS-Geräten) mehrere Finger ein touch-Event auslösen
	//(wird bei jeder User-Aktion (Click) aufgerufen und setzt daher auch gleich den Bildschirmschoner-Timer zurück)
	//return: true=Singletouch, false=Multitouch

	clickCancel();	//Klick nicht an Childs durchreichen
	initScreensaverTimer();
	var event=window.event;
	if (event && event.touches) {
		if (event.touches.length>1) {
			return false;
		}
	}
	return true;
}

function getDivPosition(obj) {
	//Liefert die absolute Positions eines Elements zurück (klettert quasi den DOM-Baum nach oben)
	//return: Objekt (z.B. Position.x und Position.y)
	var xPos=0;
	var yPos=0;
	while(obj) {
		xPos+=obj.offsetLeft;
		yPos+=obj.offsetTop;
		obj=obj.offsetParent;
	}
	return [xPos,yPos]; //{x:xPos,y:yPos};
}

function createNewDiv(parentId,id) {
	//erzeugt neues Div und hängt es an parent
	//parentId: Parent-Objekt-ID
	//id: gewünschte ID des neuen DIVs
	var div=document.createElement('div');
	document.getElementById(parentId).appendChild(div);
	div.id=id;
	return div;
}

function clearObject(objId,mode) {
	//Leert ein Objekt (entspricht quasi obj.innerHTML="")
	//mode: 1=setzt obj.style.display auf "none"
	var obj=document.getElementById(objId);
	if (mode==1) {obj.style.display="none";}
	while (obj.firstChild) {obj.removeChild(obj.firstChild);}
}

function shakeObj(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		obj.style.webkitAnimation="none";
		obj.style.webkitAnimationPlayState="paused";
		visu_setTimeout(objId,100,function(id){
			if (document.getElementById(id)) {
				document.getElementById(id).style.webkitAnimation='animShake 0.3s linear';
				document.getElementById(id).style.webkitAnimationPlayState='running';
			}
		});
	}
}


/*
============================================================================
Ajax & Websocket
============================================================================
*/

function ajax(visuId,cmd,json1,json2) {
	var req=new XMLHttpRequest();
	req.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			try {
				eval(this.responseText);
			} catch(e) {
				console.log("Ajax-Response fehlerhaft! Error-Msg: "+e.message+" / URL: "+decodeURIComponent(url)+" / Response: "+this.responseText);
			}
		}
		if (this.readyState==4 && this.status!=200) {
			console.log("Ajax-Abruf gescheitert! http-status: "+this.status+" / URL: "+decodeURIComponent(url));
			serverNotReady(visuId,"");
		}
	}
	var url="apps/app0.php?cmd="+encodeURIComponent(cmd)+"&visuid="+encodeURIComponent(visuId)+"&sid="+encodeURIComponent(sid)+"&vid="+encodeURIComponent(edomiVersion);
	req.open("POST",url,true);
	req.timeout=30000; //nach 30s: Timeout
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send("data1="+JSON.stringify(json1)+"&data2="+JSON.stringify(json2));
}

function websocket_init(pageId,websocketPort) {
	showWarningError("");
	visu_startPageId=pageId;
	visu_socket=new class_websocket();
	visu_socket.open(((window.location.protocol=='https:')?'wss':'ws'),window.location.hostname,websocketPort);
}

function class_websocket() {
	var that=this;
	
	this.open=function(serverProtocol,serverIp,serverPort) {
		try {
			socket=new WebSocket(serverProtocol+'://'+serverIp+':'+serverPort);

			socket.onopen=function (event) {
				//Browser-Exit => Client offline setzen
				window.onbeforeunload=function (event) {
					if (socket.readyState==1) {
						socket.close();
					}
				}
			};

			socket.onmessage=function (event) {
				var tmp=JSON.parse(event.data);
				if (tmp[0]=='LOGIN') {
					that.request_login();

				} else if (tmp[0]=='START') {
					includeVisuelements('../data/liveproject/vse/vse_include_visu'+visu_visuid+'.js',function(){prepareVisu();},function(){socket.close();});

				} else if (tmp[0]=='CMD') {
					eval(tmp[1]);

				} else if (tmp[0]=='PING') {
					that.request_pong();
					visu_setTimeout("websocket",30000,function(id) {visu_socket.pingTimeout();});					
				}
			};

			socket.onclose=function (event) {
				serverNotReady(visu_visuid,"");
			};
			
			socket.onerror=function (event) {
				serverNotReady(visu_visuid,"");
			};
		
		} catch (exception) {
			serverNotReady(visu_visuid,"");
		}
	}

	this.pingTimeout=function () {
		socket.onclose=function() {};
		socket.onerror=function() {};
		serverNotReady(visu_visuid,"");
	}

	this.socketSend=function(data) {
		if (socket.readyState==1) {	
			var tmp=JSON.stringify(data);
			socket.send(tmp);
		}
	}


	this.request_login=function() {
		//Login per SID und visuId
		that.socketSend(['LOGIN',sid,visu_visuid]);
	}

	this.request_pong=function() {
		//Antwort auf PING
		that.socketSend(['PONG',sid]);
	}

	this.request_close=function() {
		//Verbindung schließen
		that.socketSend(['CLOSE',sid,visu_visuid]);
	}

	this.request_initPage=function(pageid,userClick) {
		//Seite/Popup aufrufen (KOs updaten)
		that.socketSend(['INITPAGE',sid,pageid,((userClick===true)?1:0)]);
	}

	this.request_closePopup=function(pageid,userClick) {
		//Popup schließen (KOs updaten)
		that.socketSend(['CLOSEPOPUP',sid,pageid,((userClick===true)?1:0)]);
	}

	this.request_execCmdList=function(elementId) {
		//CMD-Liste ausführen
		that.socketSend(['EXECCMDLIST',sid,elementId]);
	}

	this.request_setKoValue=function(koId,value) {
		//KO-Value setzen
		that.socketSend(['SETKOVALUE',sid,koId,value]);
	}
	
	this.response_initPage=function(page,currentPageIDs,background,item) {
		if (page.pagetyp==1) {	//Popup
			if (focusPopup(page.id)) {
				return;

			} else {
				controlCANCEL();

				if (page.xpos!="" && page.ypos!="") {
					var parentWinId=openWindow(page.xsize,page.ysize,page.xpos,page.ypos,((page.bgmodal==1)?true:false),((page.bganim==1)?true:false),((page.bgdark==1)?true:false),((page.bgshadow==1)?true:false));
				} else {
					var parentWinId=openWindow(page.xsize,page.ysize,undefined,undefined,((page.bgmodal==1)?true:false),((page.bganim==1)?true:false),((page.bgdark==1)?true:false),((page.bgshadow==1)?true:false));
				}

				if (page.autoclose!=0) {
					visu_setTimeout(parentWinId,page.autoclose*1000,function(id){
						if (document.getElementById(id)) {closePopup(id);}
					});
				}
			}

		} else {	//normale Seite/globale Inkludeseite
			controlCANCEL();

			closeAllPopups();
			clearObject("w0-page",0);

			var parentWinId="w0";
			element_linked={};
		}

		//Page-Daten
		var obj=document.getElementById(parentWinId);
		obj.dataset.pageid=page.id;
		obj.dataset.pagetyp=page.pagetyp;

		//Container der Elemente (w#-page): winId (w#) merken (wird für PopupClose gebraucht...)
		document.getElementById(parentWinId+"-page").dataset.winid=parentWinId;

		//Offset der Seite (Popup) bezogen auf linke/obere Ecke des Browsers
		var pageXpos=document.getElementById(parentWinId+"-page").offsetLeft;
		var pageYpos=document.getElementById(parentWinId+"-page").offsetTop;

		//Seitenhintergrund
		if (background!="") {
			document.getElementById(parentWinId+"-page").style.background=background;
			document.getElementById(parentWinId+"-page").style.backgroundSize="100% 100%";
			document.getElementById(parentWinId+"-page").style.backgroundRepeat="no-repeat";
		} else {
			document.getElementById(parentWinId+"-page").style.background="";
		}

		//Visuelemente aufbauen
		for (var t=0;t<item.length;t++) {	
			var obj=createNewDiv(parentWinId+"-page","e-"+item[t].id);
			obj.dataset.id=item[t].id;
			obj.dataset.controltyp=item[t].controltyp;

			obj.dataset.xpos=item[t].xpos;
			obj.dataset.ypos=item[t].ypos;

			obj.dataset.var1=item[t].var1;
			obj.dataset.var2=item[t].var2;
			obj.dataset.var3=item[t].var3;
			obj.dataset.var4=item[t].var4;
			obj.dataset.var5=item[t].var5;
			obj.dataset.var6=item[t].var6;
			obj.dataset.var7=item[t].var7;
			obj.dataset.var8=item[t].var8;
			obj.dataset.var9=item[t].var9;
			obj.dataset.var10=item[t].var10;
			obj.dataset.var11=item[t].var11;
			obj.dataset.var12=item[t].var12;
			obj.dataset.var13=item[t].var13;
			obj.dataset.var14=item[t].var14;
			obj.dataset.var15=item[t].var15;
			obj.dataset.var16=item[t].var16;
			obj.dataset.var17=item[t].var17;
			obj.dataset.var18=item[t].var18;
			obj.dataset.var19=item[t].var19;
			obj.dataset.var20=item[t].var20;

			obj.dataset.groupid=item[t].groupid;
			if (item[t].controltyp==0) {obj.dataset.grouptag=item[t].grouptag;}
			obj.dataset.linkid=item[t].linkid;

			obj.dataset.gotopageid=item[t].gotopageid;
			obj.dataset.closepopupid=item[t].closepopupid;
			obj.dataset.closepopup=item[t].closepopup;
			obj.dataset.hascmd=item[t].hascmd;
			obj.dataset.pagex=pageXpos;
			obj.dataset.pagey=pageYpos;

			//Live-Vorschau
			obj.dataset.livekoid=0;
			obj.dataset.livetimeout=item[t].livepreview;
			if (item[t].livepreview!=0 && item[t].koid1>0) {obj.dataset.livekoid=item[t].koid1;}

			//Seitensteuerung/Befehle vorhanden?
			obj.dataset.hascommands=0;
			if (item[t].gotopageid>0 || item[t].closepopupid>0 || item[t].closepopup>0 || item[t].hascmd>0) {obj.dataset.hascommands=1;}
			
			//KOs
			obj.dataset.koid1=item[t].koid1;
			obj.dataset.koid2=item[t].koid2;
			obj.dataset.koid3=item[t].koid3;
			obj.dataset.value1=item[t].kovalues.kovalue1;
			obj.dataset.value2=item[t].kovalues.kovalue2;
			obj.dataset.value3=item[t].kovalues.kovalue3;
			
			//Design (CSS) zuweisen und merken
			obj.dataset.d_text=item[t].design.text;			
			var tmp=pS(item[t].design.css,item[t].kovalues.kovalue1);
			element_setCss(obj,tmp);				
			obj.dataset.d_css=item[t].design.css;
			obj.dataset.d_cssparsed=tmp;
			obj.dataset.d_csslast="";
			
			//Klicks auf unsichtbare Elemente ignorieren
			if (parseFloat(obj.style.opacity)==0) {obj.style.pointerEvents="none";}

			controlCONSTRUCT(obj);
			visuElement_clearTimeout(obj.dataset.id,"livehold");
			controlREFRESH(obj,true,true,false,element_drag.id,item[t].kovalues.kovalue1);
		}

		visupage_onChange(true,false);
	}

	this.response_refreshItems=function(itemArr) {
		for (var t=0;t<itemArr.length;t++) {
			refreshItem(itemArr[t],false);
		}
		visupage_onChange(true,true);
	}	

	this.response_refreshValues=function(valueArr) {
		for (var t=0;t<valueArr.length;t++) {
			var obj=document.getElementById("e-"+valueArr[t].id);
			if (obj) {
				if (valueArr[t].kovalues.update1==1) {obj.dataset.value1=valueArr[t].kovalues.kovalue1;}
				if (valueArr[t].kovalues.update2==1) {obj.dataset.value2=valueArr[t].kovalues.kovalue2;}
				if (valueArr[t].kovalues.update3==1) {obj.dataset.value3=valueArr[t].kovalues.kovalue3;}
			}
		}
	}	
}


/*
============================================================================
Visuelemente: "private"
============================================================================
*/
function controlCONSTRUCT(obj) {
	if (obj && obj.dataset.controltyp>0) {
		visuElement_deleteTimeouts(obj.dataset.id);
		window["VSE_"+obj.dataset.controltyp+"_CONSTRUCT"](obj.dataset.id,obj);
	}
}

function controlREFRESH(obj,isInit,isRefresh,isLive,dragId,koValue) {
	if (obj && obj.dataset.controltyp>0) {
		window["VSE_"+obj.dataset.controltyp+"_REFRESH"](obj.dataset.id,obj,isInit,isRefresh,isLive,((dragId==obj.dataset.id)?true:false),koValue);
	}
}

function controlCANCEL() {
	var elements=document.getElementById("windowContainer").querySelectorAll("[data-controltyp]");
	for (var t=0; t<elements.length; t++) {
		if (elements[t].dataset.controltyp>0) {
		
			if (window["VSE_"+elements[t].dataset.controltyp+"_CANCEL"]) {
				window["VSE_"+elements[t].dataset.controltyp+"_CANCEL"](elements[t].dataset.id);
			}

			//Drag-Operation ggf. abbrechen
			dragCancel(elements[t]);
				
		}
	}
	
	//Longclick ggf. abbrechen
	longClick_cancel(true);
}

function visupage_onChange(mode,refresh) {
	//mode: true=Seite/Popup wurde geöffnet, false=Popup wurde geschlossen (wird ggf. mehrfach aufgerufen!)
	//refresh: true=Inhalte wurden aktualisiert, false=Inhalte wurden erstmalig aufgebaut (Seitenaufbau)
	//### z.B. zum Parsen von DOM-Inhalten nach(!) dem Seitenaufbau

	visuElement_deleteTimeouts(false);

	//Gruppen ein-/ausblenden
	var groups=document.getElementById("windowContainer").querySelectorAll("[data-controltyp='0']");
	for (var t=0; t<groups.length; t++) {
		var values=groups[t].dataset.value1.split(";");
		var item=document.getElementById("windowContainer").querySelectorAll("[data-groupid='"+groups[t].dataset.id+"']");
		for (var tt=0; tt<item.length; tt++) {
			if (values.indexOf(groups[t].dataset.grouptag)>=0) {
				item[tt].style.visibility="visible";
			} else {
				item[tt].style.visibility="hidden";
			}
		}
	}
	visuElements_onChange();
	visuElements_clearGlobals();
}

function visuElements_onChange() {
	for (var key in element_linked) {
		obj=document.getElementById(key);
		if (obj) {element_linked[key].func(obj,element_linked[key].para);}
	}		
}

function visuElements_clearGlobals() {
	for (var key in element_globals) {
		obj=document.getElementById(key);
		if (!obj) {delete element_globals[key];}
	}
}

function refreshItem(item,firstInit) {
	//Refresh eines Visuelements durch proc_visu (auch nach dem ersten Aufbau)
	var obj=document.getElementById("e-"+item.id);
	if (obj) {
		obj.dataset.d_text=item.design.text;			
		obj.dataset.value1=item.kovalues.kovalue1;
		obj.dataset.value2=item.kovalues.kovalue2;
		obj.dataset.value3=item.kovalues.kovalue3;

		if (item.design.rcss==1) {
			//Animation ggf. mit diesem Trick neustarten
			if (obj.style.webkitAnimationName.substr(0,4)=="anim") {
				element_setCss(obj,"-webkit-animation:none;");
				obj.offsetWidth;
			}

			//Design (CSS) zuweisen und merken
			var tmp=pS(item.design.css,item.kovalues.kovalue1);
			element_setCss(obj,tmp);				
			obj.dataset.d_css=item.design.css;
			obj.dataset.d_cssparsed=tmp;

			//Klicks auf unsichtbare Elemente ignorieren
			if (parseFloat(obj.style.opacity)==0) {obj.style.pointerEvents="none";}
		}

		visuElement_clearTimeout(obj.dataset.id,"livehold");
		controlREFRESH(obj,firstInit,true,false,element_drag.id,item.kovalues.kovalue1);
	}
}

function setKoValuePeriodical_set(drag,unblock) {
	//setzt ein KO während der Eingabe (z.B. Drehregler) auf den Wert value, sofern eine Änderung vorliegt
	if (element_drag.id>0 && element_drag.koid>0) {
		if (element_drag.interval<0) {
			setLiveValue(drag);
		}
		if (!drag || unblock===true) {
			visuElement_clearTimeout(element_drag.id,"periodical");
		}
		if (!drag || (element_drag.interval>=0 && (unblock===true || element_drag.koblock==0))) {
			element_drag.koblock=0;
			if (element_drag.lastkovalue.toString()!==element_drag.kovalue.toString()) {
				setKoValue(element_drag.koid,element_drag.kovalue);
				element_drag.lastkovalue=element_drag.kovalue;
				if (element_drag.interval>=0) {
					controlREFRESH(element_drag.veobj,false,false,false,element_drag.id,element_drag.kovalue);
				}
			}
			if (drag && element_drag.interval>0) {
				element_drag.koblock=1;
				visuElement_setTimeout(element_drag.id,"periodical",element_drag.interval,function(){setKoValuePeriodical_set(true,true);});
			}			
		}
	}
	
	if (element_drag.id>0 && !drag) {
		if (element_drag.interval>=0) {
			controlREFRESH(element_drag.veobj,false,false,false,0,element_drag.veobj.dataset.value1);
		}
		setKoValuePeriodical_reset("");
	}
}

function setKoValuePeriodical_reset(lastValue) {
	//lastValue: ""=KoValue wird absolut gesetzt werden, (Wert)=KoValue wird relativ gesetzt werden
	visuElement_clearTimeout(element_drag.id,"periodical");
	element_drag.kovalue="";
	element_drag.kovaluesbc="";
	element_drag.lastkovalue=lastValue;
	element_drag.koblock=0;
	element_drag.interval=-1;
	element_drag.koid=0;
	element_drag.livekoid=0;
	element_drag.obj=null;
	element_drag.id=0;
}

function setLiveValue(drag) {
	if (element_drag.id>0 && element_drag.livekoid>0) {
		var obj=document.getElementById("windowContainer").querySelectorAll("[data-livekoid='"+element_drag.livekoid+"']");
		if (drag) {
			//Drag-Vorgang läuft: Livevorschau
			if (element_drag.kovaluesbc.toString()!==element_drag.kovalue.toString()) {
				element_drag.kovaluesbc=element_drag.kovalue;

				for (var t=0; t<obj.length; t++) {
					//Live-Vorschau setzen (ausser Drag-Objekt)
					if (obj[t].dataset.id!=element_drag.id) {
						visuElement_clearTimeout(obj[t].dataset.id,"livehold");
						element_setCss(obj[t],pS(obj[t].dataset.d_css,element_drag.kovalue));
						controlREFRESH(obj[t],false,false,true,element_drag.id,element_drag.kovalue);
					}
				}
	
				//Live-Vorschau setzen: Drag-Objekt
				var obj=document.getElementById("e-"+element_drag.id);
				if (obj) {
					visuElement_clearTimeout(obj.dataset.id,"livehold");
					element_setCss(obj,pS(obj.dataset.d_css,element_drag.kovalue));
					controlREFRESH(obj,false,false,true,element_drag.id,element_drag.kovalue);
				}

			}

		} else {
			//Drag-Vorgang beendet: Livevorschau beenden
			for (var t=0; t<obj.length; t++) {
				if (obj[t].dataset.livetimeout>0) {
					//mit Haltezeit: Style ggf. erst nach Ablauf der Haltezeit (timeout) wiederherstellen (ausser Drag-Objekt)
					if (obj[t].dataset.id!=element_drag.id) {
						visuElement_setTimeout(obj[t].dataset.id,"livehold",obj[t].dataset.livetimeout,function(id){setLiveValue_timeout(id);});
					}

				} else if (obj[t].dataset.id!=element_drag.id) {
					//ohne Haltezeit: Style unmittelbar wiederherstellen (ausser Drag-Objekt)
					element_setCss(obj[t],pS(obj[t].dataset.d_css,obj[t].dataset.value1));
					controlREFRESH(obj[t],false,false,false,0,obj[t].dataset.value1);
				}
			}
			
			//Drag-Vorgang beendet: Livevorschau beenden: Drag-Objekt
			var obj=document.getElementById("e-"+element_drag.id);
			if (obj) {
				if (obj.dataset.livetimeout>0) {
					//mit Haltezeit: Style ggf. erst nach Ablauf der Haltezeit (timeout) wiederherstellen (unabhängig von element_drag.livekoid)
					element_setCss(obj,pS(obj.dataset.d_css,element_drag.kovalue));
					controlREFRESH(obj,false,false,true,0,element_drag.kovalue);
					visuElement_setTimeout(obj.dataset.id,"livehold",obj.dataset.livetimeout,function(id){setLiveValue_timeout(id);});

				} else {
					//ohne Haltezeit: Style unmittelbar wiederherstellen (unabhängig von element_drag.livekoid)
					element_setCss(obj,pS(obj.dataset.d_css,obj.dataset.value1));
					controlREFRESH(obj,false,false,false,0,obj.dataset.value1);
				}
			}
			
		}
		visuElements_onChange();
	}	
}

function setLiveValue_timeout(elementId) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		element_setCss(obj,pS(obj.dataset.d_css,obj.dataset.value1));
		controlREFRESH(obj,false,false,false,0,obj.dataset.value1);
	}
}

function dragUpdate(koValue) {
	//neuen Wert ggf. zyklisch setzen
	element_drag.kovalue=koValue;
	setKoValuePeriodical_set(true);
}

function dragEnd(koValue) {
	//Drag-Operation beenden (aufräumen), ggf. koValue setzen (optional)
	drag_removeAllEventListeners();
	if (element_drag.id>0) {
		if (koValue!==undefined) {element_drag.kovalue=koValue;}
		setKoValuePeriodical_set(false);
	}
}

function dragCancel(obj) {
	//Drag-Operation abbrechen
	if (element_drag.id>0 && obj.dataset.id==element_drag.id) {
		if (window["VSE_"+obj.dataset.controltyp+"_DRAGEND"]) {
			window["VSE_"+obj.dataset.controltyp+"_DRAGEND"](obj.dataset.id,element_drag.obj,false);
		}
		dragEnd();
	}
}

function drag_addEventListener(event,func) {
	visu_dragEventlistener.push({event:event,func:func});
	document.addEventListener(event,func,{passive:false});
}

function drag_removeAllEventListeners() {
	while (visu_dragEventlistener.length>0) {
		var v=visu_dragEventlistener.pop();
		document.removeEventListener(v.event,v.func,{passive:false});
	}
}

function drag_mapDragValue(pos,koValue,format,coord,modus,minX,maxX,minY,maxY,minA,maxA,vlist,vstep,vsnap) {
	//(intern für visuElement_mapDragValueReset() und visuElement_mapDragValue())
	//pos:{x,y,w,h} (relative Mausposition bezogen auf das Drag-Object, Breite/Höhe des Drag-Objects)
	//koValue: (ungeparsed (nur bei modus=1/2 sinnvoll), nur bei isStart=true notwendig) Default-KO-Wert
	//format: 0=X+Y (KO-Format: "X,Y"), 1=nur X, 2=nur Y
	//coord: 0=kartesisch, 1=polar
	//modus: 0=absolut, 1=relativ, 2=Inkremental (nur X und coord muss 1 sein)
	//minXY/maxXY: leer=Default (VE-Size in px bzw. 0..360 Grad), -oo..oo=Range
	//minA/maxA: Winkelbereich bei polar (jeweils 0..360 Grad, min<max)
	//vlist: Anzahl der Nachkommastellen
	//vstep: Raster
	//vsnap: (nur bei Inkrementalgeber) Anzahl der Rastungen (360 muss restlos durch vsnap teilbar sein)

	//return: 
	//	{valuex,valuey,cursorx,cursory}:
	//		valuex/y: X/Y-Wert (bei VSE 11/12: valuex, bei 13: valuex oder valuey (je nach Ausrichtung), bei 33: valuex/y für Winkel/Radius bzw. x/y-Wert)
	//		cursorx/y: Cursor-Position (z.B. für VSE 33), kartesisch oder Winkel
	//	oder false=Fehler bzw. nicht möglich
	
	if (pos) {
		//Min/Max-Defaultwerte ermitteln
		vstep=parseFloat(vstep);
		vlist=parseInt(vlist);
		vsnap=parseInt(vsnap);
		if (pos.w>=pos.h) {var maxR=pos.h/2;} else {var maxR=pos.w/2;}
		if (isNaN(parseFloat(minA)) || modus==2) {minA=0;}
		if (isNaN(parseFloat(maxA)) || modus==2) {maxA=360;}
		if (isNaN(parseFloat(minX))) {
			if (coord==0) {minX=0;} else {minX=minA;}
		}
		if (isNaN(parseFloat(maxX))) {
			if (coord==0) {maxX=pos.w;} else {maxX=maxA;}
		}
		if (isNaN(parseFloat(minY))) {
			if (coord==0) {minY=0;} else {minY=0;}
		}
		if (isNaN(parseFloat(maxY))) {
			if (coord==0) {maxY=pos.h;} else {maxY=maxR;}
		}
		minX=parseFloat(minX);
		maxX=parseFloat(maxX);
		minY=parseFloat(minY);
		maxY=parseFloat(maxY);
		minA=parseFloat(minA);
		maxA=parseFloat(maxA);


		if (element_drag.initKovalue!==false) {
			//KO-Wert beim Start parsen und merken und Mausposition merken
			var tmp=element_drag.initKovalue.toString().split(",");
			element_drag.initKovalue=false;
			if (isNaN(parseFloat(tmp[0]))) {
				tmp[0]=minX;
			}
			if (format==2) {
				if (tmp.length<2) {tmp[1]=tmp[0];}
			} else if (tmp.length<2 || isNaN(parseFloat(tmp[1]))) {
				tmp[1]=minY;
			}
			element_drag.startx=pos.x;
			element_drag.starty=pos.y;
			element_drag.valuex=parseFloat(tmp[0]);
			element_drag.valuey=parseFloat(tmp[1]);
			element_drag.lastcx="";
		}

		//Cursor und Value ermitteln
		if (coord==0) {
			if (modus==0) {
				var x=(pos.x/pos.w)*Math.abs(maxX-minX)+minX;
				var cursorX=pos.x;
				var y=(pos.y/pos.h)*Math.abs(maxY-minY)+minY;
				var cursorY=pos.y;

			} else if (modus==1) {
				defaultvalue=valueRange(element_drag.valuex,element_drag.valuey);
				var x=((pos.x-element_drag.startx)/pos.w)*Math.abs(maxX-minX)+defaultvalue.x;
				var cursorX=pos.w*((x-minX)/Math.abs(maxX-minX));
				var y=((pos.y-element_drag.starty)/pos.h)*Math.abs(maxY-minY)+defaultvalue.y;
				var cursorY=pos.h*((y-minY)/Math.abs(maxY-minY));
			}
			
		} else {
			if (modus==0) {
				var tmp=parseFloat((Math.atan2(-(pos.x-pos.w/2),-(pos.y-pos.h/2))-Math.PI)*(-180/Math.PI));
				var x=((tmp-minA)/Math.abs(maxA-minA))*Math.abs(maxX-minX)+minX;
				var cursorX=tmp;				
				var tmp=parseFloat(Math.sqrt(Math.pow(pos.x-pos.w/2,2)+Math.pow(pos.y-pos.h/2,2)));
				var y=(tmp/maxR)*Math.abs(maxY-minY)+minY;
				var cursorY=maxR*((y-minY)/Math.abs(maxY-minY));

			} else if (modus==1) {
				defaultvalue=valueRange(element_drag.valuex,element_drag.valuey);
				var tmp1=parseFloat((Math.atan2(-(element_drag.startx-(pos.w/2)),-(element_drag.starty-(pos.h/2)))-Math.PI)*(-180/Math.PI));
				var tmp2=((defaultvalue.x-minX)/Math.abs(maxX-minX))*Math.abs(maxA-minA)+minA;
				var tmp=parseFloat((Math.atan2(-(pos.x-pos.w/2),-(pos.y-pos.h/2))-Math.PI+((tmp1-tmp2)/180*Math.PI))*(-180/Math.PI));
				if (tmp<0 && tmp>-0.00001) {tmp=0;}	//Rundungsfehler (Float) ausgleichen
				if (tmp<0) {tmp=360+tmp;}
				if (tmp>360) {tmp=tmp-360;}
				var x=((tmp-minA)/Math.abs(maxA-minA))*Math.abs(maxX-minX)+minX;
				var cursorX=tmp;				
				var tmp=Math.sqrt(Math.pow(pos.x-pos.w/2,2)+Math.pow(pos.y-pos.h/2,2))-Math.sqrt(Math.pow(element_drag.startx-(pos.w/2),2)+Math.pow(element_drag.starty-(pos.h/2),2));
				var y=(tmp/maxR)*Math.abs(maxY-minY)+defaultvalue.y;
				var cursorY=maxR*((y-minY)/Math.abs(maxY-minY));

			} else if (modus==2) {
				var tmp1=parseFloat((Math.atan2(-(element_drag.startx-(pos.w/2)),-(element_drag.starty-(pos.h/2)))-Math.PI)*(-180/Math.PI));
				var tmp2=parseFloat((Math.atan2(-(pos.x-pos.w/2),-(pos.y-pos.h/2))-Math.PI)*(-180/Math.PI));
				tmp1=parseInt(tmp1/vsnap)*vsnap;
				tmp2=parseInt(tmp2/vsnap)*vsnap;
				var tmp=tmp2-tmp1;
				var tmpStep=0;
				
				if ((tmp>0 && tmp<180) || tmp<-180) {
					tmpStep=1;
				} else if ((tmp<0 && tmp>-180) || tmp>180) {
					tmpStep=-1;
				}

				if (!isNaN(vstep) && vstep!=0) {
					tmpStep*=vstep;
				} else if (vlist>=0) {
					tmpStep/=(Math.pow(10,vlist));
				}

				var x=element_drag.valuex;
				if (tmpStep!=0) {
					x+=tmpStep;
					var tmp=valueRange(x,0);
					element_drag.valuex=tmp.x;
					element_drag.startx=pos.x;
					element_drag.starty=pos.y;
				}

				var tmp=parseFloat((Math.atan2(-(pos.x-pos.w/2),-(pos.y-pos.h/2))-Math.PI)*(-180/Math.PI));
				var cursorX=parseInt(tmp/vsnap)*vsnap;

				var y=0;
				var cursorY=0;
			}
		}
						
		if (coord==0) {
			if (cursorX<0) {cursorX=0;}
			if (cursorX>pos.w) {cursorX=pos.w;}
			if (cursorY<0) {cursorY=0;}
			if (cursorY>pos.h) {cursorY=pos.h;}
		} else {
			if (cursorX<minA) {cursorX=minA;}
			if (cursorX>maxA) {cursorX=maxA;}
			if (cursorY<0) {cursorY=0;}
			if (cursorY>maxR) {cursorY=maxR;}
		}
		
		//Polar: Anschlag (nur Winkel)
		if (coord==1 && modus!=2) {
			if (element_drag.lastcx=="") {
				element_drag.lastcx=cursorX;
			} else {
				var tmp=parseFloat(element_drag.lastcx)-cursorX;
				if (tmp>=Math.abs(maxA-minA)/2) {
					x=maxX;
					cursorX=maxA;
				} else if (tmp<=-Math.abs(maxA-minA)/2) {
					x=minX;
					cursorX=minA;
				} else {
					element_drag.lastcx=cursorX;
				}
			}
		}		

		value=valueRange(x,y);

		//Raster
		if (!isNaN(vstep) && vstep!=0) {
			value.x=Math.round(value.x/vstep)*vstep;
			value.y=Math.round(value.y/vstep)*vstep;
		}

		//Nachkommastellen
		if (vlist>=0) {
			value.x=value.x.toFixed(vlist);
			value.y=value.y.toFixed(vlist);
		}
		
		if (isNaN(value.x)) {value.x=minX;}
		if (isNaN(value.y)) {value.y=minY;}

		return {valuex:value.x,valuey:value.y,cursorx:cursorX,cursory:cursorY};
	}
	return false;
	
	function valueRange(x,y) {
		x=parseFloat(x);
		y=parseFloat(y);
		if (x<minX) {x=minX;}
		if (x>maxX) {x=maxX;}
		if (y<minY) {y=minY;}
		if (y>maxY) {y=maxY;}
		return {x:x,y:y};
	}
}

function element_setCss(obj,css) {
	if (visu_cssbuffer) {
		if (obj.dataset.d_csslast!=css) {
			obj.dataset.d_csslast=css;
			obj.style.cssText=css;
		}
	} else {
		obj.style.cssText=css;
	}
	obj.dataset.d_cssparsed=css;
}

function longClick(veId,objId,funcShort,funcLong,visibleShort,visibleLong,visibleLongAnim) {
	//Longclick initialisieren
	var obj=document.getElementById(objId);
	if (veId>0 && obj) {
		if (visibleLongAnim===true) {
			var d=document.getElementById("longclick");
			d.style.display="block";
			d.style.borderTopColor=visu_indiColor;
			d.style.borderBottomColor=visu_indiColor;

			var tmp=obj.getBoundingClientRect();
			if (tmp.width>tmp.height) {var w=tmp.height;} else {var w=tmp.width;}
			w+=w*visu_longclickSize/100;
			var x=parseFloat((tmp.left+window.scrollX)+tmp.width/2)-(w/2);
			var y=parseFloat((tmp.top+window.scrollY)+tmp.height/2)-(w/2);
			d.style.left=x+'px';
			d.style.top=y+'px';
			d.style.width=w+'px';
			d.style.height=w+'px';
		}
		element_longclick.veId=veId;
		element_longclick.objId=objId;
		element_longclick.obj=obj;
		element_longclick.funcShort=funcShort;
		element_longclick.funcLong=funcLong;
		element_longclick.visibleShort=visibleShort;
		element_longclick.visibleLong=visibleLong;
		visu_setTimeout("longclick",1000,function(){longClick_success();});

		window.removeEventListener(upEvent,longClick_cancel,false);
		window.addEventListener(upEvent,longClick_cancel,false);
	}
}

function longClick_success() {
	window.removeEventListener(upEvent,longClick_cancel,false);
	if (element_longclick.obj) {
		if (element_longclick.funcLong!==false) {
			if (element_longclick.visibleLong) {visuElement_indicate(element_longclick.obj);}
			element_longclick.funcLong(element_longclick.veId,element_longclick.objId);
		}
	}
	longClick_cancel(true);
}

function longClick_cancel(abort) {
	//abort: (optional) true=Longclick nur abbrechen (ohne Callback)
	window.removeEventListener(upEvent,longClick_cancel,false);
	if (abort!==true && element_longclick.obj) {
		if (element_longclick.funcShort!==false) {
			if (element_longclick.visibleShort) {visuElement_indicate(element_longclick.obj);}
			element_longclick.funcShort(element_longclick.veId,element_longclick.objId);
		}
	}
	element_longclick.veId=0;
	element_longclick.objId=null;
	element_longclick.obj=0;
	visu_clearTimeout("longclick");
	var d=document.getElementById("longclick");
	if (d) {d.style.display="none";}
}

function clickCancel() {
	//verhindert bei verschachtelten DIVs, dass das Click-Event vom Parent bearbeitet wird, wenn auf das Child geklickt wird
	var event=window.event;
	if (event) {
		if (event.cancelBubble) {event.cancelBubble=true;}
		if (event.stopPropagation) {event.stopPropagation();}
	}
}

function checkboxInit(obj,addClickEvent) {
	if (addClickEvent!==false) {
		visuElement_onClick(obj,function(){checkbox_onClick_toggle(obj);},false);
	}
	var list=obj.dataset.list.split("|");
	var tmp=((list[obj.dataset.value]!="")?list[obj.dataset.value]:"&nbsp;");
	if (obj.dataset.value>=1) {
		obj.style.background=visu_indiColor;
		obj.innerHTML="<span style='color:"+visu_indiColorText+";'>"+tmp+"</span>";
	} else {
		obj.style.background="none";
		obj.innerHTML=tmp;
	}
}
function checkbox_onClick_toggle(obj) {
	var list=obj.dataset.list.split("|");
	obj.dataset.value++;
	if (obj.dataset.value>=list.length) {obj.dataset.value=0;}
	checkboxInit(obj,false);
}

function selectboxInit(obj) {
	if (obj) {
		var n="";
		var id=obj.dataset.value;

		var list=obj.dataset.list.split("|");

		n+="<select onChange='document.getElementById(\""+obj.id+"\").dataset.value=this.value;' class='controlSelectboxTag'>";
		for (var t=0;t<list.length;t++) {
			
			var i=list[t].indexOf('#');
			var tmp=[list[t].slice(0,i),list[t].slice(i+1)];
	
			if (id==tmp[0]) {
				n+="<option value='"+tmp[0]+"' selected>"+tmp[1]+"</option>";
			} else {
				n+="<option value='"+tmp[0]+"'>"+tmp[1]+"</option>";
			}
		}
		n+="</select>";
		obj.innerHTML=n;
	}
}


/*
============================================================================
Visuelemente: "public"
============================================================================
*/

function visuElement_callPhp(vseID,cmd,json1,json2) {
	if (json1===undefined) {json1=null;}
	if (json2===undefined) {json2=null;}
	var req=new XMLHttpRequest();
	req.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			try {
				eval(this.responseText);
			} catch(e) {
				console.log("Ajax-Response fehlerhaft! Error-Msg: "+e.message+" / URL: "+decodeURIComponent(url)+" / Response: "+this.responseText);
			}
		}
		if (this.readyState==4 && this.status!=200) {console.log("Ajax-Abruf gescheitert! http-status: "+this.status+" / URL: "+decodeURIComponent(url));}
	}	
	var url="../data/liveproject/vse/vse_include_visu"+visu_visuid+".php?cmd="+encodeURIComponent(cmd)+"&vseid="+encodeURIComponent(vseID)+"&visuid="+encodeURIComponent(visu_visuid)+"&sid="+encodeURIComponent(sid)+"&vid="+encodeURIComponent(edomiVersion);
	req.open("POST",url,true);
	req.timeout=30000; //nach 30s: Timeout
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send("data1="+encodeURIComponent(JSON.stringify(json1))+"&data2="+encodeURIComponent(JSON.stringify(json2)));
}

function visuElement_modify(elementId,data) {
	if (elementId>0) {
		element_linked["e-"+elementId]=data;
	}
}

function visuElement_newGlobal(elementId,data) {
	if (elementId>0) {
		element_globals["e-"+elementId]=data;
	}
}

function visuElement_getGlobal(elementId) {
	if (elementId>0 && element_globals["e-"+elementId]) {
		return element_globals["e-"+elementId];
	}
	return false;
}

function visuElement_hasKo(veId,koId) {
	var obj=document.getElementById("e-"+veId);
	if (obj) {
		if (koId==1 && obj.dataset.koid1>0) {return true;}
		if (koId==2 && obj.dataset.koid2>0) {return true;}
		if (koId==3 && obj.dataset.koid3>0) {return true;}
	}
	return false;
}

function visuElement_hasCommands(veId) {
	var obj=document.getElementById("e-"+veId);
	if (obj && obj.dataset.hascommands==1) {return true;}
	return false;
}

function visuElement_getText(veId) {
	var obj=document.getElementById("e-"+veId);
	if (obj) {return obj.dataset.d_text;}
	return "";
}

function visuElement_getCaption(veId) {
	var obj=document.getElementById("e-"+veId);
	if (obj) {
		var tmp=obj.dataset.d_text.match(/\{(.*?)\}/);
		if (tmp) {
			return tmp[0];
		} else {
			return obj.dataset.d_text;
		}
	}
	return "";
}

function visuElement_setTimeout(veId,timerId,timeout,func) {
	var ts=Date.now();
	visu_timer["ve-"+veId+"-"+timerId]={ve:true,veid:veId,timerid:timerId,timeout:timeout,func:func,ts:ts};
}

function visuElement_clearTimeout(veId,timerId) {
	delete visu_timer["ve-"+veId+"-"+timerId];
}

function setKoValue(koId,value) {
	if (koId>0 && visu_socket) {
		visu_socket.request_setKoValue(koId,value);
	}
}

function visuElement_setKoValue(elementId,id,value) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		if (id==1 && obj.dataset.koid1>0) {setKoValue(obj.dataset.koid1,value); return true;}
		if (id==2 && obj.dataset.koid2>0) {setKoValue(obj.dataset.koid2,value); return true;}
		if (id==3 && obj.dataset.koid3>0) {setKoValue(obj.dataset.koid3,value); return true;}
	}
	return false;
}

function visuElement_getKoValue(elementId,id) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		if (id==1 && obj.dataset.koid1>0) {return obj.dataset.value1;}
		if (id==2 && obj.dataset.koid2>0) {return obj.dataset.value2;}
		if (id==3 && obj.dataset.koid3>0) {return obj.dataset.value3;}
	}
	return "";
}

function visuElement_doCommands(elementId,modeCmd,modePage,modePopup,modeCurPopup) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {	
		if (modeCmd===undefined) {modeCmd=true;}
		if (modePage===undefined) {modePage=true;}
		if (modePopup===undefined) {modePopup=true;}
		if (modeCurPopup===undefined) {modeCurPopup=true;}
			
		if (modeCmd && obj.dataset.hascmd>0) { 				//Befehle ausführen
			if (visu_socket) {visu_socket.request_execCmdList(obj.dataset.id);}
		}

		if (modePage && obj.dataset.gotopageid>0) { 		//Seite aufrufen
			openPage(obj.dataset.gotopageid,true);
		}
		
		if (modePopup && obj.dataset.closepopupid>0) { 		//Popup schliessen
			closePopupById(obj.dataset.closepopupid,true);
		}
		
		if (modeCurPopup && obj.dataset.closepopup>0) { 	//aktuelles Popup schliessen
			closePopup(obj.parentNode.dataset.winid,true);
		}
	}
}

function visuElement_openPage(pageId) {
	if (pageId>0) {	
		openPage(pageId);
	}
}

function visuElement_indicate(obj) {
	if (obj) {
		obj.className="indicateClick";
		visu_setTimeout(obj.id,200,function(id){
			if (document.getElementById(id)) {document.getElementById(id).className="";}
		});
	}
}

function visuElement_onClick(obj,functionShort,indicateShort,functionLong,indicateLong,animationLong,forceDownEvent) {
	if (obj) {
		if (indicateShort===undefined) {indicateShort=true;}
		if (functionLong===undefined) {functionLong=false;}
		if (indicateLong===undefined) {indicateLong=true;}
		if (animationLong===undefined) {animationLong=true;}
		if (forceDownEvent===undefined) {forceDownEvent=false;}

		//Visuelement-ID ermitteln
		var objId=obj.id;
		var veId=parseInt(objId.split("-",2)[1]);

		if (functionShort && !functionLong) {
			obj.addEventListener(((forceDownEvent)?downEvent:clickEvent),function(){
				if (checkClick()) {
					if (indicateShort) {visuElement_indicate(document.getElementById(objId));}
					functionShort(veId,objId);
				}
			},false);
			
		} else if (functionLong) {
			obj.addEventListener(downEvent,function(){
				if (checkClick()) {
					longClick(veId,objId,functionShort,functionLong,indicateShort,indicateLong,animationLong);
				}
			},false);
		}
	}
}

function visuElement_onDown(obj,functionShort,indicateShort) {
	visuElement_onClick(obj,functionShort,indicateShort,false,false,false,true);
}

function visuElement_onDrag(obj,koIdInit,koId,interval,noDownEvent) {
	if (obj) {
		//Visuelement-Object ermitteln (parent quasi)
		var veObj=document.getElementById(obj.id.split("-",2).join("-"));
	}

	if (obj && veObj) {
		if (noDownEvent===true) {
			onDown();
		} else {
			obj.addEventListener(downEvent,function(){
				if (checkClick()){onDown();}
			},false);
		}
	}

	function onDown() {
		if (veObj.dataset.controltyp>0) {
			var elementId=veObj.dataset.id;

			var funcMove=function(){
				var event=window.event;
				if (event) {
					event.stopPropagation();
					event.preventDefault();
				}
				var r=window["VSE_"+veObj.dataset.controltyp+"_DRAGMOVE"](elementId,obj); 
				if (r!==undefined){
					if (r===false) {
						dragCancel(veObj);
					} else {
						dragUpdate(r);
					}
				}
			}
			
			var funcEnd=function(){
				var r=window["VSE_"+veObj.dataset.controltyp+"_DRAGEND"](elementId,obj,element_drag.kovalue);
				dragEnd(r);
			}

			//Drag-Start
			drag_removeAllEventListeners();
			var event=window.event;
			if (event) {
				event.stopPropagation();
				event.preventDefault();
			}
			var koValueInit=window["VSE_"+veObj.dataset.controltyp+"_DRAGSTART"](elementId,obj);
			if (koValueInit===false) {return;}

			var koValue="";
			if (koIdInit==1) {	
				var koValue=visuElement_getKoValue(elementId,1);
			} else if (koIdInit==2) {
				var koValue=visuElement_getKoValue(elementId,2);
			}
	
			setKoValuePeriodical_reset(koValue);
			if (koValueInit!==undefined) {element_drag.kovalue=koValue;} //ggf. KO-Initalwert aus Rückgabe von VSE#_DRAGSTART() setzen

			element_drag.id=elementId;
			element_drag.obj=obj;
			element_drag.veobj=veObj;
			element_drag.interval=interval;
			if (koId==1) {
				element_drag.koid=parseInt(veObj.dataset.koid1);
				element_drag.livekoid=parseInt(veObj.dataset.koid1);
			} else if (koId==2) {
				element_drag.koid=parseInt(veObj.dataset.koid2);
				element_drag.livekoid=parseInt(veObj.dataset.koid1);
			} else {
				element_drag.koid=0;
				element_drag.livekoid=0;
			}
							
			funcMove();
			if (element_drag.interval>=0 && element_drag.koid>0) {
				controlREFRESH(document.getElementById("e-"+element_drag.id),false,false,false,element_drag.id,element_drag.veobj.dataset.value1);
			}

			drag_addEventListener(moveEvent,funcMove);
			drag_addEventListener(upEvent,funcEnd);		
			if (cancelEvent) {drag_addEventListener(cancelEvent,funcEnd);}
		}
	}
}

function visuElement_dragStart(obj,koIdInit,koId,interval) {
	visuElement_onDrag(obj,koIdInit,koId,interval,true);
}

function visuElement_mapDragValueReset(koValue) {
	element_drag.initKovalue=koValue;
}

function visuElement_mapDragValue(pos,koValue,format,coord,modus,minX,maxX,minY,maxY,minA,maxA,vlist,vstep,vsnap) {
	return drag_mapDragValue(pos,koValue,format,coord,modus,minX,maxX,minY,maxY,minA,maxA,vlist,vstep,vsnap);
}

function visuElement_setDragKoValue(koValue) {
	if (element_drag.id>0) {
		element_drag.kovalue=koValue;
		return true;
	}
	return false;
}

function visuElement_getDragKoValue() {
	if (element_drag.id>0) {
		return element_drag.kovalue;
	}
	return "";
}

function visuElement_getMousePosition(parent,obj,flip) {
	var event=window.event;
	if (parent && obj) {
		var x=0;
		var y=0;
		var w=obj.offsetWidth;
		var h=obj.offsetHeight;
		var bbox=obj.getBoundingClientRect();
		if (event) {
			if (visu_touchMode && event.touches) {
				x=event.touches[0].clientX-parseInt(bbox.left);
				y=event.touches[0].clientY-parseInt(bbox.top);
			} else {
				x=event.clientX-parseInt(bbox.left);
				y=event.clientY-parseInt(bbox.top);
			}
		}

		//Maus-Position ggf. relativ zum Visuelement rotieren
		var a=visuElement_getAngle(parent);
		if (a!=0) {
			x-=bbox.width/2;
			y-=bbox.height/2;
			var tmpx=(Math.cos(-a/180*Math.PI)*x-Math.sin(-a/180*Math.PI)*y)+w/2;
			var tmpy=(Math.sin(-a/180*Math.PI)*x+Math.cos(-a/180*Math.PI)*y)+h/2;
			x=tmpx;
			y=tmpy;		
		}

		if (flip&1) {x=w-x;}
		if (flip&2) {y=h-y;}

		return {x:x,y:y,w:w,h:h,a:a};
	}
	return false;
}

function visuElement_getAbsoluteChildPosition(objParent,objChild) {
	var a=visuElement_getAngle(objParent);
	var x=objParent.offsetLeft;
	var y=objParent.offsetTop;
	var w=objParent.offsetWidth;
	var h=objParent.offsetHeight;
	var x2=objParent.clientLeft;
	var y2=objParent.clientTop;
	var w2=objChild.offsetWidth;
	var h2=objChild.offsetHeight;
	var p0=math_rotatePoint(w/2,h/2,x2,y2,a,x,y);
	var p1=math_rotatePoint(w/2,h/2,x2+w2/2,y2+h2/2,a,x,y);
	return {x0:p0.x,y0:p0.y,xm:p1.x,ym:p1.y};
}

function visuElement_formGetValues(elementId) {
	var n=new Array();
	var tmp=document.getElementById("e-"+elementId).querySelectorAll("[data-type]");
	for (var t=0; t<tmp.length; t++) {
		if (tmp[t].id) {
			var id=tmp[t].id.split("-")[2].replace("formobj","");
			if (tmp[t].dataset.type==1) {n[id]=stringCleanup(tmp[t].value);}			//Input (Text/Hidden)
			if (tmp[t].dataset.type==4) {n[id]=stringCleanup(tmp[t].dataset.value);}	//checkmulti
			if (tmp[t].dataset.type==5) {n[id]=stringCleanup(tmp[t].dataset.value);}	//check
			if (tmp[t].dataset.type==6) {n[id]=stringCleanup(tmp[t].dataset.value);}	//select
		}
	}
	return n;
}

function visuElement_formSetControl(elementId,id,option,value) {
	var obj=document.getElementById("e-"+elementId+"-formobj"+id);
	if (obj && obj.id) {
		if (obj.dataset.type==1) {	//Input (Text/Hidden)
			obj.placeholder=option;
			obj.value=value;
		}							
		if (obj.dataset.type==4) {	//checkmulti
			obj.dataset.list=option;
			obj.dataset.value=value;
			checkboxInit(obj);
		}			
		if (obj.dataset.type==5) {	//check
			obj.dataset.list=option+"|"+option;
			obj.dataset.value=value;
			checkboxInit(obj);
		}	
		if (obj.dataset.type==6) {	//select
			obj.dataset.list=option;
			obj.dataset.value=value;
			selectboxInit(obj);
		}				
	}
}

function visuElement_formNewInput(elementId,id,len,style) {
	if (style===undefined) {style="";}
	return "<input id='e-"+elementId+"-formobj"+id+"' type='text' data-type='1' maxlength='"+len+"' placeholder='' value='' class='controlInput' style='white-space:nowrap; "+style+"'>";	
}

function visuElement_formNewCheck(elementId,id,style) {
	if (style===undefined) {style="";}
	return "<div class='controlCheckbox' id='e-"+elementId+"-formobj"+id+"' data-type='5' data-list='' data-value='' style='width:100%; white-space:nowrap; "+style+"'></div>";
}

function visuElement_formNewCheckmulti(elementId,id,style) {
	if (style===undefined) {style="";}
	return "<div class='controlCheckbox' id='e-"+elementId+"-formobj"+id+"' data-type='4' data-list='' data-value='' style='width:100%; white-space:nowrap; "+style+"'></div>";
}

function visuElement_formNewSelect(elementId,id,style) {
	if (style===undefined) {style="";}
	return "<div class='controlSelectbox' id='e-"+elementId+"-formobj"+id+"' data-type='6' data-list='' data-value='' style='width:100%; white-space:nowrap; "+style+"'></div>";
}
