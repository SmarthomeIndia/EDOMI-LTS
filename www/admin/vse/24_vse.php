###[DEF]###
[name	=Codeschloss]

[folderid=162]
[xsize	=180]
[ysize	=240]

[var1	=0000]
[var2	=3]
[var3	=0]
[var4	=3]

[flagText		=0]
[flagKo1		=1]	
[flagKo2		=1]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]

[captionKo2		=Codeschloss-Status]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = text,2,'Code (nur Ziffern)','']

[row]
	[var2 = select,1,'Versuche bis Sperrung','1#1 Versuch erlauben|2#2 Versuche erlauben|3#3 Versuche erlauben|5#5 Versuche erlauben|10#10 Versuche erlauben']
	[var4 = select,1,'Indikatoren','0#deaktiviert|1#nur Klick-Indikator|2#nur Code-Bestätigung|3#Klick-Indikator und Code-Bestätigung']

[row]
	[var3 = select,2,'Darstellung','0#neutral|1#Tastenkreuz']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0' style='border-collapse:collapse;'>";
		n+="<tr align='center'><td style='border-bottom:1px dotted;'>1</td><td style='border-bottom:1px dotted; border-left:1px dotted; border-right:1px dotted;'>2</td><td style='border-bottom:1px dotted;'>3</td></tr>";
		n+="<tr align='center'><td style='border-bottom:1px dotted;'>4</td><td style='border-bottom:1px dotted; border-left:1px dotted; border-right:1px dotted;'>5</td><td style='border-bottom:1px dotted;'>6</td></tr>";
		n+="<tr align='center'><td style='border-bottom:1px dotted;'>7</td><td style='border-bottom:1px dotted; border-left:1px dotted; border-right:1px dotted;'>8</td><td style='border-bottom:1px dotted;'>9</td></tr>";
		n+="<tr align='center'><td style='color:#80ff00;'></td><td style='border-left:1px dotted; border-right:1px dotted;'>0</td><td>-</td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
	return false;
}

###[/EDITOR.JS]###


###[VISU.PHP]###
<?
function PHP_VSE_VSEID($cmd,$json1,$json2) {

	if ($cmd=='codeCheck') {
		$item=sql_getValues('edomiLive.visuElement','var1,var2','id='.$json1['elementId']);
		if ($item!==false) {
			if ($json1['code']===$item['var1']) {
?>
				VSE_VSEID_callbackCodecheck(<?echo $json1['elementId'];?>,true,"<?echo $item['var2'];?>");
<?
			} else {
?>
				VSE_VSEID_callbackCodecheck(<?echo $json1['elementId'];?>,false,null);
<?
			}
		}
	}
}
?>

