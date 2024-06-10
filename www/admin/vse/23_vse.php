###[DEF]###
[name	=Anwesenheitssimulation]

[folderid=164]
[xsize	=250]
[ysize	=200]

[var1	=0 #root=110]
[var3	=1]
[var10	=]
[var11	=1]

[flagText		=0]
[flagKo1		=2]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]

[captionKo1		=Steuerungs-KO der Anwesenheitssim. (0=Deaktiviert, 1=Abspielen, 2=Aufnehmen)]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = root,2,'Anwesenheitssimulation',110]

[row=Bedienung]
	[var3 = select,1,'Schaltflächen','0#keine|1#Steuerung']
	[var11= select,1,'Statusanzeige','0#deaktiviert|1#Indikatorfarbe|2#Zusatzhintergrundfarbe 1']

[row=Darstellung]
	[var10= text,2,'Kopf-/Fusszeilenhöhe (px, leer=Standard)','']
###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//gaid auf das Steuerungs-KO der AWS setzen
$tmp=sql_getValues('edomiProject.editAws','gaid','id='.$item['var1']);
if ($tmp!==false) {
	sql_call("UPDATE edomiLive.visuElement SET gaid=".$tmp['gaid']." WHERE id=".$item['id']);
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.PHP]###
<?
$property[0]=sql_getValue('edomiProject.editAws','name','id='.$item['var1']);
?>
###[/EDITOR.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
	var n="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr style='height:"+mheight+"px;'><td align='center'><div style='max-height:"+mheight+"px; overflow:hidden;'>"+property[0]+"</div></td></tr>";
		n+="<tr><td align='center' style='border-top:1px dotted; border-bottom:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>ANWESENHEITSSIMULATION</span>")+"</td></tr>";
	if (obj.dataset.var3==1) {
		if (obj.dataset.var11>0) {var color=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");} else {var color="transparent";}
		n+="<tr style='height:"+mheight+"px;'><td>";
			n+="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
				n+="<tr align='center'>";
					n+="<td "+((koValue==0)?"style='background:"+color+";'":"")+">"+graphics_svg_icon(0)+"</td>";
					n+="<td "+((koValue==2)?"style='background:"+color+";'":"")+">"+graphics_svg_icon(4)+"</td>";
					n+="<td "+((koValue==1)?"style='background:"+color+";'":"")+">"+graphics_svg_icon(3)+"</td>";
				n+="</tr>";
			n+="</table>";
		n+="</td></tr>";
	}
	n+="</table>";
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
	return property[0];
}

###[/EDITOR.JS]###


###[VISU.PHP]###
<?
function PHP_VSE_VSEID($cmd,$json1,$json2) {
	global $global_weekdays;

	if ($cmd=='awsInfo') {
		$anzTotal=0;
		for ($t=1;$t<=7;$t++) {$anz[$t]=0;}
		$ss1=sql_call("SELECT COUNT(*) AS anz1,LEFT(timestamp,1) AS wday FROM edomiLive.awsData WHERE (targetid=".$json1['awsId'].") GROUP BY LEFT(timestamp,1) ORDER BY LEFT(timestamp,1) ASC");
		while ($n=sql_result($ss1)) {
			$anz[$n['wday']]=$n['anz1'];
			$anzTotal+=$n['anz1'];
		}
		sql_close($ss1);
?>
		var n="<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>";
		n+="<tr valign='middle'><td align='center'>";
		n+="<table width='80%' height='80%' border='0' cellspacing='0' cellpadding='3'>";
		n+="<tr height='80%' align='center' valign='bottom'>";
<?
		$maxAnz=max($anz);
		for ($t=1;$t<=7;$t++) {
			if ($anz[$t]>0) {$y=intVal(100/$maxAnz*$anz[$t]);} else {$y=0;}
?>
			n+="<td><div style='width:1px; height:<?echo $y;?>%; border:1px solid; opacity:0.75;'></div></td>";
<?
		}
?>
		n+="</tr>";
		n+="<tr height='10%' align='center' style='opacity:0.75;'>";
<?
		for ($t=1;$t<=7;$t++) {
			if ($t==date("N")) {
?>
				n+="<td><span style='border-bottom:1px solid;'><?echo substr($global_weekdays[$t-1],0,2)?></span></td>";
<?
			} else {
?>
				n+="<td><span style='border-bottom:1px solid transparent;'><?echo substr($global_weekdays[$t-1],0,2)?></span></td>";
<?
			}
		}
?>
		n+="</tr>";
		n+="<tr height='10%' align='center'><td style='opacity:0.75;' colspan='7'><?echo $anzTotal;?> Aufzeichnungen</td></tr>";
		n+="</table>";
		n+="</td></tr>";
		n+="</table>";
		VSE_VSEID_callbackList(<?echo $json1['elementId'];?>,n,"<?echo escapeString(sql_getValue('edomiLive.aws','name','id='.$json1['awsId']),1);?>");
<?
	}
}
?>

