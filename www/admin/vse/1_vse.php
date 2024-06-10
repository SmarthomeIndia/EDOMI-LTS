###[DEF]###
[name	=Universalelement]

[folderid=161]
[xsize	=100]
[ysize	=50]

[var1	=0]
[var2	=0]
[var3	=3]
[var4	=0]
[var15	=0]
[var16	=1]
[var7	=2]
[var8	=0]
[var9	=]
[var10	=]

[var5	=0]
[var6	=0]

[var11	=0]
[var12	=0]
[var13	=0]
[var14	=0]

[flagText		=1]
[flagKo1		=1]
[flagKo2		=1]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]
###[/DEF]###


###[PROPERTIES]###
[columns=50,25,25]
[row]
	[var1 =	select,1,'Klick-Modus','0#Kurz-Klick|1#Lang-Klick|2#Kurz- und Lang-Klick']
	[var2 =	select,2,'Klick-Indikatoren','3#deaktiviert|1#nur Klick-Indikator|2#nur Lang-Klick-Animation|0#Klick-Indikator und Lang-Klick-Animation']

[row=Klick-Verhalten]
	[var3 = select,1,'Kurz-Klick','0#deaktiviert|1#Seitensteuerung|2#Befehle|3#Seitensteuerung und Befehle|4#KO2 setzen|5#Seitensteuerung und KO2 setzen|6#Befehle und KO2 setzen|7#Seitensteuerung, Befehle und KO2 setzen']
	[var15 = text,2,'Kurz-Klick: KO2-Wert','max. 20 Zeichen']

[row]
	[var4 = select,1,'Lang-Klick','0#deaktiviert|1#Seitensteuerung|2#Befehle|3#Seitensteuerung und Befehle|4#KO2 setzen|5#Seitensteuerung und KO2 setzen|6#Befehle und KO2 setzen|7#Seitensteuerung, Befehle und KO2 setzen']
	[var16 = text,2,'Lang-Klick: KO2-Wert','max. 20 Zeichen']
	
[row=Hintergrundbild]
	[var7 = select,1,'Skalierung','0#Originalgröße des Bildes|1#Individuelle Größe|2#wie Visuelement (ggf. verzerren)|3#wie Visuelement (Seitenverhältnis beibehalten, ggf. beschneiden)|4#wie Visuelement (Seitenverhältnis beibehalten)']
	[var9 = text,1,'Breite (px)','']
	[var10= text,1,'Höhe (px)','']

[row]
	[var8 = select,3,'Wiederholung','0#ohne|1#horizontal|2#vertikal|3#horizontal und vertikal']

[row=Beschriftung]
	[var5 = select,1,'vertikale Ausrichtung','1#oben|0#mittig|2#unten']
	[var6 = text,2,'vertikaler Innenabstand','']

[row=Symbol (Zusatzbild 1)]
	[var11= select,1,'Position','0#kein Symbol|1#links|2#rechts|3#oben|4#unten']
	[var12= select,2,'Ausrichtung','1#links/oben|0#mittig|2#rechts/unten']

