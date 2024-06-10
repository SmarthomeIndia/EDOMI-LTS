###[DEF]###
[name	=Wertanzeige]

[folderid=161]
[xsize	=100]
[ysize	=100]

[var1	=6]
[var2	=0]
[var3	=]
[var4	=]
[var5	=0]
[var6	=0]
[var7	=100]
[var8	=50]
[var9	=0]
[var10	=360]
[var11	=0]

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
	[var1 = select,2,'Typ','100#Linear: Strich|101#Linear: Kreis|102#Linear: Balken|103#Linear: Balken (runde Enden)|1#Polar: Zeiger|2#Polar: Zeiger (mit Mittelpunkt)|3#Polar: Strich|4#Polar: Kreis|5#Polar: Segment|6#Polar: Kontur|7#Polar: Kontur (runde Enden)']
	[var7 = text,1,'Größe (%)','']
	[var11= select,1,'Richtung','0#normal|1#invertiert']

[row]
	[var9 = text,2,'Startwinkel','']
	[var10= text,2,'Endwinkel','']

[row=Darstellung]
	[var2 = text,4,'Zeiger: Stärke/Größe (px, 0=automatisch)','']

[row=Bereichsanzeige]
	[var8 = select,1,'Opazität','0#unsichtbar|100#100%|90#90%|80#80%|70#70%|60#60%|50#50%|40#40%|30#30%|20#20%|10#10%']
	[var6 = select,1,'Typ','0#Kontur|1#Kontur (runde Enden)|2#Segment (nur bei polar)']
	[var5 = text,2,'Stärke (px, 0=automatisch)','']

