/* 
*/ 
/*
============================================================================
Admin/Visu: Diverse Funktionen
============================================================================
*/

visu_indiColor='#80e000';			//Defaultfarbe für alle Indikatoren (Klicks, ZSU, etc.)
visu_indiColorText='inherit';		//Default-Textfarbe für einige Indikatoren (Tastatureingabe, Drehregler, etc.)


SEPARATOR1=String.fromCharCode(29);			//bis 1.18: $
SEPARATOR2=String.fromCharCode(30);			//bis 1.18: @
AJAX_SEPARATOR1=String.fromCharCode(28);	//bis 1.18: ~
AJAX_SEPARATOR2=String.fromCharCode(31);	//bis 1.18: |

visu_defaultFgColor='#000000';				//Default-FG-Farbe für Visuelemente (visu/main.css, shared/main.js, shared/config.php)


function includeVisuelements(url,callback,callbackError) {
	//vorhandene VSE-Funktionen entfernen
	for (var func in window) {
		if (func.indexOf("VSE_")==0) {delete window[func];}
	}
	
	//VSE-Funktionen inkludieren
	var id="visuelementsJS";
    var oldscript=document.getElementById(id);
    var script=document.createElement('script');
    script.type='text/javascript';
	script.id=id;
	script.onload=callback;
	if (callbackError===true) {script.onerror=callback;} else {script.onerror=callbackError;}
	script.src=url+"?"+performance.now();
    if (oldscript) {
		document.head.replaceChild(script,oldscript);
    } else {
		document.head.appendChild(script);
    }
}

function stringCleanup(n) {
	//entfernt reservierte Zeichen, bevor der String per Ajax übertragen wird (entspricht quasi PHP:sql_encodeValue();)

	//entfernt alle Zeichen <32 (außer TAB, LF, CR)
	n=n.replace(/[\x00-\x08\x0B-\x0C\x0E-\x1F]/g,"");
	return n;
}

function stringCleanupSeparator(n) {
	//entfernt reservierte Zeichen, bevor der String per Ajax übertragen wird (entspricht quasi PHP:sql_encodeValue();)
	//(im Gegensatz zu stringCleanup() werden die Separatoren nicht(!) entfernt)

	//entfernt alle Zeichen <32 (außer TAB, LF, CR und den SEPARATOREN)
	//### x1c..x1f sind die Separatoren - eigentlich SEPARATOR1/2/3/4 in HEX umwandeln...
	n=n.replace(/[\x00-\x08\x0B-\x0C\x0E-\x1B]/g,"");
	return n;
}