[row]
	[var14= text,1,'Innenabstand (px)','']
	[var13= text,2,'Größe (px)','']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	if (obj.dataset.var5==1) {var textAlign="top";} else if (obj.dataset.var5==2) {var textAlign="bottom";} else {var textAlign="middle";}
	if (obj.dataset.var11==1 || obj.dataset.var11==2) {
		if (obj.dataset.var12==1) {var imgAlign="top";} else if (obj.dataset.var12==2) {var imgAlign="bottom";} else  {var imgAlign="middle";}
	} else if (obj.dataset.var11==3 || obj.dataset.var11==4) {
		if (obj.dataset.var12==1) {var imgAlign="left";} else if (obj.dataset.var12==2) {var imgAlign="right";} else  {var imgAlign="center";}
	}
	var	n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
		if (obj.dataset.var11==3) {n+="<tr><td align='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; height:"+obj.dataset.var13+"px;'><img src='"+visuElement_getImageUrl(obj,1)+"' valign='middle' draggable='false' style='margin:0; width:auto; height:"+obj.dataset.var13+"px;'></td></tr>";}
		n+="<tr>";
			if (obj.dataset.var11==1) {n+="<td valign='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; width:"+obj.dataset.var13+"px;'><img src='"+visuElement_getImageUrl(obj,1)+"' valign='middle' draggable='false' style='margin:0; width:"+obj.dataset.var13+"px; height:auto;'></td>";}
			n+="<td valign='"+textAlign+"'><div style='padding:"+obj.dataset.var6+"px "+((obj.dataset.var11>0)?visuElement_getCssProperty(obj,"padding-right","0px"):"0px")+" "+obj.dataset.var6+"px "+((obj.dataset.var11>0)?visuElement_getCssProperty(obj,"padding-left","0px"):"0px")+";'>"+meta.itemText+"</div></td>";
			if (obj.dataset.var11==2) {n+="<td valign='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; width:"+obj.dataset.var13+"px;'><img src='"+visuElement_getImageUrl(obj,1)+"' valign='middle' draggable='false' style='margin:0; width:"+obj.dataset.var13+"px; height:auto;'></td>";}
		n+="</tr>";
		if (obj.dataset.var11==4) {n+="<tr><td align='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; height:"+obj.dataset.var13+"px;'><img src='"+visuElement_getImageUrl(obj,1)+"' valign='middle' draggable='false' style='margin:0; width:auto; height:"+obj.dataset.var13+"px;'></td></tr>";}
	n+="</table>";
	obj.innerHTML=n;

	//Symbol aktiv: kein Design-Padding
	if (obj.dataset.var11>0) {obj.style.padding="0";}
	
	//Hintergrundbild: Style festlegen
	if (obj.dataset.var7==0) {obj.style.backgroundSize="auto";}
	if (obj.dataset.var7==1) {obj.style.backgroundSize=obj.dataset.var9+"px "+obj.dataset.var10+"px";}
	if (obj.dataset.var7==2) {obj.style.backgroundSize="100% 100%";}
	if (obj.dataset.var7==3) {obj.style.backgroundSize="cover";}
	if (obj.dataset.var7==4) {obj.style.backgroundSize="contain";}						
	if (obj.dataset.var8==0) {obj.style.backgroundRepeat="no-repeat";}
	if (obj.dataset.var8==1) {obj.style.backgroundRepeat="repeat-x";}
	if (obj.dataset.var8==2) {obj.style.backgroundRepeat="repeat-y";}
	if (obj.dataset.var8==3) {obj.style.backgroundRepeat="repeat";}

	return true;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	if (obj.dataset.var5==1) {var textAlign="top";} else if (obj.dataset.var5==2) {var textAlign="bottom";} else {var textAlign="middle";}
	if (obj.dataset.var11==1 || obj.dataset.var11==2) {
		if (obj.dataset.var12==1) {var imgAlign="top";} else if (obj.dataset.var12==2) {var imgAlign="bottom";} else  {var imgAlign="middle";}
	} else if (obj.dataset.var11==3 || obj.dataset.var11==4) {
		if (obj.dataset.var12==1) {var imgAlign="left";} else if (obj.dataset.var12==2) {var imgAlign="right";} else  {var imgAlign="center";}
	}
	var	n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
		if (obj.dataset.var11==3) {n+="<tr><td align='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; height:"+obj.dataset.var13+"px;'><img id='e-"+elementId+"-icon' src='' valign='middle' draggable='false' style='margin:0; width:auto; height:"+obj.dataset.var13+"px;'></td></tr>";}
		n+="<tr>";
			if (obj.dataset.var11==1) {n+="<td valign='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; width:"+obj.dataset.var13+"px;'><img id='e-"+elementId+"-icon' src='' valign='middle' draggable='false' style='margin:0; width:"+obj.dataset.var13+"px; height:auto;'></td>";}
			n+="<td valign='"+textAlign+"'><div id='e-"+elementId+"-text' style='padding:"+obj.dataset.var6+"px "+((obj.dataset.var11>0)?visuElement_getCssProperty(obj,"padding-right","0px"):"0px")+" "+obj.dataset.var6+"px "+((obj.dataset.var11>0)?visuElement_getCssProperty(obj,"padding-left","0px"):"0px")+";'></div></td>";
			if (obj.dataset.var11==2) {n+="<td valign='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; width:"+obj.dataset.var13+"px;'><img id='e-"+elementId+"-icon' src='' valign='middle' draggable='false' style='margin:0; width:"+obj.dataset.var13+"px; height:auto;'></td>";}
		n+="</tr>";
		if (obj.dataset.var11==4) {n+="<tr><td align='"+imgAlign+"' style='padding:"+obj.dataset.var14+"px; height:"+obj.dataset.var13+"px;'><img id='e-"+elementId+"-icon' src='' valign='middle' draggable='false' style='margin:0; width:auto; height:"+obj.dataset.var13+"px;'></td></tr>";}
	n+="</table>";
	obj.innerHTML=n;

	if (visuElement_hasCommands(elementId) || visuElement_hasKo(elementId,2)) {
		if (obj.dataset.var1==0) {
			//nur Shortclick
			visuElement_onClick(obj,
				function(veId,objId){VSE_VSEID_longclickControl(veId,false);},((obj.dataset.var2==0 || obj.dataset.var2==1)?true:false)
			);
		} else if (obj.dataset.var1==1) {
			//nur Longclick
			visuElement_onClick(obj,
				false,false,
				function(veId,objId){VSE_VSEID_longclickControl(veId,true);},((obj.dataset.var2==0 || obj.dataset.var2==1)?true:false),((obj.dataset.var2==0 || obj.dataset.var2==2)?true:false)
			);
		} else {
			//Short-/Longclick
			visuElement_onClick(obj,
				function(veId,objId){VSE_VSEID_longclickControl(veId,false);},((obj.dataset.var2==0 || obj.dataset.var2==1)?true:false),
				function(veId,objId){VSE_VSEID_longclickControl(veId,true);},((obj.dataset.var2==0 || obj.dataset.var2==1)?true:false),((obj.dataset.var2==0 || obj.dataset.var2==2)?true:false)
			);
		}
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//keine Seitensteuerung/Befehle und kein KO2 angegeben: VE ist klicktranparent
	if (visuElement_hasCommands(elementId)===false && !(visuElement_hasKo(elementId,2))) {obj.style.pointerEvents="none";}

	//Symbol aktiv: kein Design-Padding
	if (obj.dataset.var11>0) {obj.style.padding="0";}

	//Hintergrundbild: Style festlegen
	if (obj.dataset.var7==0) {obj.style.backgroundSize="auto";}
	if (obj.dataset.var7==1) {obj.style.backgroundSize=obj.dataset.var9+"px "+obj.dataset.var10+"px";}
	if (obj.dataset.var7==2) {obj.style.backgroundSize="100% 100%";}
	if (obj.dataset.var7==3) {obj.style.backgroundSize="cover";}
	if (obj.dataset.var7==4) {obj.style.backgroundSize="contain";}						
	if (obj.dataset.var8==0) {obj.style.backgroundRepeat="no-repeat";}
	if (obj.dataset.var8==1) {obj.style.backgroundRepeat="repeat-x";}
	if (obj.dataset.var8==2) {obj.style.backgroundRepeat="repeat-y";}
	if (obj.dataset.var8==3) {obj.style.backgroundRepeat="repeat";}	
	
	if (obj.dataset.var11>0) {document.getElementById("e-"+elementId+"-icon").src=visuElement_getImageUrl(obj,1);}
	document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);
}

