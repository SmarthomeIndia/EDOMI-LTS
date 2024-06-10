/* 
*/ 
function app103_quit(winId,callback) {
	closeWindow(winId);
	if (callback!="") {
		eval(callback);
	}
}

function app103_clickLog(winId,filename,ts) {
	var event=window.event;
	if (event.button==0) {
		window.open("../data/log/"+filename+"?"+ts,"_blank");
	}
	if (event.button==2) {
		apps_contextMenu=new class_contextMenu(winId);
		apps_contextMenu.addItem("Löschen","ajaxConfirm('Soll diese Logdatei wirklich gelöscht werden?','menu1_delete','103','"+winId+"','','"+filename+"','','Löschen');");
		apps_contextMenu.addHr();
		apps_contextMenu.addText(filename);
		apps_contextMenu.show();
	}
	clickCancel();
}

function app103_clickArchiv(winId,prjId) {
	var event=window.event;
	if (event.button==0) {
		ajaxConfirmSecure("Soll dieses archivierte Projekt wirklich geöffnet werden?<br><br>Das aktuelle Arbeitsprojekt geht verloren, falls es nicht zuvor archiviert wurde!<br><br>Hinweis: Das Öffnen eines archivierten Projekts aus einer älteren EDOMI-Version kann u.U. mehrere Minuten dauern.","menu12_loadArchiv","103",winId,"",prjId,"","Öffnen");
	}
	if (event.button==2) {
		apps_contextMenu=new class_contextMenu(winId);
		apps_contextMenu.addItem("Umbenennen","ajax('menu12_renameArchiv','103','"+winId+"','','"+prjId+"');");
		apps_contextMenu.addHr();
		apps_contextMenu.addItem("Löschen","ajaxConfirm('Soll dieses archivierte Projekt wirklich gelöscht werden?','menu12_deleteArchiv','103','"+winId+"','','"+prjId+"','','Löschen');");
		apps_contextMenu.show();
	}
	clickCancel();
}
