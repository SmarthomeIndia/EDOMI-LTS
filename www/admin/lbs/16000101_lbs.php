###[DEF]###
[name		=Timer (stoppbar)		]

[e#1 TRIGGER=Trigger/Stop #init=0		]
[e#2		=Dauer (s) #init=10		]
[e#3		=retriggerbar #init=0	]
[e#4		=Aktiviert #init=1		]

[a#1		=0|1					]
[a#2		=Timeout				]

[v#1		=10						]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet einen retriggerbaren Timer nach. Jedes neue Telegramm &ne;0 an E1 startet den Timer, bzw. startet ggf. den Timer neu (retriggern). 

Wird der Timer gestartet, wird A1=1 gesetzt und nach Ablauf der Zeit an E2 wird A1=0 und A2=1 gesetzt. Ist E3=1, wird der Timer mit jedem neuen Telegramm &ne;0 an E1 neu gestartet - unabhängig davon, ob der Timer bereits "läuft" oder nicht.

Trifft während der Laufzeit ein Telegramm =0 an E1 oder E4 ein, wird der Timer abgebrochen und A1 sowie A2 werden auf 0 gesetzt. 

Mit E4 kann der Baustein aktiviert oder deaktiviert werden.

E1: &ne;0 = Triggern (Timer starten), 0 = Timer stoppen (Abbruch) und A1=0 setzen
E2: Zeit in Sekunden, bis der Timer abgelaufen ist
E3: 1=Timer ist retriggerbar, 0=Timer ist nicht retriggerbar
E4: 1=Timer ist aktiviert, 0=Timer ist deaktiviert (wird E4 während des "laufenden" Timers auf 0 gesetzt, wird abgebrochen und A1=0 gesetzt)
A1: 1=Timer gestartet, 0=Timer abgelaufen oder abgebrochen (beim Retriggern wird A1 nicht(!) erneut auf 1 gesetzt)
A2: 1=Timer ist erfolgreich abgelaufen, 0=Timer wurde zur Laufzeit gestoppt oder deaktiviert (beim Start des Timers wird A2 nicht verändert)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if (logic_getState($id)==0) {

			if ($E[4]['value']==1) { //Aktiviert?
				if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
					logic_setVar($id,1,(getMicrotime()+$E[2]['value']));
					logic_setOutput($id,1,1);
					logic_setState($id,1,$E[2]['value']*1000); 
				}
			}

		} else {

			//Retriggern
			if ($E[4]['value']==1 && $E[3]['value']==1) { //Aktiviert? Retriggerbar?
				if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
					logic_setVar($id,1,(getMicrotime()+$E[2]['value']));
					logic_setState($id,1,$E[2]['value']*1000); 
				}
			}

			if ($E[1]['value']==0 || $E[4]['value']==0 || getMicrotime()>=logic_getVar($id,1)) { //inwzischen deaktiviert, E1=0 oder Zeit abgelaufen?
				logic_setOutput($id,1,0);
				if ($E[1]['value']==0 || $E[4]['value']==0) {
					logic_setOutput($id,2,0);
				} else {
					logic_setOutput($id,2,1);
				}
				logic_setState($id,0);
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
