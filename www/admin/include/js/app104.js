/* 
*/ 
function app104_mouseMove(winId) {
	var event=window.event;
	if (camView && app104_drag.mode>0) {

		var dx=event.pageX-app104_drag.x;
		var dy=event.pageY-app104_drag.y;

		if (app104_drag.mode==1) {
			var f=2+(app104_drag.zoom/100)*2;
			if (app104_drag.srctyp==1) {
				var tmp=math_rotatePoint(event.pageX,event.pageY,app104_drag.x,app104_drag.y,app104_drag.a2,-event.pageX,-event.pageY)
				app104_drag.v1-=tmp.x/f;
				app104_drag.v2-=tmp.y/f;
				if (app104_drag.v1<-100) {app104_drag.v1=-100;}
				if (app104_drag.v1>100) {app104_drag.v1=100;}
				if (app104_drag.v2<-100) {app104_drag.v2=-100;}
				if (app104_drag.v2>100) {app104_drag.v2=100;}
				camView.setProperty("db_x",app104_drag.v1);
				camView.setProperty("db_y",app104_drag.v2);
			} else {
				app104_drag.v1-=dy/f;
				app104_drag.v2+=dx/f;
				if (app104_drag.v1<0) {app104_drag.v1=0;}
				if (app104_drag.v1>90) {app104_drag.v1=90;}
				if (app104_drag.v2<-180) {app104_drag.v2=180;}
				if (app104_drag.v2>180) {app104_drag.v2=-180;}
				camView.setProperty("db_a1",app104_drag.v1);
				camView.setProperty("db_a2",app104_drag.v2);
			}
			camView.render();
		}
		
		if (app104_drag.mode==2) {		
			if (app104_drag.srctyp==1) {app104_drag.zoom+=dx/(app104_drag.zoom/10+1);} else {app104_drag.zoom+=dx/10;}
			if (app104_drag.zoom<0) {app104_drag.zoom=0;}
			if (app104_drag.zoom>100) {app104_drag.zoom=100;}
			camView.setProperty("db_zoom",app104_drag.zoom*5);
			camView.render();
		}

		app104_drag.x=event.pageX;
		app104_drag.y=event.pageY;
	}
}

function app104_mouseDown(winId) {
	var event=window.event;
	if (camView) {
		if (event.button==2 || (event.button==0 && event.shiftKey)) {
			app104_drag.mode=2;
		} else if (event.button==0) {
			app104_drag.mode=1;
		}
		app104_drag.x=event.pageX;
		app104_drag.y=event.pageY;		
	}
}

function app104_mouseUp(winId) {
	app104_drag.mode=0;
}




function app104_refresh(winId) {
	app104_info.ts=-1;
	var view=app104_views[((app104_info.mode==1)?"arc":"dvr")+app104_info.id];
	ajax("start","104",winId,"",app104_info.id+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+view);
}

function app104_setMode(winId,mode) {
	document.getElementById(winId+"-mode"+app104_info.mode).className="app104_modeB";
	document.getElementById(winId+"-mode"+mode).className="app104_modeA";
	app104_info.mode=mode;
	app104_info.id=app104_info.lastId[mode];
	var view=app104_views[((mode==1)?"arc":"dvr")+app104_info.id];
	ajax("start","104",winId,"",app104_info.id+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+view);
}

function app104_changeView(winId) {
	var view=document.getElementById(winId+"-view").dataset.value;
	app104_views[((app104_info.mode==1)?"arc":"dvr")+app104_info.id]=view;
	ajax("start","104",winId,"",app104_info.id+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+view);
}

function app104_metaLoadArray(winId,value,eventStep) {
	if (eventStep===undefined) {eventStep=0;}
	ajax("metaLoadArray","104",winId,"",app104_info.id+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+app104_files[value][0]+AJAX_SEPARATOR1+eventStep);
}

function app104_filesLoadImage(winId,value) {
	document.getElementById(winId+"-timeline").style.opacity="0.5";
	document.getElementById(winId+"-slider2").value=0;
	app104_loadImage(winId,{ts:app104_files[value][0],fn:app104_files[value][1],pos:app104_files[value][2],len:app104_files[value][3],caption:app104_files[value][4]});
}

function app104_metaLoadImage(winId,value) {
	document.getElementById(winId+"-timeline").style.opacity="1";
	app104_loadImage(winId,{ts:app104_meta[value][0],fn:app104_meta[value][1],pos:app104_meta[value][2],len:app104_meta[value][3],caption:app104_meta[value][4]});
}

