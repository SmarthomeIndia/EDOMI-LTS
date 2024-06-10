###[DEF]###
[name	=Bild-URL/Webseite]

[folderid=161]
[xsize	=300]
[ysize	=200]

[var1	=0]
[var2	=0]
[var3	=0]
[var4	=0]
[var5	=0]
[var6	=1]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]

[captionText	=URL]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var2 = select,1,'URL-Typ','0#Bild|1#Webseite']
	[var6 = check,1,'&nbsp;','Caching verhindern']

[row]
	[var4 = select,2,'Bild: Format','0#Breite und Höhe anpassen|1#Breite anpassen|2#Höhe anpassen|3#Originalgröße']

[row]
	[var1 = select,2,'Webseite: Hintergrund',''0#Streifenmuster|1#kein Hintergrund (transparent)']

[row=Aktualisierung]
	[var5 = check,2,'','Aktualisierung per KO']

[row]
	[var3 = select,2,'Aktualisierung per Intervall','0#deaktiviert|1#jede Sekunde|2#alle 2 Sekunden|3#alle 3 Sekunden|4#alle 4 Sekunden|5#alle 5 Sekunden|10#alle 10 Sekunden|15#alle 15 Sekunden|20#alle 20 Sekunden|30#alle 30 Sekunden|60#jede Minute|120#alle 2 Minuten|180#alle 3 Minuten|300#alle 5 Minuten|600#alle 10 Minuten|900#alle 15 Minuten|1800#alle 30 Minuten|3600#jede Stunde']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr><td align='center'>"+((isPreview)?"":"<span class='app2_pseudoElement'>BILD-URL/WEBSEITE</span>")+"</td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
	return false;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	if (obj.dataset.var2==0) { 
		//Bild-URL
		if (obj.dataset.var4==0) {var tmp='width:100%; height:100%;';}
		else if (obj.dataset.var4==1) {var tmp='width:100%; height:auto;';}
		else if (obj.dataset.var4==2) {var tmp='width:auto; height:100%;';}
		else if (obj.dataset.var4==3) {var tmp='width:auto; height:auto;';}
		//per Default wird ein transparentes Pixel angezeigt - die echte URL wird per JS gesetzt
		var n="<img id='e-"+elementId+"-urlcontent' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4//8/AwAI/AL+5gz/qwAAAABJRU5ErkJggg==' draggable='false' style='"+tmp+" margin:0; padding:0; border:none; overflow:hidden; pointer-events:none;'>";
		n+="<div id='e-"+elementId+"-reloadanim' class='reloadAnim'></div>";
	} else {
		//Webseiten-URL (iFrame)
		//per Default wird "about:blank" angezeigt - die echte URL wird per JS gesetzt
		if (obj.dataset.var1==1) {
			var n="<iframe id='e-"+elementId+"-urlcontent' src='about:blank' style='position:absolute; left:0; top:0; width:100%; height:100%; margin:0; padding:0; border:none; overflow:hidden; background:transparent;'></iframe>";
		} else {
			var n="<iframe id='e-"+elementId+"-urlcontent' src='about:blank' style='position:absolute; left:0; top:0; width:100%; height:100%; margin:0; padding:0; border:none; overflow:hidden; background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAAXNSR0IArs4c6QAAAChJREFUCB1jZGBgMAZiZODDhMwDsn2AeAuyIFgApAgmCBeACaIIgAQBzpEEiaAV3YQAAAAASUVORK5CYII=);'></iframe>";
		}
		n+="<div id='e-"+elementId+"-reloadanim' class='reloadAnim'></div>";
	}
	obj.innerHTML=n;

	obj.dataset.blocked=0;	

	if (visuElement_hasCommands(elementId)) {
		visuElement_onClick(obj,function(veId,objId){visuElement_doCommands(veId);});
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//keine Seitensteuerung/Befehle und Typ="Bild-URL" angegeben: VE ist klicktranparent
	if (!visuElement_hasCommands(elementId) && obj.dataset.var2==0) {obj.style.pointerEvents="none";}

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	if (isInit || (isRefresh && obj.dataset.var5==1)) {
		VSE_VSEID_update(elementId);
	}
}

VSE_VSEID_update=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.blocked==0) {	
			d.dataset.blocked=1;

			var url=visuElement_parseString(visuElement_getText(elementId),visuElement_getKoValue(d.dataset.id,1));

			if (d.dataset.var3>0) {
				visuElement_clearTimeout(elementId,2);
			}
			document.getElementById("e-"+elementId+"-reloadanim").style.display="inline";
			
			if (url=="") {url="about:blank";}
	
			if (d.dataset.var2==0) { 
				document.getElementById("e-"+elementId+"-urlcontent").onload=function() {VSE_VSEID_setTimer(elementId,true);}
				document.getElementById("e-"+elementId+"-urlcontent").onerror=function() {VSE_VSEID_setTimer(elementId,false);}
			} else {
				document.getElementById("e-"+elementId+"-urlcontent").onload=function() {VSE_VSEID_setTimer(elementId,true);}
				visuElement_setTimeout(elementId,1,30000,function(id){VSE_VSEID_setTimer(id,false);});
			}

			if (d.dataset.var6==0) { 
				document.getElementById("e-"+elementId+"-urlcontent").src=url;
			} else {
				var tmp=new Date(); 
				var ts="TS"+tmp.getDate()+(tmp.getMonth()+1)+tmp.getFullYear()+tmp.getHours()+tmp.getMinutes()+tmp.getSeconds()+tmp.getMilliseconds();
				if (url.indexOf("?")>=0) {
					document.getElementById("e-"+elementId+"-urlcontent").src=url+"&"+ts;
				} else {
					document.getElementById("e-"+elementId+"-urlcontent").src=url+"?&"+ts;
				}
			}
		}
	}
}