###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	if (obj.dataset.var3==0) { 
		//normal
		var n="<table id='e-"+elementId+"-container' cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'>";
			n+="<tr align='center'><td id='e-"+elementId+"-k1'>1</td><td id='e-"+elementId+"-k2'>2</td><td id='e-"+elementId+"-k3'>3</td></tr>";
			n+="<tr align='center'><td id='e-"+elementId+"-k4'>4</td><td id='e-"+elementId+"-k5'>5</td><td id='e-"+elementId+"-k6'>6</td></tr>";
			n+="<tr align='center'><td id='e-"+elementId+"-k7'>7</td><td id='e-"+elementId+"-k8'>8</td><td id='e-"+elementId+"-k9'>9</td></tr>";
			n+="<tr align='center'>";
				n+="<td>";
					n+="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
						n+="<tr id='e-"+elementId+"-mode1'><td align='center'><div id='e-"+elementId+"-info' style='color:"+visu_indiColor+";'></div>";
						n+="<tr id='e-"+elementId+"-mode2' style='display:none;'><td align='center'><input id='e-"+elementId+"-edit' type='password' value='' onBlur='VSE_VSEID_KeyboardCancel(\""+elementId+"\");' onKeyUp='VSE_VSEID_KeyboardDown(\""+elementId+"\");' class='controlInputTagBlank' style='left:0; top:0; background:"+visu_indiColor+"; color:"+visu_indiColorText+";'>";
					n+="</table>";
				n+="</td>";
				n+="<td id='e-"+elementId+"-k0'>0</td><td id='e-"+elementId+"-kd'>-</td>";
			n+="</tr>";
		n+="</table>";
		
	} else { 
		//mit Tastenkreuz
		var n="<table id='e-"+elementId+"-container' cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%; border-collapse:collapse;'>";
			n+="<tr align='center'><td id='e-"+elementId+"-k1' style='border-bottom:1px solid;'>1</td><td id='e-"+elementId+"-k2' style='border-bottom:1px solid; border-left:1px solid; border-right:1px solid;'>2</td><td id='e-"+elementId+"-k3' style='border-bottom:1px solid;'>3</td></tr>";
			n+="<tr align='center'><td id='e-"+elementId+"-k4' style='border-bottom:1px solid;'>4</td><td id='e-"+elementId+"-k5' style='border-bottom:1px solid; border-left:1px solid; border-right:1px solid;'>5</td><td id='e-"+elementId+"-k6' style='border-bottom:1px solid;'>6</td></tr>";
			n+="<tr align='center'><td id='e-"+elementId+"-k7' style='border-bottom:1px solid;'>7</td><td id='e-"+elementId+"-k8' style='border-bottom:1px solid; border-left:1px solid; border-right:1px solid;'>8</td><td id='e-"+elementId+"-k9' style='border-bottom:1px solid;'>9</td></tr>";
			n+="<tr align='center'>";
				n+="<td>";
					n+="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
						n+="<tr id='e-"+elementId+"-mode1'><td align='center'><div id='e-"+elementId+"-info' style='color:"+visu_indiColor+";'></div>";
						n+="<tr id='e-"+elementId+"-mode2' style='display:none;'><td align='center'><input id='e-"+elementId+"-edit' type='password' value='' onBlur='VSE_VSEID_KeyboardCancel(\""+elementId+"\");' onKeyUp='VSE_VSEID_KeyboardDown(\""+elementId+"\");' class='controlInputTagBlank' style='left:0; top:0; background:"+visu_indiColor+"; color:"+visu_indiColorText+";'>";
					n+="</table>";
				n+="</td>";
				n+="<td id='e-"+elementId+"-k0' style='border-left:1px solid; border-right:1px solid;'>0</td><td id='e-"+elementId+"-kd'>-</td>";
			n+="</tr>";
		n+="</table>";
	}
	obj.innerHTML=n;

	obj.dataset.code="";
	obj.dataset.var1=obj.dataset.var1.length;

	visuElement_onClick(document.getElementById("e-"+elementId+"-k1"),function(veId,objId){VSE_VSEID_virtualKey(veId,1);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k2"),function(veId,objId){VSE_VSEID_virtualKey(veId,2);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k3"),function(veId,objId){VSE_VSEID_virtualKey(veId,3);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k4"),function(veId,objId){VSE_VSEID_virtualKey(veId,4);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k5"),function(veId,objId){VSE_VSEID_virtualKey(veId,5);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k6"),function(veId,objId){VSE_VSEID_virtualKey(veId,6);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k7"),function(veId,objId){VSE_VSEID_virtualKey(veId,7);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k8"),function(veId,objId){VSE_VSEID_virtualKey(veId,8);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k9"),function(veId,objId){VSE_VSEID_virtualKey(veId,9);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-k0"),function(veId,objId){VSE_VSEID_virtualKey(veId,0);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-kd"),function(veId,objId){VSE_VSEID_virtualKey(veId,-1);},((obj.dataset.var4&1)?true:false));
	visuElement_onClick(document.getElementById("e-"+elementId+"-mode1"),function(veId,objId){VSE_VSEID_KeyboardStart(veId);},false);
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
}

VSE_VSEID_KeyboardStart=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.var2>0) {
			visuElement_clearTimeout(elementId,2);
			d.dataset.code="";

			document.getElementById("e-"+elementId+"-info").innerHTML="";
			document.getElementById("e-"+elementId+"-edit").value="";
			
			document.getElementById("e-"+elementId+"-mode1").style.display="none";
			document.getElementById("e-"+elementId+"-mode2").style.display="table-row";
			
			visuElement_setTimeout(elementId,1,1,function(id){if (document.getElementById("e-"+id+"-edit")) {document.getElementById("e-"+id+"-edit").focus();}});

		} else {
			//Codeschloss ist blockiert
			shakeObj("e-"+elementId+"-container");
		}
	}
}

VSE_VSEID_KeyboardDown=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	var event=window.event;
	if (event && d) {
		if (event.keyCode==13) {
			VSE_VSEID_KeyboardSend(elementId);
		}
	}
}

VSE_VSEID_KeyboardSend=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.var2>0) {
			visuElement_callPhp("codeCheck",{elementId:elementId,code:document.getElementById("e-"+elementId+"-edit").value},null);
			document.getElementById("e-"+elementId+"-edit").value="";
		}
	}
}

VSE_VSEID_KeyboardCancel=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-mode1").style.display="table-row";
		document.getElementById("e-"+elementId+"-mode2").style.display="none";
	}
}

VSE_VSEID_virtualKey=function(elementId,key) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.var2>0) {
			document.getElementById("e-"+elementId+"-mode1").style.display="table-row";
			document.getElementById("e-"+elementId+"-mode2").style.display="none";

			if (d.dataset.code.length<d.dataset.var1) {
				if (key>=0 && key<=9) {
					d.dataset.code+=(key+"");
					visuElement_setTimeout(elementId,2,3000,function(id){VSE_VSEID_Reset(id);});
				}
				if (key==-1) {
					visuElement_clearTimeout(elementId,2);
					d.dataset.code="";
				}

				var n="";
				for (var t=1;t<=d.dataset.code.length;t++) {n+="&middot;";}
				document.getElementById("e-"+elementId+"-info").innerHTML=n;
			} 
			
			if (d.dataset.code.length==d.dataset.var1) {
				visuElement_callPhp("codeCheck",{elementId:elementId,code:d.dataset.code},null);
				d.dataset.code="";
			}

		} else {
			//Codeschloss ist blockiert
			shakeObj("e-"+elementId+"-container");
			document.getElementById("e-"+elementId+"-info").innerHTML="";
		}
	}
}

