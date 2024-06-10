###[DEF]###
[name	=Weiterleitung]

[folderid=164]
[xsize	=100]
[ysize	=50]

[var1	=1]
[var2	=0]
[var11	=0]
[var12	=0]

[var3	=]
[var4	=]
[var5	=]
[var6	=]
[var7	=]
[var8	=]
[var9	=]
[var10	=]
[var13	=0 #root=22]
[var14	=0 #root=22]
[var15	=0 #root=22]
[var16	=0 #root=22]
[var17	=0 #root=22]
[var18	=0 #root=22]
[var19	=0 #root=22]
[var20	=0 #root=22]

[flagText		=0]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=0]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=0]
[flagDynDesign	=0]
###[/DEF]###


###[PROPERTIES]###
[columns=40,60]
[row]
	[var1 = select,2,'Ausführung','1#bei Seitenaufruf|2#KO-gesteuert|3#bei Seitenaufruf und KO-gesteuert']

[row=KO-Werte und Visuseiten]
	[var3 = text,1,'KO-Wert(e)','']
	[var13 = root,1,'Visuseite/Popup',22]
[row]
	[var4 = text,1,'','']
	[var14 = root,1,'',22]
[row]
	[var5 = text,1,'','']
	[var15 = root,1,'',22]
[row]
	[var6 = text,1,'','']
	[var16 = root,1,'',22]
[row]
	[var7 = text,1,'','']
	[var17 = root,1,'',22]
[row]
	[var8 = text,1,'','']
	[var18 = root,1,'',22]
[row]
	[var9 = text,1,'','']
	[var19 = root,1,'',22]
[row]
	[var10 = text,1,'','']
	[var20 = root,1,'',22]

[row=Seitensteuerung und Befehle]
	[var11 = select,2,'Seitensteuerung bei Weiterleitung','0#deaktiviert|1#Popup schließen']

[row]
	[var12 = select,2,'Seitensteuerung wenn keine Weiterleitung','0#deaktiviert|2#Seite aufrufen|1#Popup schließen|3#Seite aufrufen und Popup schließen']

[row]
	[var2 = select,2,'Befehle ausführen','0#deaktiviert|1#bei Weiterleitung|2#wenn keine Weiterleitung|3#immer']
###[/PROPERTIES]###

###[ACTIVATION.PHP]###
<?
//PageIds löschen, wenn nicht Bestandteil der entsprechenden Visu
for ($t=13;$t<=20;$t++) {
	if ($item['var'.$t]>0) {
		$tmp=sql_getValue('edomiProject.editVisuPage','id','id='.$item['var'.$t].' AND visuid='.$item['visuid']);
		if (isEmpty($tmp)) {
			sql_call("UPDATE edomiLive.visuElement SET var".$t."=NULL WHERE id=".$item['id']);
		}
	}
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	if (isPreview) {
		var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'><tr><td><span class='app2_pseudoElement'>(UNSICHTBAR)</span></td></tr></table>";
	} else {
		var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'><tr><td><span class='app2_pseudoElement'>WEITERLEITUNG</span></td></tr></table>";
	}
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
	return false;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	//unsichtbares Visuelement
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//VE ist immer unsichtbar
	obj.style.display="none";

	if (isInit && obj.dataset.var1&1) {
		var pageId=VSE_VSEID_getPage(elementId,obj,koValue);
		if (pageId>0) {
			visuElement_openPage(pageId);
		}

	} else if (isRefresh && obj.dataset.var1&2 && visuElement_hasKo(elementId,1)) {
		var pageId=VSE_VSEID_getPage(elementId,obj,koValue);
		if (pageId>0) {
			visuElement_openPage(pageId);
		}
	}
}

VSE_VSEID_getPage=function(elementId,obj,koValue) {
	var page=0;
	if (obj) {
		for (var t=3;t<=10;t++) {
			if (obj.dataset["var"+t]!="" && obj.dataset["var"+(t+10)]>0) {
				var tmp=obj.dataset["var"+t].split(";");
				for (var tt=0;tt<tmp.length;tt++) {
					if (koValue.toString()===tmp[tt].toString()) {
						page=obj.dataset["var"+(t+10)];
						break;
					}
				}
			}
			if (page>0) {break;}
		}
	
		if (page>0) {
			visuElement_doCommands(elementId,((obj.dataset.var2&1)?true:false),false,((obj.dataset.var11&1)?true:false),((obj.dataset.var11&1)?true:false));
		} else {
			visuElement_doCommands(elementId,((obj.dataset.var2&2)?true:false),((obj.dataset.var12&2)?true:false),((obj.dataset.var12&1)?true:false),((obj.dataset.var12&1)?true:false));
		}
	}
	return page;
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Weiterleitung" ermöglicht das Aufrufen beliebiger Visuseiten in Abhängigkeit des KO1-Wertes. Zudem kann festgelegt werden, ob Seitensteuerung/Befehle bei einer erfolgreichen Weiterleitung oder bei einer nicht erfolgreichen Weiterleitung ausgeführt werden sollen.

Sobald die Visuseite, die dieses Visuelement enthält aufgerufen wird, werden ggf. die entsprechenden Seitensteuerungen/Befehle ausgeführt (ohne Nutzerinteraktion). Optional kann die Ausführung auch KO-gesteuert erfolgen.

<b>Hinweis:</b>
Dieses Visuelement ist in der Visualisierung nicht sichtbar.

<b>Achtung:</b>
Das automatisierte Aufrufen von Visuseiten kann u.U. zu einer Endlosschleife führen, z.B. wenn das Visuelement derart konfiguriert wurde, dass die das Visuelement enthaltene Visuseite erneut aufgerufen wird.


<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Ausführung: legt fest, unter welchen Bedingungen die Weiterleitung ggf. ausgeführt werden soll
		<ul>
			<li>bei Seitenaufruf: die Ausführung erfolgt ggf. bei jedem Aufruf der Visuseite, die dieses Visuelement enthält (sofern der KO1-Wert den Bedingungen entspricht bzw. nicht entspricht)</li>
			<li>KO-gesteuert: die Ausführung erfolgt ggf. immer dann, wenn der sich Wert des KO1 ändert (sofern der KO1-Wert den Bedingungen entspricht bzw. nicht entspricht)</li>
			<li>Wichtig: Sofern die o.g. Bedingung erfüllt ist, erfolgt die Ausführung bei jedem Seitenaufruf erneut, bis die o.g. Bedingung nicht mehr erfüllt ist.</li>
		</ul>
	</li>
</ul>

<ul>
	<li>
		KO-Werte und Visuseiten: legt fest, welche KO1-Werte das Aufrufen der entsprechenden Visuseite bewirken
		<ul>
			<li>KO-Wert(e): KO-Wert (ggf. auch mehrere), die zu einem Aufruf der zugewiesenen Visuseite führen (mehrere Werte sind mit einem Semikolon zu separieren, z.B. "1;2;3")</li>
			<li>Visuseite/Popup: diese Visuseite/Popup wird aufgerufen, wenn der KO1-Wert den o.g. Angaben entspricht</li>
		</ul>
	</li>
</ul>

<ul>
	<li>
		Seitensteuerung und Befehle: legt fest, unter welchen Bedingungen die Seitensteuerungen/Befehle ausgeführt werden sollen
		<ul>
			<li>Seitensteuerung bei Weiterleitung: bei einer erfolgreichen Weiterleitung (KO1-Wert entspricht einer der Angaben) kann optional das aktuelle Popup (auf dem sich dieses Visuelement befindet) geschlossen werden</li>
			<li>Seitensteuerung wenn keine Weiterleitung: bei einer nicht erfolgten Weiterleitung (KO1-Wert entspricht nicht den Angaben) können optional die entsprechenden Seitensteuerungen des Visuelements ausgeführt werden</li>
			<li>Befehle ausführen: legt fest, ob und unter welchen Bedinungen die Befehle des Visuelements ausgeführt werden sollen</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerung
		<ul>
			<li>dieses KO wird ausschließlich zur Ausführung der Weiterleitung (s.o.) verwendet, indem dessen Wert mit den Angaben im Feld "KO-Wert(e)" verglichen wird</li>
		</ul>
	</li>
</ul>


<h2>Besonderheiten</h2>
<ul>
	<li>
		Verhalten des Visuelements:
		<ul>
			<li>wird die Visuseite, die das Visuelement enthält erneut aufgerufen, erfolgt die Ausführung der Weiterleitung bzw. der Seitensteuerungen/Befehle ggf. erneut</li>
		</ul>
	</li>

	<li>Designs stehen nicht zu Verfügung (das Visuelement ist in der Visualisierung nicht sichtbar)</li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
In der Visualisierung ist dieses Element vollständig unsichtbar und daher nicht bedienbar. Die Steuerung erfolgt ausschließlich über den KO-Wert des zugewiesenen KOs bzw. automatisch beim Aufruf der Visuseite.
###[/HELP]###