function app104_loadImage(winId,meta) {
	if (app104_info.load) {
		var img=document.getElementById(winId+"-img");
		app104_info.ts=meta.ts;

		if (camView) {
			if (app104_info.mode==1) {
				camView.setProperty("url","../data/liveproject/cam/archiv/"+encodeURIComponent(meta.fn)+".jpg?nocache="+performance.now());		
			} else {
				camView.setProperty("url","apps/app_dvrloadimg.php?cmd=loadImg&appid=104&n1="+encodeURIComponent(meta.fn)+"&n2="+encodeURIComponent(meta.pos)+"&n3="+encodeURIComponent(meta.len)+"&nocache="+performance.now());		
			}
			if (app104_info.camviewInited) {
				camView.loadRender("app104_imgOnLoad(\""+winId+"\")","app104_imgOnLoad(\""+winId+"\")");
			} else {
				app104_info.camviewInited=true;
				camView.initLoadRender("app104_imgOnLoad(\""+winId+"\")","app104_imgOnLoad(\""+winId+"\")");
			}
		} else {
			if (app104_info.mode==1) {
				img.src="../data/liveproject/cam/archiv/"+encodeURIComponent(meta.fn)+".jpg?nocache="+performance.now();
			} else {
				img.src="apps/app_dvrloadimg.php?cmd=loadImg&appid=104&n1="+encodeURIComponent(meta.fn)+"&n2="+encodeURIComponent(meta.pos)+"&n3="+encodeURIComponent(meta.len)+"&nocache="+performance.now();
			}
		}

		var caption=document.getElementById(winId+"-cap");
		var tmp=meta.caption.split("/");
		if (app104_info.mode==1) {
			caption.innerHTML=tmp[0]+" / "+tmp[1]+" / "+tmp[2]+"<span style='color:#c0c0c0;'>."+tmp[3]+"</span>";
			caption.style.background="#808080";
		} else {
			caption.innerHTML=tmp[0]+" / "+tmp[1]+" / "+tmp[2];
			var s2=document.getElementById(winId+"-slider2");
			var value=parseInt(s2.value);
			if (value>=0 && value<app104_meta.length && app104_meta[value][6]!=0) {
				caption.style.background="rgba(255,255,0,0.5)";
			} else {
				caption.style.background="#808080";
			}
		}

		app104_info.load=false;
		app104_info.buffer=false;
	} else {
		app104_info.buffer=meta;
	}
}

function app104_imgOnLoad(winId) {
	app104_info.load=true;
	if (document.getElementById(winId+"-main")) {
		if (camView) {
			document.getElementById(winId+"-img").style.display="none";
			document.getElementById(winId+"-cnv").style.display="block";
		} else {
			document.getElementById(winId+"-img").style.display="block";
			document.getElementById(winId+"-cnv").style.display="none";
		}
	
		if (app104_info.buffer!==false) {
			app104_loadImage(winId,app104_info.buffer);
		}
	}
}

function app104_saveImage(winId) {
	if (document.getElementById(winId+"-buffsave").dataset.active=="0") {return;}
	var id=document.getElementById(winId+"-slider2").value;
	if (app104_meta[id]) {
		if (camView) {
			openBusyWindow();
			camView.setProperty("db_srcs",100);
			camView.loadRender("app104_saveImageRendered(\""+winId+"\","+id+",true)","app104_saveImageRendered(\""+winId+"\","+id+",false)");
		} else {
			ajax("saveImage","104",winId,"",app104_meta[id][0]+AJAX_SEPARATOR1+app104_meta[id][1]+AJAX_SEPARATOR1+app104_meta[id][2]+AJAX_SEPARATOR1+app104_meta[id][3]+AJAX_SEPARATOR1+app104_meta[id][4]+AJAX_SEPARATOR1+app104_meta[id][5]+AJAX_SEPARATOR1+app104_meta[id][6]+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+app104_info.camViewId);
		}
	}
}

function app104_saveImageRendered(winId,id,ok) {
	camView.setProperty("db_srcs",parseInt(document.getElementById(winId+"-srcs").value));
	hideBusyWindow();
	if (ok) {
		var tmp=document.getElementById(winId+"-cnv").toDataURL("image/jpeg",1);
		var data=tmp.split(",",2);
		if (data[1]) {
			ajax("saveImage","104",winId,data[1],app104_meta[id][0]+AJAX_SEPARATOR1+app104_meta[id][1]+AJAX_SEPARATOR1+app104_meta[id][2]+AJAX_SEPARATOR1+app104_meta[id][3]+AJAX_SEPARATOR1+app104_meta[id][4]+AJAX_SEPARATOR1+app104_meta[id][5]+AJAX_SEPARATOR1+app104_meta[id][6]+AJAX_SEPARATOR1+app104_info.mode+AJAX_SEPARATOR1+app104_info.camViewId);
		} else {
			ok=false;
		}
	}
	if (!ok) {
		jsConfirm("Beim Ablegen der Bildkopie ist ein Problem aufgetreten.","","none");
	}
}

