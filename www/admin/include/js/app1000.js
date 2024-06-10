/* 
*/ 
function app1000_menuClick(winId,objId,folderId) {
	var event=window.event;
	if (event.button==0) {app1000_menuClickLeft(winId,objId,folderId);}
//	if (event.button==2) {app1000_menuClickRight(winId,objId);}
	clickCancel();
}

function app1000_menuClickLeft(winId,objId,folderId) {
	app1000_resetMarkedFolder(winId);
	app1000_folderOpen(winId,folderId,true);
}

function app1000_columnContentClick(winId) {
	var event=window.event;
	if (event.button==0) {
		app1000_itemUnsetCursor(winId);
		app1000_elementSelectNone(winId);
	}
	clickCancel();
}

function app1000_folderOpen(winId,folderId,mode) {

	app1000_elementSelectNone(winId);
	app1000_itemSearchReset(winId,true)
	scrollToTop(winId+"-folderRoot");

	var dataArr=document.getElementById(winId+"-global").dataset.jsdata.split(AJAX_SEPARATOR1);
	dataArr[6]=folderId;
	if (mode===true) {
		ajax("initFolders","1000",winId,dataArr.join(AJAX_SEPARATOR1),"");
	} else {
		ajax("refreshFolders","1000",winId,dataArr.join(AJAX_SEPARATOR1),"");
	}
}

function app1000_folderCreate(winId,folderId,parentId,caption,parentMustExist,backLinkId,backLinkPath) {

	var id=folderId.split("_");
	
	if (document.getElementById(winId+"-f-"+parentId+"-c")) {
		//User-Ordner oder Sys-Ordner
		var folderTyp=(id[0]<1000)?1:0;

		var f_container=createNewDiv(winId+"-f-"+parentId+"-c",winId+"-div-"+folderId);

		//Folder erstellen
		var f_folder=createNewDiv(f_container.id,winId+"-f-"+folderId);
		
		//Folder-Content erstellen
		var f_content=createNewDiv(f_container.id,winId+"-f-"+folderId+"-c");
		f_content.style.display="none";
		
		f_folder.dataset.visible=0;
		
	} else {
		if (parentMustExist===true) {return false;}
		//Root-Ordner
		var folderTyp=2;
		
		var f_container=createNewDiv(winId+"-folderRoot",winId+"-div-"+folderId);

		//Folder erstellen
		var f_folder=createNewDiv(f_container.id,winId+"-f-"+folderId);

		//Folder-Content erstellen
		var f_content=createNewDiv(f_container.id,winId+"-f-"+folderId+"-c");
		f_content.style.display="block";

		f_folder.dataset.visible=1;
	}

	f_container.style.display="block";
	f_content.style.marginLeft="15px";		

	f_folder.className='columnContentList';

	f_folder.dataset.typ=folderTyp;	//0=UserFolder, 1=SysFolder, 2=RootFolder
	f_folder.dataset.selected=0;

	if (folderTyp==0) {
		f_folder.innerHTML="<img id='"+winId+"-img-"+folderId+"' src='../shared/img/folder1.png' width='12' height='12' valign='middle' style='margin:0;' draggable='false'> <span id='"+winId+"-cap-"+folderId+"'>"+caption+"</span>";
		f_folder.setAttribute('onMouseDown','app1000_folderClick("'+winId+'","'+f_folder.id+'");');
		f_folder.style.color="#0000ff";
	} else if (folderTyp==1) {
		f_folder.innerHTML="<img id='"+winId+"-img-"+folderId+"' onClick='app1000_folderOpen(\""+winId+"\",\""+folderId+"\");' src='../shared/img/folder1.png' width='12' height='12' valign='middle' style='margin:0;' draggable='false'> <span id='"+winId+"-cap-"+folderId+"'>"+caption+"</span>";
		f_folder.setAttribute('onMouseDown','app1000_folderClick("'+winId+'","'+f_folder.id+'");');
		f_folder.setAttribute('onDblClick','app1000_folderOpen("'+winId+'","'+folderId+'");');	//### optional (könnte kontraproduktiv sein, bzw. zu ungewolltem Öffnen führen)
		f_folder.style.lineHeight='22px';
	} else if (folderTyp==2) {
		if (backLinkId>0) {
			f_folder.innerHTML="<div class='app1000_back' onClick='app1000_folderOpen(\""+winId+"\",\""+backLinkId+"\");'>"+backLinkPath+"</div><span id='"+winId+"-cap-"+folderId+"'>"+caption+"</span>";
		} else {
			f_folder.innerHTML="<span id='"+winId+"-cap-"+folderId+"'>"+caption+"</span>";
		}
		f_folder.setAttribute('onMouseDown','app1000_folderClick("'+winId+'","'+f_folder.id+'");');
		f_folder.style.fontSize='12px';
		f_folder.style.lineHeight='24px';
		
		if (document.getElementById(winId).dataset.markedfolder=="") {document.getElementById(winId).dataset.markedfolder=f_folder.id;}
	}
}