VSE_VSEID_setTimer=function(elementId,statusOk) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (statusOk) {
			document.getElementById("e-"+elementId+"-urlcontent").style.webkitFilter="contrast(1)";
			document.getElementById("e-"+elementId+"-urlcontent").style.opacity="1";
		} else {
			document.getElementById("e-"+elementId+"-urlcontent").style.webkitFilter="contrast(0.3)";
			document.getElementById("e-"+elementId+"-urlcontent").style.opacity="0.75";
		}

		document.getElementById("e-"+elementId+"-reloadanim").style.display="none";
		if (d.dataset.var3>0) {
			visuElement_clearTimeout(elementId,2);
		}

		if (d.dataset.var2==0) {
			document.getElementById("e-"+elementId+"-urlcontent").onload=function() {return false;};
			document.getElementById("e-"+elementId+"-urlcontent").onerror=function() {return false;};
		} else {
			document.getElementById("e-"+elementId+"-urlcontent").onload=function() {return false;};
			visuElement_clearTimeout(elementId,1);
		}

		if (d.dataset.blocked==1) {
			d.dataset.blocked=0;
			if (d.dataset.var3>0) {
				visuElement_setTimeout(elementId,2,d.dataset.var3*1000,function(id){VSE_VSEID_update(id);});
			}
		}
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Bild-URL/Webseite" zeigt eine Bild-URL an oder bettet eine Webseite ein.

<b>Achtung:</b>
Das Einbetten externer Bild- oder Webseiten-URLs kann u.U. ein Sicherheitsrisiko darstellen. Ausserdem ist u.U. ein "Ausbrechen" der Webseite aus dem eingebettenen "iFrame" möglich, so dass die eingebettete Webseite die angezeigte Visualisierung im Browser ersetzt. 

