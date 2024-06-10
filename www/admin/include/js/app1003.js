/* 
*/ 
function app1003_resetForm(winId) {
	//leert alle Eingabefelder
	for (var t=1;t<=48;t++) {
		var obj=document.getElementById(winId+"-fd"+parseInt(t+10));
		if (obj) {
			if (obj.dataset.type<=1) {
				obj.value="";
			} else {
				obj.dataset.value="";
			}
		}
	}
	controlInitAll(winId+"-form1");
}

function app1003_setDefaultStyle(objId,newValue) {
	//übernimmt den Defaultstyle (oder die Vorlage des Defaultstyles) in einen dynamischen Style, sofern das Zielfeld leer ist
	var obj=document.getElementById(objId);
	if (obj) {
		if (obj.dataset.type<=1) {
			if (obj.value=="") {
				obj.value=newValue;
			}
		} else {
			if (obj.dataset.value=="" || obj.dataset.value=="0") {
				obj.dataset.value=newValue;
			}
		}
	}
}