function app1000_folderSaveExpanded(winId) {
	app1000_visibleFolders=new Array();
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-visible]");
	for (var t=0; t<tmp.length; t++) {
		if (tmp[t].dataset.visible==1) {app1000_visibleFolders.push(tmp[t].id);}
	}
}

function app1000_folderRestoreExpanded(winId) {
	for (var t=0; t<app1000_visibleFolders.length; t++) {
		app1000_folderCollapse(winId,app1000_visibleFolders[t],1);
	}
}



function app1000_rootFolderShowHint(winId,caption) {
	var folder=document.getElementById(winId+"-folderRoot").querySelector("[data-typ='2']");
	if (folder) {
		var obj=document.getElementById(folder.id+"-c");
		if (obj) {
			if (!obj.querySelector("[data-selected]")) {	//"selected" als Indikator für Item/Folder
				obj.innerHTML+="<br><div style='color:#808080; font-size:10px; width:90%;'><b>Dieser Systemordner ist leer.</b><br><br>"+caption+"</div>";
			}
		}
	}
}





function app1000_folderCollapse(winId,objId,mode) {
	//Ordner auf-/zuklappen (ausser Root)
	var obj=document.getElementById(objId);
	var objContent=document.getElementById(objId+"-c");
	if (obj && objContent && obj.dataset.typ!=2) {
		if (mode==2) {	//Toggeln
			if (obj.dataset.visible==0) {mode=1;} else {mode=0;}
		}
		if (mode==1 && obj.dataset.selected==0) {	//Aufklappen (sofern der Ordner nicht selektiert ist)
			objContent.style.display="block";
			obj.dataset.visible=1;
			document.getElementById(objId.replace("-f-","-img-")).src="../shared/img/folder1b.png";
		} else if (mode==0) {	//Zuklappen
			objContent.style.display="none";
			obj.dataset.visible=0;
			document.getElementById(objId.replace("-f-","-img-")).src="../shared/img/folder1.png";
		}
	}
}

function app1000_folderCollapseAll(winId) {
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-visible='1']");
	for (var t=0; t<tmp.length; t++) {
		app1000_folderCollapse(winId,tmp[t].id,0);
	}
}

function app1000_folderExpandToFolder(winId,objId,noScroll) {
	//Ordner aufklappen, einschl. aller übergeordneten Ordner
	var obj=document.getElementById(objId);
	if (obj && obj.dataset.typ<2) {
		app1000_folderCollapse(winId,obj.id,1)
	}
	while (obj && obj.dataset.typ<2) {
		var tmp=obj.parentNode.parentNode.id.split("-");
		obj=document.getElementById(winId+"-f-"+tmp[2]);
		app1000_folderCollapse(winId,obj.id,1);
	}
	if (noScroll!==true) {scrollToObject(winId+"-folderRoot",objId);}
}

function app1000_folderRestoreHistory(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	if (jsdata) {
		var dataArr=jsdata.split(AJAX_SEPARATOR1);
		var tmp=app1000_history["root"+dataArr[1]];
		if (tmp) {app1000_folderExpandToFolder(winId,winId+"-f-"+tmp);}
	}
}

