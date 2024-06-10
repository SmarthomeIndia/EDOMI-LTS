###[DEF]###
[name	=Touchpad]

[folderid=162]
[xsize	=100]
[ysize	=100]
[text	={#}]

[var1	=1]
[var2	=3]
[var3	=0]
[var4	=-1]
[var5	=]
[var6	=]
[var7	=]
[var8	=]
[var9	=]
[var10	=]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=1]
[flagKo3		=1]
[flagPage		=0]
[flagCmd		=0]
[flagDesign		=1]
[flagDynDesign	=1]

[captionKo1		=Status]
[captionKo2		=Wert setzen]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = select,2,'Modus','1#Kartesisch (relativ)|2#Polar (relativ)|5#Kartesisch (absolut)|6#Polar (absolut)|9#Kartesisch (relativ) mit Rückstellung|10#Polar (relativ) mit Rückstellung|13#Kartesisch (absolut) mit Rückstellung|14#Polar (absolut) mit Rückstellung']

[row]
	[var2 = select,1,'Darstellung','0#neutral|1#Cursor|2#Eingabewert|3#Cursor und Eingabewert']
	[var3 = select,1,'KO-Wert-Format','0#X und Y (bzw. Winkel und Radius)|1#nur X (bzw. Winkel)|2#nur Y (bzw. Radius)']

[row=Wertebereich]
	[var5 = text,1,'X: Minimum (leer=autom.)','']
	[var6 = text,1,'X: Maximum (leer=autom.)','']

[row]
	[var7 = text,1,'Y: Minimum (leer=autom.)','']
	[var8 = text,1,'Y: Maximum (leer=autom.)','']

[row]
	[var9 = text,1,'Raster (leer=ohne)','']
	[var10= select,1,'Nachkommastellen','#beliebig|0#0 (x)|1#1 (x.y)|2#2 (x.yy)|3#3 (x.yyy)|4#4 (x.yyyy)|5#5 (x.yyyyy)']

[row=Zyklisches Setzen]
	[var4 = select,2,'KO2 zyklisch setzen','-1#deaktiviert|0#aktiviert|100#aktiviert (alle 100 ms setzen)|250#aktiviert (alle 250 ms setzen)|500#aktiviert (alle 500 ms setzen)|1000#aktiviert (alle 1000 ms setzen)']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid und gaid2 gegenseitig ergänzen, falls nicht angegeben
if (($item['gaid']>0) && !($item['gaid2']>0)) {
	sql_call("UPDATE edomiLive.visuElement SET gaid2=".$item['gaid']." WHERE id=".$item['id']);
}
if (!($item['gaid']>0) && ($item['gaid2']>0)) {
	sql_call("UPDATE edomiLive.visuElement SET gaid=".$item['gaid2']." WHERE id=".$item['id']);
}

