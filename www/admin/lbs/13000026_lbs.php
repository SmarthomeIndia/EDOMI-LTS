###[DEF]###
[name		=Schaltfolge 1..10-fach	]
[titel		=Schaltfolge			]

[e#1 trigger=Trigger 			]
[e#2 	=Ausgänge 1..10 #init=1  ]
[e#3 	=Wert Aus #init=0		]
[e#4 	=Wert Ein #init=1		]
[e#5		=Reset &ne;0			]

[a#1		=		]
[a#2		=		]
[a#3		=		]
[a#4		=		]
[a#5		=		]
[a#6		=		]
[a#7		=		]
[a#8		=		]
[a#9		=		]
[a#10		=		]

[v#1		=0		]
###[/DEF]###


###[HELP]###
Bei jedem Triggern des Bausteins wird ein Ausgang auf den Wert an E4 und der vorherige Ausgang auf den Wert an E3 gesetzt.

Jedes neue Telegramm &ne;0 an E1 triggert den Baustein. An E2 muss die Anzahl der gewünschten Ausgänge im Bereich von 1..10 angegeben werden.

Beispiel:
Die Anzahl der Ausgänge beträgt 3 (E2=3). E3 ist 0, E4 ist 1.
Bei ersten Triggern des Bausteins (E1) wird A1=1 gesetzt. Bei nächsten Triggern wird A1=0 und A2=1 gesetzt. Beim nächsten Triggern wird A2=0 und A3=1 gesetzt.
Erfolgt nun ein weiteres Triggern, wird A3=0 und A1=1 gesetzt - der Zyklus beginnt also von vorn.
Die Schaltfolge ist demnach:
1. Trigger: A1=1
2. Trigger: A2=1, A1=0
3. Trigger: A3=1, A2=0
4. Trigger: A1=1, A3=0 (der Zyklus beginnt von vorn)
...

Sonderfall: Die Anzahl der Ausgänge beträgt 1 (E2=1). E3 ist 0, E4 ist 1.
In diesem Fall wird A1 "getoggelt", d.h. A1 wechselt bei jedem Triggern des Bausteins zwischen 1 und 0:
1. Trigger: A1=1
2. Trigger: A1=0
3. Trigger: A1=1
...

Ein Telegramm &ne;0 an E5 setzt den Baustein zurück: Der zuletzt auf "Ein" (E4) gesetzte Ausgang wird auf "Aus" (E3) gesetzt und der Zyklus beginnt beim nächsten Triggern (E1) mit A1.

Wenn die Anzahl der Ausgänge (E2) mit einen ungültigen Wert definiert ist, ignoriert der Baustein jeden Trigger und belässt alle Ausgänge unverändert.

E1: Trigger (jedes neue Telegramm &ne;0 triggert den Baustein)
E2: Anzahl der Ausgänge (1..10)
E3: Wert für "Aus": Der vorherige Ausgang wird auf diesen Wert gesetzt. Ist E3=[leer] wird der Ausgang nicht verändert.
E4: Wert für "Ein": Der aktuelle Ausgang wird auf diesen Wert gesetzt. Ist E4=[leer] wird der Ausgang nicht verändert.
E5: Reset (jedes neue Telegramm &ne;0 setzt den Baustein zurück)
A1..A10: Ausgänge (wechseln zwischen Wert an E3 und Wert an E4)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if ($E[1]['value']!=0 && $E[1]['refresh']==1 && $E[2]['value']>=1 && $E[2]['value']<=10) {
			$V1=logic_getVar($id,1);

			if ($E[2]['value']==1) {
				//Sonderfall: Nur A1 toggeln
				if ($V1==0) {
					$V1=1;
					if (!isEmpty($E[4]['value'])) {logic_setOutput($id,1,$E[4]['value']);}
				} else {
					$V1=0;
					if (!isEmpty($E[3]['value'])) {logic_setOutput($id,1,$E[3]['value']);}
				}
			} else {
				if ($V1>0 && !isEmpty($E[3]['value'])) {logic_setOutput($id,$V1,$E[3]['value']);}
				if ($V1<$E[2]['value']) {$V1++;} else {$V1=1;}
				if (!isEmpty($E[4]['value'])) {logic_setOutput($id,$V1,$E[4]['value']);}
			}

			logic_setVar($id,1,$V1);
		}

		//Reset
		if ($E[5]['value']!=0 && $E[5]['refresh']==1) {
			$V1=logic_getVar($id,1);
			if ($V1>0 && !isEmpty($E[3]['value'])) {logic_setOutput($id,$V1,$E[3]['value']);}
			logic_setVar($id,1,0);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###