function app1000_itemExpandToItem(winId,objId,noSetCursor) {
	//Cursor auf Item setzen (und: Ordner aufklappen, einschl. aller übergeordneten Ordner)
	var obj=document.getElementById(objId);
	if (obj) {
		var tmp=obj.parentNode.parentNode.id.split("-");
		app1000_folderExpandToFolder(winId,winId+"-f-"+tmp[2],true);
		if (noSetCursor!==true) {app1000_itemSetCursor(winId,objId);}
		scrollToObject(winId+"-folderRoot",objId);
	}
}





function app1000_itemCreate(winId,itemId,folderIdFull,caption) {
	var folder=document.getElementById(winId+"-f-"+folderIdFull+"-c");
	if (folder) {
		folderId=folderIdFull.split("_");
		var item=createNewDiv(folder.id,winId+"-i-"+folderId[0]+"-"+itemId);
		item.className='columnContentList';
		item.style.color='#000000';
	
		item.dataset.folderid=folderIdFull;	//die vollständige FolderId (bei virtuellen Ordnern z.B. "22_1")
		item.dataset.selected=0;
		item.dataset.isresult=0;

		item.innerHTML=caption;
		item.setAttribute('onMouseDown','app1000_itemClick("'+winId+'","'+winId+"-i-"+folderId[0]+"-"+itemId+'");');
		item.setAttribute('onDblClick','app1000_itemDblClick("'+winId+'","'+winId+"-i-"+folderId[0]+"-"+itemId+'");');
	}
}






function app1000_setMarkedFolder(winId,objId) {
	var obj=document.getElementById(objId);
	if (obj) {
	
		document.getElementById(winId+"-global").dataset.sysfolderid="";
		var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[id*='-f-']");
		for (var t=0; t<tmp.length; t++) {
			tmp[t].style.textDecoration="none";
		}
	
		obj.style.textDecoration="underline";
		document.getElementById(winId).dataset.markedfolder=objId;
	}
}

function app1000_setMarkedFolderFromItem(winId,objId) {
	var tmp=app1000_elementGetParentFolder(objId);
	if (tmp!==false) {
		app1000_setMarkedFolder(winId,tmp.id)
	}
}

function app1000_refreshMarkedFolder(winId) {
	objId=document.getElementById(winId).dataset.markedfolder;
	document.getElementById(winId+"-global").dataset.sysfolderid="";
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[id*='-f-']");
	for (var t=0; t<tmp.length; t++) {
		if (objId!="" && objId==tmp[t].id) {
			tmp[t].style.textDecoration="underline";
		} else {
			tmp[t].style.textDecoration="none";
		}
	}

}

function app1000_resetMarkedFolder(winId) {
	document.getElementById(winId).dataset.markedfolder="";
}







function app1000_folderClick(winId,objId) {
	var event=window.event;
	if (event.button==0) {app1000_folderClickLeft(winId,objId);}
	if (event.button==2) {app1000_folderClickRight(winId,objId);}
	clickCancel();
}

function app1000_folderClickLeft(winId,objId) {
	var event=window.event;
	if (event.shiftKey) {
		app1000_elementSelect(winId,objId,2);
	} else {
		app1000_folderCollapse(winId,objId,2);
		app1000_setMarkedFolder(winId,objId);
	}
}

function app1000_folderClickRight(winId,objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var jsdata=document.getElementById(winId+"-global").dataset.jsdata;

		var sysFolderId=app1000_elementGetSysfolderId(winId,objId);
		var	selected_sysFolderId=document.getElementById(winId+"-global").dataset.sysfolderid;
		var selected_Count=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']").length;
		var folderId=objId.split("-")[2];

		apps_contextMenu=new class_contextMenu(winId);
		ajax("folderContextMenu","1000",winId,jsdata,objId+AJAX_SEPARATOR1+sysFolderId+AJAX_SEPARATOR1+selected_sysFolderId+AJAX_SEPARATOR1+folderId+AJAX_SEPARATOR1+selected_Count);
	}
}




function app1000_elementGetType(objId) {
	var tmp=objId.split("-");
	if (tmp[1]=="f") {return 1;}
	if (tmp[1]=="i") {return 2;}
	return false;
}

