###[DEF]###
[name	=Tastatureingabe]

[folderid=162]
[xsize	=100]
[ysize	=50]
[text	={#}]

[var1	=0]
[var2	=0]
[var3	=0]
[var5	=]
[var6	=]
[var7	=]
[var8	=-1]

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
	[var1 =	select,1,'Modus','0#normal|1#Passwort']
	[var2 =	select,1,'Inhalt vorselektieren','0#deaktiviert|1#aktiviert']

[row]
	[var3 =	select,2,'Eingabe abschließen (KO2-Wert setzen)','0#nur mit Enter-Taste|1#mit Enter-Taste und bei Fokusverlust']

[row=Wertebereich (KO2)]
	[var5 = text,1,'Minimum (leer=KO-Filter)','']
	[var6 = text,1,'Maximum (leer=KO-Filter)','']

[row]
	[var7 = text,1,'Raster (leer=KO-Filter)','']
	[var8 = select,1,'Nachkommastellen','-1#KO-Filter|#beliebig|0#0 (x)|1#1 (x.y)|2#2 (x.yy)|3#3 (x.yyy)|4#4 (x.yyyy)|5#5 (x.yyyyy)']
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

//Min/Max/Raster/Nachkommastellen ggf. aus KO-Konfiguration/DPT-Array übernehmen (vmin/vmax/vstep/vlist)
$tmp=sql_getValues('edomiProject.editKo','valuetyp,vmin,vmax,vstep,vlist','id='.$item['gaid']);
if ($tmp!==false) {
	//Min/Max ggf. aus DPT-Array holen
	if (isEmpty($tmp['vmin'])) {$tmp['vmin']=$global_dptData[$tmp['valuetyp']][0];}
	if (isEmpty($tmp['vmax'])) {$tmp['vmax']=$global_dptData[$tmp['valuetyp']][1];}
	//leere Werte ersetzen
	if (isEmpty($item['var5'])) {sql_call("UPDATE edomiLive.visuElement SET var5='".sql_encodeValue($tmp['vmin'])."' WHERE (id=".$item['id'].")");}
	if (isEmpty($item['var6'])) {sql_call("UPDATE edomiLive.visuElement SET var6='".sql_encodeValue($tmp['vmax'])."' WHERE (id=".$item['id'].")");}
	if (isEmpty($item['var7'])) {sql_call("UPDATE edomiLive.visuElement SET var7='".sql_encodeValue($tmp['vstep'])."' WHERE (id=".$item['id'].")");}
	if ($item['var8']=='-1') 	{sql_call("UPDATE edomiLive.visuElement SET var8='".sql_encodeValue($tmp['vlist'])."' WHERE (id=".$item['id'].")");}
}

//Min/Max ggf. vertauschen
if ($item['var5']>$item['var6'] && !isEmpty($item['var6'])) {
	sql_call("UPDATE edomiLive.visuElement SET var5='".sql_encodeValue($item['var6'])."',var6='".sql_encodeValue($item['var5'])."' WHERE (id=".$item['id'].")");
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var	n="<table cellpadding='0' cellspacing='0' border='0' style='left:0; top:0; width:100%; height:100%;'><tr><td>"+meta.itemText+"</td></tr></table>";
	obj.innerHTML=n;

	return true;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var n="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'>";
		n+="<tr id='e-"+elementId+"-mode1'><td><span id='e-"+elementId+"-text'></span></td></tr>";
		n+="<tr id='e-"+elementId+"-mode2' style='display:none;'><td><input id='e-"+elementId+"-edit' type='"+((obj.dataset.var1==0)?"text":"password")+"' value='' onBlur='VSE_VSEID_Blur(\""+elementId+"\");' onKeyUp='VSE_VSEID_KeyDown(\""+elementId+"\");' class='controlInputTagBlank' style='left:0; top:0; background:"+visu_indiColor+"; color:"+visu_indiColorText+";'></td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	if (visuElement_hasKo(elementId,2)) {
		visuElement_onClick(obj,function(veId,objId){VSE_VSEID_Edit(veId);},false);
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	if (obj.dataset.var1==0) {
		document.getElementById("e-"+elementId+"-text").innerHTML=visuElement_parseString(visuElement_getText(elementId),koValue);
	} else {
		var n=new Array(visuElement_parseString(visuElement_getText(elementId),koValue).length+1).join("&bull;");
		document.getElementById("e-"+elementId+"-text").innerHTML=n;
	}
}

VSE_VSEID_CANCEL=function(elementId) {
	VSE_VSEID_Cancel(elementId);
}

VSE_VSEID_Edit=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		var kovalue=visuElement_getKoValue(elementId,1);
		document.getElementById("e-"+elementId+"-edit").value=kovalue;

		document.getElementById("e-"+elementId+"-mode1").style.display="none";
		document.getElementById("e-"+elementId+"-mode2").style.display="table-row";

		 //Focus und Preselect (Verzögerung erforderlich)
		if (d.dataset.var2==1) {
			visuElement_setTimeout(elementId,1,1,function(id){if (document.getElementById("e-"+id+"-edit")) {document.getElementById("e-"+id+"-edit").focus();}});
			visuElement_setTimeout(elementId,2,250,function(id){if (document.getElementById("e-"+id+"-edit")) {document.getElementById("e-"+id+"-edit").setSelectionRange(0,10001);}});	//Verzögerung für iOS
		} else {
			visuElement_setTimeout(elementId,1,1,function(id){if (document.getElementById("e-"+id+"-edit")) {document.getElementById("e-"+id+"-edit").focus();}});
		}
	}
}

