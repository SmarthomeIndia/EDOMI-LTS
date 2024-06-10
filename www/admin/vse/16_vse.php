###[DEF]###
[name	=Notizen]

[folderid=162]
[xsize	=300]
[ysize	=200]

[var10	=]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=0]
[flagCmd		=0]
[flagDesign		=1]
[flagDynDesign	=1]

[captionText	=Titel]
[captionKo1		=Notiz (internes KO vom Typ Variant)]
###[/DEF]###


###[PROPERTIES]###
[columns=100]
[row]
	[var10= text,1,'Kopfzeilenhöhe (px, leer=Standard)','']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
	var n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
		n+="<tr style='height:"+mheight+"px;'>";
			n+="<td width='20%' align='center'>&lt;</td>";
			n+="<td width='60%' align='center'><div style='max-height:"+mheight+"px; overflow:hidden;'>"+meta.itemText+"</div></td>";
			n+="<td width='20%' align='center'>&gt;</td>";
		n+="</tr>";
		n+="<tr><td colspan='3' style='border-top:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>NOTIZEN</span>")+"</td></tr>";
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
	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
	var n="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
		n+="<tr id='e-"+elementId+"-menu1' style='height:"+mheight+"px;'>";
			n+="<td width='20%' align='center' id='e-"+elementId+"-last'>&lt;</td>";
			n+="<td width='60%' align='center' id='e-"+elementId+"-info'><div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div></td>";
			n+="<td width='20%' align='center' id='e-"+elementId+"-next'>&gt;</td>";
		n+="</tr>";
		n+="<tr id='e-"+elementId+"-menu2' style='height:"+mheight+"px; display:none;'>";
			n+="<td width='33%' align='center' id='e-"+elementId+"-delete'>Löschen</td>";
			n+="<td width='33%' align='center' id='e-"+elementId+"-cancel'>Abbrechen</td>";
			n+="<td width='33%' align='center' id='e-"+elementId+"-save'>Speichern</td>";
		n+="</tr>";
		n+="<tr><td colspan='3' align='left' style='padding:5px; border-top:1px solid;'>";
			n+="<div style='position:relative; height:100%;'>";
				n+="<div id='e-"+elementId+"-text' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto; word-wrap:break-word;'></div>";
				n+="<textarea id='e-"+elementId+"-textEdit' wrap='off' style='display:none; width:100%; height:100%; word-wrap:break-word; font-family:inherit; font-size:inherit; resize:none; margin:0; padding:0; color:var(--fgc0); background:transparent; border:none; outline:none; tab-size:4; box-sizing:border-box; -webkit-appearance:none;'></textarea>";
			n+="</div>";
		n+="</td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	if (visuElement_hasKo(elementId,1)) {
		visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){scrollUp("e-"+veId+"-text");});
		visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){scrollToTop("e-"+veId+"-text");});
		visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){scrollDown("e-"+veId+"-text");});
		visuElement_onClick(document.getElementById("e-"+elementId+"-text"),function(veId,objId){VSE_VSEID_Edit(veId,true);},false);
		visuElement_onClick(document.getElementById("e-"+elementId+"-delete"),function(veId,objId){VSE_VSEID_Clear(veId);});
		visuElement_onClick(document.getElementById("e-"+elementId+"-cancel"),function(veId,objId){VSE_VSEID_Edit(veId,false);},false);
		visuElement_onClick(document.getElementById("e-"+elementId+"-save"),function(veId,objId){VSE_VSEID_Set(veId);},false);
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";

	document.getElementById("e-"+elementId+"-infotext").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);

	var n=escapeHtml(koValue.toString());
	document.getElementById("e-"+elementId+"-text").innerHTML=n;

	function escapeHtml(n) {
		return n
		.replace(/"/g,"&quot;")
		.replace(/'/g,"&#039;")
		.replace(/&/g,"&amp;")
		.replace(/</g,"&lt;")
		.replace(/>/g,"&gt;")
		.replace(/[\x0A\x0E]/g,"<br>")
		.replace(/[\x09]/g,"&nbsp;&nbsp;&nbsp;&nbsp;");
	}
}

VSE_VSEID_CANCEL=function(elementId) {
	VSE_VSEID_Edit(elementId,false);
}

VSE_VSEID_Edit=function(elementId,mode) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (mode) {
			//Öffnen
			document.getElementById("e-"+elementId+"-text").style.display="none";
			document.getElementById("e-"+elementId+"-textEdit").style.display="block";
			visuElement_setTimeout(elementId,1,1,function(id){if (document.getElementById("e-"+id+"-textEdit")) {document.getElementById("e-"+id+"-textEdit").focus();}});

			document.getElementById("e-"+elementId+"-textEdit").value=visuElement_getKoValue(elementId,1);	
			document.getElementById("e-"+elementId+"-menu1").style.display="none";
			document.getElementById("e-"+elementId+"-menu2").style.display="table-row";
		} else {
			//Schliessen
			document.getElementById("e-"+elementId+"-text").style.display="block";
			document.getElementById("e-"+elementId+"-textEdit").style.display="none";	
			document.getElementById("e-"+elementId+"-menu2").style.display="none";
			document.getElementById("e-"+elementId+"-menu1").style.display="table-row";
		}
	}
}