function app1000_elementGetSysfolderId(winId,objId) {
	//Übergeordeten Sys-Folder (typ=1/2) eines Items/Folders suchen (wenn objId bereits ein Sys-Folder ist, wird dieser zurückgegeben)
	//return: folderId (ggf. auch vOrdner, z.B. "22_1"), oder false=nicht gefunden

	var obj=document.getElementById(objId);

	if (obj && app1000_elementGetType(objId)==1) {
		//ist objId bereits ein Sys-Folder?
		if (obj.dataset.typ>0) {
			return obj.id.split("-")[2];
		}
	}
	
	while (obj) {
		var tmp=obj.parentNode.parentNode.id.split("-");
		obj=document.getElementById(winId+"-f-"+tmp[2]);
		if (obj) {
			if (obj.dataset.typ>0) {
				return obj.id.split("-")[2];
			}
		} else {
			return false;
		}
	}
	return false;
}

function app1000_elementGetParentFolder(objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var elementTyp=app1000_elementGetType(objId);
		if (elementTyp==1) {
			var parent=obj.parentNode.parentNode;
		} else if (elementTyp==2) {
			var parent=obj.parentNode;
		} else {
			return false;
		}
		if (parent) {
			var tmp=parent.id.split("-");
			if (tmp[1]=="f" && tmp[3]=="c") {
				var r=document.getElementById(tmp[0]+"-"+tmp[1]+"-"+tmp[2]);
				if (r) {return r;}
			}
		}
	}
	return false;
}



function app1000_elementSelect(winId,objId,mode) {
	//Elemente (Folder+Items) markieren/demarkieren und alle markierten Elemente hervorheben
	//mode: 0=unselect, 1=select, 2=toggle

	var obj=document.getElementById(objId);
	var optionTyp=document.getElementById(winId+"-global").dataset.optiontyp;
	var	cur_sysFolderId=document.getElementById(winId+"-global").dataset.sysfolderid;

	if (obj && optionTyp!=4) {

		var elementTyp=app1000_elementGetType(objId);

		if (elementTyp==2 || (elementTyp==1 && obj.dataset.typ==0)) {	//Item oder Userfolder
			var sysFolderId=app1000_elementGetSysfolderId(winId,objId);

			//Wechsel des Sysfolders?
			if (cur_sysFolderId!=sysFolderId) {
				app1000_elementSelectNone(winId);
				document.getElementById(winId+"-global").dataset.sysfolderid=sysFolderId;
			}

			if (mode==2) {	//toggeln
				if (obj.dataset.selected==0) {mode=1;} else {mode=0;}
			}
			if (mode==1) {	//select
				app1000_itemUnsetCursor(winId);
				if (elementTyp==1) {
					//ein Ordner wird selektiert => alle Inhalte deselektieren und zuklappen
					app1000_elementSelectNoneInFolder(winId,obj.id);
					app1000_folderCollapse(winId,obj.id,0);
				}
				obj.dataset.selected=1;
				obj.style.background=apps_colorSelected;
			} else if (mode==0) {	//unselect
				obj.dataset.selected=0;
				obj.style.background="";
			}
		}
	}
}

function app1000_elementSelectAllInFolder(winId,objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		app1000_elementSelectNoneInFolder(winId,objId);
		if (obj.dataset.selected==1) {return;}
		var tmp=document.getElementById(objId+"-c").querySelectorAll("[data-selected='0']");
		for (var t=0; t<tmp.length; t++) {
			//nur Elemente im Root des Zielordners selektieren
			var objParent=app1000_elementGetParentFolder(tmp[t].id);
			if (objParent.id==objId) {
				app1000_elementSelect(winId,tmp[t].id,1);
			}
		}
		app1000_folderCollapse(winId,objId,1);
	}
}

function app1000_elementSelectNoneInFolder(winId,objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		if (obj.dataset.selected==1) {return;}
		var tmp=document.getElementById(objId+"-c").querySelectorAll("[data-selected='1']");
		for (var t=0; t<tmp.length; t++) {
			app1000_elementSelect(winId,tmp[t].id,0);
		}
	}
}

function app1000_elementSelectNone(winId) {
	document.getElementById(winId+"-global").dataset.sysfolderid="";
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected]");
	for (var t=0; t<tmp.length; t++) {
		tmp[t].dataset.selected=0;
		tmp[t].style.background="";
	}
}

