###[DEF]###
[name	=Skala]

[folderid=161]
[xsize	=100]
[ysize	=100]

[var1	=1]
[var2	=30]
[var3	=330]
[var4	=10]
[var5	=2]
[var6	=100]
[var7	=80]
[var8	=5]
[var9	=1]
[var10	=90]
[var11	=80]
[var12	=2]
[var13	=]
[var14	=60]
[var15	=]
[var16	=0]
[var17	=100]
[var18	=80]
[var19	=1]
[var20	=100]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]
###[/DEF]###


###[PROPERTIES]###
[columns=25,25,25,25]
[row]
	[var1 = select,2,'Typ','0#Linear|1#Polar']
	[var20= text,2,'Größe (%)','']

[row]
	[var2 = text,2,'Startwinkel','']
	[var3 = text,2,'Endwinkel','']

[row=Intervalle]
	[var4 = text,1,'Anzahl (mind. 1)','']
	[var5 = text,1,'Stärke (px)','']
	[var6 = text,1,'Anfang (%)','']
	[var7 = text,1,'Ende (%)','']

[row=Nebenintervalle]
	[var8 = text,1,'Anzahl (0=ohne)','']
	[var9 = text,1,'Stärke (px)','']
	[var10= text,1,'Anfang (%)','']
	[var11= text,1,'Ende (%)','']

[row=Beschriftung]
	[var12= text,1,'Intervall (0=ohne)','']
	[var13= text,1,'Einheit','']
	[var14= text,1,'Position (%)','']
	[var15= select,1,'Nachkommastellen','#beliebig|0#0 (x)|1#1 (x.y)|2#2 (x.yy)|3#3 (x.yyy)|4#4 (x.yyyy)|5#5 (x.yyyyy)']

[row]
	[var16= text,2,'Startwert','']
	[var17= text,2,'Endwert','']