VSE_VSEID_Reset=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		d.dataset.code="";
		document.getElementById("e-"+elementId+"-info").innerHTML="";
	}
}

VSE_VSEID_callbackCodecheck=function(elementId,ok,tryCount) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-mode1").style.display="table-row";
		document.getElementById("e-"+elementId+"-mode2").style.display="none";
		visuElement_clearTimeout(elementId,2);
		d.dataset.code="";
		document.getElementById("e-"+elementId+"-info").innerHTML="";
		if (ok) {
			if (d.dataset.var4&2) {visuElement_indicate(document.getElementById("e-"+elementId+"-container"));}
			d.dataset.var2=tryCount;
			visuElement_setKoValue(d.dataset.id,2,1);
			visuElement_doCommands(elementId);
		} else {
			shakeObj("e-"+elementId+"-container");
			d.dataset.var2=parseInt(d.dataset.var2)-1;
			if (d.dataset.var2<=0) {
				visuElement_setKoValue(d.dataset.id,2,-1);
			} else {
				visuElement_setKoValue(d.dataset.id,2,0);
			}
		}
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Codeschloss" führt die zugewiesenen Seitensteuerungen/Befehle nur nach Eingabe eines korrekten Zifferncodes aus.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Code: einzugebener Zifferncode (maximal 20 Ziffern von jeweils 0..9, z.B. "1234")</li>

	<li>
		Versuche bis Sperrung: legt fest, wieviele Fehlversuche (falscher Code) erlaubt werden
		<ul>
			<li>wird die festgelegte Anzahl der Fehlversuche überschritten, wird das Visuelement gesperrt</li>
			<li>Hinweis: das Visuelelement bleibt nur solange gesperrt, bis die Visuseite oder die Visualisierung erneut aufgerufen wird</li>
		</ul>
	</li>

	<li>
		Indikatoren: legt fest, ob beim Anklicken bzw. erfolgreichen Codeeingabe ein Klick-Indikator angezeigt werden soll
		<ul>
			<li>Klick-Indikator: beim Anklicken z.B. einer Ziffer wird ein Klick-Indikator angezeigt</li>
			<li>Code-Bestätigung: bei Eingabe des korrekten Codes wird ein Klick-Indikator für das gesamte Visuelement zur Bestätigung angezeigt</li>
		</ul>
	</li>

	<li>Darstellung: trennt ggf. die virtuellen Zifferntasten mit Linien voneinander ab ("Tastenkreuz")</li>
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
		KO2: Codeschloss-Status
		<ul>
			<li>dieses (optionale) KO wird vom Visuelement wie folgt gesetzt:</li>
			<li>1: es wurde der korrekte Code eingegeben (die Befehle werden ausgeführt)</li>
			<li>0: es wurde ein falscher Code eingegeben</li>
			<li>-1: das Codeschloss ist gesperrt (die Anzahl der "Versuche bis Sperrung" (s.o.) wurde überschritten)</li>
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
	<li>das Feld "Beschriftung" steht nicht zu Verfügung, bzw. wird ignoriert</li>
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Der Zifferncode wird mit der Maus durch Anklicken der entsprechenden Schaltflächen eingegeben. Jede einzelne Ziffer muss innerhalb von 3 Sekunden eingegeben werden, ansonsten wird die Eingabe vollständig zurückgesetzt (verworfen).
Während der Eingabe wird unten links die Anzahl der bereits eingegebenen Ziffern in Indikatorfarbe angezeigt. Mit der Schaltfläche "-" wird die Eingabe vollständig zurückgesetzt (verworfen).

Nach Eingabe der letzen Ziffer wird der Code überprüft:
Bei korrekter Eingabe des Codes wird der Hintergrund des Visuelements kurzzeitig in Indikatorfarbe dargestellt und die zugewiesenen Seitensteuerungen/Befehle werden ausgeführt.
Bei fehlerhafter Eingage des Codes wackelt das Visuelement kurzzeitig hin und her, die Eingabe wird verworfen.
Wird die zulässige Anzahl an Fehlversuchen überschritten, wird das Visuelement für weitere Eingaben gesperrt. Diese temporäre Sperre wird erst bei einem erneuten Aufruf der Visuseite bzw. der Visualisierung aufgehoben.


<h3>Code-Eingabe per Tastatur</h3>
Mit einem Klick auf das Feld unten links (Anzeige der eingegebenen Ziffern) wird ein Eingabefeld angezeigt. Nun kann der Zifferncode mit Hilfe der Tastatur eingegeben werden und mit der ENTER-Taste bestätigt werden.
Sobald eine Ziffer oder ein anderes Visuelement angeklickt wird, wird das Eingabefeld wieder ausgeblendet.
###[/HELP]###