<b>Hinweis:</b>
Das Einbetten externer Bild- oder Webseiten-URLs muss vom Ziel-Server ermöglicht (erlaubt) werden. Unter Umständen verweigert der Ziel-Server das Ausliefern eines Bildes bzw. einer Webseite in die Visualisierung.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		URL-Typ: legt fest, ob die im Feld "Beschriftung" angegebene URL ein Bild oder eine Webseite repräsentiert
		<ul>
			<li>Beim URL-Typ "Webseite" muss die Webseite innerhalb von 30 Sekunden geladen werden, ansonsten wird das Laden abgebrochen (Timeout).</li>
			<li>Hinweis: die eigentliche URL muss im Feld "Beschriftung" angegeben werden (auch möglich in dynamischen Designs)</li>
		</ul>
	</li>

	<li>Caching verhindern: ist diese Option aktiviert, wird an die URL ein Zeitstempel in der Form "?&TS..." bzw. "&TS..." angehängt, um das Zwischenspeichern der Inhalte im Browser-Cache zu verhindern</li>

	<li>
		Bild-Format (nur bei URL-Typ "Bild"): legt fest, wie das angezeigte Bild bezogen auf das Visuelement skaliert werden soll
		<ul>
			<li>Breite und Höhe anpassen: Das Bild wird genau an die Visuelementgröße angepasst (das Seitenverhältnis wird dabei ignoriert)</li>
			<li>Breite anpassen: Die Bildbreite wird an die Visuelementbreite angepasst, die Höhe wird unter Beibehaltung des Seitenverhältnisses angepasst</li>
			<li>Höhe anpassen: Die Bildhöhe wird an die Visuelementhöhe angepasst, die Breite wird unter Beibehaltung des Seitenverhältnisses angepasst</li>
			<li>Originalgröße: Das Bild wird in seiner nativen Größe angezeigt, ggf. werden Bildteile abgeschnitten</li>
		</ul>
	</li>

	<li>
		Webseiten-Hintergrund (nur bei URL-Typ "Webseite"): legt fest, welcher Hintergrund angezeigt werden soll bis die Webseite geladen wurde und diese Einstellung ggf. überschreibt
		<ul>
			<li>Streifenmuster: Als Hintergrund wird ein Streifenmuster angezeigt, bis die Webseite geladen wurde (ggf. wird dann der Hintergrund der Webseite verwendet).</li>
			<li>kein Hintergrund (transparent): Es wird kein Hintergrund angezeigt, bis die Webseite geladen wurde (ggf. wird dann der Hintergrund der Webseite verwendet).</li>
		</ul>
	</li>

	<li>
		Aktualisierung per KO: legt fest, ob das Bild bzw. die Webseite bei Änderung des KO1-Wertes (s.u.) aktualisiert werden soll
		<ul>
			<li>deaktiviert: das Bild bzw. die Webseite wird ggf. ausschließlich per Intervall (s.u.) aktualisiert</li>
			<li>aktiviert: das Bild bzw. die Webseite wird bei jeder KO1-Wertänderung aktualisiert (und ggf. zusätzlich per Intervall, s.u.)</li>
		</ul>
	</li>

	<li>
		Aktualisierung per Intervall: legt fest, in welchem Intervall das Bild bzw. die Webseite aktualisiert werden soll
		<ul>
			<li>deaktiviert: das Bild bzw. die Webseite wird nicht per Intervall aktualisiert (ggf. jedoch per KO, s.o.)</li>
			<li>"alle x Sekunden/Minuten/Stunden": das Bild bzw. die Webseite wird in dem ausgewählten Intervall aktualisiert (und ggf. zusätzlich per KO, s.o.)</li>
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
			<li>immer wenn das KO auf einen Wert gesetzt wird, wird das Bild bzw. die Webseite ggf. aktualisiert (siehe "Aktualisierung per KO")</li>
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
	<li>Im Feld "Beschriftung" (auch in dynamischen Designs) muss die URL des Bildes bzw. der Webseite angegeben werden.</li>
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
</ul>

<b>Wichtig:</b>
<link>Konfigurierte Bilder***1000-28</link> stehen für dieses Visuelement nur dann (als lokale URL) zu Verfügung, wenn ein konfiguriertes Bild in einer Visualisierung tatsächlich genutzt wird: Ungenutzte Bild-Dateien werden bei der <link>Projektaktivierung***103-13</link> nicht übertragen.


<h2>Bedienung in der Visualisierung</h2>
Mit einem Klick auf dieses Visuelement werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt.

<b>Hinweis:</b>
Beim URL-Typ "Webseite" werden Klicks auf die angezeigte Webseite angewendet, nicht auf das Visuelement selbst. Klicks auf Bereiche ausserhalb der Webseite (z.B. falls das Visuelement über einen Rahmen verfügt) führen zur Ausführung aller zugewiesenen Seitensteuerungen/Befehle.
###[/HELP]###


