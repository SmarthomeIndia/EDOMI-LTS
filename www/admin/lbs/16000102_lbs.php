###[DEF]###
[name		=Countdown-Timer		]

[e#1 TRIGGER=Trigger/Stop 			]
[e#2		=Dauer (s) #init=10		]

[a#1		=0|1					]
[a#2		=Status					]
[a#3		=Restzeit (s)			]
[a#4		=Restzeit (%)			]

[v#1		=						]
[v#2		=						]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet einen Countdown-Timer (z.B. Küchenwecker bzw. Eieruhr) mit Restzeitanzeige nach.

Ein Telegramm &ne;0 an E1 startet den Baustein, bzw. startet den Baustein neu:
A1 wird auf 0 gesetzt, während A2 (Status) auf 1 gesetzt wird.
Der Timer beginnt abzulaufen und kann jederzeit durch ein Telegramm =0 an E1 gestoppt werden (ein Retriggern ist jedoch nicht möglich).
An Ausgang A3 wird sekündlich die verbleibende Restzeit in Sekunden, an A4 in Prozent ausgegeben.
Läuft der Timer vollständig ab, werden A3 bzw. A4 (Restzeit) auf 0 gesetzt, A1 wird auf 1 gesetzt und A2 auf 0.
Wird der Timer jedoch vor Ablauf gestoppt (E1=0), werden die Ausgänge A2..A4 auf 0 gesetzt.

Hinweis:
Das Setzen von E2 beeinflusst einen bereits laufenden Timer nicht. Jedoch führt jedes Telegramm an E1 oder E2 zur Aktualsierung der Ausgänge A3 und A4.

E1: &ne;0 = Trigger (Start), 0 = Stopp (ein bereits laufender Timer wird gestoppt) 
E2: Zeit in Sekunden
A1: beim Start des Timers wird A1 auf 0 gesetzt. Nach Ablauf der Zeit (E2) wird A1 auf 1 gesetzt (beim Stoppen des Timers vor Ablauf der Zeit bleibt A1 unverändert).
A2: beim Start des Timers wird A2 auf 1 gesetzt (Status: Timer läuft). Nach Ablauf der Zeit (E2) oder beim Stoppen des Timers wird A2 auf 0 gesetzt.
A3: Hier wird sekündlich die verbleibende Restzeit in Sekunden ausgegeben. Nach Ablauf der Zeit oder beim Stoppen des Timers wird A3 auf 0 gesetzt.
A4: wie A3, die Restzeit wird jedoch in Prozent ausgegeben (ganzzahlig)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		$tmp=getMicrotime();

		if (logic_getState($id)==0) {
			if ($E[1]['value']!=0 && $E[1]['refresh']==1 && $E[2]['value']>0) {
				logic_setVar($id,1,($tmp+$E[2]['value']));
				logic_setVar($id,2,$E[2]['value']);
				logic_setOutput($id,1,0);
				logic_setOutput($id,2,1);
				logic_setOutput($id,3,$E[2]['value']);
				logic_setOutput($id,4,100);
				logic_setState($id,1,1000,true);	//1-Sekunden-Takt als kleinsten gemeinsamen Nenner (Timer und Restzeit)
			}

		} else {
			if ($E[1]['value']==0 && $E[1]['refresh']==1) {
				logic_setOutput($id,2,0);
				logic_setOutput($id,3,0);
				logic_setOutput($id,4,0);
				logic_setState($id,0);
			} else if ($tmp>=$V[1]) {
				logic_setOutput($id,1,1);
				logic_setOutput($id,2,0);
				logic_setOutput($id,3,0);
				logic_setOutput($id,4,0);
				logic_setState($id,0);
			} else {
				logic_setOutput($id,3,round($V[1]-$tmp));
				logic_setOutput($id,4,round(($V[1]-$tmp)/$V[2]*100));
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