[row=Kontur]
	[var19= text,2,'Stärke (px, 0=ohne)','']
	[var18= text,2,'Position (%)','']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="";
	n+="<div id='"+obj.id+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<svg id='"+obj.id+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>";
	n+="</div>";
	n+="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'><tr><td>"+meta.itemText+"</td></tr></table>";
	obj.innerHTML=n;

	var objSvg=document.getElementById(obj.id+"-svg");
	var objSvgContainer=document.getElementById(obj.id+"-svgcontainer");
	objSvg.innerHTML=graphics_svg_scale("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		size:obj.dataset.var20,
		angleFrom:parseFloat(obj.dataset.var2),
		angleTo:parseFloat(obj.dataset.var3),
		tickCount:parseInt(obj.dataset.var4),
		tickWidth:parseFloat(obj.dataset.var5),
		tickSizeFrom:parseFloat(obj.dataset.var6),
		tickSizeTo:parseFloat(obj.dataset.var7),
		subtickCount:parseInt(obj.dataset.var8),
		subtickWidth:parseFloat(obj.dataset.var9),
		subtickSizeFrom:parseFloat(obj.dataset.var10),
		subtickSizeTo:parseFloat(obj.dataset.var11),
		captionStep:parseInt(obj.dataset.var12),
		captionSuffix:obj.dataset.var13,
		captionSize:parseFloat(obj.dataset.var14),
		captionFixed:parseInt(obj.dataset.var15),
		captionRangeFrom:parseFloat(obj.dataset.var16),
		captionRangeTo:parseFloat(obj.dataset.var17),
		contourSize:parseFloat(obj.dataset.var18),
		contourWidth:parseFloat(obj.dataset.var19)
	});
	
	return true;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="";
	n+="<div id='e-"+elementId+"-svgcontainer' style='display:block; position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<svg id='e-"+elementId+"-svg' style='display:block; left:0; top:0; width:100%; height:100%; box-sizing:border-box;'></svg>";
	n+="</div>";
	n+="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr><td><span id='e-"+elementId+"-text'></span></td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	if (visuElement_hasCommands(elementId)) {
		visuElement_onClick(obj,function(veId,objId){visuElement_doCommands(veId);});
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//keine Seitensteuerung/Befehle angegeben: VE ist klicktranparent
	if (!visuElement_hasCommands(elementId)) {obj.style.pointerEvents="none";}

	document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

	var objSvg=document.getElementById("e-"+elementId+"-svg");
	var objSvgContainer=document.getElementById("e-"+elementId+"-svgcontainer");
	objSvg.innerHTML=graphics_svg_scale("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		size:obj.dataset.var20,
		angleFrom:parseFloat(obj.dataset.var2),
		angleTo:parseFloat(obj.dataset.var3),
		tickCount:parseInt(obj.dataset.var4),
		tickWidth:parseFloat(obj.dataset.var5),
		tickSizeFrom:parseFloat(obj.dataset.var6),
		tickSizeTo:parseFloat(obj.dataset.var7),
		subtickCount:parseInt(obj.dataset.var8),
		subtickWidth:parseFloat(obj.dataset.var9),
		subtickSizeFrom:parseFloat(obj.dataset.var10),
		subtickSizeTo:parseFloat(obj.dataset.var11),
		captionStep:parseInt(obj.dataset.var12),
		captionSuffix:obj.dataset.var13,
		captionSize:parseFloat(obj.dataset.var14),
		captionFixed:parseInt(obj.dataset.var15),
		captionRangeFrom:parseFloat(obj.dataset.var16),
		captionRangeTo:parseFloat(obj.dataset.var17),
		contourSize:parseFloat(obj.dataset.var18),
		contourWidth:parseFloat(obj.dataset.var19)
	});
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Skala" generiert eine statische Skala (SVG), z.B. als Hintergrund für eine <link>Wertanzeige***1002-27</link>.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Typ: legt fest, welche Art von Skala generiert werden soll
		<ul>
			<li>
				Linear: es wird eine lineare Skala erzeugt (wie z.B. bei einem Lineal)
				<ul>
					<li>Hinweis: die Ausrichtung der linearen Skala (horizontal oder vertikal) erfolgt stets automatisch anhand des Seitenverhältnisses (Breite:Höhe): Ist "Breite &ge; Höhe" wird die Skala horizontal ausgerichtet, ansonsten vertikal.</li>
				</ul>
			</li>
			<li>Polar: es wird eine kreisrunde Skala erzeugt (wie z.B. bei einer analogen Uhr)</li>
		</ul>
	</li>

	<li>
		Größe: legt die Länge (linear) bzw. den Durchmesser (polar) der grafischen Anzeige relativ zur Größe des Visuelements fest
		<ul>
			<li>erlaubt sind Werte von 0..100 Prozent</li>
			<li>bei linearer Wertanzeige: "Größe" legt lediglich die horizontale bzw. vertikale Ausdehung der Skala fest, alle Positionsangaben beziehen sich weiterhin auf die Visuelementgröße (Breite bzw. Höhe).</li>
			<li>bei polarer Wertanzeige: "Größe" reduziert ggf. den Gesamtdurchmesser der Skala, alle Positionsangaben beziehen sich nunmehr auf die neuberechnete Größe der Skala.</li>
		</ul>
	</li>

	<li>
		Startwinkel/Endwinkel: definiert die Ausdehnung der Skala (nur bei Typ "Polar") im Bereich von 0..360 (Grad)
		<ul>
			<li>beide Werte sind als absolute Winkelwerte anzugeben, z.B. führt 45/315 zu einem 3/4-Kreis (270 Grad), der nach unten offen ist</li>
			<li>Bezugspunkt (0 Grad) ist die 6-Uhr-Position</li>
			<li>Winkelwerte werden stets im Uhrzeigersinn umgesetzt, z.B. führt die Angabe 0/90 zu einem Viertelkreis unten links</li>
		</ul>
	</li>

	<li>
		Intervalle: definiert Anzahl und Darstellung der Hauptintervalle
		<ul>
			<li>Anzahl: legt die Anzahl der Hauptintervalle fest (mindestens "1", ansonsten wird keine Skala generiert)</li>
			<li>Stärke: legt die Linienstärke der Hauptintervalle in Pixeln fest</li>
			<li>
				Anfang/Ende: definiert die Start- und Endposition der Linien in Prozent:
				<ul>
					<li>beim Typ "Linear": die Angaben beziehen sich auf den linken bzw. oberen Rand des Visuelements (je nach Ausrichtung), z.B. erzeugt 0%/50% eine Linie vom linken Rand (0%) bis zur Mitte des Visuelements (50%)</li>
					<li>beim Typ "Polar": die Angaben beziehen sich auf den Radius der Skala, z.B. erzeugt 0%/100% eine Linie vom Mittelpunkt (0%) bis zum äußersten Rand des Visuelements (100%)</li>
				</ul>
			</li>
		</ul>
	</li>

	<li>
		Nebenintervalle: definiert Anzahl und Darstellung der (optionalen) Nebenintervalle
		<ul>
			<li>Anzahl: legt die Anzahl der Nebenintervalle fest (die Angabe "0" erzeugt keine Nebenintervalle)</li>
			<li>Stärke: legt die Linienstärke der Nebenintervalle in Pixeln fest</li>
			<li>
				Anfang/Ende: definiert die Start- und Endposition der Linien in Prozent:
				<ul>
					<li>beim Typ "Linear": die Angaben beziehen sich auf den linken bzw. oberen Rand des Visuelements (je nach Ausrichtung), z.B. erzeugt 0%/50% eine Linie vom linken Rand (0%) bis zur Mitte des Visuelements (50%)</li>
					<li>beim Typ "Polar": die Angaben beziehen sich auf den Radius der Skala, z.B. erzeugt 0%/100% eine Linie vom Mittelpunkt (0%) bis zum äußersten Rand des Visuelements (100%)</li>
				</ul>
			</li>
		</ul>
	</li>

	<li>
		Beschriftung: definiert Schrittweite und Darstellung der (optionalen) Beschriftung
		<ul>
			<li>
				Intervall: legt die Schrittweite der Beschriftung fest (die Angabe "0" erzeugt keine Beschriftung)
				<ul>
					<li>eine Schrittweite von "1" erzeugt an jedem "Tick" eine Beschriftung, "2" führt zur Beschriftung von jedem 2. Tick</li>
					<li>entspricht die Angabe der Anzahl der Hauptintervalle (s.o.), wird die Beschriftung nur beim ersten und beim letzten Tick erzeugt</li>
					<li>ist die Angabe größer als die Anzahl der Hauptintervalle (s.o.), wird die Beschriftung nur beim ersten Tick erzeugt</li>
				</ul>
			</li>
			<li>Einheit: optional wird diese Angabe hinter jede Beschriftung angefügt</li>
			<li>
				Position: definiert die Position der Beschriftung in Prozent (bezogen auf die Mitte des jeweiligen Beschriftungstextes)
				<ul>
					<li>beim Typ "Linear": die Angaben beziehen sich auf den linken bzw. oberen Rand des Visuelements (je nach Ausrichtung), z.B. erzeugt 50% eine Beschriftung in der Mitte des Visuelements (50%)</li>
					<li>beim Typ "Polar": die Angaben beziehen sich auf den Radius der Skala, z.B. erzeugt 0% eine Beschriftung im Mittelpunkt (0%)</li>
				</ul>
			</li>
			<li>Nachkommastellen: die Beschriftung wird ggf. auf die angegebene Anzahl von Nachkommastellen gerundet</li>
			<li>
				Startwert/Endwert: definiert den Start- und Endwert für die Beschriftung (die Zwischenwerte werden abhängig von der Anzahl der Hauptintervalle berechnet)
				<ul>
					<li>die Beschriftung erfolgt stets im Uhrzeigersinn (bzw. von links nach rechts oder von oben nach unten)</li>
					<li>der Startwert darf ggf. größer(!) als der Endwert sein, um eine Umkehrung der Beschriftungsrichtung zu erreichen</li>
					<li>Hinweis: Beträgt die Ausdehnung der Skala genau 360 Grad (nur bei Typ "Polar"), wird am Anfang bzw. Ende der Skala (0/360 Grad) nur die Beschriftung mit dem höheren Wert angezeigt (z.B. 25,50,75,100 anstelle von 0,25,50,75,100).</li>
				</ul>
			</li>
			<li>
				Hinweis: bei einer linearen Skala wird die Beschriftung wie folgt positioniert:
				<ul>
					<li>bei einer "Größe" von 100(%): die Beschriftung des ersten/letzten Ticks wird derart eingerückt, dass diese vollständig sichtbar ist</li>
					<li>bei einer "Größe" &lt; 100(%): die Beschriftung des ersten/letzten Ticks erfolgt wie bei alle anderen Ticks, so dass diese u.U. nicht vollständig sichtbar ist (die "Größe" ist entsprechend anzupassen)</li>
				</ul>
			</li>
		</ul>
	</li>

	<li>
		Kontur: optional kann eine Begrenzungslinie generiert werden
		<ul>
			<li>Stärke: legt die Linienstärke der Begrenzungslinie in Pixeln fest</li>
			<li>
				Position: definiert die Position der Begrenzungslinie in Prozent
				<ul>
					<li>beim Typ "Linear": die Angaben beziehen sich auf den linken bzw. oberen Rand des Visuelements (je nach Ausrichtung), z.B. erzeugt 50% eine Begrenzungslinie in der Mitte des Visuelements (50%)</li>
					<li>beim Typ "Polar": die Angaben beziehen sich auf den Radius der Skala, z.B. erzeugt 0% eine Begrenzungslinie im Mittelpunkt (0%)</li>
				</ul>
			</li>
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
		KO3: Steuerung des dynamischen Designs
		<ul>
			<li>dieser KO-Wert wird ausschließlich zur Steuerung eines <link>dynamischen Designs***1003</link> verwendet</li>
			<li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
			<li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
		</ul>
	</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit einem Klick auf dieses Visuelement werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt.
###[/HELP]###