function stringEscapeSingleQuote(n) {
	n=n.replace(/[\']/g,"\\'");
	return n;
}

function math_mapValue(value,minV,maxV,minRange,maxRange) {
	var tmp1=parseFloat(value);
	var minRange=parseFloat(minRange);
	var maxRange=parseFloat(maxRange);
	var minV=parseFloat(minV);
	var maxV=parseFloat(maxV);
	if (isNaN(tmp1) || tmp1<minV) {tmp1=minV;}
	if (tmp1>maxV) {tmp1=maxV;}
	var tmp=((tmp1-minV)/Math.abs(maxV-minV))*Math.abs(maxRange-minRange)+minRange;
	return tmp;
}

function math_polarToXY(mx,my,angle,radius) {
	return {x:Math.sin((-angle)*Math.PI/180)*radius+mx,y:Math.cos((-angle)*Math.PI/180)*radius+my};
}

function math_rotatePoint(mx,my,x,y,angle,offX,offY) {
	var cos=Math.cos((Math.PI/180)*-angle);
	var sin=Math.sin((Math.PI/180)*-angle);
	var rx=(cos*(x-mx))+(sin*(y-my))+mx;
	var ry=(cos*(y-my))-(sin*(x-mx))+my;
	return {x:rx+offX,y:ry+offY};
}

function scrollToTop(objId) {
	var obj=document.getElementById(objId);
	if (obj) {obj.scrollTop=0;}
}

function scrollToEnd(objId) {
	var obj=document.getElementById(objId);
	if (obj) {obj.scrollTop=obj.scrollHeight;}
}

function scrollUp(objId) {
	var obj=document.getElementById(objId);
	if (obj) {obj.scrollTop-=parseInt(obj.clientHeight);}
}

function scrollDown(objId) {
	var obj=document.getElementById(objId);
	if (obj) {obj.scrollTop+=parseInt(obj.clientHeight);}
}

function visuElement_getFgColor(obj,id) {
	if (id===undefined) {id=0;}
	var tmp=window.getComputedStyle(obj).getPropertyValue("--fgc"+id);
	if (tmp!="") {return tmp;}
	return visu_defaultFgColor;
}

function visuElement_getBgColor(obj,id) {
	if (id===undefined) {id=0;}
	var tmp=window.getComputedStyle(obj).getPropertyValue("--bgc"+id);
	if (tmp!="") {return tmp;}
	return "none";
}

function visuElement_getImageUrl(obj,id) {
	if (id===undefined) {id=0;}
	var tmp=window.getComputedStyle(obj).getPropertyValue("--img"+id);
	tmp=tmp.replace('"',"");
	tmp=tmp.replace("'","");
	return tmp;
}

function visuElement_getAngle(obj) {
	if (obj) {
		var tmp=window.getComputedStyle(obj).getPropertyValue("--rt");
		if (tmp!="") {return parseFloat(tmp);}
	}
	return 0;
}

function visuElement_getOffset(obj,xy) {
	if (obj) {
		var tmp=window.getComputedStyle(obj).getPropertyValue("--d"+((xy==0)?"x":"y"));
		if (tmp!="") {return parseFloat(tmp);}
	}
	return 0;
}

function visuElement_getScale(obj,xy) {
	if (obj) {
		var tmp=window.getComputedStyle(obj).getPropertyValue("--s"+((xy==0)?"x":"y"));
		if (tmp!="") {return parseFloat(tmp);}
	}
	return 0;
}

function visuElement_getCssProperty(obj,property,defaultValue) {
	if (obj) {
		var tmp=window.getComputedStyle(obj).getPropertyValue(property);
		if (tmp!="") {return tmp;}
	}
	return defaultValue;
}

function visuElement_getVarProperty(obj,id) {
	if (obj) {
		var tmp=window.getComputedStyle(obj).getPropertyValue("--var"+id);
		if (tmp!="") {return tmp;}
	}
	return false;
}

function visuElement_centerAndAspect(container,obj,scale) {
	if (container && obj) {
		var w=container.clientWidth;
		var h=container.clientHeight;
		if (w>=h) {var size=h;} else {var size=w;}
		if (scale!==undefined) {size=size*scale/100;}
		obj.style.left=((w-size)/2)+"px";
		obj.style.top=((h-size)/2)+"px";
		obj.style.width=size+"px";
		obj.style.height=size+"px";
		return {x:((w-size)/2),y:((h-size)/2),w:size,h:size};
	}
}

function visuElement_parseString(n,ko) {
	return pS(n,ko);
}


function canvasScale(canvas,cWidth,cHeight) {
	if (canvas && canvas.getContext) {
		var context=canvas.getContext("2d");
		canvas.width=cWidth*displayPixelRatio;
		canvas.height=cHeight*displayPixelRatio;
		canvas.style.width=cWidth+"px";
		canvas.style.height=cHeight+"px";
		context.scale(displayPixelRatio,displayPixelRatio);
		return context;
	}
	return false;
}


function radix_StringToArray(dataString,sep,radix) {
	var n=dataString.split(sep);
	for (var t=0;t<n.length;t++) {n[t]=parseInt(n[t],radix);}
	return n;
}

function radix_ArrayToString(dataArray,sep,radix) {
	var n="";
	for (var t=0;t<dataArray.length;t++) {n+=dataArray[t].toString(radix)+sep;}
	n=n.substring(0,n.length-1);
	return n;
}

function pS(n,ko) {
	//parseStyle
	//berechnet (eval) alles innerhalb von "{...}" in einem String n und ersetzt dabei "#" durch ko
	//return: In den String n eingesetztes Ergebnis
	//n: der String, z.B. "Temperatur beträgt {#*4+12+#+2} Grad"
	//ko: Wert von #
	
	//-------------------------------------------------------------------------------------------------------------------------------------
	//Spezialfunktionen innerhalb(!) von {...}, also z.B. "Gerundet: {round(#*5.1)} Grad"	
	//-------------------------------------------------------------------------------------------------------------------------------------
	//Mathematische Funktionen	
	function abs(x) {return Math.abs(parseFloat(x));}									//absoluter Wert
	function floor(x) {return Math.floor(parseFloat(x));}								//Wert abrunden
	function ceil(x) {return Math.ceil(parseFloat(x));}									//Wert aufrunden
	function pow(x,y) {return Math.pow(parseFloat(x),y);}								//Potenzieren
	function sqrt(x) {return Math.sqrt(parseFloat(x));}									//Quadratwurzel
	function log(x) {return Math.log(parseFloat(x));}									//Logarithmus
	function sin(x) {return Math.sin(parseFloat(x));}									//Sinus
	function cos(x) {return Math.cos(parseFloat(x));}									//Cosinus
	function tan(x) {return Math.tan(parseFloat(x));}									//Tangens
	function round(x) {return Math.round(parseFloat(x));}								//Wert runden
	function fixed(x,anz) {return parseFloat(x).toFixed(anz);}							//Wert auf "anz" Nachkommastellen bringen

	//String-Funktionen mit Übergabe des Strings (Achtung: Aufruf z.B. mit str_left('#',2) - wenn das KO '' enthält, muss ggf. escaped werden!)
	function str_left(x,anz) {return x.toString().substr(0,anz);}						//"anz" Zeichen von links des Strings "x" ausgeben 
	function str_right(x,anz) {return x.toString().substr(x.toString().length-anz);}	//"anz" Zeichen von rechts des Strings "x" ausgeben 
	function str_mid(x,pos,anz) {return x.toString().substr(pos,anz);}					//"anz" Zeichen ab der Position "pos" des Strings "x" ausgeben ("pos" beginnt mit 0)
	function str_split(x,pos,sep) {														//Splittet den String x mittels sep (optional) und liefert den Wert mit dem Array-Index pos zurück
		if (!sep) {sep="|";}
		var n=x.split(sep);
		if (pos<n.length) {
			return n[pos];
		} else {
			return "";
		}
	}
	function str_stringornum(x) {														//versucht den String "x" in FLOAT zu konvertieren oder gibt einen STRING zurück
		if (!isNaN(parseFloat(x)) && isFinite(x)) {return parseFloat(x);} else {return x.toString();}
	}
	function str_len(x) {return x.toString().length;}									//Länge des Strings "x" ermitteln 
	function str_replace(x,n1,n2) {return x.toString().split(n1).join(n2);}				//ersetzt jedes Vorkommen von "n1" durch "n2" im String "x"
	function str_lcase(x) {return x.toString().toLowerCase();}							//wandelt den String "x" in Kleinbuchstaben um 
	function str_ucase(x) {return x.toString().toUpperCase();}							//wandelt den String "x" in Großbuchstaben um 
	function str_trim(x) {return x.toString().trim();}									//entfernt "whitespace" am Anfang und Ende des Strings "x" 
	function str_secondstotime(x) {														//Sekunden in h:mm:ss umwandeln
		if (isNaN(x)) {x=0;}
		return Math.floor(x/3600)+":"+(Math.floor((x%3600)/60)<10?"0":"")+Math.floor((x%3600)/60)+":"+((x%60)<10?"0":"")+(x%60);
	}

	//String-Funktionen ohne Übergabe des Strings (immer auf das KO bezogen)
	function left(anz) {return ko.toString().substr(0,anz);}							//"anz" Zeichen von links des KO-Werts (String) ausgeben 
	function right(anz) {return ko.toString().substr(ko.toString().length-anz);}		//"anz" Zeichen von rechts des KO-Werts (String) ausgeben 
	function mid(pos,anz) {return ko.toString().substr(pos,anz);}						//"anz" Zeichen ab der Position "pos" des KO-Werts (String) ausgeben ("pos" beginnt mit 0)
	function split(pos,sep) {return str_split(ko,pos,sep);}								//Splittet den KO-Wert mittels sep (optional) und liefert den Wert mit dem Array-Index pos zurück
	function stringornum(x) {return str_stringornum(ko);}								//versucht den KO-Wert in FLOAT zu konvertieren oder gibt einen STRING zurück
	function len() {return ko.toString().length;}										//Länge des KO-Werts (String) ermitteln 
	function replace(n1,n2) {return str_replace(ko.toString(),n1,n2);}					//ersetzt jedes Vorkommen von "n1" durch "n2" 
	function lcase() {return ko.toString().toLowerCase();}								//wandelt den KO-Wert in Kleinbuchstaben um 
	function ucase() {return ko.toString().toUpperCase();}								//wandelt den KO-Wert in Großbuchstaben um 
	function trim() {return ko.toString().trim();}										//entfernt "whitespace" am Anfang und Ende des KO-Werts 
	function secondstotime() {return str_secondstotime(ko);}							//Sekunden in h:mm:ss umwandeln

	//Spezialfunktionen
	function hsvrgb() {return HSVHEXtoRGBHEX(ko);}										//KO-Wert (Hex-String) von HSV in RGB umwandeln (keine Parameter!)
	function hsvlight(mode) {															//KO-Wert (Hex-String) von HSV in RGBA umwandeln
		var rgba=RGBHEXtoRGBA(HSVHEXtoRGBHEX(ko));
		if (mode==true) {
			if (parseFloat(rgba[3])>0) {rgba[3]=1;}
		}
		return "rgba("+rgba[0]+","+rgba[1]+","+rgba[2]+","+rgba[3]+")";
	}
	function rgblight(mode) {															//KO-Wert (Hex-String) von RGB in RGBA umwandeln
		var rgba=RGBHEXtoRGBA(ko);
		if (mode==true) {
			if (parseFloat(rgba[3])>0) {rgba[3]=1;}
		}
		return "rgba("+rgba[0]+","+rgba[1]+","+rgba[2]+","+rgba[3]+")";
	}
	function polarX(vmin,vmax,amin,amax,r) {											//KO-Wert in polare X-Koordinate umwandeln (z.B. Drehregler-Knopf)
		return polar(0,vmin,vmax,amin,amax,r);
	}
	function polarY(vmin,vmax,amin,amax,r) {											//KO-Wert in polare Y-Koordinate umwandeln (z.B. Drehregler-Knopf)
		return polar(1,vmin,vmax,amin,amax,r);
	}
	function polar(coord,vmin,vmax,amin,amax,r) {										//(Hilfsfunktion) KO-Wert in polare X- oder Y-Koordinate umwandeln (z.B. Drehregler-Knopf)
		var x=parseFloat(ko);
		var amin=parseFloat(amin);
		var amax=parseFloat(amax);
		var vmin=parseFloat(vmin);
		var vmax=parseFloat(vmax);
		var r=parseFloat(r);

		if (isNaN(x) || x<vmin) {x=vmin;}
		if (x>vmax) {x=vmax;}
		if (coord==0) {
			var tmp=Math.sin(-((x-vmin)*(Math.abs(amax-amin)/Math.abs(vmax-vmin)))*Math.PI/180-(amin*Math.PI/180))*r+r;
		} else {
			var tmp=Math.cos(-((x-vmin)*(Math.abs(amax-amin)/Math.abs(vmax-vmin)))*Math.PI/180-(amin*Math.PI/180))*r+r;
		}
		if (isNaN(tmp)) {tmp=0;}
		return parseFloat(tmp.toFixed(2));
	}
	function range(vmin,vmax,r) {														//KO-Wert linear in einen Wertebereich umwandeln (z.B. Schieberegler-Knopf)
		var x=parseFloat(ko);
		var vmin=parseFloat(vmin);
		var vmax=parseFloat(vmax);
		var r=parseFloat(r);
		
		if (isNaN(x) || x<vmin) {x=vmin;}
		if (x>vmax) {x=vmax;}
		var tmp=((x-vmin)/Math.abs(vmax-vmin))*r;
		if (isNaN(tmp)) {tmp=0;}
		return parseFloat(tmp.toFixed(2));
	}
	function colorcalc(value,op,koValue) {												//KO-Wert (3xhex) (oder optional "koValue") mit einem 3xhex-Wert verrechnen (z.B. RGB oder HSV)
		if (koValue===undefined) {var koValue=ko;}
		r="";
		for (var t=0;t<3;t++) {
			var n=parseInt(koValue.substr(t*2,2),16);
			var c=parseInt(value.substr(t*2,2),16);
			if (op==0) 		{var e=parseInt(n+c);}
			else if (op==1)	{var e=parseInt(n-c);}
			else if (op==2)	{var e=parseInt(n*c);}
			else if (op==3)	{var e=parseInt(n/c);}
			else 			{var e=parseInt(n);}
	
			if (isNaN(e)) {
				if (isNaN(n)) {
					r=r+"00";
				} else {
					n=n.toString(16);
					if (n.length==1) {n='0'+n;}
					r=r+n;
				}
			} else {
				if (e<0) {e=0;}
				if (e>255) {e=255;}
				e=e.toString(16);
				if (e.length==1) {e='0'+e;}
				r=r+e;
			}
		}
		return r;
	}
	//-------------------------------------------------------------------------------------------------------------------------------------


	if (n===undefined || n==null || n=="") {return "";}
	n=n.toString();
	if (n.length==0) {return n;}

	//alle "{#}" werden direkt durch den KO-Wert ersetzt und nicht weiter berechnet
	n=replaceString(n,ko,"{#}");

	var curpos=0;
	var regex=/{([\s\S]*?)}/;
	var maxIterations=10000;	//nur zur Sicherheit
	while (match=regex.exec(n.substring(curpos,n.length))) {
		
		var tmp=match[1];
		var pos=match.index+curpos;
		var lll=match[0].length;

		tmp=replaceString(tmp,ko,"#").toString();
	  
		try {
			var r=eval(tmp);
		} catch(e) { 
			var r=ko;	//eval() gescheitert => KO ist vermutlich ein String
		}
		if (r===undefined) {r=ko;}
		if (r===null) {r="";}
		
		r=r.toString();
		n=replaceStringAtPosition(n,r,pos,lll);
		curpos=pos+r.length;

		maxIterations--;
		if (maxIterations<=0) {break;}
	}

	return n;
	
	function replaceString(n,newString,searchString) {
		return n.split(searchString).join(newString);
	}
	
	function replaceStringAtPosition(n,newString,pos,lll) {
		return n.substring(0,pos)+newString+n.substring((pos+lll),n.length); 
	}
}

function RGBHEXtoRGBA(rgbhex) {
	if (rgbhex) {
		var hsv=RGBHEXtoHSV(rgbhex);
		if (hsv!==false) {
			var rgb=HSVtoRGB(hsv[0],hsv[1],255);
			if (rgb!==false) {
				return [rgb[0].toFixed(0),rgb[1].toFixed(0),rgb[2].toFixed(0),(hsv[2]/255)];
			}
		}
	}
	return [0,0,0,0];
}

function HSVtoRGBA(h,s,v) {
	var rgb=HSVtoRGBHEX(h,s,v);
	var r=RGBHEXtoRGBA(rgb);
	return r;
}

function HSVHEXtoRGBHEX(hsv) {
	var r=HSVHEXtoHSV(hsv);
	if (r!==false) {
		return HSVtoRGBHEX(r[0],r[1],r[2]);
	}
	return false;
}

function RGBHEXtoHSV(rgb) {
	if (rgb) {
		var r=RGBHEXtoRGB(rgb);
		if (r!==false) {
			return RGBtoHSV(r[0],r[1],r[2]);
		}
	}
	return false;
}

function HSVtoRGBHEX(h,s,v) {
	var r=HSVtoRGB(h,s,v);
	return RGBtoRGBHEX(r[0],r[1],r[2]);
}

function HSVtoHSVHEX(h,s,v) {
	h=parseInt(h);
	s=parseInt(s);
	v=parseInt(v);
	if (h>=0 && h<=255 && s>=0 && s<=255 && v>=0 && v<=255) {
		h=h.toString(16);
		if (h.length==1) {h='0'+h;}
		s=s.toString(16);
		if (s.length==1) {s='0'+s;}
		v=v.toString(16);
		if (v.length==1) {v='0'+v;}
		var n=h+s+v;
		if (n.length==6) {
			return n;
		}
	}
	return false;
}

function HSVHEXtoHSV(hsv) {
	hsv=hsv.toString().replace("#","");
	var h=parseInt(hsv.substr(0,2),16);
	var s=parseInt(hsv.substr(2,2),16);
	var v=parseInt(hsv.substr(4,2),16);
	if (h>=0 && h<=255 && s>=0 && s<=255 && v>=0 && v<=255) {
		return [h,s,v];
	}
	return false;
}

function RGBHEXtoRGB(rgb) {
	if (rgb) {
		rgb=rgb.toString().replace("#","");
		var r=parseInt(rgb.substr(0,2),16);
		var g=parseInt(rgb.substr(2,2),16);
		var b=parseInt(rgb.substr(4,2),16);
		if (r>=0 && r<=255 && g>=0 && g<=255 && b>=0 && b<=255) {
			return [r,g,b];
		}
	}
	return false;
}

function RGBtoRGBHEX(r,g,b) {
	r=parseInt(r).toString(16);
	if (r.length==1) {r='0'+r;}
	g=parseInt(g).toString(16);
	if (g.length==1) {g='0'+g;}
	b=parseInt(b).toString(16);
	if (b.length==1) {b='0'+b;}
	var n=r+g+b;
	if (n.length==6) {
		return n;
	}
	return false;
}

function RGBtoHSV (r,g,b) {
	r/=255;
	g/=255;
	b/=255;
	var max=Math.max(r,g,b);
	var min=Math.min(r,g,b);
	var h,s;
	var v=max;
	var d=max-min;
	s=max==0 ? 0 : d/max;
	if (max==min) {
		h=0;
	} else {
		switch(max){
			case r: h=(g-b)/d+(g<b ? 6 : 0); break;
			case g: h=(b-r)/d+2; break;
			case b: h=(r-g)/d+4; break;
		}
		h/=6;
	}
	if (h>=0 && h<=1 && s>=0 && s<=1 && v>=0 && v<=1) {
		//return [(h*255).toFixed(0),(s*255).toFixed(0),(v*255).toFixed(0)];
		return [(h*255),(s*255),(v*255)];
	}
	return false;
}

function HSVtoRGB (h,s,v) {
	var r,g,b;
	h/=255;
	s/=255;
	v/=255;
	var i=Math.floor(h*6);
	var f=h*6-i;
	var p=v*(1-s);
	var q=v*(1-f*s);
	var t=v*(1-(1-f)*s);
	switch (i%6) {
		case 0: r=v,g=t,b=p; break;
		case 1: r=q,g=v,b=p; break;
		case 2: r=p,g=v,b=t; break;
		case 3: r=p,g=q,b=v; break;
		case 4: r=t,g=p,b=v; break;
		case 5: r=v,g=p,b=q; break;
	}
	if (r>=0 && r<=1 && g>=0 && g<=1 && b>=0 && b<=1) {
		//return [(r*255).toFixed(0),(g*255).toFixed(0),(b*255).toFixed(0)];
		return [(r*255),(g*255),(b*255)];
	}
	return false;
}

function RGBtoGREYSCALE (r,g,b) {
	r/=255;
	g/=255;
	b/=255;
	var bw=(0.2126*r)+(0.7152*g)+(0.0722*b);
	bw=Math.round(bw*255);
	if (bw>=0 && bw<=255) {
		return bw;
	}
	return false;
}
