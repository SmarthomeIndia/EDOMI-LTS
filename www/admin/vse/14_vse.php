###[DEF]###
[name	=Liste/Tabelle]

[folderid=161]
[xsize	=200]
[ysize	=250]
[text	={#}]

[var1	=]
[var2	=]
[var3	=0]
[var4	=0]
[var5	=0]
[var6	=3]
[var7	=0]
[var8	=0]
[var9	=0]
[var10	=]
[var11	=0]
[var12	=0]
[var13	=0]
[var14	=]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=1]
[flagKo3		=1]
[flagPage		=1]
[flagCmd		=1]
[flagDesign		=1]
[flagDynDesign	=1]

[captionText	=Daten]
###[/DEF]###


###[PROPERTIES]###
[columns=50,50]
[row]
	[var1 = text,1,'Zeilentrenner (leer=Zeilenumbruch)','']
	[var2 = text,1,'Spaltentrenner (leer=keine Spalten)','']

[row]
	[var14 = text,1,'Hervorhebung (leer=keine)','']
	[var3 = select,1,'Sortierung','0#ohne|1#aufsteigend (ganze Zeile)|2#absteigend (ganze Zeile)|3#aufsteigend (Spalte 1)|4#absteigend (Spalte 1)|5#aufsteigend (Spalte 1, ausblenden)|6#absteigend (Spalte 1, ausblenden)']

[row=Titelzeile]
	[var7 = select,1,'Anzeige','0#keine Titelzeile|1#1. Zeile ist Titelzeile|2#1. Zeile ist Titelzeile (Zeilenhöhe minimieren)']
	[var13 = select,1,'Trennlinien','0#ohne|1#Zeile|2#Spalten|3#Zeile und Spalten']

[row=Darstellung]
	[var4 = text,1,'Zeilenhöhe (px, 0=optimal)','']
	[var9 = select,1,'Spaltenbreite','0#gleichmäßig/individuell|1#automatisch']
[row]
	[var5 = text,1,'Innenabstand (px, 0=ohne)','']
	[var6 = select,1,'Trennlinien','0#ohne|1#Zeilen|2#Spalten|3#Zeilen und Spalten']

[row]
	[var8 = select,2,'Blättern-Schaltflächen','0#nur anzeigen wenn erforderlich|1#immer anzeigen']

[row]
	[var10= text,2,'Kopfzeilenhöhe (px, leer=Standard)','']

[row=Klick-Verhalten]
	[var11 = select,1,'Reaktion auf Klick','0#deaktiviert|1#gesamte Zeile|2#einzelne Zelle']
	[var12 = select,1,'KO2-Wert','0#Index (datenbasiert)|1#Index (wie angezeigt)|2#Inhalt (gesamt)|3#Inhalt (ohne Spalte 1)|4#Inhalt (nur Spalte 1)']

###[/PROPERTIES]###


###[ACTIVATION.PHP]###
<?
//Zeilentrenner auf <br> setzen, falls leer (Hinweis: in der Visu wird CHR(10/13) in <br> umgewandelt)
if (isEmpty($item['var1'])) {
	sql_call("UPDATE edomiLive.visuElement SET var1='<br>' WHERE (id=".$item['id'].")");
}
?>
###[/ACTIVATION.PHP]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
	var n="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr style='height:"+mheight+"px;'>";
			n+="<td width='50%' align='center'>&lt;</td>";
			n+="<td width='50%' align='center'>&gt;</td>";
		n+="</tr>";
		n+="<tr><td colspan='2' style='border-top:1px dotted;'>"+((isPreview)?"":"<span class='app2_pseudoElement'>LISTE/TABELLE</span>")+"</td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	//kein Padding
	obj.style.padding="0";

	return false;
}
###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	var mheight=(obj.dataset.var10!='')?obj.dataset.var10:40;
	var n="<table cellpadding='0' cellspacing='0' border='0' style='position:absolute; left:0; top:0; width:100%; height:100%;'>";
		n+="<tr id='e-"+elementId+"-scroll' style='height:"+mheight+"px; display:none;'>";
			n+="<td width='50%' align='center' id='e-"+elementId+"-last'>&lt;</td>";
			n+="<td width='50%' align='center' id='e-"+elementId+"-next'>&gt;</td>";
		n+="</tr>";
		n+="<tr><td colspan='2' id='e-"+elementId+"-scroll2'><div style='position:relative; height:100%;'><div id='e-"+elementId+"-edit' style='position:absolute; top:0; left:0; right:0; bottom:0; overflow-x:hidden; overflow-y:auto;'></div></div></td></tr>";
	n+="</table>";
	obj.innerHTML=n;

	if (visuElement_hasCommands(elementId) && obj.dataset.var11==0) {
		visuElement_onClick(document.getElementById("e-"+elementId+"-edit"),function(veId,objId){visuElement_doCommands(veId);});
	}

	visuElement_onClick(document.getElementById("e-"+elementId+"-last"),function(veId,objId){scrollUp("e-"+veId+"-edit");});
	visuElement_onClick(document.getElementById("e-"+elementId+"-next"),function(veId,objId){scrollDown("e-"+veId+"-edit");});

	visuElement_newGlobal(elementId,{data:new Array()});
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//kein Padding
	obj.style.padding="0";

	VSE_VSEID_update(elementId,visuElement_parseString(visuElement_getText(elementId),koValue));
}

