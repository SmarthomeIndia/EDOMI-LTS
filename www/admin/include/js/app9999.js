/* 
*/ 
function app9999_historyAdd(objId,helpFn) {
	if (helpFn.toString()!==app9999_history[app9999_history.length-1].toString()) {
		app9999_history.push(helpFn);
	}
}

function app9999_historyBack(winId,objId) {
	if (app9999_history.length>1) {
		app9999_history.pop();
		ajax("showHelp","9999",winId,app9999_history[app9999_history.length-1].toString(),"");
	}
}

function app9999_showMenu(winId,mode) {
	var menu=document.getElementById(winId+"-menu");
	var menubutton=document.getElementById(winId+"-menubutton");
	if (mode) {
		if (menu.style.display=="none") {
			menu.style.display="block";
			menubutton.style.background="#a0a0a0";
		} else {
			menu.style.display="none";
			menubutton.style.background="";
		}
	} else {
		menu.style.display="none";
		menubutton.style.background="";
	}
}
