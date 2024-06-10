###[DEF]###
[name	=Farbauswahl (Bild)]

[folderid=162]
[xsize	=300]
[ysize	=200]

[var1	=2]
[var2	=1]
[var3	=0]
[var4	=-1]
[var5	=90]
[var6	=5]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=1]
[flagKo3		=1]
[flagPage		=0]
[flagCmd		=0]
[flagDesign		=1]
[flagDynDesign	=1]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = select,2,'Modus','0#Dimmwert setzen|1#RGB-Wert setzen|2#HSV-Wert setzen']

[row]
	[var2 = select,2,'Darstellung','0#neutral|1#Cursor']

[row]
	[var5 = text,1,'Cursor-Durchmesser (px)','']
	[var6 = text,1,'Cursor-Stärke (px)','']

[row=Farbauswahl abbrechen]
	[var3 = text,2,'Alpha-Schwellenwert (0=deaktiviert)','']

[row=Zyklisches Setzen]
	[var4 = select,2,'KO2 zyklisch setzen','-1#deaktiviert|0#aktiviert|100#aktiviert (alle 100 ms setzen)|250#aktiviert (alle 250 ms setzen)|500#aktiviert (alle 500 ms setzen)|1000#aktiviert (alle 1000 ms setzen)']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'><tr><td>"+meta.itemText+"</td></tr></table>";
	obj.innerHTML=n;

	//kein Padding, BG-Bild stretchen
	obj.style.padding="0";
	obj.style.backgroundSize="100% 100%";
	obj.style.backgroundRepeat="no-repeat";
	
	return true;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="<canvas id='e-"+elementId+"-canvas' style='position:absolute; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></canvas>";
	n+="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%; pointer-events:none;'>";
		n+="<tr><td><span id='e-"+elementId+"-text'></span></td></tr>";
	n+="</table>";
	n+="<div id='e-"+elementId+"-cursor' style='display:none; position:absolute; z-index:99999; left:0; top:0; width:"+obj.dataset.var5+"px; height:"+obj.dataset.var5+"px; border-radius:100%; border-style:solid; border-width:"+obj.dataset.var6+"px; box-sizing:border-box; pointer-events:none;'></div>";
	obj.innerHTML=n;

	obj.dataset.cancelvalue="";
	obj.dataset.imgurl="";
	
	if (visuElement_hasKo(elementId,2)) {
		visuElement_onDrag(document.getElementById("e-"+elementId+"-canvas"),0,2,obj.dataset.var4);
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//kein Padding, kein Hintergrundbild (nur Farbe)
	obj.style.padding="0";
	obj.style.background="var(--bgc0)";

	document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

	//Canvas-Refresh (Bild): nur bei Init, echtem Refresh und Bildwechsel (anti-flicker)
	var imgUrl=visuElement_getImageUrl(obj,0);
	if (isInit || (isRefresh && obj.dataset.imgurl.toString()!==imgUrl.toString())) {
		obj.dataset.imgurl=imgUrl;
		var canvas=document.getElementById("e-"+elementId+"-canvas");
		var w=canvas.offsetWidth;
		var h=canvas.offsetHeight;
		var c=canvasScale(canvas,w,h);
		if (c!==false) {
			c.clearRect(0,0,w,h);
			if (imgUrl!="") {
				var tmp=new Image();
				tmp.onload=function() {c.drawImage(tmp,0,0,w,h);}
				tmp.src=imgUrl;
			}
		}
	}
}

VSE_VSEID_DRAGSTART=function(elementId,dragObj) {
	var obj=document.getElementById("e-"+elementId);

	obj.dataset.cancelvalue=visuElement_getKoValue(elementId,2);

	if (obj.dataset.var2==1) {
		document.getElementById("e-"+elementId+"-text").style.display="none";
		document.getElementById("e-"+elementId+"-cursor").style.display="block";
	}
}

VSE_VSEID_DRAGMOVE=function(elementId,dragObj) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		var canvas=document.getElementById("e-"+elementId+"-canvas");

		var mousePos=visuElement_getMousePosition(obj,dragObj,0);
		var x=parseInt(mousePos.x);
		var y=parseInt(mousePos.y);			
		if (x<0) {x=0;}
		if (y<0) {y=0;}
		if (x>mousePos.w-1) {x=mousePos.w-1;}
		if (y>mousePos.h-1) {y=mousePos.h-1;}

		if (canvas.getContext) {
			var value="";
			var c=canvas.getContext("2d");
			var color=c.getImageData(x*displayPixelRatio,y*displayPixelRatio,1,1).data;
			var cursor=document.getElementById("e-"+elementId+"-cursor");
			var cursorColor="";

			var minAlpha=parseInt(obj.dataset.var3);
			if (isNaN(minAlpha)) {minAlpha=0;}
			if (minAlpha<0) {minAlpha=0;}
			if (minAlpha>255) {minAlpha=255;}

			if (color[3]<minAlpha) {
				if (obj.dataset.var2==1) {
					cursor.style.display="none";
				}
				
				return obj.dataset.cancelvalue;			

			} else {
				if (obj.dataset.var1==0) {
					var bw=RGBtoGREYSCALE(color[0],color[1],color[2]);
					if (bw!==false) {
						value=bw;
						cursorColor="rgb("+bw+","+bw+","+bw+")";
					}
				} else if (obj.dataset.var1==1) {
					var rgb=RGBtoRGBHEX(color[0],color[1],color[2]);
					if (rgb!==false) {
						value=rgb;
						cursorColor="#"+rgb;					
					}
				} else if (obj.dataset.var1==2) {
					var hsv=RGBtoHSV(color[0],color[1],color[2]);
					if (hsv!==false) {
						value=HSVtoHSVHEX(hsv[0],hsv[1],hsv[2]);
					}
					var rgb=RGBtoRGBHEX(color[0],color[1],color[2]);
					if (rgb!==false) {
						cursorColor="#"+rgb;			
					}
				}							

				if (obj.dataset.var2==1) {
					cursor.style.display="block";
					cursor.style.left=parseFloat(x-(cursor.offsetWidth/2))+'px';
					cursor.style.top=parseFloat(y-(cursor.offsetHeight/2))+'px';
					cursor.style.borderColor=cursorColor;
				}
				
				return value;			
			}
			
		}
	}
}