VSE_VSEID_KeyDown=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	var event=window.event;
	if (event && d) {
		if (event.keyCode==13) {
			if (d.dataset.var3==1) {
				document.getElementById("e-"+elementId+"-edit").blur();
			} else {
				VSE_VSEID_Send(elementId);
			}
		}
	}
}

VSE_VSEID_Send=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		var obj=document.getElementById("e-"+elementId+"-edit");
		visuElement_setKoValue(d.dataset.id,2,VSE_VSEID_CheckRange(d,obj.value));

		document.getElementById("e-"+elementId+"-mode1").style.display="table-row";
		document.getElementById("e-"+elementId+"-mode2").style.display="none";
		document.getElementById("e-"+elementId+"-edit").blur();
	}
}

VSE_VSEID_Blur=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		if (d.dataset.var3==1) {
			VSE_VSEID_Send(elementId);
		} else {
			VSE_VSEID_Cancel(elementId);
		}
	}
}

VSE_VSEID_Cancel=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-mode1").style.display="table-row";
		document.getElementById("e-"+elementId+"-mode2").style.display="none";
	}
}

VSE_VSEID_CheckRange=function(d,value) {
	if (d) {
		//nummerischer Wert gefordert?
		var vmin=parseFloat(d.dataset.var5);
		var vmax=parseFloat(d.dataset.var6);
		if (!isNaN(vmin) && !isNaN(vmax)) {
			value=parseFloat(value);

			if (isNaN(value) || value<vmin) {value=vmin;}
			if (value>vmax) {value=vmax;}

			var vstep=parseFloat(d.dataset.var7);
			if (!isNaN(vstep) && vstep!=0) {value=Math.round(value/vstep)*vstep;}
			
			var vlist=parseInt(d.dataset.var8);
			if (vlist>=0) {value=value.toFixed(vlist);}
		}
	}
	return value;
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Tastatureingabe" ermöglicht das Bearbeiten eines KO-Wertes per Tastatur (bzw. Bildschirmtastatur auf Smartphones und Tablets).

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Modus: zeigt die Eingaben lesbar an ("normal") oder ersetzt die Eingabe und die Beschriftung visuell mit Platzhaltern ("Passwort")</li>

	<li>
		Inhalt vorselektieren: legt fest, ob der angezeigte Inhalt beim Bearbeiten vorselektiert werden soll oder nicht
		<ul>
			<li>bei aktivierter Vorselektierung wird der Inhalt ausgewählt und eine Eingabe führt zum Überschreiben des Inhalts</li>
		</ul>
	</li>

	<li>
		Eingabe abschließen (KO2-Wert setzen): legt fest, auf welche Weise die Eingabe abgeschlossen und der KO2-Wert auf den Inhalt der Eingabe gesetzt werden soll
		<ul>
			<li>nur mit Enter-Taste: die Eingabe wird nur mittels der Enter-Taste übernommen (jeder Verlust des Eingabefokus verwirft die Eingabe, z.B. durch einen Klick ausserhalb des Visuelements oder bei einem Seitenwechsel ohne Nutzerinteraktion)</li>
			<li>mit Enter-Taste und bei Fokusverlust: die Eingabe wird mittels der Enter-Taste oder durch den Verlust des Eingabefokus übernommen (jeder Verlust des Eingabefokus führt zur Übernahme der Eingabe, z.B. durch einen Klick ausserhalb des Visuelements oder bei einem Seitenwechsel ohne Nutzerinteraktion)</li>
		</ul>
	</li>
 
	<li>
		Minimum (Integer/Float): unterer Grenzwert der Eingabe (unterschreitet die Eingabe das Minimum, wird die Eingabe auf diesen Wert korrigiert)
		<ul>
			<li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
		</ul>
	</li>

	<li>
		Maximum (Integer/Float): oberer Grenzwert der Eingabe (überschreitet die Eingabe das Maximum, wird die Eingabe auf diesen Wert korrigiert)
		<ul>
			<li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
		</ul>
	</li>

	<li>
		Raster (Integer/Float): die Eingabe wird auf einen Wert mit dieser "Schrittweite" umgerechnet
		<ul>
			<li>z.B. Raster=0.5: die Eingabe 0.45 wird zu 0, die Eingabe 2.98 wird zu 2.5 umgerechnet</li>
			<li>wird dieses Feld [leer] belassen, werden ggf. die KO-Filtereinstellungen angewendet</li>
		</ul>
	</li>

	<li>
		Nachkommastellen: die Eingabe wird ggf. auf die angegebene Anzahl von Nachkommastellen gerundet
		<ul>
			<li>Option "KO-Filter": ggf. werden die KO-Filtereinstellungen angewendet</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Status
		<ul>
			<li>dieser KO-Wert wird als Ausgangswert einer Eingabe verwendet</li>
			<li>dieser KO-Wert wird zudem zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
		</ul>
	</li>

	<li>
		KO2: Wert setzen
		<ul>
			<li>dieses KO wird auf den per Tastatur eingegebenen Wert gesetzt</li>
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
	<li>Sofern nur die Eingabe von Zahlen erlaubt werden soll, müssen die Eigenschaften Minimum und/oder Maximum angegeben werden (im Visuelement bzw. in den KO-Einstellungen). Andernfalls können sowohl Zahlen, als auch Texte eingegeben werden.</li>
	<li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Zum Eingeben eines Wertes wird das Visuelement angeklickt.
Das Eingabefeld wird dann in Indikatorfarbe dargestellt und ist für Tastatureingaben bereit (auf Smartphones und Tablets wird ggf. die Bildschirmtastatur des Betriebsystems eingeblendet).
Mit der Enter-Taste wird die Eingabe abgeschlossen, ein Klick ausserhalb des Visuelements bricht die Eingabe ab.
###[/HELP]###