//Min/Max ggf. vertauschen
if ($item['var5']>$item['var6'] && !isEmpty($item['var6'])) {
	sql_call("UPDATE edomiLive.visuElement SET var5='".sql_encodeValue($item['var6'])."',var6='".sql_encodeValue($item['var5'])."' WHERE (id=".$item['id'].")");
}
if ($item['var7']>$item['var8'] && !isEmpty($item['var8'])) {
	sql_call("UPDATE edomiLive.visuElement SET var7='".sql_encodeValue($item['var8'])."',var8='".sql_encodeValue($item['var7'])."' WHERE (id=".$item['id'].")");
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'><tr><td>"+meta.itemText+"</td></tr></table>";
	obj.innerHTML=n;

	//kein Padding
	obj.style.padding="0";
	
	return true;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
		n+="<tr><td><span id='e-"+elementId+"-text'></span></td></tr>";
	n+="</table>";
	n+="<div id='e-"+elementId+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<svg id='e-"+elementId+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>";
	n+="</div>";

	if (obj.dataset.var2&2) {n+="<div id='e-"+elementId+"-editvalue' style='display:none; position:absolute; left:0; top:0; right:0; bottom:0; color:"+visu_indiColorText+"; pointer-events:none;'></div>";}
	obj.innerHTML=n;
	
	if (visuElement_hasKo(elementId,2)) {
		visuElement_onDrag(document.getElementById("e-"+elementId+"-svgcontainer"),((obj.dataset.var1&4)?0:1),2,obj.dataset.var4);
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//kein Padding
	obj.style.padding="0";

	var kovalue=visuElement_getKoValue(elementId,1);
	document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		//KO2-Wert für Rückstellung merken
		obj.dataset.cancelvalue=visuElement_getKoValue(elementId,2);

		//KO-Wert als Startposition merken (für relativen Modus)
		var kovalue=visuElement_getKoValue(elementId,1);
		visuElement_mapDragValueReset(kovalue);						
		
		if (obj.dataset.var2&2) {
			document.getElementById("e-"+elementId+"-text").style.display="none";
			document.getElementById("e-"+elementId+"-editvalue").style.display="block";
		}		
	}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		//KO-Wert aus Position ermitteln
		var mousePos=visuElement_getMousePosition(obj,dragObj,0);
		var pos=visuElement_mapDragValue(mousePos,null,obj.dataset.var3,((obj.dataset.var1&1)?0:1),((obj.dataset.var1&4)?0:1),obj.dataset.var5,obj.dataset.var6,obj.dataset.var7,obj.dataset.var8,null,null,obj.dataset.var10,obj.dataset.var9);						
		var valueX=pos.valuex;
		var valueY=pos.valuey;

		var n="";
		
		//KO-Format und Cursor anzeigen
		if (obj.dataset.var3==0) {
			var value=valueX+","+valueY;
			if (obj.dataset.var1&2) {
				n+=setCursor(mousePos.w/2,mousePos.h/2,parseInt(pos.cursory)*2,parseInt(pos.cursory)*2,pos.cursorx-180);
			} else {
				n+=setCursor(pos.cursorx,pos.cursory);
			}
		} else if (obj.dataset.var3==1) {
			var value=valueX;
			if (obj.dataset.var1&2) {
				if (mousePos.w>=mousePos.h) {var tmp=mousePos.h;} else {var tmp=mousePos.w;}
				n+=setCursor(mousePos.w/2,mousePos.h/2,tmp,tmp,pos.cursorx-180);
			} else {
				n+=setCursor(pos.cursorx,mousePos.h/2);
			}
		} else if (obj.dataset.var3==2) {
			var value=valueY;
			if (obj.dataset.var1&2) {
				n+=setCursor(mousePos.w/2,mousePos.h/2,parseInt(pos.cursory)*2,parseInt(pos.cursory)*2,0);
			} else {
				n+=setCursor(mousePos.w/2,pos.cursory);
			} 
		}

		if (obj.dataset.var2&1) {document.getElementById("e-"+elementId+"-svg").innerHTML=n;}
		if (obj.dataset.var2&2) {document.getElementById("e-"+elementId+"-editvalue").innerHTML=VSE_VSEID_formatEditvalue(visuElement_parseString(visuElement_getCaption(elementId),value.replace(","," &nbsp; ")));}
		return value;
	}
	
	function setCursor(x,y,w,h,a) {
		var n="";
		if (obj.dataset.var2&1) {
			if (obj.dataset.var1&1) {
				if (obj.dataset.var3==0 || obj.dataset.var3==1) {n+=line(x,0,x,mousePos.h);}
				if (obj.dataset.var3==0 || obj.dataset.var3==2) {n+=line(0,y,mousePos.w,y);}
			} else if (obj.dataset.var1&2) {
				n+=centerCircle(w/2);
				if (obj.dataset.var3!=2) {n+=radialLine(mousePos.w,mousePos.h,a-180,w/2);}
			}
		}
		return n;
	}

	function line(x1,y1,x2,y2) {
		return "<line x1='"+x1+"' y1='"+y1+"' x2='"+x2+"' y2='"+y2+"' stroke='"+visu_indiColor+"' stroke-width='2' vector-effect='non-scaling-stroke' fill='none'/>";
	}

	function radialLine(w,h,a,r) {
		var p1=getPolar(w/2,h/2,a,0);
		var p2=getPolar(w/2,h/2,a,r-1);
		return "<line x1='"+p1.x+"' y1='"+p1.y+"' x2='"+p2.x+"' y2='"+p2.y+"' stroke-linecap='butt' stroke='"+visu_indiColor+"' stroke-width='2' vector-effect='non-scaling-stroke'/>";

		function getPolar(mx,my,angle,radius) {
			return {x:Math.sin((-angle)*Math.PI/180)*radius+mx,y:Math.cos((-angle)*Math.PI/180)*radius+my};
		}
	}

	function centerCircle(r) {
		if (r>=1) {
			return "<circle cx='50%' cy='50%' r='"+(r-1)+"' stroke='"+visu_indiColor+"' stroke-width='2' vector-effect='non-scaling-stroke' fill='none'/>";
		}
		return "";
	}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		if (obj.dataset.var2&2) {
			document.getElementById("e-"+elementId+"-text").style.display="inline";
			document.getElementById("e-"+elementId+"-editvalue").style.display="none";
		}
		if (obj.dataset.var2&1) {document.getElementById("e-"+elementId+"-svg").innerHTML="";}

		//Rückstellung
		if (obj.dataset.var1&8) {return obj.dataset.cancelvalue;}
	}
}