function app1000_elementSelectFromList(winId,list) {
	app1000_elementSelectNone(winId);
	var element=list.split(";");
	for (var t=0; t<element.length; t++) {
		app1000_elementSelect(winId,element[t],1);
		app1000_itemExpandToItem(winId,element[t],true);
	}
}

function app1000_elementGetSelected(winId) {
	var r=new Array();
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']");
	for (var t=0; t<tmp.length; t++) {
		r.push(tmp[t].id);
	}
	return r;
}





function app1000_elementDuplicateSelected(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var data="";
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']");
	for (var t=0; t<tmp.length; t++) {data+=tmp[t].id+";";}
	if (data!="") {ajax("duplicateSelected","1000",winId,jsdata,data);}
}

function app1000_elementPasteSelected(winId,folderId,mode) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var data="";
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']");
	for (var t=0; t<tmp.length; t++) {data+=tmp[t].id+";";}
	if (data!="") {
		if (mode==1) {
			ajax("moveSelected","1000",winId,jsdata,data+AJAX_SEPARATOR1+folderId);
		} else {
			ajax("copySelected","1000",winId,jsdata,data+AJAX_SEPARATOR1+folderId);
		}
	}
}

function app1000_elementDeleteSelected(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var data="";
	var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']");
	for (var t=0; t<tmp.length; t++) {data+=tmp[t].id+";";}
	if (data!="") {ajaxConfirmSecure("Sollen wirklich alle markierten Elemente/Ordner ("+tmp.length+") gelöscht werden?<br><br>Ggf. werden alle Verweise auf diese Elemente zurückgesetzt oder gelöscht!","deleteSelected","1000",winId,jsdata,data,"","Löschen");}
}







function app1000_buttonEditClick(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var cursor=document.getElementById(winId+"-global").dataset.cursor;
	var obj=document.getElementById(cursor);
	if (obj && jsdata) {
		ajax("editItem","1000",winId,jsdata,obj.id);
	} else {
		shakeObj(winId);
	}
}

function app1000_buttonNewClick(winId) {
	var objId=document.getElementById(winId).dataset.markedfolder;
	if (objId!="") {
		var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
		var sysFolderId=app1000_elementGetSysfolderId(winId,objId);
		var	selected_sysFolderId=document.getElementById(winId+"-global").dataset.sysfolderid;
		var selected_Count=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']").length;
		var folderId=objId.split("-")[2];
		ajax("folderCreateElement","1000",winId,jsdata,objId+AJAX_SEPARATOR1+sysFolderId+AJAX_SEPARATOR1+selected_sysFolderId+AJAX_SEPARATOR1+folderId+AJAX_SEPARATOR1+selected_Count);

	} else {
		shakeObj(winId);
	}
}

function app1000_buttonReturnClick(winId) {
	var cursor=document.getElementById(winId+"-global").dataset.cursor;
	var obj=document.getElementById(cursor);
	if (obj) {
		app1000_itemReturnValue(winId,obj.id)
	} else {
		shakeObj(winId);
	}
}






function app1000_itemShowCursor(winId,mode) {
	var cursor=document.getElementById(winId+"-global").dataset.cursor;
	var obj=document.getElementById(cursor);
	if (obj) {
		if (mode==1) {
			obj.style.background="#80e000";
		} else {
			obj.style.background="";
		}
	}
}

function app1000_itemSetCursor(winId,objId) {
	//Item: alle Markierungen löschen und Cursor auf das aktuelle Element setzen
	app1000_elementSelectNone(winId);
	document.getElementById(winId+"-global").dataset.cursor=objId;
	app1000_itemShowCursor(winId,1);
	app1000_setMarkedFolderFromItem(winId,objId);
}

function app1000_itemUnsetCursor(winId) {
	//Item: Cursor löschen
	document.getElementById(winId+"-global").dataset.cursor="";
	app1000_itemShowCursor(winId,0);
}





function app1000_itemSearch(winId) {
	document.getElementById(winId+"-searchInput").style.background="";
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var value=stringCleanup(document.getElementById(winId+"-searchInput").value);
	if (value!="") {
		ajax("searchItem","1000",winId,jsdata,value);
	}
}