VSE_VSEID_Clear=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-textEdit").value="";	
		visuElement_setTimeout(elementId,1,1,function(id){if (document.getElementById("e-"+id+"-textEdit")) {document.getElementById("e-"+id+"-textEdit").focus();}});
	}
}

VSE_VSEID_Set=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		visuElement_setKoValue(d.dataset.id,1,document.getElementById("e-"+elementId+"-textEdit").value);
		VSE_VSEID_Edit(elementId,false);
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Notizen" ermöglicht das Erstellen und Bearbeiten von Text-Notizen mit max. 10.000 Zeichen.

<b>Wichtig:</b>
Bitte die Hinweise zum Kommunikationsobjekt beachten (s.u.)!

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Kopfzeilenhöhe: legt optional die Höhe der Kopfzeile in Pixeln fest</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Notiz
		<ul>
			<li><b>dieses KO muss ein internes KO von Typ "Variant" sein</b></li>
			<li>die Notiz wird in diesem KO gespeichert (das KO bzw. die Notiz kann somit z.B. auch in einem Datenarchiv archiviert werden)</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>Hinweis: die Verwendung des KO-Wertes zur Steuerung und Beschriftung ergibt in den meisten Fällen keinen Sinn, da der KO-Wert aus den Notizinhalten besteht</li>
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
Die Angabe von KO1 (intern, Typ "Variant) ist zwingend erforderlich, damit die Notiz gespeichert werden kann: Die Notiz wird KO1 als Wert zugewiesen.


<h2>Besonderheiten</h2>
<ul>
	<li>es muss stets ein internes KO von Typ "Variant" angegeben werden, damit die Notiz gespeichert werden kann</li>
	<li>die Angaben im Feld "Beschriftung" werden als Titel des Visuelements angezeigt</li>
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
	<li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Bei diesem Visuelement werden zwei Zustände unterschieden: Anzeigen der Notiz und Bearbeiten der Notiz.

<b>Modus: Anzeige</b>
Die Notiz wird angezeigt und ggf. automatisch mit der Visuseite aktualisiert. Es werden Pfeil-Schaltflächen zum Blättern (Scrollen) durch den Inhalt der Notiz angezeigt.

<b>Modus: Bearbeiten</b>
Durch Anklicken der angezeigten Notiz wird in den Bearbeitungsmodus gewechselt. Nun kann die Notiz wie folgt bearbeitet und gespeichert werden:

<ul>
	<li>Löschen: die Eingabe wird vollständig gelöscht</li>
	<li>Abbrechen: alle Änderungen werden verworfen und der Bearbeitungsmodus wird beendet</li>
	<li>Speichern: die Eingaben werden im KO (s.o.) gespeichert und der Bearbeitungsmodus wird beendet</li>
</ul>
###[/HELP]###


