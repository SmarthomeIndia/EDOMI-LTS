###[DEF]###
[name		=Wert-/Zeitdifferenz		]

[e#1 TRIGGER=Trigger/Stop #init=0		]
[e#2		=Messwert				]

[a#1		=&Delta;Wert					]
[a#2		=&Delta;Zeit					]

[v#1		=						]
[v#2		=						]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet die Differenz von nacheinander eintreffenden Werten, z.B. um die Differenz eines Zählerstandes in Abhängigkeit eines Ereignisses zu ermitteln.

Ein Telegramm &ne;0 an E1 startet die Messung, bzw. startet die Messung neu:
A1 und A2 werden auf 0 gesetzt. Der Wert an E2 wird als Referenzwert intern gespeichert (inklusive Zeitstempel).
Jedes eintreffende Telegramm an E2 während einer Messung (E1&ne;0) führt zu einer Aktualisierung von A1 (Wertdifferenz) und A2 (Zeitdifferenz).

Ein Telegramm =0 an E1 beendet die Messung:
A1 bleibt unverändert, da eine Wertänderung an E2 während der Messung A1 bereits entsprechend gesetzt hat.
A2 wird auf die gesamte Laufzeit der Messung gesetzt.
A1 und A2 verhalten sich also wie SBC-Ausgänge (Send-By-Change).

Hinweise:
Telegramme =0 an E1 werden ignoriert, wenn zuvor keine Messung mit einem Telegramm &ne;0 an E1 gestartet wurde.
Wird E2 nicht belegt bzw. nicht verändert, verhält sich der Baustein wie eine einfache Stoppuhr (A2 wird auf die Zeitdifferenz zwischen E1&ne;0 und E1=0 gesetzt).
 
Achtung:
Eine Aktualisierung von A1 und A2 erfolgt nur beim Starten und Stoppen einer Messung und bei eintreffenden Telegrammen an E2 während einer Messung. Es erfolgt <i>keine</i> zyklische Änderung von A1 und A2!
 
 
E1: Starten (&ne;0) bzw. Stoppen (=0) einer Messung
E2: Messwert (nummerisch), dessen Differenz berechnet werden soll (z.B. ein Zählerstand)
A1: Messwert-Differenz (nummerisch): wird beim Start auf 0 gesetzt, dann bei jedem eintreffenden Telegramm an E2 auf die Wertdifferenz, beim Beenden der Messung erfolgt keine Änderung
A2: Zeitdifferenz (Sekunden, FLOAT): wird beim Start auf 0 gesetzt, dann bei jedem eintreffenden Telegramm an E2 auf die Zeitdifferenz, beim Beenden der Messung auf die gesamte Zeitdifferenz der Messung
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		//Differenzen ausgeben
		if (!isEmpty($V[2]) && $E[2]['refresh']==1) {
			logic_setOutput($id,1,$E[2]['value']-$V[1]);
			logic_setOutput($id,2,getMicrotime()-$V[2]);
		}

		if ($E[1]['refresh']==1) {
			if ($E[1]['value']==0) {
				//Stop
				if (!isEmpty($V[2])) {
					logic_setVar($id,2,null);
					logic_setOutput($id,2,getMicrotime()-$V[2]);
				} else {
					//### ignorieren bzw. resetten
				}

			} else {
				//Start
				logic_setVar($id,1,$E[2]['value']);
				logic_setVar($id,2,getMicrotime());
				logic_setOutput($id,1,0);
				logic_setOutput($id,2,0);
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