[row=Wertebereich]
	[var3 = text,2,'Minimum (leer=KO-Filter)','']
	[var4 = text,2,'Maximum (leer=KO-Filter)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//Min/Max ggf. aus KO-Konfiguration/DPT-Array übernehmen (vmin/vmax)
$tmp=sql_getValues('edomiProject.editKo','valuetyp,vmin,vmax','id='.$item['gaid']);
if ($tmp!==false) {
	//Min/Max ggf. aus DPT-Array holen
	if (isEmpty($tmp['vmin'])) {$tmp['vmin']=$global_dptData[$tmp['valuetyp']][0];}
	if (isEmpty($tmp['vmax'])) {$tmp['vmax']=$global_dptData[$tmp['valuetyp']][1];}
	//leere Werte ersetzen
	if (isEmpty($item['var3'])) {sql_call("UPDATE edomiLive.visuElement SET var3='".sql_encodeValue($tmp['vmin'])."' WHERE (id=".$item['id'].")");}
	if (isEmpty($item['var4'])) {sql_call("UPDATE edomiLive.visuElement SET var4='".sql_encodeValue($tmp['vmax'])."' WHERE (id=".$item['id'].")");}
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
//Min/Max ggf. aus KO-Konfiguration/DPT-Array übernehmen (vmin/vmax)
$tmp=sql_getValues('edomiProject.editKo','valuetyp,vmin,vmax','id='.$item['gaid']);
if ($tmp!==false) {
	//Min/Max ggf. aus DPT-Array holen
	if (isEmpty($tmp['vmin'])) {$tmp['vmin']=$global_dptData[$tmp['valuetyp']][0];}
	if (isEmpty($tmp['vmax'])) {$tmp['vmax']=$global_dptData[$tmp['valuetyp']][1];}
	$property[0]=$tmp['vmin'];
	$property[1]=$tmp['vmax'];
} else {
	$property[0]='';
	$property[1]='';
}
?>
###[/EDITOR.PHP]###


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
	objSvg.innerHTML=graphics_svg_gauge("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		size:parseFloat(obj.dataset.var7),
		pointerWidth:parseFloat(obj.dataset.var2),
		vmin:((obj.dataset.var3!="")?obj.dataset.var3:property[0]),
		vmax:((obj.dataset.var4!="")?obj.dataset.var4:property[1]),
		rangeSize:parseFloat(obj.dataset.var5),
		rangeMode:obj.dataset.var6,
		rangeOpacity:obj.dataset.var8,
		angleFrom:parseFloat(obj.dataset.var9),
		angleTo:parseFloat(obj.dataset.var10),
		invert:((obj.dataset.var11==1)?true:false)
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
	objSvg.innerHTML=graphics_svg_gauge("var(--fgc0)",objSvgContainer.offsetWidth,objSvgContainer.offsetHeight,{
		mode:obj.dataset.var1,
		size:parseFloat(obj.dataset.var7),
		pointerWidth:parseFloat(obj.dataset.var2),
		vmin:obj.dataset.var3,
		vmax:obj.dataset.var4,
		rangeSize:parseFloat(obj.dataset.var5),	
		rangeMode:obj.dataset.var6,
		rangeOpacity:obj.dataset.var8,	
		angleFrom:parseFloat(obj.dataset.var9),
		angleTo:parseFloat(obj.dataset.var10),
		invert:((obj.dataset.var11==1)?true:false)
	},koValue);
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Wertanzeige" zeigt einen KO-Wert in Form einer linearen oder kreisrunden grafischen Darstellung (SVG) an.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Typ: definiert die grundsätzliche grafische Darstellung der Wertanzeige
		<ul>
			<li>Linear: es wird eine lineare Darstellung erzeugt
				<ul>
					<li>Strich: Strich</li>
					<li>Kreis: gefüllter Kreis</li>
					<li>Balken: Balken</li>
					<li>Balken (runde Enden): Balken mit abgerundeten Enden</li>
					<li>Hinweis: die Ausrichtung der Grafik (horizontal oder vertikal) erfolgt stets automatisch anhand des Seitenverhältnisses (Breite:Höhe): Ist "Breite &ge; Höhe" wird die Grafik horizontal ausgerichtet, ansonsten vertikal.</li>
				</ul>
			</li>

			<li>Polar: es wird eine kreisrunde Darstellung erzeugt
				<ul>
					<li>Zeiger: Zeiger (wie bei einem Tachometer)</li>
					<li>Zeiger (mit Mittelpunkt): Zeiger mit Anzeige des Mittelpunkts</li>
					<li>Strich: Strich am Innenrand der "Schleifbahn"</li>
					<li>Kreis: gefüllter Kreis am Innenrand der "Schleifbahn"</li>
					<li>Segment: gefülltes Kreissegment</li>
					<li>Kontur: Kreissegmentkontur am Innenrand der "Schleifbahn"</li>
					<li>Kontur (runde Enden): Kreissegmentkontur am Innenrand der "Schleifbahn" mit abgerundeten Enden</li>
				</ul>
			</li>
		</ul>
	</li>

	<li>
		Größe: legt die Länge (linear) bzw. den Durchmesser (polar) der grafischen Anzeige relativ zur Größe des Visuelements fest
		<ul>
			<li>erlaubt sind Werte von 0..100 Prozent</li>
		</ul>
	</li>

	<li>
		Richtung: definiert die Anzeigerichtung der Wertanzeige
		<ul>
			<li>normal: die Anzeige erfolgt bei polarer Wertanzeige im Uhrzeigersinn (der Minimum-Wert entspricht dem Startwinkel), bei linearer Wertanzeige von links nach rechts (bzw. von oben nach unten)</li>
			<li>invertiert: die Anzeige erfolgt bei polarer Wertanzeige gegen den Uhrzeigersinn (der Minimum-Wert entspricht dem Endwinkel), bei linearer Wertanzeige von rechts nach links (bzw. von unten nach oben)</li>
		</ul>
	</li>

	<li>
		Startwinkel/Endwinkel: definiert die Ausdehnung der polaren Wertanzeige im Bereich von 0..360 Grad (nur beim Typ "Polar")
		<ul>
			<li>beide Werte sind als absolute Winkelwerte anzugeben, z.B. führt 45/315 zu einem 3/4-Kreis (270 Grad), der nach unten offen ist</li>
			<li>Bezugspunkt (0 Grad) ist die 6-Uhr-Position</li>
			<li>Winkelwerte werden stets im Uhrzeigersinn umgesetzt, z.B. führt die Angabe 0/90 zu einem Viertelkreis unten links</li>
			<li>Hinweis: Diese Option steht nur bei einer polaren Wertanzeige zu Verfügung.</li>
		</ul>
	</li>

	<li>
		Zeiger: Stärke/Größe in Pixeln (die Angabe von "0" führt zu einer automatischen Anpassung an die Schleifbahn-Stärke)
		<ul>
			<li>beim Typ "Zeiger": diese Angabe bestimmt die Linienstärke der Zeiger (in Pixeln): die Angabe von "0" führt zu einer Skalierung in Abhängigkeit von der Größe des Visuelements</li>
			<li>beim Typ "Strich": diese Angabe bestimmt die Linienstärke des Strichs (in Pixeln), die radiale Länge entspricht stets der Angabe "Bereichsanzeige: Stärke" (auch wenn die Bereichsanzeige ausgeblendet ist)</li>
			<li>beim Typ "Kreis": diese Angabe bestimmt den Radius des Kreises (in Pixeln)</li>
			<li>beim Typ "Segment": diese Angabe wird ignoriert</li>
			<li>beim Typ "Kontur/Balken": diese Angabe bestimmt die Linienstärke der Kontur bzw. des Balkens (in Pixeln)</li>
		</ul>
	</li>

	<li>
		Bereichsanzeige: definiert das Erscheinungsbild der gesamten "Schleifbahn" bzw. eines Abschnitts (bei KO-Steuerung, s.u.)
		<ul>
			<li>
				Opazität: legt die Opazität der Bereichsanzeige fest oder blendet die Bereichsanzeige aus
			</li>
			<li>
				Typ: legt die Darstellung der Bereichsanzeige fest
				<ul>
					<li>Hinweis: der Typ "Segment" ist nur für die polare Wertanzeige verfügbar, bei einer linearen Wertanzeige wird "Segment" wie "Kontur (runde Enden)" dargestellt.</li>
				</ul>
			</li>
			<li>
				Stärke: bestimmt die Stärke der Bereichsanzeige in Pixeln (die Angabe von "0" führt zu einer automatischen Skalierung in Abhängigkeit von der Größe des Visuelements)
				<ul>
					<li>beim Typ "Strich": diese Angabe bestimmt zudem die Länge des Strichs (in Pixeln)</li>
					<li>beim Typ "Kreis": diese Angabe bestimmt zudem den Durchmesser(!) des Kreises (in Pixeln), sofern die Angabe "Zeiger: Stärke/Größe"=0 ist (automatisch)</li>
					<li>beim Typ "Kontur/Balken": diese Angabe bestimmt zudem die Stärke der Kontur bzw. des Balkens (in Pixeln), sofern die Angabe "Zeiger: Stärke/Größe"=0 ist (automatisch)</li>
				</ul>
			</li>
			<li>
				Hinweis: optional kann über den KO1-Wert der Umfang der angezeigten Bereichsanzeige begrenzt werden (siehe bei Kommunikationsobjekte)
			</li>
		</ul>
	</li>

	<li>
		Wertebereich (Minimum/Maxmium): definiert den Wertebereich für die Anzeige des KO-Wertes (Integer/Float)
		<ul>
			<li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
			<li>Hinweis: Wenn der Minimum-Wert <i>größer</i> als der Maxmium-Wert ist, werden die Werte intern korrigiert (für eine invertierte Darstellung ist die Option "Richtung" entsprechend auszuwählen).</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerung
		<ul>
			<li>dieser KO-Wert wird ggf. als Wert und Position angezeigt</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>
				ggf. wird dieser KO-Wert zudem als Begrenzung der Bereichsanzeige verwendet:
				<ul>
					<li>enthält der KO-Wert mehrere (durch ein Semikolon separierte) Werte, werden diese Werte als "Wert", "Minimum" und "Maximum" interpretiert</li>
					<li>z.B. führt der KO-Wert "20;15;25" zur Anzeige des Wertes "20" und die Bereichsanzeige wird auf die Werte "15" bis "25" begrenzt</li>
					<li>ein fehlender Minimum- bzw. Maximum-Wert (z.B. "20;;25") wird durch den entsprechenden Wert des definierten Wertebereichs ersetzt</li>
				</ul>
			</li>
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