function app1000_itemSearchKey(winId) {
	var event=window.event;
	if (event.keyCode==13) {app1000_itemSearch(winId);}
	if (event.keyCode==37 || event.keyCode==39 || event.keyCode==38 || event.keyCode==40) {
		if (document.getElementById(winId+"-global").dataset.searchresult!="") {
			if (event.keyCode==37 || event.keyCode==38) {app1000_itemSearchMove(winId,-1);}
			if (event.keyCode==39 || event.keyCode==40) {app1000_itemSearchMove(winId,1);}
		}
	}
}

function app1000_itemSearchMove(winId,mode,newResult) {

	var result=document.getElementById(winId+"-global").dataset.searchresult.split(";");
	var resultMax=result.length-1;

	if (newResult) {
		var cursor=0;

		//Array umsortieren (an die Reihenfolge im DOM anpassen)
		var result=document.getElementById(winId+"-global").dataset.searchresult.split(";");
		if (resultMax>0) {
			for (var t=0;t<resultMax;t++) {
				var obj=document.getElementById(winId+"-i-"+result[t]);
				if (obj) {obj.dataset.isresult=1;}
			}
			
			var resultSorted="";
			var tmp=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-isresult='1']");
			for (var t=0; t<tmp.length; t++) {
				var tmpSplit=tmp[t].id.split("-");
				resultSorted+=tmpSplit[2]+"-"+tmpSplit[3]+";";
				var obj=document.getElementById(tmp[t].id);
				if (obj) {obj.dataset.isresult=0;}
			}
			document.getElementById(winId+"-global").dataset.searchresult=resultSorted;
		}

		result=document.getElementById(winId+"-global").dataset.searchresult.split(";");
		resultMax=result.length-1;

	} else {
		var cursor=document.getElementById(winId+"-global").dataset.searchcursor;
	}
	
	if (resultMax>0) {
		if (!newResult) {
			if (mode==0) {
				if (app1000_itemSearchSelectAll(winId)) {
					cursor=cursor=resultMax-1;
				} else {
					cursor=parseInt(cursor);
					shakeObj(winId);
				}
			} else {
				if (mode==-1) {cursor=parseInt(cursor)-1;}
				if (mode==1) {cursor=parseInt(cursor)+1;}
			}
		}
		
		if (mode!=0) {
/*
###
wenn ein Item zwischenzeitlich gelöscht wurde, ist dieses Item natürlich nicht mehr verfügbar und alle Ordner sind eingeklappt
=> im Prinzip könnte das hier geprüft werden (gucken ob das obj im DOM ist) und ggf. per cursor überspringen...
---> ist aber nicht so wichtig... Außerdem: Wenn nur 1 Item im Result ist und dieses dann gelöscht wird....
*/


			if (cursor>=resultMax) {cursor=0;}
			if (cursor<0) {cursor=resultMax-1;}
			app1000_folderCollapseAll(winId);	//optional (erleichtert i.d.R. die Orientierung, da hierdurch stets nur der Ordner mit dem aktuellen Treffer geöffnet ist)
			app1000_itemExpandToItem(winId,winId+"-i-"+result[cursor]);
		}
		
		if (resultMax==1) {
			var tmp=100;
		} else {
			var tmp=((cursor)/(resultMax-1))*100;
		}
		document.getElementById(winId+"-searchInput").style.background="-webkit-linear-gradient(left,#c0ffa0 0%,#c0ffa0 "+tmp+"%,#e0ffd0 "+tmp+"%,#e0ffd0 "+tmp+"%)";

	} else if (newResult) {
		shakeObj(winId);
	}
	document.getElementById(winId+"-global").dataset.searchcursor=cursor;
}

function app1000_itemSearchReset(winId,mode) {
	document.getElementById(winId+"-global").dataset.searchcursor="";
	document.getElementById(winId+"-global").dataset.searchresult="";
	if (mode) {document.getElementById(winId+"-searchInput").value="";}
	document.getElementById(winId+"-searchInput").style.background="";
}

