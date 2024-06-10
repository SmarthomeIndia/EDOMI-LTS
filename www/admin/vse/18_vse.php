###[DEF]###
[name	=Skizze]

[folderid=162]
[xsize	=300]
[ysize	=200]

[var1	=0]
[var2	=2]
[var3	=20]

[flagText		=0]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=0]
[flagCmd		=0]
[flagDesign		=1]
[flagDynDesign	=1]

[captionKo1		=Skizze (internes KO vom Typ Variant)]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = select,2,'Modus','0#beliebige Anzahl Polygone zulassen|1#max. 1 Polygon zulassen']

[row=Darstellung]
	[var2 = select,1,'Linienstärke (px)','1#1 px|2#2 px|3#3 px|4#4 px|5#5 px|6#6 px|7#7 px|8#8 px|9#9 px|10#10 px|15#15 px|20#20 px|30#30 px|40#40 px|50#50 px']
	[var3 = select,1,'Glättung (Polygone optimieren)','0#ohne|10#10% (minimal)|20#20%|30#30%|40#40%|50#50%|60#60%|70#70%|80#80%|90#90%|100#100% (maximal)']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
		n+="<tr><td>"+((isPreview)?"":"<span class='app2_pseudoElement'>SKIZZE</span>")+"</td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	return true;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
		n+="<tr><td><canvas id='e-"+elementId+"-canvas' style='width:100%; height:100%;'></canvas></td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	obj.dataset.blocked=0;

	if (visuElement_hasKo(elementId,1)) {
		visuElement_onDrag(document.getElementById("e-"+elementId+"-canvas"),((obj.dataset.var1==0)?1:0),1,-1);
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//kein Padding
	obj.style.padding="0";

	//Aktualisierung: nur wenn das Visuelement nicht das aktive Drag-Objekt ist (sonst erfolgt die Aktualisierung in der Drag-Funktion)
	if (!isActive) {
		VSE_VSEID_render(elementId,koValue,false,false);

		//blocked=0 nur setzen, wenn KO1 real gesetzt worden ist (also nicht ggf. während der Live-Vorschau)
		if (isRefresh) {
			obj.dataset.blocked=0;	//KO wurde gesetzt => Freigabe für das nächste Polygon
		}
	}	
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.blocked==0) {
			if (d.dataset.var1==0) {
				//aktuellen KO-Wert zurückgeben: dieser Wert wird als Initialwert verwendet (die Skizze soll ergänzt werden, nicht ersetzt)
				return visuElement_getKoValue(elementId,1);
			} else {
				//Canvas leeren
				VSE_VSEID_render(elementId,"",false,false);
			}
		} else {
			return false;	//Drag-Operation abbrechen
		} 
	}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		var mousePos=visuElement_getMousePosition(obj,dragObj,0);
		var x=mousePos.x;
		var y=mousePos.y;			
		if (x<0) {x=0;}
		if (y<0) {y=0;}
		if (x>mousePos.w-1) {x=mousePos.w-1;}
		if (y>mousePos.h-1) {y=mousePos.h-1;}

		var n=parseInt(x/mousePos.w*10000).toString(36)+","+parseInt(y/mousePos.h*10000).toString(36)+",";
		var value=visuElement_getDragKoValue()+n; 

		if (value.length<10000) {
			VSE_VSEID_render(elementId,value.substring(0,value.length-1),true,true);
			return value;
		} else {
			return false;	//Drag-Operation abbrechen
		}
	}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		if (dragValue===false) {
			obj.dataset.blocked=0;	//Abbruch durch Visu-Engine oder KO-Wert>10000 Zeichen => freigeben
		} else {
			obj.dataset.blocked=1;	//auf den nächsten Refresh warten
		}

		var value=visuElement_getDragKoValue();
		value=value.substring(0,value.length-1);
		if (obj.dataset.var3>0) {
			value=VSE_VSEID_OptimizePolygon(value,(obj.dataset.var3*10000/100)/100);
		}
		value+=";";
		VSE_VSEID_render(elementId,value,false,false);
		visuElement_setDragKoValue(value);
	}
}