###[/VISU.PHP]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {

	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;

	var n="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr style='height:"+mheight+"px;'><td align='center' id='e-"+elementId+"-info'><div id='e-"+elementId+"-infotext' style='max-height:"+mheight+"px; overflow:hidden;'></div></td></tr>";
		n+="<tr><td align='center' style='border-top:1px solid;"+((obj.dataset.var3==1)?"border-bottom:1px solid;":"")+"'><div id='e-"+elementId+"-edit' style='width:100%; height:100%; overflow:hidden;'></div></td></tr>";

	if (obj.dataset.var3==1) {
		n+="<tr style='height:"+mheight+"px;'><td>";
			n+="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'>";
				n+="<tr>";
					n+="<td id='e-"+elementId+"-off'>"+graphics_svg_icon(0)+"</td>";
					n+="<td id='e-"+elementId+"-rec'>"+graphics_svg_icon(4)+"</td>";
					n+="<td id='e-"+elementId+"-play'>"+graphics_svg_icon(3)+"</td>";
				n+="</tr>";
			n+="</table>";
		n+="</td></tr>";
	}
	n+="</table>";
	obj.innerHTML=n;
	
	VSE_VSEID_ShowInfo(elementId);

	if (visuElement_hasCommands(elementId)) {
		visuElement_onClick(document.getElementById("e-"+elementId+"-edit"),function(veId,objId){visuElement_doCommands(veId);});
	}

	visuElement_onClick(document.getElementById("e-"+elementId+"-info"),function(veId,objId){VSE_VSEID_ShowInfo(veId);});
	if (obj.dataset.var3==1) {
		visuElement_onClick(document.getElementById("e-"+elementId+"-off"),function(veId,objId){visuElement_setKoValue(veId,1,0);});
		visuElement_onClick(document.getElementById("e-"+elementId+"-rec"),function(veId,objId){visuElement_setKoValue(veId,1,2);});
		visuElement_onClick(document.getElementById("e-"+elementId+"-play"),function(veId,objId){visuElement_setKoValue(veId,1,1);});
	}
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	if (obj.dataset.var11!=0) {
		var s0=document.getElementById("e-"+elementId+"-off");
		var s1=document.getElementById("e-"+elementId+"-play");
		var s2=document.getElementById("e-"+elementId+"-rec");
		if (s0 && s1 && s2) {
			if (koValue==2) {
				s0.style.background="none";
				s1.style.background="none";
				s2.style.background=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");
			} else if (koValue==1) {
				s0.style.background="none";
				s1.style.background=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");
				s2.style.background="none";
			} else {
				s0.style.background=((obj.dataset.var11==1)?visu_indiColor:"var(--bgc1)");
				s1.style.background="none";
				s2.style.background="none";
			}
		}
	}
	VSE_VSEID_ShowInfo(elementId);
}

VSE_VSEID_ShowInfo=function(elementId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		visuElement_callPhp("awsInfo",{elementId:elementId,awsId:d.dataset.var1},null);
	}
}