function app1000_itemSearchSelectAll(winId) {
	if (!document.getElementById(winId+"-folderRoot").querySelector("[data-typ='1']")) {	
		var result=document.getElementById(winId+"-global").dataset.searchresult.split(";");
		var resultMax=result.length-1;
		if (resultMax>0) {
			app1000_elementSelectNone(winId);
			app1000_folderCollapseAll(winId);
			for (var t=0;t<resultMax;t++) {
				var objId=winId+"-i-"+result[t];
				app1000_elementSelect(winId,objId,1);
				app1000_itemExpandToItem(winId,objId,true);
			}
		}
		return true;
	}
	return false;
}






function app1000_itemClick(winId,objId) {
	var event=window.event;
	if (event.button==0) {app1000_itemClickLeft(winId,objId);}
	if (event.button==2) {app1000_itemClickRight(winId,objId);}
	clickCancel();
}

function app1000_itemClickLeft(winId,objId) {
	var event=window.event;
	if (event.shiftKey) {
		app1000_elementSelect(winId,objId,2);
	} else {
		app1000_itemSetCursor(winId,objId);
	}
}

function app1000_itemClickRight(winId,objId) {
	var obj=document.getElementById(objId);
	if (obj) {
		var jsdata=document.getElementById(winId+"-global").dataset.jsdata;

		var sysFolderId=app1000_elementGetSysfolderId(winId,objId);
		var	selected_sysFolderId=document.getElementById(winId+"-global").dataset.sysfolderid;
		var selected_Count=document.getElementById(winId+"-folderRoot").querySelectorAll("[data-selected='1']").length;

		apps_contextMenu=new class_contextMenu(winId);
		ajax("itemContextMenu","1000",winId,jsdata,objId+AJAX_SEPARATOR1+sysFolderId+AJAX_SEPARATOR1+selected_sysFolderId+AJAX_SEPARATOR1+obj.dataset.folderid+AJAX_SEPARATOR1+selected_Count);
	}
}


function app1000_itemDblClick(winId,objId) {
	var event=window.event;
	if (event.shiftKey) {return;}

	app1000_itemSetCursor(winId,objId);

	var obj=document.getElementById(objId);
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	var optionTyp=document.getElementById(winId+"-global").dataset.optiontyp;
	
	if (obj && jsdata) {
		if (optionTyp==0 || optionTyp==5) {
			//Edit
			ajax("editItem","1000",winId,jsdata,objId);
		} else {
			//Return
			app1000_itemReturnValue(winId,objId);
		}
	}
}

function app1000_itemReturnValue(winId,objId) {
	var obj=document.getElementById(objId);
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	if (obj && jsdata) {
		var dataArr=jsdata.split(AJAX_SEPARATOR1);

		var tmp=obj.parentNode.id.split("-");
		app1000_history["root"+dataArr[1]]=tmp[2];	//FolderId merken (Key = Root-Folder aus jsdata)
	
		var tmp=obj.id.split("-");
		controlReturn(winId,dataArr[0],tmp[3]);
		if (dataArr[4]) {eval(dataArr[4]);}	//ggf. callback

	} else if (jsdata) {
		//objId gibt es nicht, z.B. bei typ=6
		var dataArr=jsdata.split(AJAX_SEPARATOR1);
		controlReturn(winId,dataArr[0],dataArr[2]);	//value ist immer Defaultvalue, da keine objId wählbar war
		if (dataArr[4]) {eval(dataArr[4]);}	//ggf. callback
	}
}

function app1000_itemReturnReset(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	if (jsdata) {
		var dataArr=jsdata.split(AJAX_SEPARATOR1);
		controlReturn(winId,dataArr[0],0);
		if (dataArr[4]) {eval(dataArr[4]);}	//ggf. callback
	}
}

function app1000_cancel(winId) {
	var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
	if (jsdata) {
		var dataArr=jsdata.split(AJAX_SEPARATOR1);
		controlCancel(winId,dataArr[0]);
		if (dataArr[5]) {eval(dataArr[5]);}	//ggf. callback2
	}
}

function app1000_editCancel(winId) {
	var optionTyp=document.getElementById(winId+"-global").dataset.optiontyp;
	if (optionTyp==6) {
		app1000_cancel(winId);
	} else {
		var jsdata=document.getElementById(winId+"-global").dataset.jsdata;
		if (jsdata) {
			ajax("refreshFolders","1000",winId,jsdata,"");
		}
		clearObject(winId+"-edit",1);
	}
}