VSE_VSEID_render=function(elementId,dataString,memory,lastonly) {
	var d=document.getElementById("e-"+elementId);
	if (d) {

		var canvas=document.getElementById("e-"+elementId+"-canvas");
		var w=canvas.offsetWidth;
		var h=canvas.offsetHeight;

		if (lastonly) {
			if (canvas && canvas.getContext) {var c=canvas.getContext("2d");} else {c=false;}
		} else {
			var c=canvasScale(canvas,w,h);
		}
		
		if (c!==false) {

			dataString=dataString.toString();

			c.strokeStyle=visuElement_getFgColor(d,0);	//Hinweis: var(--fgc0) funktioniert bei Canvas nicht
			c.lineWidth=d.dataset.var2;
			c.lineCap="round";

			if (lastonly) {
				var tmp=dataString.split(";");
				polygon=[radix_StringToArray(tmp.pop(),",",36)];
			} else {
				var polygon=new Array();
				var tmp=dataString.split(";");
				for (var t=0;t<tmp.length;t++) {polygon[t]=radix_StringToArray(tmp[t],",",36);}
				c.clearRect(0,0,w,h);
			}

			var polysMax=polygon.length;
			for (var t=0;t<polysMax;t++) {
				var pointsMax=polygon[t].length;
				c.beginPath();
				if (pointsMax>4) {
					c.moveTo(polygon[t][0]/10000*w,polygon[t][1]/10000*h);
					for (var tt=2;tt<pointsMax-4;tt+=2) {c.quadraticCurveTo(polygon[t][tt]/10000*w,polygon[t][tt+1]/10000*h,(polygon[t][tt]/10000*w+polygon[t][tt+2]/10000*w)/2,(polygon[t][tt+1]/10000*h+polygon[t][tt+3]/10000*h)/2);}
					if (!lastonly) {c.quadraticCurveTo(polygon[t][tt]/10000*w,polygon[t][tt+1]/10000*h,polygon[t][tt+2]/10000*w,polygon[t][tt+3]/10000*h);}
				} else if (pointsMax>2) {
					c.moveTo(polygon[t][0]/10000*w,polygon[t][1]/10000*h);
					c.lineTo(polygon[t][2]/10000*w+0.1,polygon[t][3]/10000*h+0.1);
				} else if (pointsMax>0) {
					c.moveTo(polygon[t][0]/10000*w,polygon[t][1]/10000*h);
					c.lineTo(polygon[t][0]/10000*w+0.1,polygon[t][1]/10000*h+0.1);
				}
				c.lineJoin="round";
				c.stroke();
			}

			if (memory) {
				c.fillStyle=visu_indiColor;
				var n=100-parseFloat(dataString.length/10000*100);
				c.clearRect(0,0,w,3);
				c.fillRect(0,0,w/100*n,3);
			}
			
		}
	}
}

VSE_VSEID_OptimizePolygon=function(dataString,e) {
	//nur das aktuelle Polygon optimieren und alle Polygone zurückgeben
	var dataArray=dataString.split(";");
	var points=arrayToMatrix(radix_StringToArray(dataArray.pop(),",",36),2);

	for (var t=(points.length-1);t>0;t--) {
		if (points[0][0]==points[t][0] && points[0][1]==points[t][1]) {points.pop();} else {break;}
	}

	var r=optimize(points,0,points.length-1,e);
	r=r.concat([points[points.length-1]]);

	var newArr=new Array();
	for (var t=0;t<r.length;t++) {newArr=newArr.concat(r[t]);}

	var newDataString="";
	for (var t=0;t<dataArray.length;t++) {newDataString+=dataArray[t]+";";}
	newDataString+=radix_ArrayToString(newArr,",",36);

	return newDataString;

	function optimize(points,id0,id1,e) {
		if (id0>=(id1-1)) {return [points[id0]];}
		var px=points[id0][0];
		var py=points[id0][1];
		var dx=points[id1][0]-px;
		var dy=points[id1][1]-py;
		var nn=Math.sqrt(dx*dx+dy*dy);
		var nx=-dy/nn;
		var ny=dx/nn;
		var ii=id0;
		var max=-1;
		for (var i=id0+1;i<id1;i++) {
			var p=points[i];
			var qx=p[0]-px;
			var qy=p[1]-py;
			var d=Math.abs(qx*nx+qy*ny);
			if (d>max) {
				max=d;
				ii=i;
			}
		}
		if (max<e) {return [points[id0]];}
		var p1=optimize(points,id0,ii,e);
		var p2=optimize(points,ii,id1,e);
		return p1.concat(p2);        
	}

	function arrayToMatrix(arr,dim) {
		var r=new Array();
		var t,tt;
		for (t=0,tt=-1;t<arr.length;t++) {
			if (t%dim===0) {
				tt++;
				r[tt]=new Array();
			}
			r[tt].push(arr[t]);
		}
		return r;
	}

}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Skizze" ermöglicht das Erstellen und Bearbeiten einer grafischen Skizze, mit der Maus wird eine Vektor-Zeichnung erstellt (die maximale Anzahl an Polygonen ist abhängig von deren Komplexität).

