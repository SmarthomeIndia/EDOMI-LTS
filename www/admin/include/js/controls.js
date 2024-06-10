/* 
*/ 
/*
============================================================================
Controls (alle Apps)
============================================================================
*/

function controlGetFormData(formId,info) {
	//bildet aus allen Controls in dem "Formular" formId einen String ("1;2;3;...;")
	//die Controls müssen alle eine ID in der Art "winId-fd#" haben, wobei # eine laufende Nummer ist (fd1,fd2,...)
	//die Reihenfolge ist egal, aus fd1=1,fd3=3,fd2=2 wird "1;2;3;" (NICHT "1;3;2;")
	//fehlende Nummern werden im String aufgefüllt: aus fd1=1,fd2=2,fd4=4 wird "1;2;;4;"
	//info (optional): true=dataset.info wird vor(!) value eingefügt (die Separierung muss individuell implementiert werden)
	var n=new Array();
	if (info===true) {var n2=new Array();}
	var tmp=document.getElementById(formId).querySelectorAll("[data-type]");
	for (var t=0; t<tmp.length; t++) {
		var type=tmp[t].dataset.type;
		if (tmp[t].id) {
			var id=tmp[t].id.split("-")[1].replace("fd","");

			//0: Dummy-Control (dient nur der Übertragung per Formular)
			if (type==0)  {n[id]=stringCleanup(tmp[t].dataset.value);}		//Dummy-Control
	
			//1..999: Eigene Input-Controls (ohne DB usw.)
			//---------------------------------------------------------------------------------
			if (type==1)  {n[id]=stringCleanup(tmp[t].value);}				//Input/Textarea
			if (type==4)  {n[id]=stringCleanup(tmp[t].dataset.value);}		//Checkbox (Multistate)
			if (type==5)  {n[id]=stringCleanup(tmp[t].dataset.value);}		//Checkbox
			if (type==6)  {n[id]=stringCleanup(tmp[t].dataset.value);}		//Selectbox
			if (type==9)  {n[id]=stringCleanup(tmp[t].dataset.value);}		//Folder-Select
			//Controls für VSE-Properties
			//---------------------------------------------------------------------------------
			if (type==10) {n[id]=stringCleanup(tmp[t].value);}				//Input/Textarea
			if (type==11) {n[id]=stringCleanup(tmp[t].dataset.value);}		//Checkbox (Multistate)
			if (type==12) {n[id]=stringCleanup(tmp[t].dataset.value);}		//Checkbox
			if (type==13) {n[id]=stringCleanup(tmp[t].dataset.value);}		//Selectbox
	
			//1000: editRoot-Verweise
			//---------------------------------------------------------------------------------
			if (type==1000) {n[id]=stringCleanup(tmp[t].dataset.value);}
						
			if (info===true) {
				if (tmp[t].dataset.info) {n2[id]=stringCleanup(tmp[t].dataset.info);}			
			}
		}
	}
	
	var r="";
	for (var t=0; t<n.length; t++) {
		if (n[t]==undefined) {n[t]="";}
		if (info===true) {
			r+=n2[t]+n[t]+AJAX_SEPARATOR1;
		} else {
			r+=n[t]+AJAX_SEPARATOR1;
		}
	}
	return r;
}

function controlInitAll(parentId) {
	//Initialisiert alle Controls, die Childs von parentId sind (z.B. in einem Formular)
	if (document.getElementById(parentId)) {
		var tmp=document.getElementById(parentId).querySelectorAll("[data-type]");

		//bulk-Verarbeitung (Ajax-Controls)
		var r="";
		for (var t=0; t<tmp.length; t++) {
			if (tmp[t].dataset.type>0)  {r+=controlInit(tmp[t].id,true);}
		}
		if (r!="") {ajax("initControls",1000,"",r,"");}
	}
}