/*
============================================================================
Preview für diverse Items
============================================================================
*/

function app1000_colorBGpreview(objId,colorData,ko) {
	document.getElementById(objId).style.background="transparent";
	if (colorData!='') {document.getElementById(objId).style.background=pS(colorData,ko);}
}

function app1000_colorFGpreview(objId,colorData,ko) {
	document.getElementById(objId).style.color="transparent";
	if (colorData!='') {document.getElementById(objId).style.color=pS(colorData,ko);}
}

function app1000_animationPreview(winId) {
	var obj=document.getElementById(winId+"-animpreview");
	if (obj) {
		var duration=parseInt(document.getElementById(winId+"-duration").innerHTML);
		var count=parseInt(document.getElementById(winId+"-count").innerHTML);
		if (duration>0) {
			var p4=document.getElementById(winId+"-fd4").value;
			var p5=["linear","ease","ease-in","ease-out","ease-in-out"][document.getElementById(winId+"-fd5").dataset.value];
			var p6=parseFloat(document.getElementById(winId+"-fd6").value);if (isNaN(p6) || p6<0) {p6=0;}			
			var p7=["normal","reverse","alternate","alternate-reverse"][document.getElementById(winId+"-fd7").dataset.value];
			var p8=["none","forwards","backwards","both"][document.getElementById(winId+"-fd8").dataset.value];

			document.getElementById("cssAnims").innerHTML="@-webkit-keyframes animPreviewData {"+p4+"}";
			obj.style.webkitAnimation="none";
			obj.offsetWidth;
			obj.style.webkitAnimation="animPreviewData "+duration+"s "+p5+" "+p6+"s "+count+" "+p7+" "+p8;
		} else {
			obj.style.webkitAnimation="none";
		}
	}
}
function app1000_animationStop(winId) {
	var obj=document.getElementById(winId+"-animpreview");
	if (obj) {
		obj.style.webkitAnimation="none";
	}
}
function app1000_animationDuration(winId,n) {
	var obj=document.getElementById(winId+"-duration");
	if (obj) {
		obj.innerHTML=n+"s";
	}
}
function app1000_animationCount(winId,n) {
	var obj=document.getElementById(winId+"-count");
	if (obj) {
		obj.innerHTML=n+"x";
	}
}

function app1000_fontPreview(winId) {
	var obj=document.getElementById(winId+"-preview");
	var ttf=((document.getElementById(winId+"-fd4").dataset.value=="1")?true:false);
	var fontId=document.getElementById(winId+"-fd1").value;
	var sysFont=document.getElementById(winId+"-fd5").value.split(",")[0];
	var fontTmp=((document.getElementById(winId+"-fd10").value=="1")?true:false);
	var fontExists=((document.getElementById(winId+"-fd11").value=="1")?true:false);

	if (ttf) {
		document.getElementById(winId+"-btnpreview").style.display="none";
		document.getElementById(winId+"-formupload1").style.display="inline";
		if (fontTmp) {
			ttfPreview("../../data/tmp/font-tmp.ttf");
		} else {
			if (fontId>=1) {
				if (fontExists) {
					ttfPreview("../../data/project/visu/etc/font-"+fontId+".ttf");
				} else {
					obj.style.fontFamily="EDOMIfont,Arial";
				}
			} else {
				obj.style.fontFamily="EDOMIfont,Arial";
			}
		}
	} else {
		document.getElementById(winId+"-btnpreview").style.display="inline";
		document.getElementById(winId+"-formupload1").style.display="none";
		if (sysFont=="") {obj.style.fontFamily="EDOMIfont,Arial";} else {obj.style.fontFamily=sysFont+",EDOMIfont,Arial";}
	}

	function ttfPreview(fn) {
		document.getElementById("cssFonts").innerHTML="@font-face {font-family:'PREVIEWFONT'; font-style:normal; font-weight:normal; src:url('"+fn+"?"+performance.now()+"') format('truetype');}";
		obj.style.fontFamily="PREVIEWFONT,EDOMIfont,Arial";
	}
}
