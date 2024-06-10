###[DEF]###
[name		=Watchdog					]

[e#1 TRIGGER	=Trigger 				]
[e#2			=Dauer (s) #init=10		]
[e#3 			=Aktiviert #init=1		]

[a#1		=							]

[v#1		=10							]
###[/DEF]###


###[HELP]###
Dieser Baustein überwacht z.B. ein zyklisch eintreffendes Telegramm an E1. 

Ein Telegramm &ne;0 an E1 startet den Baustein, bzw. startet den Baustein neu:
Ein Timer beginnt abzulaufen und wird durch jedes weitere Telegramm &ne;0 an E1 neu gestartet. Der Ausgang A1 wird beim ersten(!) eintreffenden Telegramm &ne;0 an E1 auf 0 gesetzt.
Während der Timer läuft und neue Telegramme &ne;0 an E1 eintreffen, wird A1 nicht verändert.
Läuft der Timer vollständig ab (ohne das ein neues Telegramm &ne;0 an E1 eintrifft), wird A1=1 gesetzt. Trifft nun wieder ein neues Telegramm &ne;0 an E1 ein, wird der Baustein neu gestartet und A1=0 gesetzt.

Sobald E3=1 gesetzt wird, ist der Baustein aktiviert. Ist E3=0, wird E1 ignoriert (der Baustein ist deaktiviert).
Wird E3=0 gesetzt während der Baustein bereits arbeitet, wird der Baustein unmittelbar deaktiviert. A1 bleibt dann unverändert.


E1: &ne;0 = Trigger, alle anderen Telegramme werden ignoriert und führen zum Ablauf des Timers. Sobald ein Telegramm &ne;0 eintrifft, wird der Timer neu gestartet.
E2: Zeit in Sekunden
E3: 1 = Baustein aktivieren, 0 = Baustein deaktivieren

A1: Beim ersten(!) neuen Telegramm &ne;0 an E1 wird A1=0 gesetzt. Nach Ablauf der Zeit ohne ein neues Telegramm &ne;0 an E1 wird A1=1 gesetzt.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if (logic_getState($id)==0) {

			if ($E[3]['value']==1) {
				if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
					logic_setVar($id,1,(getMicrotime()+$E[2]['value']));
					logic_setOutput($id,1,0); //Neustart: A1=0
					logic_setState($id,1,$E[2]['value']*1000);
				}
			}

		} else {

			//Retriggern
			if ($E[3]['value']==1 && $E[1]['value']!=0 && $E[1]['refresh']==1) {
				logic_setVar($id,1,(getMicrotime()+$E[2]['value']));
				logic_setState($id,1,$E[2]['value']*1000);
			}

			if ($E[3]['value']==0 || getMicrotime()>=logic_getVar($id,1)) {
				if ($E[3]['value']!=0) {
					logic_setOutput($id,1,1);
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
