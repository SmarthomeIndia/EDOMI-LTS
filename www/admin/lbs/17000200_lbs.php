###[DEF]###
[name		=Tastsensor-Auswerter	]

[e#1 TRIGGER=Trigger 				]
[e#2		=Aktiviert 	 #init=1	]
[e#3		=Freigaben 	 			]

[a#1		=Taster 1: kurz			]
[a#2		=Taster 2: kurz			]
[a#3		=Taster 3: kurz			]
[a#4		=Taster 4: kurz			]
[a#5		=Taster 5: kurz			]
[a#6		=Taster 6: kurz			]
[a#7		=Taster 7: kurz			]
[a#8		=Taster 8: kurz			]

[a#9		=Taster 1: lang			]
[a#10		=Taster 2: lang			]
[a#11		=Taster 3: lang			]
[a#12		=Taster 4: lang			]
[a#13		=Taster 5: lang			]
[a#14		=Taster 6: lang			]
[a#15		=Taster 7: lang			]
[a#16		=Taster 8: lang			]
###[/DEF]###


###[HELP]###
Auswertbaustein für 8-fach Taster mit kurz/lang-Unterscheidung.

Die Taster müssen wie folgt programmiert werden:
Taster sendet (Byte-Wert) 1/2/3/..8 = kurzer Druck auf Taste 1..8
Taster sendet (Byte-Wert) 11/12/13/..18 = langer Druck auf Taste 1..8

A1..8 werden bei einem kurzen Tastendruck auf 1 gesetzt. 
A9..16 werden bei einem langen Tastendruck auf 1 gesetzt.

Die Ausgänge werden niemals(!) auf 0 zurückgesetzt!

Ist E2=1, ist der Baustein aktiviert. Ist E2=0, ist der Baustein deaktiviert.
An E3 kann eine Liste mit Freigaben angegeben werden (für diese Tasterfunktionen bleibt eine Deaktivierung (E2=0) wirkungslos). Erwartet wird eine Komma-separierte Liste der gewünschten Tasterwerte (E1) - z.B. führt "4,2,18" dazu, dass die Taster 4 (kurz), 2 (kurz) und 8 (lang) stets freigegeben sind (unabhängig von E2).


E1: Trigger (1..8=kurz, 11..18=lang)
E2: Aktiviert 0/1: 1=Baustein ist aktiviert, 0=Baustein ist deaktiviert
E3: Freigaben: Komma-separierte Liste mit Freigaben der gewünschten Tasterwerte trotz Deaktivierung (s.o.)
A1..A8: 1 bei kurzem Tastendruck
A9..A16: 1 bei langem Tastendruck
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
	
		$tmp=explode(',',$E[3]['value']);
		
		if ($E[1]['refresh']==1) { //NEUER Tastendruck?
			if ($E[1]['value']==1 && ($E[2]['value']==1 || array_search('1',$tmp)!==false)) {logic_setOutput($id,1,1);}
			if ($E[1]['value']==2 && ($E[2]['value']==1 || array_search('2',$tmp)!==false)) {logic_setOutput($id,2,1);}
			if ($E[1]['value']==3 && ($E[2]['value']==1 || array_search('3',$tmp)!==false)) {logic_setOutput($id,3,1);}
			if ($E[1]['value']==4 && ($E[2]['value']==1 || array_search('4',$tmp)!==false)) {logic_setOutput($id,4,1);}
			if ($E[1]['value']==5 && ($E[2]['value']==1 || array_search('5',$tmp)!==false)) {logic_setOutput($id,5,1);}
			if ($E[1]['value']==6 && ($E[2]['value']==1 || array_search('6',$tmp)!==false)) {logic_setOutput($id,6,1);}
			if ($E[1]['value']==7 && ($E[2]['value']==1 || array_search('7',$tmp)!==false)) {logic_setOutput($id,7,1);}
			if ($E[1]['value']==8 && ($E[2]['value']==1 || array_search('8',$tmp)!==false)) {logic_setOutput($id,8,1);}
			if ($E[1]['value']==11 && ($E[2]['value']==1 || array_search('11',$tmp)!==false)) {logic_setOutput($id,9,1);}
			if ($E[1]['value']==12 && ($E[2]['value']==1 || array_search('12',$tmp)!==false)) {logic_setOutput($id,10,1);}
			if ($E[1]['value']==13 && ($E[2]['value']==1 || array_search('13',$tmp)!==false)) {logic_setOutput($id,11,1);}
			if ($E[1]['value']==14 && ($E[2]['value']==1 || array_search('14',$tmp)!==false)) {logic_setOutput($id,12,1);}
			if ($E[1]['value']==15 && ($E[2]['value']==1 || array_search('15',$tmp)!==false)) {logic_setOutput($id,13,1);}
			if ($E[1]['value']==16 && ($E[2]['value']==1 || array_search('16',$tmp)!==false)) {logic_setOutput($id,14,1);}
			if ($E[1]['value']==17 && ($E[2]['value']==1 || array_search('17',$tmp)!==false)) {logic_setOutput($id,15,1);}
			if ($E[1]['value']==18 && ($E[2]['value']==1 || array_search('18',$tmp)!==false)) {logic_setOutput($id,16,1);}
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