VSE_VSEID_callbackList=function(elementId,content,title) {
	var d=document.getElementById("e-"+elementId);
	if (d) {
		document.getElementById("e-"+elementId+"-infotext").innerHTML=title;
		document.getElementById("e-"+elementId+"-edit").innerHTML=content;
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Anwesenheitssimulation" stellt eine <link>konfigurierte Anwesenheitssimulation***1000-110</link> in der Visualisierung dar und ermöglicht das Steuern der Anwesenheitssimulation, zudem werden statistische Daten angezeigt.

<b>Hinweis:</b>
Die Anwesenheitssimulation arbeitet unabhängig von diesem Visuelement. Das Visuelement ist also nicht erforderlich, um eine Anwesenheitssimulation zu nutzen.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>Anwesenheitssimulation: Auswahl der <link>konfigurierten Anwesenheitssimulation***1000-110</link>, die angezeigt und gesteuert werden soll</li>

	<li>
		Schaltflächen: legt fest, ob der Betriebszustand der Anwesenheitssimulation verändert werden kann
		<ul>
			<li>Hinweis: die Schaltflächen werden mit Symbolen beschriftet (von links nach rechts: deaktivieren, aufnehmen, abspielen)</li>
			<li>Wichtig: Die Anwesenheitssimulation kann unabhängig davon stets über das entsprechende KO gesteuert werden.</li>
		</ul>
	</li>

	<li>
		Statusanzeige: legt fest, ob und wie der aktuelle Status (KO1) angezeigt wird
		<ul>
			<li>deaktiviert: keine Statusanzeige</li>
			<li>Indikatorfarbe: die entsprechende Schaltfläche wird mit der <link>Indikatorfarbe***1000-21</link> hinterlegt</li>
			<li>Zusatzhintergrundfarbe 1: die entsprechende Schaltfläche wird mit der <link>Zusatzhintergrundfarbe 1***1003</link> hinterlegt</li>
		</ul>
	</li>

	<li>Kopf-/Fusszeilenhöhe: legt optional die Höhe der Kopf- und Fusszeile in Pixeln fest</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerungs-KO der Anwesenheitssimulation
		<ul>
			<li>dieses KO ist stets mit dem Steuerungs-KO der zugewiesenen Anwesenheitssimulation verknüpft</li>
			<li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>dieser KO-Wert wird zudem beim Bedienen der Anwesenheitssimulation in der Visualisierung entsprechend gesetzt (Anwesenheitssimulation: deaktiviert/Abspielen/Aufnehmen)</li>
			<li>immer wenn das KO auf einen Wert gesetzt wird, wird die angezeigte Statistik der Anwesenheitssimulation aktualisiert</li>
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
	<li>Designs: Innenabstand und Textausrichtung werden ignoriert</li>
	<li>Hinweis: wenn keine Seitensteuerungen/Befehle zugewiesen wurden, verhält sich dieses Visuelement dennoch nicht <link>klicktransparent***1002</link></li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Das Visuelement ist in 3 Teilbereiche gegliedert (von oben nach unten): 

<ul>
	<li>
		Titelleiste:
		<ul>
			<li>hier werden der Name der <link>Anwesenheitssimulation***1000-110</link> angezeigt</li>
			<li>ein Klick auf den Namen der Anwesenheitssimulation aktualisiert die angezeigte Statistik</li>
		</ul>
	</li>

	<li>
		Statistik:
		<ul>
			<li>hier wird eine Statistik der aufgezeichneten KO-Werte pro Wochentag angezeigt</li>
			<li>Hinweis: es erfolgt keine(!) automatische Aktualisierung z.B. wenn KOs aufgezeichnet werden - erst bei einer Statusänderung (KO) oder durch einen Klick auf den Titel (s.o.) wird die Statistik aktualisiert</li>
			<li>mit einem Klick auf diesen Bereich werden alle zugewiesenen Seitensteuerungen/Befehle ausgeführt</li>
		</ul>
	</li>

	<li>
		Steuerungs-Schaltflächen:
		<ul>
			<li>"aus": deaktiviert die Anwesenheitssimulation (das Steuerungs-KO der Anwesenheitssimulation wird auf den Wert "0" gesetzt)</li>
			<li>"+": neue KO-Werte werden aufgezeichnet (das Steuerungs-KO der Anwesenheitssimulation wird auf den Wert "2" gesetzt)</li>
			<li>"ein": aufgezeichnete KO-Werte werden abgespielt (das Steuerungs-KO der Anwesenheitssimulation wird auf den Wert "1" gesetzt)</li>
		</ul>
	</li>
</ul>
###[/HELP]###