function controlInit(senderId,bulk) {
	//Initialisiert das Control senderId
	//senderId: das aufzubauende Control
	//bulk (OPTIONAL): true=Ajax-Requests gesammelt zurückgeben
	var senderObj=document.getElementById(senderId);
	var r="";
	if (bulk!==true) {bulk=false;}

	//1..999: Eigene Input-Controls (ohne DB usw.)
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==5 || senderObj.dataset.type==4 || senderObj.dataset.type==11 || senderObj.dataset.type==12) { //Checkbox
		if (senderObj.dataset.type==4 || senderObj.dataset.type==11) {
			var list=senderObj.dataset.list.split("|");
			senderObj.innerHTML=((list[senderObj.dataset.value]!="")?list[senderObj.dataset.value]:"&nbsp;");
		}
		if (senderObj.dataset.value>=1) {
			senderObj.style.background="#80e000";
			senderObj.style.color="";
		} else {
			senderObj.style.background="#d9d9d9";
			senderObj.style.color="#909090";
		}
		if (!senderObj.getAttribute("onMouseDown")) { //nur zur Sicherheit...
			senderObj.setAttribute("onMouseDown","controlClick('"+senderId+"');");
		}
	}

	if (senderObj.dataset.type==6) { //Selectbox
		var n="";
		var id=senderObj.dataset.value;
		var list=senderObj.dataset.list.split(";");
		var options=senderObj.dataset.options;

		controlChange(senderId);

		//Default-Auswahl (1. Item) setzen, wenn kein passender Eintrag vorhanden ist
		var tmp=false;
		for (var t=0;t<(list.length-1);t++) {
			var item=list[t].split("|");
			if (id==item[0]) {
				tmp=true;
				break;
			}
		}
		if (tmp===false) {
			id=list[0].split("|")[0];
			senderObj.dataset.value=id;
		}

		n+="<select onChange='document.getElementById(\""+senderId+"\").dataset.value=this.value; controlChange(\""+senderId+"\");' class='control6select'>";
		for (var t=0;t<(list.length-1);t++) {

			var item=list[t].split("|");
			item[1]=item[1].replace(/</g,"&lt;");
			item[1]=item[1].replace(/>/g,"&gt;");

			if (id==item[0]) {
				n+="<option value='"+item[0]+"' selected>"+item[1]+"</option>";
			} else {
				n+="<option value='"+item[0]+"'>"+item[1]+"</option>";
			}
		}
		n+="</select>";
		senderObj.innerHTML=n;
	}

	if (senderObj.dataset.type==13) { //VSE-Selectbox
		var n="";
		var id=senderObj.dataset.value;
		var list=senderObj.dataset.list.split("|");
		var options=senderObj.dataset.options;

		controlChange(senderId);

		//Default-Auswahl (1. Item) setzen, wenn kein passender Eintrag vorhanden ist
		var tmp=false;
		for (var t=0;t<list.length;t++) {
			var i=list[t].indexOf('#');
			var tmp=[list[t].slice(0,i),list[t].slice(i+1)];
			if (id==tmp[0]) {
				tmp=true;
				break;
			}
		}
		if (tmp===false) {
			var i=list[0].indexOf('#');
			var tmp=[list[0].slice(0,i),list[0].slice(i+1)];
			id=tmp[0];
			senderObj.dataset.value=id;
		}

		n+="<select onChange='document.getElementById(\""+senderId+"\").dataset.value=this.value; controlChange(\""+senderId+"\");' class='control6select'>";
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
		senderObj.innerHTML=n;
	}

	//1000: editRoot-Verweise
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==1000) {
		if (bulk) {
			r="initControl-1000"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.root+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
			senderObj.setAttribute("onMouseDown","controlClick('"+senderId+"');");
		} else {
			ajax("initControl-1000",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.root+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+"","");
			senderObj.setAttribute("onMouseDown","controlClick('"+senderId+"');");
		}
	}

	//1001..9999: Spezielle Controls (z.B. LogicCmd, Listen-Items, etc.)
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==1001) { //Szenen-Liste
		if (bulk) {
			r="initControl-1001"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1001",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid,"");
		}
	}
	if (senderObj.dataset.type==1002) { //VisuElement-Edit
		if (bulk) {
			r="initControl-1002"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1002",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options,"");
		}
	}
	if (senderObj.dataset.type==1003) { //Style-Liste (Visu)
		if (bulk) {
			r="initControl-1003"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1003",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid,"");
		}
	}
	if (senderObj.dataset.type==1007) { //Befehle-Liste
		if (bulk) {
			r="initControl-1007"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1007",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db,"");
		}
	}
	if (senderObj.dataset.type==1008) { //Diagramm-Liste
		if (bulk) {
			r="initControl-1008"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1008",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db,"");
		}
	}
	if (senderObj.dataset.type==1009) { //AWS-Liste
		if (bulk) {
			r="initControl-1009"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1009",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db,"");
		}
	}
	if (senderObj.dataset.type==1017) { //VisuUser-Liste
		if (bulk) {
			r="initControl-1017"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1017",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid,"");
		}
	}
	if (senderObj.dataset.type==1019) { //ZSU-Daten
		if (bulk) {
			r="initControl-1019"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1019",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db,"");
		}
	}
	if (senderObj.dataset.type==1022) { //TSU-Daten
		if (bulk) {
			r="initControl-1022"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1022",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db,"");
		}
	}
	if (senderObj.dataset.type==1023) { //ZSU-Makrovorgaben
		if (bulk) {
			r="initControl-1023"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1023",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid,"");
		}
	}
	if (senderObj.dataset.type==1024) { //TSU-Makrovorgaben
		if (bulk) {
			r="initControl-1024"+AJAX_SEPARATOR1+senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+""+AJAX_SEPARATOR2;
		} else {
			ajax("initControl-1024",1000,"",senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid,"");
		}
	}
	return r;
}