VSE_VSEID_update=function(elementId,list) {
	var veVar=visuElement_getGlobal(elementId);
	var d=document.getElementById("e-"+elementId);
	if (d && veVar) {
		veVar.data=new Array();
		var obj=document.getElementById("e-"+elementId+"-edit");

		//Zeilen erzeugen
		var rows=list.split(d.dataset.var1);
		var rowsMax=rows.length;
		if (rows[rowsMax-1]=="") {
			rows.pop();
			rowsMax--;
		}	
		
		if (rowsMax>0) {
			//Spalten erzeugen
			var colsMin=0;
			var colsMax=1;
			if (d.dataset.var2=="") {
				for (var t=0;t<rowsMax;t++) {
					veVar.data[t]=[rows[t]];
					veVar.data[t].push(t);
				}
			} else {
				for (var t=0;t<rowsMax;t++) {
					veVar.data[t]=rows[t].split(d.dataset.var2);
					veVar.data[t].push(t);
					if (veVar.data[t].length>colsMax) {colsMax=veVar.data[t].length;}
				}
				colsMax--;
			}
			if ((d.dataset.var3==5 || d.dataset.var3==6) && colsMax>1) {colsMin=1;}

			//Rendern: Header
			var b0="";
			var b1="";
			var b2="";
			var n="<table cellpadding='"+d.dataset.var5+"' cellspacing='0' border='0' style='width:100%;"+((d.dataset.var4==0)?" height:100%;":"")+((d.dataset.var9==1)?" table-layout:auto;":"")+"'>";

			//Rendern: Titelzeile (ggf. Spaltenbreiten parsen)
			if (d.dataset.var7>=1) {
				var col0=veVar.data.shift();
				rowsMax--;

				var nn="";
				if (d.dataset.var13==1 || d.dataset.var13==3) {b1="border-bottom:1px solid var(--fgc1);";}
				if (d.dataset.var7==2) {nn+="<tr height='1'>";} else {nn+="<tr"+((d.dataset.var4>0)?" height='"+d.dataset.var4+"'":"")+">";}

				var tmp="";
				var isSet=false;
				for (var tt=colsMin;tt<colsMax;tt++) {
					if (col0[tt]) {
						var meta=col0[tt].split("***");
						if (meta.length==2 && !isNaN(parseFloat(meta[1]))) {
							tmp+="<col width='"+parseFloat(meta[1])+"%'>";
							isSet=true;
						} else {
							tmp+="<col width=''>";
						}
						col0[tt]=meta[0];
					}

					if (tt<(colsMax-1) && (d.dataset.var13==2 || d.dataset.var13==3)) {b2="border-right:1px solid var(--fgc1);";} else {b2="";}
					nn+="<td style='"+b1+b2+" color:var(--fgc2); background:var(--bgc2);'>"+(((col0[tt]=="" || col0[tt]===undefined))?"&nbsp;":col0[tt])+"</td>";
				}
				if (isSet && d.dataset.var9==0) {n+="<colgroup>"+tmp+"</colgroup>";}
				n+=nn;
			}			

			//Daten sortieren
			if (d.dataset.var3==1) {
				veVar.data.sort();
			} else if (d.dataset.var3==2) {
				veVar.data.sort();
				veVar.data.reverse();
			} else if (d.dataset.var3==3 || d.dataset.var3==5) {
				veVar.data.sort(sort1up);
			} else if (d.dataset.var3==4 || d.dataset.var3==6) {
				veVar.data.sort(sort1down);
			}

			//Rendern: Daten
			for (var t=0;t<rowsMax;t++) {
				if (t<(rowsMax-1) && (d.dataset.var6==1 || d.dataset.var6==3)) {b1="border-bottom:1px solid var(--fgc1);";} else {b1="";}

				if (d.dataset.var14!="" && veVar.data[t][0].indexOf(d.dataset.var14)>=0) {
					b0=" style='background:var(--bgc1);'";
					veVar.data[t][0]=veVar.data[t][0].replace(d.dataset.var14,"");
				} else {
					b0="";
				}

				if (d.dataset.var11==1) {
					n+="<tr"+((d.dataset.var4>0)?" height='"+d.dataset.var4+"'":"")+b0+" id='e-"+elementId+"-"+t+"'>";
				} else {
					n+="<tr"+((d.dataset.var4>0)?" height='"+d.dataset.var4+"'":"")+b0+">";
				}

				for (var tt=colsMin;tt<colsMax;tt++) {
					if (tt<(colsMax-1) && (d.dataset.var6==2 || d.dataset.var6==3)) {b2="border-right:1px solid var(--fgc1);";} else {b2="";}

					if (tt<veVar.data[t].length-1) {
						if (veVar.data[t][tt]===undefined) {veVar.data[t][tt]="";}

						if (d.dataset.var11==2) {
							n+="<td style='"+b1+b2+"' id='e-"+elementId+"-"+t+"x"+tt+"'>"+((veVar.data[t][tt]=="")?"&nbsp;":veVar.data[t][tt])+"</td>";
						} else {
							n+="<td style='"+b1+b2+"'>"+((veVar.data[t][tt]=="")?"&nbsp;":veVar.data[t][tt])+"</td>";
						}
					} else {
						n+="<td style='"+b1+b2+"'>&nbsp;</td>";
					}
				}
				n+="</tr>";
			}
			
			//Rendern: Fertig
			n+="</table>";			
			obj.innerHTML=n;

			//Klickhandler zuweisen
			if (visuElement_hasKo(elementId,2)) {
				if (d.dataset.var11==1) {
					for (var t=0;t<rowsMax;t++) {
						visuElement_onClick(document.getElementById("e-"+elementId+"-"+t),function(veId,objId){VSE_VSEID_contentClick(veId,objId);});
					}

				} else if (d.dataset.var11==2) {
					for (var t=0;t<rowsMax;t++) {
						for (var tt=colsMin;tt<colsMax;tt++) {
							visuElement_onClick(document.getElementById("e-"+elementId+"-"+t+"x"+tt),function(veId,objId){VSE_VSEID_contentClick(veId,objId);});
						}
					}
				}
			}

		} else {
			//keine Daten (wichtig für ClickEvent!)
			obj.innerHTML="<table cellpadding='0' cellspacing='0' border='0' style='width:100%; height:100%;'><tr><td>&nbsp;</td></tr></table>";
		}

		//Kopfzeile
		if (d.dataset.var8==1 || parseInt(obj.scrollHeight)>parseInt(obj.clientHeight)) {
			document.getElementById("e-"+elementId+"-scroll").style.display="table-row";
			document.getElementById("e-"+elementId+"-scroll2").style.borderTop="1px solid";
		} else {
			document.getElementById("e-"+elementId+"-scroll").style.display="none";
			document.getElementById("e-"+elementId+"-scroll2").style.borderTop="none";
		}
	}

	function sort1up(a,b) {
		if (a[0]===b[0]) {
			return 0;
		} else {
			return (a[0]<b[0])?-1:1;
		}
	}
	
	function sort1down(a,b) {
		if (a[0]===b[0]) {
			return 0;
		} else {
			return (a[0]>b[0])?-1:1;
		}
	}
}

