###[DEF]###
[name		=Minimum/Maximum		]

[e#1 TRIGGER=Trigger ]
[e#2		=Reset 			]

[a#1		=Min					]
[a#2		=Max					]
[a#3		=Min (Reset)			]
[a#4		=Max (Reset)			]

[v#1		=						]	Min
[v#2		=						]	Max
###[/DEF]###


###[HELP]###
Dieser Baustein ermittelt den Minimum- und Maximumwert von Telegrammen an E1.

Jedes Telegramm &ne;[leer] an E1 triggert den Baustein und führt zu einem Vergleich mit dem Vorgängerwert:
Ist der aktuelle Wert kleiner als der Vorgängerwert, wird A1 auf den aktuellen Wert gesetzt.
Ist der aktuelle Wert größer als der Vorgängerwert, wird A2 auf den aktuellen Wert gesetzt.

Ist noch kein Vorgängerwert vorhanden (z.B. beim ersten Triggern des Bausteins), werden A1 und A2 auf den aktuellen Wert an E1 gesetzt.

Mit einem Telegramm &ne;0 an E2 wird der Baustein zurückgesetzt: A1 und A2 werden auf den aktuellen Wert an E1 gesetzt, die internen Vergleichswerte ebenso. Beim Zurücksetzen des Bausteins werden die aktuellen Minimum-/Maximum-Werte an A3 bzw. A4 ausgegeben, erst anschließend wird der Baustein zurückgesetzt. A3 bzw. A4 können z.B. verwendet werden, um die ermittelten Minimum-/Maximum-Werte in einem Datenarchiv zu archivieren.

<h3>Anwendungsbeispiel</h3>
Ermittlung der täglichen Min-/Max-Temperatur: E1 wird das Temperatur-KO zugewiesen, der Baustein wird mit Hilfe des System-KOs[20] an E2 täglich um Mitternacht zurückgesetzt. An A1 bzw. A2 liegen dann bei jeder Temperaturänderung die Min-/Max-Werte an, um Mitternacht werden A1 und A2 auf die aktuelle Temperatur gesetzt (Reset). Die Minimum-/Maximum-Werte des Vortags(!) werden bei einem Reset an A3 bzw. A4 ausgegeben und können z.B. in einem Datenarchiv archiviert werden.


<h2>Ein- und Ausgänge</h2>
E1: Jedes neue Telegramm &ne;[leer] triggert den Baustein und führt ggf. zu einer Ermittlung des Minimum/Maximum-Werts.
E2: Ein Telegramm &ne;0 setzt den Baustein zurück, d.h. A1 und A2 (und die internen Vergleichswerte) werden auf den aktuellen Wert an E1 gesetzt.

A1: Minimum-Wert (wird nur bei Änderung gesetzt)
A2: Maximum-Wert (wird nur bei Änderung gesetzt)
A3: Minimum-Wert zum Zeitpunkt eines Resets per E2
A4: Maximum-Wert zum Zeitpunkt eines Resets per E2
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[2]['value']!=0 && $E[2]['refresh']==1) {					//Reset
			logic_setOutput($id,3,$V[1]);
			logic_setOutput($id,4,$V[2]);
			$V[1]=$E[1]['value'];
			$V[2]=$E[1]['value'];
			logic_setVar($id,1,$V[1]);
			logic_setVar($id,2,$V[2]);
			logic_setOutput($id,1,$V[1]);
			logic_setOutput($id,2,$V[2]);

		} else if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {	//Min/Max
			if (isEmpty($V[1]) || $E[1]['value']<$V[1]) {
				$V[1]=$E[1]['value'];
				logic_setVar($id,1,$V[1]);
				logic_setOutput($id,1,$V[1]);
			}
			if (isEmpty($V[2]) || $E[1]['value']>$V[2]) {
				$V[2]=$E[1]['value'];
				logic_setVar($id,2,$V[2]);
				logic_setOutput($id,2,$V[2]);
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