VSE_VSEID_longclickControl=function(elementId,long) {
	var obj=document.getElementById("e-"+elementId);
	if (obj) {
		//Kurz-Klick
		if ((obj.dataset.var1==0 || obj.dataset.var1==2) && !long) {
			if (visuElement_hasCommands(elementId)) {
				if (obj.dataset.var3&1 && obj.dataset.var3&2) {
					visuElement_doCommands(elementId);
				} else if (obj.dataset.var3&1) {
					visuElement_doCommands(elementId,false,true,true,true);
				} else if (obj.dataset.var3&2) {
					visuElement_doCommands(elementId,true,false,false,false);
				}
			}
			if (obj.dataset.var3&4) {
				visuElement_setKoValue(elementId,2,obj.dataset.var15);
			}

		//Lang-Klick
		} else if ((obj.dataset.var1==1 || obj.dataset.var1==2) && long) {
			if (visuElement_hasCommands(elementId)) {
				if (obj.dataset.var4&1 && obj.dataset.var4&2) {
					visuElement_doCommands(elementId);
				} else if (obj.dataset.var4&1) {
					visuElement_doCommands(elementId,false,true,true,true);
				} else if (obj.dataset.var4&2) {
					visuElement_doCommands(elementId,true,false,false,false);
				}
			}
			if (obj.dataset.var4&4) {
				visuElement_setKoValue(elementId,2,obj.dataset.var16);
			}
		}
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Universalelement" kann vielfältig eingesetzt werden, da es keine spezielle Funktionalität implementiert. Mit dem Universalelement können z.B. Schaltflächen, Beschriftungen, Symbole oder Hintergründe realisiert werden.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Klick-Modus: legt fest, auf welches Klick-Ereignis das Visuelement reagieren soll
		<ul>
			<li>Kurz-Klick: das Visuelement reagiert nur auf einen kurzen (normalen) Klick</li>
			<li>Lang-Klick: das Visuelement reagiert nur auf einen langen Klick (1 Sekunde)</li>
			<li>Kurz- und Lang-Klick: das Visuelement reagiert auf einen kurzen und auf einen langen Klick</li>
		</ul>
	</li>

	<li>
		Klick-Indikatoren: legt fest, ob der Klick-Indikator bzw. die Lang-Klick-Animation angezeigt werden sollen
		<ul>
			<li>deaktiviert: Klick-Indikator und Lang-Klick-Animation werden nicht angezeigt</li>
			<li>nur Klick-Indikator: es wird nur der Klick-Indikator angezeigt (bei kurzem und langem Klick)</li>
			<li>nur Lang-Klick-Animation: es wird nur die Lang-Klick-Animation angezeigt (sofern der Klick-Modus Lang-Klicks erlaubt)</li>
			<li>Klick-Indikator und Lang-Klick-Animation: der Klick-Indikator und die Lang-Klick-Animation werden angezeigt</li>
		</ul>
	</li>

	<li>
		Klick-Verhalten: legt individuell für einen Kurz- und/oder Lang-Klick fest, welche Aktionen ausgeführt werden sollen
		<ul>
			<li>deaktiviert: bei einem Klick wird keine Aktion ausgeführt (der Klick-Indikator wird jedoch ggf. angezeigt)</li>
			<li>Seitensteuerung: bei einem Klick werden die definierten Seitensteuerungen ausgeführt (Seite aufrufen und Popup schließen)</li>
			<li>Befehle: bei einem Klick werden die definierten Befehle ausgeführt</li>
			<li>KO2 setzen: bei einem Klick wird KO2 ggf. auf den für dieses Klickereignis angegebenen Wert gesetzt</li>
		</ul>
	</li>

	<li>
		Hintergrundbild: legt diverse Darstellungsparameter des ggf. im <link>Design***1003</link> definierten Hintergrundbildes fest 
		<ul>
			<li>Skalierung: gibt an, wie das Hintergrundbild bezogen auf die Größe des Visuelements skaliert wird</li>
			<li>Breite/Höhe: legt die Größe des Bildes in Pixeln fest (nur im Zusammenhang mit der Skalierungs-Option "individuelle Größe")</li>
			<li>Wiederholung: gibt an, ob und in welche Richtung das Hintergrundbild ggf. wiederholt/gekachelt wird (nur mit der Skalierungs-Option "individuelle Größe" sinnvoll)</li>
		</ul>
	</li>

	<li>
		Beschriftung: legt diverse Darstellungsparameter der Beschriftung (zusätzlich zu den Design-Einstellungen) fest 
		<ul>
			<li>vertikale Ausrichtung: legt fest, wie die Beschriftung vertikal ausgerichtet wird</li>
			<li>vertikaler Innenabstand: legt den Innenabstand in vertikaler Richtung fest (in Pixeln)</li>
		</ul>
	</li>

	<li>
		Symbol: optional kann ein Symbol ("Zusatzbild 1" in den Design-Einstellungen) angezeigt werden 
		<ul>
			<li>Position: definiert ggf. die Position des Symbols</li>
			<li>Ausrichtung: legt fest, wie das Symbol innerhalb dessen Position ausgerichtet wird</li>
			<li>Innenabstand: definiert den Innenabstand des Symbols innerhalb dessen Position (in Pixeln)</li>
			<li>Größe: legt die Größe des Symbols in Pixeln fest (je nach Position gibt dieser Wert die Breite oder die Höhe an)</li>
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
			<li>dieses KO wird beim Anklicken ggf. auf den angegebenen Wert gesetzt</li>
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
	<li>Dieses Visuelement verhält sich nur dann <link>klicktransparent***1002</link>, wenn keine Seitensteuerungen/Befehle <i>und</i> kein KO2 zugewiesen wurden!</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit einem Kurz- bzw. Lang-Klick (je nach Einstellungen) auf dieses Visuelement wird KO2 ggf. auf den für dieses Klickereignis angebenen Wert gesetzt, anschließend werden ggf. alle zugewiesenen Seitensteuerungen/Befehle ausgeführt.
###[/HELP]###
