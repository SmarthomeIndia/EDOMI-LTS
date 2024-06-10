###[DEF]###
[name		=Restzeit-Timer			]

[e#1 TRIGGER=Trigger 				]
[e#2		=Dauer (s) #init=10		]

[a#1		=0|1					]
[a#2		=Restzeit (s)			]
[a#3		=Restzeit (%)			]

[v#1		=						]
[v#2		=						]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet einen (retriggerbaren) Timer mit Restzeitanzeige nach.

Ein Telegramm &ne;0 an E1 startet den Baustein, bzw. startet den Baustein neu:
Der Timer beginnt abzulaufen und wird durch jedes weitere Telegramm &ne;0 an E1 neu gestartet.
Ausgang A1 wird bei JEDEM Telegramm &ne;0 an E1 auf 1 gesetzt.
An Ausgang A2 wird sekündlich die verbleibende Restzeit in Sekunden, an A3 in Prozent ausgegeben.
Läuft der Timer vollständig ab, werden A2 bzw. A3 (Restzeit) auf 0 gesetzt. A1 wird ebenfalls auf 0 gesetzt.

Anwendungsbeispiel:
BWM-Signal verlängern und Restzeit visualisieren:
An A2 oder A3 (Restzeit) muss ein KO gesetzt werden - dieses KO kann dann z.B. in der Visu angezeigt werden oder ein dynamisches Design abrufen.

Hinweis:
Das Setzen von E2 beeinflusst einen bereits laufenden Timer nicht. Jedoch führt jedes Telegramm an E1 oder E2 zur Aktualsierung der Ausgänge A2 und A3.

E1: &ne;0 = Trigger. Sobald ein neues Telegramm &ne;0 eintrifft, wird der Timer wieder neu gestartet (retriggern).
E2: Zeit in Sekunden
A1: Bei jedem(!) Telegramm &ne;0 an E1 wird A1 auf 1 gesetzt. Nach Ablauf der Zeit wird A1 auf 0 gesetzt.
A2: Hier wird sekündlich die verbleibende Restzeit in Sekunden ausgegeben. Nach Ablauf der Zeit wird A2 auf 0 gesetzt.
A3: wie A2, die Restzeit wird jedoch in Prozent ausgegeben (ganzzahlig)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		$tmp=getMicrotime();

		if (logic_getState($id)==0) {
			if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
				logic_setVar($id,1,($tmp+$E[2]['value']));
				logic_setVar($id,2,$E[2]['value']);
				logic_setOutput($id,1,1);
				logic_setOutput($id,2,$E[2]['value']);
				logic_setOutput($id,3,100);
				logic_setState($id,1,1000,true);		//1-Sekunden-Takt als kleinsten gemeinsamen Nenner (Timer und Restzeit)
			}

		} else {
			//Retriggern
			if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
				logic_setVar($id,1,($tmp+$E[2]['value']));
				logic_setVar($id,2,$E[2]['value']);
				logic_setOutput($id,1,1);
				logic_setOutput($id,2,$E[2]['value']);
				logic_setOutput($id,3,100);

			} else {
				if ($tmp>=$V[1]) {
					logic_setOutput($id,1,0);
					logic_setOutput($id,2,0);
					logic_setOutput($id,3,0);
					logic_setState($id,0);
				} else {
					logic_setOutput($id,2,round($V[1]-$tmp));
					logic_setOutput($id,3,round(($V[1]-$tmp)/$V[2]*100));
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
