/* 
*/ 
function app1020_open(winId) {
	var camId=document.getElementById(winId+"-fd4").dataset.value;
	if (camId>=1) {
		openWindow(1020,"",winId+AJAX_SEPARATOR1+camId);
	} else {
		shakeObj(winId);
	}
}

function app1020_changeValue(winId,id,property,value) {
	var obj=document.getElementById(winId+"-v"+id);
	if (obj) {obj.innerHTML=app1020_formatCaption(id,value);}
	if (id==15 || id==16) {
		if (value=="" || isNaN(value) || parseInt(value)<1) {value=1;} else if (parseInt(value)>99999) {value=99999;}	
		document.getElementById(winId+"-fd"+id).value=parseInt(value);
	}
	camView.setProperty(property,parseInt(value));
	camView.render();	
}

function app1020_formatCaption(id,value) {
	if (id==10) {r=(value/5).toFixed(1)+"%";}
	else if (id==11 || id==12) {r=value+"&deg;";}
	else if (id==13 || id==14 || id==17 || id==18 || id==20) {r=value+"%";}
	else if (id==19) {r=(value/90*100).toFixed(0)+"%";}
	else {r=value;}
	return r;
}