<b>Wichtig:</b>
Bitte die Hinweise zum Kommunikationsobjekt beachten (s.u.)!

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Modus: legt die maximal zulässige Anzahl an Polygonen fest (falls nur 1 Polygon zugelassen wird, wird die Skizze vor jedem Zeichenvorgang automatisch gelöscht)</li>
	<li>Linienstärke: legt die Linienstärke der Polygone in Pixeln fest</li>
	<li>Glättung: legt fest, in welchem Maß die Polygone geglättet werden (dies spart u.U. Speicherplatz und läßt die Skizze "harmonischer" erscheinen)</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Skizze
		<ul>
			<li><b>dieses KO muss ein internes KO von Typ "Variant" sein</b></li>
			<li>die Skizze wird in diesem KO gespeichert (das KO bzw. die Skizze kann somit z.B. auch in einem Datenarchiv archiviert werden)</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>Hinweis: die Verwendung des KO-Wertes zur Steuerung und Beschriftung ergibt in den meisten Fällen keinen Sinn, da der KO-Wert aus den Skizzendaten besteht</li>
			<li>Hinweis: Dieser KO-Wert wird während der Bedienung des Visuelements als Vorschau-Wert für das KO1 aller anderen Visuelemente mit <link>aktivierter Live-Vorschau***1002</link> bereitgestellt.</li>
		</ul>
	</li>

	<li>
		KO3: Steuerung des dynamischen Designs
		<ul>
			<li>dieser KO-Wert wird ausschließlich zur Steuerung eines <link>dynamischen Designs***1003</link> verwendet</li>
			<li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
			<li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
		</ul>
	</li>
</ul>

<b>Hinweis:</b>
Die Angabe von KO1 (intern, Typ "Variant) ist zwingend erforderlich, damit die Skizze gespeichert werden kann: Die Skizze wird KO1 als Wert zugewiesen.


<h2>Besonderheiten</h2>
<ul>
	<li>es muss stets ein internes KO von Typ "Variant" angegeben werden, damit die Skizze gespeichert werden kann</li>
	<li>eine Skizze wird in relativen Koordinaten gespeichert (Vektoren), d.h. eine Skizze skaliert stets mit dem Visuelement (ohne Berücksichtigung des Seitenverhältnisses)</li>
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
	<li>Beschriftung und Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit der Maus können einzelne Polygone durch Anklicken und Festhalten bzw. Loslassen gezeichnet werden. Nach jedem Erstellen eines Polygons wird die Skizze in das KO geschrieben. Das nächste Polygon kann erst dann gezeichnet werden, wenn der KO-Wert verarbeitet wurde.

Am oberen Rand der Skizze wird der verbleibende Speicherplatz (in Indikatorfarbe) für die gesamte Skizze angezeigt, ggf. wird der verbleibende Speicherplatz nach jedem Polygon neuberechnet (Glättung, s.o.).

<b>Hinweis:</b>
Zum Löschen einer Skizze genügt es, das entsprechende KO zu leeren (also auf "" zu setzen).
###[/HELP]###