function app104_addItem(winId,id,name,mode,selected,disabled) {
	menuItem=createNewDiv(winId+"-list",winId+"-i-"+id);
	menuItem.className="controlListItem";
	menuItem.style.display="block";
	if (disabled) {menuItem.style.opacity="0.5";}
	menuItem.style.color="#ffffff";
	menuItem.innerHTML=name+" <span class='id'>"+id+"</span>";

	if (selected) {
		menuItem.style.background=apps_colorSelected;
	} else {
		menuItem.style.background="#343434";
	}

	var view=app104_views[((mode==1)?"arc":"dvr")+id];
	menuItem.setAttribute("onMouseDown","ajax(\"start\",\"104\",\""+winId+"\",\"\",\""+id+AJAX_SEPARATOR1+mode+AJAX_SEPARATOR1+view+"\");");
}

function app104_addFolder(winId,id,name) {
	menuItem=createNewDiv(winId+"-list",winId+"-f-"+id);
	menuItem.className="controlListItem";
	menuItem.style.display="block";
	menuItem.style.color="#7070ff";
	menuItem.style.background="#343434";
	menuItem.innerHTML="<div style='padding-top:3px;'>"+name+"</div><div style='width:100%; height:1px; margin:3px 0 0 0; background:#7070ff;'></div>";
}

function app104_setSrcs(winId,value) {
	if (camView) {
		camView.setProperty("db_srcs",value);
		document.getElementById(winId+"-srcscaption").innerHTML=value+"%";
		var tmp=document.getElementById(winId+"-slider2").value;
		app104_metaLoadImage(winId,tmp);
	}
}

function app104_addStep1(winId,step) {
	var sl=document.getElementById(winId+"-slider1");
	var value=parseInt(sl.value)+step;
	if (value>=0 && value<app104_files.length) {
		sl.value=value;
		app104_metaLoadArray(winId,value);
	}
}

function app104_addStep2(winId,step) {
	var sl=document.getElementById(winId+"-slider2");
	var value=parseInt(sl.value)+step;
	if (value<0) {
		app104_addStep1(winId,-1);
	} else if (value>=app104_meta.length) {
		app104_addStep1(winId,1);
	} else {
		sl.value=value;
		app104_loadImage(winId,{ts:app104_meta[value][0],fn:app104_meta[value][1],pos:app104_meta[value][2],len:app104_meta[value][3],caption:app104_meta[value][4]});
	}
}

function app104_seekEvent(winId,pos,step) {
	var value=false;
	if (app104_info.mode==0) {
		if (step>0) {
			for (var t=pos;t<app104_meta.length;t++) {
				if (app104_meta[t][6]==1 && (t==0 || app104_meta[t-1][6]==0)) {
					value=t;
					break;
				}
			}
		} else {
			for (var t=pos;t>=0;t--) {
				if (app104_meta[t][6]==1 && (t==0 || app104_meta[t-1][6]==0)) {
					value=t;
					break;
				}
			}
		}
	}
	return value;
}

function app104_addStep3(winId,step) {
	if (app104_info.mode==1) {
		app104_addStep2(winId,step);
	} else {
		var s1=document.getElementById(winId+"-slider1");
		var s2=document.getElementById(winId+"-slider2");
		var pos=parseInt(s2.value)+step;
		var value=app104_seekEvent(winId,pos,step);

		if (value!==false) {
			s2.value=value;
			app104_loadImage(winId,{ts:app104_meta[value][0],fn:app104_meta[value][1],pos:app104_meta[value][2],len:app104_meta[value][3],caption:app104_meta[value][4]});
		} else {
			var pos=parseInt(s1.value)+step;
			if (step>0) {
				for (var t=pos;t<app104_files.length;t++) {
					if (app104_files[t][5]>0) {
						value=t;
						break;
					}
				}
			} else {
				for (var t=pos;t>=0;t--) {
					if (app104_files[t][5]>0) {
						value=t;
						break;
					}
				}
			}

			if (value!==false) {
				s1.value=value;
				app104_metaLoadArray(winId,value,step);
			}
		}
	}
}

function app104_play2(winId,step,mode) {
	if (mode==1) {
		window.clearInterval(app104_info.timer);
		app104_info.timer=window.setInterval(function(){app104_info.play=true;app104_addStep2(winId,step);},250);
	} else if (mode==0) {
		window.clearInterval(app104_info.timer);
		if (!app104_info.play) {app104_addStep2(winId,step);}
	} else if (mode==-1) {
		window.clearInterval(app104_info.timer);
	}
	app104_info.play=false;
}

function app104_getHour(ts) {
	var tmp=new Date(ts*1000);
	var d=String(tmp.getFullYear())+String(digits(tmp.getMonth()+1))+String(digits(tmp.getDate()))+String(digits(tmp.getHours()));
	return parseInt(d);
	
	function digits(n) {
		if (n<10) {n="0"+n;}
		return n;
	}
}

function app104_getDay(ts) {
	var tmp=new Date(ts*1000);
	var d=String(tmp.getFullYear())+String(digits(tmp.getMonth()+1))+String(digits(tmp.getDate()));
	return parseInt(d);
	
	function digits(n) {
		if (n<10) {n="0"+n;}
		return n;
	}
}

