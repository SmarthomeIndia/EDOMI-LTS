###[DEF]###
[name	=Analoguhr]

[folderid=161]
[xsize	=100]
[ysize	=100]

[var1	=2]
[var2	=0]
[var3	=1]
[var4	=0]
[var5	=0]
[var6	=100]
[var7	=0]
[var8	=0]
[var9	=0]
[var10	=]

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
[columns=50,50]
[row]
	[var1 = select,2,'Typ','1#Stunden-/Minutenzeiger|2#Stunden-/Minutenzeiger (mit Mittelpunkt)|3#Stunden-/Minuten-/Sekundenzeiger|4#Stunden-/Minuten-/Sekundenzeiger (mit Mittelpunkt)']

[row]
	[var7 = select,2,'24h-Anzeige: Opazität','0#ohne 24h-Anzeige|100#100%|90#90%|80#80%|70#70%|60#60%|50#50%|40#40%|30#30%|20#20%|10#10%']

[row=Zeiger]
	[var2 = text,2,'Stärke (px, 0=autom.)','']

[row]
	[var9 = text,1,'Stundenzeiger: Länge (px, 0=autom.)','']
	[var8 = text,1,'Minutenzeiger: Länge (px, 0=autom.)','']

[row=Ziffernblatt]
	[var4 = text,1,'Stärke (px, 0=autom.)','']
	[var5 = text,1,'Länge (px, 0=autom.)','']

[row]
	[var6 = select,1,'Opazität','0#ohne Ziffernblatt|100#100%|90#90%|80#80%|70#70%|60#60%|50#50%|40#40%|30#30%|20#20%|10#10%']
	[var3 = select,1,'Darstellung','0#nur Stunden|1#Stunden und Minuten']

[row]
	[var10= text,2,'Konturstärke (px, 0=automatisch, leer=ohne)','']
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
	objSvg.innerHTML=graphics_svg_clock("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		pointerWidth:parseFloat(obj.dataset.var2),
		scalaOpacity:obj.dataset.var6,
		scalaSize:parseFloat(obj.dataset.var5),
		scalaWidth:parseFloat(obj.dataset.var4),
		scalaMode:obj.dataset.var3,
		fulldayOpacity:obj.dataset.var7,	
		minuteSize:parseFloat(obj.dataset.var8),
		hourSize:parseFloat(obj.dataset.var9),
		contourWidth:obj.dataset.var10
	},koValue);

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
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

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

	var objSvg=document.getElementById("e-"+elementId+"-svg");
	var objSvgContainer=document.getElementById("e-"+elementId+"-svgcontainer");
	objSvg.innerHTML=graphics_svg_clock("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		pointerWidth:parseFloat(obj.dataset.var2),
		scalaOpacity:obj.dataset.var6,
		scalaSize:parseFloat(obj.dataset.var5),
		scalaWidth:parseFloat(obj.dataset.var4),
		scalaMode:obj.dataset.var3,
		fulldayOpacity:obj.dataset.var7,	
		minuteSize:parseFloat(obj.dataset.var8),
		hourSize:parseFloat(obj.dataset.var9),
		contourWidth:obj.dataset.var10
	},koValue);
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Analoguhr" zeigt einen KO-Wert in Form einer "analogen" Uhr (SVG) an.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Typ: legt die Anzahl und die Darstellung der Zeiger fest
		<ul>
			<li>Stunden-/Minutenzeiger: zeigt nur einen Stunden- und den Minutenzeiger an</li>
			<li>Stunden-/Minutenzeiger (mit Mittelpunkt): dto., jedoch mit Hervorhebung des Zeiger-Mittelpunkts</li>
			<li>Stunden-/Minuten-/Sekundenzeiger: zeigt einen Stunden-, Minuten- und Sekundenzeiger an</li>
			<li>Stunden-/Minuten-/Sekundenzeiger (mit Mittelpunkt): dto., jedoch mit Hervorhebung des Zeiger-Mittelpunkts</li>
		</ul>
	</li>

	<li>
		24h-Anzeige: Opazität
		<ul>
			<li>legt die Opazität der 24h-Anzeige fest oder blendet die 24h-Anzeige aus</li>
			<li>die 24h-Anzeige wird durch eine Kontur in der Stärke der Minutenskala dargestellt (ein Vollkreis entspricht 24 Stunden)</li>
		</ul>
	</li>

	<li>
		Zeiger: Stärke
		<ul>
			<li>gibt die Stärke der Stunden-/Minutenzeiger an (Linienstärke in Pixeln): die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
			<li>Hinweis: Die Stärke des Sekundenzeigers entspricht stets der Hälfte der Stärke des Stunden-/Minutenzeigers.</li>
		</ul>
	</li>

	<li>
		Stunden-/Minutenzeiger: Länge
		<ul>
			<li>gibt die radiale Länge des Stunden/-Minutenzeigers an (Strichlänge in Pixeln): die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
			<li>Hinweis: Der Sekundenzeiger hat stets die gleiche Länge wie der Minutenzeiger.</li>
		</ul>
	</li>

	<li>
		Ziffernblatt: Stärke
		<ul>
			<li>gibt die Stärke des Stunden-/Minutenskala an (Linienstärke in Pixeln): die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
		</ul>
	</li>

	<li>
		Ziffernblatt: Länge
		<ul>
			<li>gibt die radiale Länge der Stundenskala an (Strichlänge in Pixeln): die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
			<li>Hinweis: die Länge der Minutenskala entspricht stets der halben Länge der Stundenskala</li>
		</ul>
	</li>

	<li>
		Ziffernblatt: Opazität
		<ul>
			<li>legt die Opazität des Ziffernblatts fest oder blendet das Ziffernblatt aus</li>
		</ul>
	</li>

	<li>
		Ziffernblatt: Darstellung
		<ul>
			<li>legt fest, ob nur die Stunden auf dem Ziffernblatt angezeigt werden sollen (z.B. bei sehr kleinen Analoguhren) oder auch die Minuten</li>
		</ul>
	</li>

	<li>
		Ziffernblatt: Konturstärke
		<ul>
			<li>optional kann eine Kontur mit dieser Stärke (in Pixeln) generiert werden</li>
			<li>die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
			<li>ohne diese Angabe (leer) wird keine Kontur angezeigt</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerung
		<ul>
			<li>dieser KO-Wert wird als "Uhrzeit" angezeigt</li>
			<li>Hinweis: das KO kann eine Zeitangabe wie "12:00:00" enthalten, oder einen FLOAT-Wert in Minuten (z.B. entspricht "60.5" der Uhrzeit "00:01:30")</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
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


<h2>Besonderheiten</h2>
<ul>
	<li>die Beschriftung wird stets zentriert angezeigt</li>
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit einem Klick auf dieses Visuelement werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt.
###[/HELP]###


