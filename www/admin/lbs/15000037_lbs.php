###[DEF]###
[name		=Vergleicher =&#91;Liste&#93;	]
[titel		==&#91;Liste&#93;?				]

[e#1 TRIGGER= 						]
[e#2 		=Wertliste 1			]
[e#3 		=Wertliste 0			]

[a#1		=1/0					]
[a#2		=1						]
[a#3		=0						]
###[/DEF]###


###[HELP]###
Dieser Baustein vergleicht einen Wert an E1 mit einer Auflistung von Werten an E2 und E3. Jedes neue Telegramm an E1 triggert den Baustein.

Wenn der Wert an E1 einem der Werte an E2 entspricht, wird A1=1 gesetzt.
Wenn der Wert an E1 einem der Werte an E3 entspricht, wird A1=0 gesetzt.
Entspricht der Wert an E1 keinem der Werte an E2 und E3, wird A1 nicht verändert.

<b>Hinweise:</b>
Der Baustein wird nur durch E1 getriggert, d.h. eine Änderung von E2 bzw. E3 bewirkt keinen erneuten Vergleich.
Der Vergleich erfolgt auf Stringbasis, auch Groß- und Kleinschreibung werden berücksichtigt.
Die Wertliste an E2 wird stets zuerst zum Vergleich herangezogen. Sollte der gleiche Wert an E2 und E3 vorhanden sein, hat E2 stets Priorität.


E1: Vergleichswert (Trigger)
E2: Wertliste für A1=1 (separiert durch ein Semikolon, z.B. "1;2;3")
E3: Wertliste für A1=0 (separiert durch ein Semikolon, z.B. "4;5")

A1: s.o.
A2: Filter 1: wie A1, jedoch wird A2 ausschließlich auf 1 gesetzt (wenn A1=1 gesetzt wird)
A3: Filter 0: wie A1, jedoch wird A3 ausschließlich auf 0 gesetzt (wenn A1=0 gesetzt wird)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			
			$tmp=explode(';',$E[2]['value']);
			if (in_array($E[1]['value'],$tmp,true)) {
				logic_setOutput($id,1,1);
				logic_setOutput($id,2,1);
			} else {
				$tmp=explode(';',$E[3]['value']);
				if (in_array($E[1]['value'],$tmp,true)) {
					logic_setOutput($id,1,0);
					logic_setOutput($id,3,0);
				}
			}
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