VSE_VSEID_contentClick=function(elementId,objId) {
	var d=document.getElementById("e-"+elementId);
	if (d) {

		var veVar=visuElement_getGlobal(elementId);
		if (veVar) {
			var dataId=new Array();
			var tmp=objId.split("-");
			if (tmp.length==3) {
				var id=tmp[2].split("x");
				if (id.length==1) {id.push(0);}
				dataId[0]=veVar.data[id[0]][veVar.data[id[0]].length-1];
				dataId[1]=id[1];

				if (d.dataset.var11==1) {
					if (d.dataset.var12==0) {
						visuElement_setKoValue(elementId,2,dataId[0]);

					} else if (d.dataset.var12==1) {
						if (d.dataset.var7>=1 && d.dataset.var12==1) {id[0]++;}
						visuElement_setKoValue(elementId,2,id[0]);

					} else if (d.dataset.var12==2) {
						var tmp=Array.from(veVar.data[id[0]]);
						tmp.pop();
						visuElement_setKoValue(elementId,2,tmp.join(d.dataset.var2));

					} else if (d.dataset.var12==3) {
						var tmp=Array.from(veVar.data[id[0]]);
						tmp.pop();
						tmp.shift();
						visuElement_setKoValue(elementId,2,tmp.join(d.dataset.var2));

					} else if (d.dataset.var12==4) {
						visuElement_setKoValue(elementId,2,veVar.data[id[0]][0]);
					}

				} else if (d.dataset.var11==2) {
					if (d.dataset.var12==0) {
						visuElement_setKoValue(elementId,2,dataId[0]+";"+dataId[1]);

					} else if (d.dataset.var12==1) {
						if (d.dataset.var7>=1 && d.dataset.var12==1) {id[0]++;}
						visuElement_setKoValue(elementId,2,id[0]+";"+id[1]);

					} else if (d.dataset.var12==2) {
						visuElement_setKoValue(elementId,2,veVar.data[id[0]][id[1]]);

					} else if (d.dataset.var12==3 && id[1]>0) {
						visuElement_setKoValue(elementId,2,veVar.data[id[0]][id[1]]);

					} else if (d.dataset.var12==4) {
						visuElement_setKoValue(elementId,2,veVar.data[id[0]][0]);
					}
				}
			}
		}
	}	
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Liste/Tabelle" generiert aus den Angaben im Feld "Beschriftung" (auch in dynamischen Designs) eine Auflistung bzw. Tabelle.

<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Zeilentrenner: legt fest, welche Zeichenkette einzelne Zeilen voneinander abtrennen soll
		<ul>
			<li>wird dieses Feld [leer] belassen, wird als Zeilentrenner ein "Zeilenumbruch" erwartet</li>
		</ul>
	</li>

	<li>
		Spaltentrenner: legt ggf. fest, welche Zeichenkette einzelne Spalten voneinander abtrennen soll
		<ul>
			<li>wird dieses Feld [leer] belassen, werden keine Spalten erzeugt (es wird also eine Auflistung anstelle einer Tabelle angezeigt)</li>
		</ul>
	</li>

	<li>
		Hervorhebung: legt ggf. eine Zeichenkette fest, die zur Hervorhebung der gesamte Zeile führt falls diese Zeichenkette in der 1. Spalte enthalten ist 
		<ul>
			<li>falls die Zeichenkette im Inhalt der 1. Spalte (bzw. der Zeile, falls keine Spalten erzeugt werden) vorhanden ist, wird die gesamte Zeile mit der <link>Zusatzhintergrundfarbe 1***1003</link> hinterlegt und die Zeichenkette aus dem Inhalt der 1. Spalte entfernt</li>
			<li>wird dieses Feld [leer] belassen, erfolgt keine Hervorhebung</li>
			<li>Hinweis: Für die Titelzeile (s.u.) wird keine Hervorhebung generiert, die Zeichenkette wird wie jeder gewöhnliche Inhalt angezeigt.</li>
		</ul>
	</li>

	<li>
		Sortierung: ermöglicht das Sortieren der Listen-/Tabellendaten (Stringvergleich)
		<ul>
			<li>ohne: es erfolgt keine Sortierung, d.h. die Daten werden in der angegebenen Reihenfolge angezeigt</li>
			<li>aufsteigend/absteigend (ganze Zeile): die Daten werden aufsteigend bzw. absteigend sortiert, als Sortierkriterium dient die gesamte Zeile</li>
			<li>aufsteigend/absteigend (Spalte 1): die Daten werden aufsteigend bzw. absteigend sortiert, als Sortierkriterium dient nur die erste Spalte</li>
			<li>aufsteigend/absteigend (Spalte 1, ausblenden): wie zuvor, jedoch wird die erste Spalte (Sortierkriterium) nicht angezeigt</li>
			<li>Hinweis: Die erste Zeile (Titel) wird ggf. von der Sortierung ausgenommen (s.u.).</li>
			<li>Wichtig: Die Sortierung erfolgt stets per Stringvergleich! Reine Zahlenwerte werden daher u.U. anders als erwartet sortiert.</li>
		</ul>
	</li>

	<li>
		Titelzeile: legt fest, ob die erste (unsortierte) Zeile der Daten als Titelzeile (Legende) interpretiert werden soll
		<ul>
			<li>keine Titelzeile: es wird keine Titelzeile generiert</li>
			<li>1. Zeile ist Titelzeile: die vollstände 1. Zeile (ggf. einschließlich Spalten) wird als Titelzeile angezeigt und von einer Sortierung ausgeschlossen</li>
			<li>1. Zeile ist Titelzeile (Zeilenhöhe minimieren): wie zuvor, jedoch wird die Zeilenhöhe für die Titelzeile unabhängig von den o.g. Einstellungen so gering wie möglich eingestellt</li>
			<li>
				Hinweis: Die Titelzeile kann (optional) ausserdem zur Definition der jeweiligen Spaltenbreite (Tabellen) verwendet werden:
				<ul>
					<li>die Breite jeder einzelnen Spalte kann als Prozentwert wie folgt definiert werden (als Spaltentrenner wird in diesem Beispiel das Komma verwendet):</li>
					<li>"Titel1***10,Titel2***20,Titel3***70" führt zu Spaltenbreiten von 10%, 20% und 70%</li>
				</ul>
			</li>
			<li>Hinweis: Die Titelzeile wird ggf. in der <link>Zusatzvordergrundfarbe 2***1003</link> angezeigt und mit der <link>Zusatzhintergrundfarbe 2***1003</link> hinterlegt.</li>
			
			<li>
				Trennlinien: legt fest, ob die Titelzeile bzw. die einzelen Spalten mit einer Linie visuell abgetrennt werden sollen
				<ul>
					<li>die Trennlinien der Titelzeile werden ggf. in der <link>Zusatzvordergrundfarbe 1***1003</link> angezeigt</li>
				</ul>
			</li>
		</ul>
	</li>

	<li>
		Zeilenhöhe: definiert die Zeilenhöhe jeder einzelnen Zeile
		<ul>
			<li>0 = optimale Zeilenhöhe (d.h. es wird die max. Höhe des Visuelements ausgenutzt, ggf. wird gescrollt)</li>
			<li>1..oo = feste Zeilenhöhe in Pixel (unabhängig von dieser Angabe wird die Zeilenhöhe mindestens der Schriftgröße entsprechen)</li>
		</ul>
	</li>

	<li>
		Spaltenbreite: legt ggf. die Breite der einzelnen Spalten fest
		<ul>
			<li>gleichmäßig/individuell: sämtliche Spalten habe die gleiche Breite bzw. können durch die Formatierungsangaben in der Titelzeile festgelegt werden (s.o.)</li>
			<li>automatisch: die Breite jeder einzelnen Spalte wird abhängig vom Inhalt dynamisch angepaßt (Formatierungsangaben in der Titelzeile werden ignoriert)</li>
		</ul>
	</li>

	<li>Innenabstand: definiert den Innenabstand jeder einzelnen Zeile bzw. Zelle in Pixeln</li>

	<li>
		Trennlinien: legt fest, ob Spalten und/oder Zeilen mit einer Linie visuell abgetrennt werden sollen
		<ul>
			<li>die Trennlinien werden stets in der <link>Zusatzvordergrundfarbe 1***1003</link> angezeigt</li>
		</ul>
	</li>

	<li>Blättern-Schaltflächen: legt fest, ob ggf. Schaltflächen zum Blättern (Scrollen) angezeigt werden</li>

	<li>Kopfzeilenhöhe: legt optional die Höhe der Kopfzeile in Pixeln fest (Blättern-Schaltflächen)</li>

	<li>
		Klick-Verhalten: legt ggf. die Reaktion beim Anklicken des Inhaltes der Liste/Tabelle fest
		<ul>
			<li>beim Anklicken des Inhaltes wird KO2 ggf. auf den Index oder den Inhalt der angeklickten Zeile bzw. Zelle gesetzt:
				<ul>
					<li>KO2 wird auf den Index oder den Inhalt der angeklickten Zeile bzw. Zelle gesetzt (die Titelzeile ist nicht anklickbar)</li>
					<li>der Index liegt stets im Bereich "0..&infin;" (einschließlich der Titelzeile) bzw. bei mehrspaltigen Inhalten im Bereich "0..&infin;;0..&infin;"</li>
					<li>wird die 1. Spalte ausgeblendet (Sortieroption) beginnt der Spaltenindex bei 1 (anstelle von 0)</li>
					<li>Hinweis: In der Datenbasis nicht definierte Zeilen bzw. Zellen sind nicht anklickbar.</li>
					<li>Wichtig: Ggf. zugewiesene Seitensteuerungen/Befehle werden bei aktivierter Klick-Reaktion ignoriert!</li>
				</ul>
			</li>
			
			<li>
				Reaktion auf Klick: 
				<ul>
					<li>deaktiviert: das Anklicken des Inhaltes wird ignoriert, ggf. werden zugewiesene Seitensteuerung/Befehle ausgeführt</li>
					<li>gesamte Zeile: bei einer Tabelle erfolgt die Reaktion auf einen Klick zeilenweise</li>
					<li>einzelne Zelle: bei einer Tabelle erfolgt die Reaktion auf einen Klick individuell für jede einzelne Zelle</li>
				</ul>
			</li>
			
			<li>
				KO2-Wert: 
				<ul>
					<li>Index (datenbasiert): KO2 wird auf den Index der korrespondierenden (unsortierten) Daten gesetzt</li>
					<li>Index (wie angezeigt): KO2 wird auf den Indes der angezeigten (ggf. sortierten) Daten gesetzt</li>
					<li>Inhalt (gesamt): KO2 wird auf den gesamten Inhalt der Zeile bzw. Zelle gesetzt (Zelleninhalte werden ggf. mit dem Spaltentrenner (s.o.) zusammengefasst)</li>
					<li>Inhalt (ohne Spalte 1): KO2 wird auf den gesamten Inhalt der Zeile bzw. Zelle mit Ausnahme von der 1. Spalte gesetzt (Zelleninhalte werden ggf. mit dem Spaltentrenner (s.o.) zusammengefasst)</li>
					<li>Inhalt (nur Spalte 1): KO2 wird nur auf den Inhalt der 1. Spalte gesetzt</li>
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
			<li>dieser KO-Wert wird ggf. als Datengrundlage verwendet</li>
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
	<li>
		Die Auflistung bzw. Tabelle wird aus den Angaben im Feld "Beschriftung" (auch in dynamischen Designs) generiert, z.B.:
		<ul>
			<li>"A|B|C|1|2|3": Auflistung (Zeilentrenner ist hier "|")</li>
			<li>"A,B,C|1,2,3|XX,,YY": Tabelle (Zeilentrenner ist hier "|", Spaltentrenner ist hier ",")</li>
			<li>"{#}": Auflistung/Tabelle auf Grundlage des KO-Wertes</li>
			<li>prinzipiell sind auch HTML/CSS-Angaben möglich, z.B. "1|<u>2:Wichtig!</u>|3" (Unterstreichen)</li>
		</ul>
	</li>

	<li>das Feld "Beschriftung" stellt die anzuzeigenden Daten bereit</li>
	<li>Designs: Innenabstand wird ignoriert</li>
	<li>Hinweis: wenn keine Seitensteuerungen/Befehle zugewiesen wurden, verhält sich dieses Visuelement dennoch nicht <link>klicktransparent***1002</link></li>
</ul>


<h2>Bedienung in der Visualisierung</h2>
Mit den Pfeil-Schaltflächen kann ggf. durch die Auflistung/Tabelle geblättert werden (Scrollen).
Mit einem Klick auf die Auflistung bzw. Tabelle werden ggf. alle zugewiesenen Seitensteuerungen/Befehle ausgeführt, oder KO2 wird auf einen Wert gesetzt (sofern die entsprechende Option aktiviert ist).
###[/HELP]###