function controlChange(senderId) {
	//ggf. diverse Änderungen beim onChange eines Controls
	//senderId: das angeklickte Control
	var senderObj=document.getElementById(senderId);

	if (senderObj.dataset.type==6) { //Selectbox
		//DOM-Elemente ggf. aus-/einblenden
		var id=senderObj.dataset.value;
		var list=senderObj.dataset.list.split(";");
		for (var t=0;t<(list.length-1);t++) {
			var radioId=list[t].split("|")[2];
			if (radioId) {document.getElementById(radioId).style.display="none";}
		}
		for (var t=0;t<(list.length-1);t++) {
			var radioId=list[t].split("|")[2];
			if (radioId) {
				if (id==list[t].split("|")[0]) {
					if (radioId.search("radiotr")>=0) {
						document.getElementById(radioId).style.display="table-row";
					} else {
						document.getElementById(radioId).style.display="inline";
					}
					break;
				}
			}
		}
	}
}

function controlClick(senderId) {
	//senderId: das angeklickte Control
	var event=window.event;
	if (event.button==0) {controlClickLeft(senderId);}
	if (event.button==2) {controlClickRight(senderId);}
	clickCancel();
}

function controlClickLeft(senderId) {
	var senderObj=document.getElementById(senderId);

	//1..999: Eigene Input-Controls (ohne DB usw.)
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==5 || senderObj.dataset.type==4) { //Checkbox
		if (senderObj.dataset.type==4) {
			var list=senderObj.dataset.list.split("|");
			senderObj.dataset.value++;
			if (senderObj.dataset.value>=(list.length-1)) {senderObj.dataset.value=0;}
		} else {
			if (senderObj.dataset.value==1) {senderObj.dataset.value=0;} else {senderObj.dataset.value=1;}
		}
		controlInit(senderObj.id);
	}

	if (senderObj.dataset.type==11 || senderObj.dataset.type==12) { //VSE-Checkbox
		if (senderObj.dataset.type==11) {
			var list=senderObj.dataset.list.split("|");
			senderObj.dataset.value++;
			if (senderObj.dataset.value>=list.length) {senderObj.dataset.value=0;}
		} else {
			if (senderObj.dataset.value==1) {senderObj.dataset.value=0;} else {senderObj.dataset.value=1;}
		}
		controlInit(senderObj.id);
	}

	//1000: editRoot-Verweise
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==1000) {openWindow(1000,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.root+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+""+AJAX_SEPARATOR1+"");}

	//1001..9999: Spezielle Controls (z.B. LogicCmd, Listen-Items, etc.)
	//---------------------------------------------------------------------------------
	if (senderObj.dataset.type==1001) {openWindow(1001,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //Szenen-Liste (controlList)
	if (senderObj.dataset.type==1002) {openWindow(1002,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options);} //VisuElement-Edit
	if (senderObj.dataset.type==1003) {openWindow(1003,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //Style-Liste (Visu)
	if (senderObj.dataset.type==1005) {openWindow(1005,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options);} //Werteingabe (z.B. Fixwert in Logic)
	if (senderObj.dataset.type==1006) {openWindow(1006,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db);} //Aktion: Befehle (für Logic)
	if (senderObj.dataset.type==1007) {openWindow(1007,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db);} //Befehle-Liste (controlList)
	if (senderObj.dataset.type==1008) {openWindow(1008,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid+AJAX_SEPARATOR1+senderObj.dataset.db);} //Diagramm-Liste (controlList)
	if (senderObj.dataset.type==1009) {openWindow(1009,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //AWS-Liste (controlList)
	if (senderObj.dataset.type==1017) {openWindow(1017,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //VisuUser-Liste (controlList)
	if (senderObj.dataset.type==1019) {openWindow(1019,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //ZSU-Daten (controlList)
	if (senderObj.dataset.type==1022) {openWindow(1022,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //TSU-Daten (controlList)
	if (senderObj.dataset.type==1023) {openWindow(1023,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //ZSU-Makrovorgaben
	if (senderObj.dataset.type==1024) {openWindow(1024,senderObj.id+AJAX_SEPARATOR1+senderObj.dataset.type+AJAX_SEPARATOR1+senderObj.dataset.value+AJAX_SEPARATOR1+senderObj.dataset.options+AJAX_SEPARATOR1+senderObj.dataset.itemid);} //TSU-Makrovorgaben
}

function controlClickRight(senderId) {
	var senderObj=document.getElementById(senderId);
	var winId=senderId.split("-")[0]; //für Kontextmenü

	//controlList? (dann die ID des angeklickten Items zusammenbasteln)
	var itemObj=document.getElementById(senderId+"-"+senderObj.dataset.itemid);
	if (itemObj) {
		if (itemObj.dataset.cm0 || itemObj.dataset.cm1 || itemObj.dataset.cm2 || itemObj.dataset.cm3) {
			var winId=senderId.split("-")[0];

			apps_contextMenu=new class_contextMenu(winId);
			if (itemObj.dataset.cm1) {apps_contextMenu.addItem("Nach oben rücken",itemObj.dataset.cm1);}
			if (itemObj.dataset.cm2) {apps_contextMenu.addItem("Nach unten rücken",itemObj.dataset.cm2);}
			if (itemObj.dataset.cm3) {apps_contextMenu.addItem("Duplizieren",itemObj.dataset.cm3);}
			if (itemObj.dataset.cm0) {
				apps_contextMenu.addHr();
				apps_contextMenu.addItem("Löschen",itemObj.dataset.cm0);
			}
			apps_contextMenu.show();
		}
	}
}

function controlReturn(winID,senderId,value) {
	//winID: winID des Popup-Fensters
	//senderId: (optional) Id des Controls, das das Popup aufgerufen hat
	//value: der zu übernehmende Wert
	var senderObj=document.getElementById(senderId);
	if (senderObj) {
		senderObj.dataset.value=value;
		if (!(senderObj.dataset.options && senderObj.dataset.options.search('refresh=0')>=0)) {controlInit(senderId);}
		if (senderObj.dataset.callback) {eval(senderObj.dataset.callback);}
	}
	if (document.getElementById(winID)) {closeWindow(winID)};
}

function controlCancel(winID,senderId) {
	//wie controlReturn, nur wird value nicht verändert (Control wird refreshed und callback2(!) ggf. aufgerufen)
	//winID: winID des Popup-Fensters
	//senderId: (optional) Id des Controls, das das Popup aufgerufen hat
	var senderObj=document.getElementById(senderId);
	if (senderObj) {
		if (!(senderObj.dataset.options && senderObj.dataset.options.search('refresh=0')>=0)) {controlInit(senderId);}
		if (senderObj.dataset.callback2) {eval(senderObj.dataset.callback2);}
	}
	if (document.getElementById(winID)) {closeWindow(winID)};
}

function class_contextMenu(winId) {
	var event=window.event;
	var objWindowContainer=document.getElementById("windowContainer");
	var objWindow=document.getElementById(winId);
	var x=(event.pageX-parseInt(objWindow.offsetLeft)-parseInt(objWindowContainer.offsetLeft))-15;
	var y=(event.pageY-parseInt(objWindow.offsetTop)-parseInt(objWindowContainer.offsetTop))-15;
	var that=this;
	var cm=document.getElementById(winId+"-contextmenu");
	var items=new Array();

	if (cm) {cm.parentNode.removeChild(cm);}
	cm=createNewDiv(winId,winId+"-contextmenu");
	cm.className="contextMenu";
	cm.setAttribute("onMouseLeave","apps_contextMenu.close();");
	cm.setAttribute("onClick","apps_contextMenu.close();");
	cm.style.display="none";

	this.addText=function(caption,inline) {
		items.push({typ:0,caption:caption,format:((inline>0)?inline:0)});
	}

	this.addItem=function(caption,link,inline) {
		items.push({typ:1,caption:caption,link:link,format:((inline>0)?inline:0)});
	}
	
	this.addHr=function() {
		items.push({typ:2});
	}

	this.addVr=function() {
		items.push({typ:3});
	}
	
	this.show=function() {
		var hasContent=false;
		for (var t=0;t<items.length;t++) {
			if (items[t].typ==0 || items[t].typ==1) {
				hasContent=true;
				break;
			}
		}
		if (cm && hasContent) {
			for (var t=0;t<items.length;t++) {
				if (items[t].typ==0) {
					obj=createNewDiv(cm.id);
					obj.className="contextMenuText";
					if (items[t].format==1) {obj.style.display="inline-block";}
					obj.innerHTML=items[t].caption;
					obj.setAttribute("onClick","clickCancel();");
				} else if (items[t].typ==1) {
					obj=createNewDiv(cm.id);
					obj.className="contextMenuItem";
					if (items[t].format==1) {obj.style.display="inline-block";}
					obj.innerHTML=items[t].caption;
					obj.setAttribute("onClick",items[t].link);
				} else if (items[t].typ==2) {
					var tmp=true;
					if (t==0) {tmp=false;} else if (items[t-1].typ==2) {tmp=false;}
					if (t==items.length-1) {tmp=false;}
					if (tmp) {
						obj=createNewDiv(cm.id);
						obj.className="contextMenuText";
						obj.innerHTML="<div style='width:100%; height:1px; background:#c0c0c0;'></div>";
						obj.setAttribute("onClick","clickCancel();");
					}
				} else if (items[t].typ==3) {
					obj=createNewDiv(cm.id);
					obj.style.display="inline-block";
					obj.style.padding="3px";
					obj.style.color="#c0c0c0";
					obj.innerHTML="|";
					obj.setAttribute("onClick","clickCancel();");
				}
			}

			var win=document.getElementById(winId);
			if (win) {
				cm.style.display="block";
				//ggf. ins Fenster einpassen
				var w=cm.offsetWidth;
				var h=cm.offsetHeight;
				var wWin=win.offsetWidth;
				var hWin=win.offsetHeight;
				if ((x+w)>wWin) {cm.style.left=(wWin-w)+"px";} else {cm.style.left=x+"px";}
				if ((y+h)>hWin) {cm.style.top=(hWin-h)+"px";} else {cm.style.top=y+"px";}
			} else {
				that.close();
			}
			
		} else {
			that.close();
		}
	}

	this.close=function() {
		if (cm) {
			cm.parentNode.removeChild(cm);
			cm=null;
		}
	}
}


/*
============================================================================
Alle Apps 
============================================================================
*/

function appAll_setAutofocus(parentId) {
	var obj=document.getElementById(parentId).querySelector("[autofocus]");
	if (obj) {obj.focus();}
}

function appAll_enableTabKey(obj) {
	var event=window.event;
	event.preventDefault();	
	var val=obj.value,
	start=obj.selectionStart,
	end=obj.selectionEnd;
	obj.value=val.substring(0,start)+'\t'+val.substring(end);
	obj.selectionStart=obj.selectionEnd=start+1;
	return false;
}

function appAll_menuSelect(winId,objId,selectedOnly) {
	if (!selectedOnly) {
		var menu=document.getElementById(winId+"-menuRoot");
		if (menu) {
			var tmp=menu.getElementsByTagName("div")
			for (var t=0; t<tmp.length; t++) {
				tmp[t].style.background="";
			}
		}
	}
	var obj=document.getElementById(objId);
	if (obj) {
		if (!selectedOnly) {
			obj.style.background="#80e000";
		} else {
			obj.style.color="#404040";
			obj.style.background="#e0e0e0";
		}
	}
}