VSE_VSEID_DRAGEND=function(elementId,dragObj,dragValue) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		document.getElementById("e-"+elementId+"-text").style.display="inline";
		document.getElementById("e-"+elementId+"-cursor").style.display="none";
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Farbauswahl (Bild)" ermöglicht das Setzen eines KO-Wertes auf der Grundlage von Farbinformationen eines Bildes ("Colorpicker").

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Modus: Typ des (Farb-)wertes, der ermittelt werden soll (KO2 wird auf diesen Wert gesetzt)
		<ul>
			<li>Dimmwert setzen: ermittelt einen Helligkeitswert (KO2 wird auf einen Wert 0..255 gesetzt)</li>
			<li>RGB-Wert setzen: ermittelt einen RGB-Farbwert (KO2 wird auf einen Wert 000000..FFFFFF gesetzt)</li>
			<li>HSV-Wert setzen: ermittelt einen HSV-Farbwert (KO2 wird auf einen Wert 000000..FFFFFF gesetzt)</li>
		</ul>
	</li>

	<li>
		Darstellung: legt das Erscheinungsbild des Visuelements fest
		<ul>
			<li>Cursor: zeigt während der Bedienung einen ringförmigen Cursor in der aktuell gewählten Helligkeit bzw. Farbe an</li>
			<li>Hinweis: Falls die Option "Cursor" gewählt wurde, wird während der Bedienung die Beschriftung des Visuelements ausgeblendet.</li>
		</ul>
	</li>

	<li>Cursor-Durchmesser (px): legt den Gesamt-Durchmesser des Cursors in Pixeln fest (einschließlich der Cursor-Stärke)</li>

	<li>Cursor-Stärke (px): legt die Linienstärke der Umrandung des Cursors in Pixeln fest</li>

	<li>
		Alpha-Schwellenwert: legt fest, ob und wie ein ggf. vorhandener Alpha-Kanal des Bildes ausgewertet werden soll
		<ul>
			<li>0 (oder [leer]): der Alpha-Kanal wird ignoriert</li>
			<li>1..255: Farben mit einem Alpha-Wert &lt; 1..255 werden als "Abbruch" interpretiert, d.h. KO2 wird auf seinen ursprünglichen Wert gesetzt</li>
		</ul>
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
		KO1: Steuerung
		<ul>
			<li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
		</ul>
	</li>

	<li>
		KO2: Wert setzen
		<ul>
			<li>dieses KO wird auf den per Farbauswahl eingestellten Wert gesetzt</li>
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
Beim Modus "Dimmwert setzen" sollten die KOs mit dem Datentyp "Variant" bzw. "DPT 5" konfiguriert werden.
Beim Modus "RGB/HSV setzen" sollten die KOs mit dem Datentyp "Variant" bzw. "DPT 232" konfiguriert werden.


<h2>Besonderheiten</h2>
Der aktuelle Helligkeits-/RGB-/HSV-Wert an KO1 wird nicht(!) angezeigt, ggf. ist hierzu ein weiteres Visuelement einzusetzen oder KO1 wird wie üblich zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet:
Bei Bedarf kann KO1 mit dem gleichen KO wie KO2 verknüpft werden und so z.B. eine dynamische Hintergrundfarbe (oder Rahmenfarbe) definiert werden.
		
Als Bild-Grundlage dient stets das im Design gewählte Hintergrundbild. Auch ein PNG-Bild mit Alphakanal kann genutzt werden, der Alphakanal kann dabei ggf. als "Maskierung" interpretiert werden: Ein Alphawert &lt; "Alpha-Schwellenwert" (s.o.) bedeutet stets "leer", d.h. in diesem Bereich des Bildes wird kein Helligkeits-/RGB-/HSV-Wert übernommen ("Abbrechen").

<b>Hinweis:</b>
Die Verwendung von SVG-Bildern (Vektorgrafik) kann je nach Browser zu Problemen führen und wird daher ausdrücklich <i>nicht</i> empfohlen.

<ul>
	<li>KO2 wird ggf. auf seinen ursprünglichen Wert gesetzt, wenn ein Bildbereich mit einem Alphawert (s.o.) ausgewählt wurde</li>
	<li>Designs: Innenabstand wird ignoriert</li>
	<li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Ein Anklicken (und Festhalten) einer beliebigen Stelle des Bildes startet den Einstellvorgang.
Beim Verschieben der Maus wird stets der aktuelle Helligkeits-/RGB-/HSV-Wert im Bild ermittelt und ggf. als Cursor (Farbring) angezeigt.
Ein Loslassen der Maustaste beendet die Einstellung, KO2 wird ggf. auf den ermittelten Helligkeits-/RGB-/HSV-Wert gesetzt.
###[/HELP]###