VSE_VSEID_formatEditvalue=function(n) {
	return "<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'><tr><td><span style='background:"+visu_indiColor+"; color:"+visu_indiColorText+"; padding:1px; border-radius:3px;'>"+n+"</span></td></tr></table>";
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Touchpad" ermöglicht das Setzen eines KO-Wertes mit Hilfe der Maus bzw. des Touchscreens.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Modus: legt das Eingabeverhalten und Koordinatensystem des Touchpads fest
		<ul>
			<li>Kartesisch: KO2 wird auf die kartesischen Koordinaten (X,Y) der Eingabe gesetzt</li>
			<li>Polar: KO2 wird auf die polaren Koordinaten (Winkel,Radius) der Eingabe gesetzt (bezogen auf den Mittelpunkt des Visuelements)</li>
			<li>relativ: Der Wert von KO2 wird relativ zum aktuellen Wert (KO1) verändert, d.h. das Touchpad kann an einer beliebigen Position "angefasst" werden, ohne dass eine Wertänderung erfolgt. Erst beim Bewegen wird der Wert relativ zu dieser Startposition abgeändert.</li>
			<li>absolut: Der Wert von KO2 wird unabhängig vom aktuellen Wert (KO1) gesetzt, d.h. beim "Anfassen" des Touchpads wird bereits der mit dieser Position korrespondierende Wert gesetzt.</li>
			<li>mit Rückstellung: nach dem Beenden der Eingabe wird KO2 auf seinen ursprünglichen Wert gesetzt (z.B. für Joystick-Anwendungen)</li>
			<li>Hinweis: Im Modus "Polar" befindet sich der Winkel 0 Grad auf 6-Uhr-Position, im Uhrzeigersinn wird der Wert erhöht.</li>
		</ul>
	</li>

	<li>
		Darstellung: legt das Erscheinungsbild des Touchpads fest
		<ul>
			<li>Cursor: zeigt während der Bedienung einen Cursor in Indikatorfarbe an (je nach Modus wird der Cursor individuell dargestellt)</li>
			<li>Eingabewert: zeigt während der Bedienung den eingestellten Wert in Indikatorfarbe an</li>
		</ul>
	</li>

	<li>
		KO-Wert-Zuweisung: legt das Format des KO-Werts fest
		<ul>
			<li>X und Y: KO2 wird auf den X- und den Y-Wert gesetzt (separiert durch ein Komma), z.B. "80,123"</li>
			<li>nur X (bzw. Winkel): KO2 wird nur auf den X-Wert gesetzt, z.B. "80"</li>
			<li>nur Y (bzw. Radius): KO2 wird nur auf den Y-Wert gesetzt, z.B. "123"</li>
			<li>Hinweis: Beim Modus "Polar" entspricht der X-Wert dem Winkel und der Y-Wert dem Radius der Eingabe.</li>
		</ul>
	</li>

	<li>
		Minimum (Integer/Float) für X und Y: unterer Grenzwert der Eingabe
		<ul>
			<li>wird dieses Feld [leer] belassen, werden die Dimensionen des Visuelements bzw. ein Winkel von 0..360 Grad angewendet</li>
		</ul>
	</li>

	<li>
		Maximum (Integer/Float) für X und Y: oberer Grenzwert der Eingabe
		<ul>
			<li>wird dieses Feld [leer] belassen, werden die Dimensionen des Visuelements bzw. ein Winkel von 0..360 Grad angewendet</li>
		</ul>
	</li>

	<li>
		Raster (Integer/Float): die Eingabe wird auf einen Wert mit dieser "Schrittweite" umgerechnet
		<ul>
			<li>z.B. Raster=0.5: die Eingabe 0.45 wird zu 0, die Eingabe 2.98 wird zu 2.5 umgerechnet</li>
		</ul>
	</li>

	<li>
		Nachkommastellen: die Eingabe wird ggf. auf die angegebene Anzahl von Nachkommastellen gerundet
	</li>

	<li>
		KO2 zyklisch setzen: legt fest, wann und wie häufig KO2 auf einen Wert gesetzt werden soll
		<ul>
			<li>deaktiviert: das KO wird nur beim Beenden ("Loslassen") der Eingabe auf den entsprechenden Wert gesetzt</li>
			<li>aktiviert: das KO wird beim Beenden und <i>während</i> der Eingabe (jedoch nur bei einer Wertänderung) auf den entsprechenden Wert gesetzt - dies wird u.U. zu einer hohen Buslast führen!</li>
			<li>aktiviert (alle ... ms setzen): das KO wird beim Beenden und <i>während</i> der Eingabe (jedoch nur bei einer Wertänderung) auf den entsprechenden Wert gesetzt, jedoch nur in dem angegebenen Intervall</li>
			<li>Wichtig: Ist diese Option aktiviert, wird das Visuelement keine Live-Vorschau-Werte bereitstellen.</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Status
		<ul>
			<li>dieser KO-Wert wird ggf. als Wert und Position angezeigt und dient als Grundlage für eine relative Wertänderung</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
		</ul>
	</li>

	<li>
		KO2: Wert setzen
		<ul>
			<li>dieses KO wird auf den per Touchpad eingestellten Wert gesetzt</li>
			<li>im Modus "mit Rückstellung" wird KO2 zudem nach Beenden der Eingabe auf seinen ursprünglichen Wert gesetzt
			<li>Hinweis: je nach Einstellung wird das KO auf ein X/Y-Koordinatenpaar gesetzt, z.B. "80,123"</li>
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
Bei einer Aktivierung ergänzen sich KO1 und KO2 gegenseitig: Wird z.B. KO1 nicht angegeben, wird KO1 automatisch mit dem gleichen KO wie KO2 verknüpft (und umgekehrt).


<h2>Besonderheiten</h2>
<ul>
	<li>Designs: Innenabstand wird ignoriert</li>
	<li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Durch das Anklicken (und Festhalten) einer beliebigen Stelle des Visuelements wird die Eingabe gestartet.
Ein Verschieben der Maus (mit gedrückter Maustaste) werden die entsprechenden Koordinaten (je nach Modus) ermittelt.
Ein Loslassen der Maustaste beendet die Eingabe.
###[/HELP]###


