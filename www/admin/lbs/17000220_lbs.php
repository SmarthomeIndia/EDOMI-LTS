###[DEF]###
[name		=Alarmtrigger (Zähler)	]

[e#1 TRIGGER=Trigger			 	]
[e#2		=Anzahl 	#init=3	]
[e#3		=Dauer (s) #init=10]
[e#4		=Aktiviert/Reset	#init=0	]

[a#1		=Alarm		 			]
[a#2		=Zähler 				]

[v#1		=0						] (Zähler)
[v#2		=0						] (Timer)
###[/DEF]###


###[HELP]###
Ein Signal &ne;0 an E1 erhöht einen internen Zähler. Wird innerhalb der Zeitspanne (E3) die Triggeranzahl (E2) erreicht oder überschritten, wird Alarm ausgelöst (A1 wird auf 1 gesetzt) und der Zähler zurückgesetzt.
Jeder Alarm setzt A1 erneut auf 1. Der Ausgang A1 wird erst bei einem Signal an E4 wieder auf 0 gesetzt.
Am Ausgang A2 liegt der aktuelle Zählerstand an und kann z.B. zu Anzeigezwecken genutzt werden.

Typischer Anwendungsfall:
Einen oder mehrere Bewegungsmelder mit E1 verbinden. Der Vorteil gegenüber einer direkten Auswertung der BWMs ist, dass Fehlalarme vermieden werden können (da vereinzelte Auslösungen nicht direkt zu einem Alarm führen).

Hinweis:
Der Baustein überprüft NICHT, ob verschiedene(!) BWMs den Zähler erhöhen (es gibt schließlich nur einen Signal-Eingang E1). Wird also ein und derselbe BWM innerhalb der Zeitspanne oft genug getriggert, wird Alarm ausgelöst.

E1: Signal 0/&ne;0: Ein Telegramm &ne;0 erhöht den Zähler, falls der Baustein aktiv ist
E2: Triggeranzahl 1..oo: Anzahl der erforderlichen Trigger an E1, damit Alarm ausgelöst wird. Die Triggeranzahl muss innerhalb der Zeitspanne an E3 erreicht werden, sonst wird der interne Zähler wieder auf 0 gesetzt.
E3: Dauer in Sekunden, bis der interne Zähler wieder auf 0 gesetzt wird
E4: 0=Deaktiviert, 1=Aktiviert/Reset: wird im Normalfall mit dem Ausgang "Aktiv" der Alarmanlage verbunden. Bei JEDEM Telegramm wird der Alarm zurückgesetzt und der Zähler auf 0 gesetzt.

A1: Alarmausgang 0/1: Wird bei Alarm auf 1 gesetzt (und wird erst beim nächsten Deaktivieren/Reset wieder auf 0 gesetzt). Jeder Alarm setzt A1 erneut auf 1, so dass ein nachfolgender Alarmtrigger-Eingang erneut getriggert wird.
A2: Zählerstand 0..oo: Der aktuelle Stand des internen Zählers. Wird bei Alarm oder Deaktivierung/Reset wieder auf 0 gesetzt.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[4]['refresh']==1) { //BS wurde Aktiviert/Deaktiviert oder Resettet
			logic_setOutput($id,1,0);
			logic_setOutput($id,2,0);
			logic_setVar($id,1,0);
			logic_setState($id,0);
		}

		if ($E[4]['value']==1) { //Aktiviert?

			//Zähler erhöhen
			if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
				$V[1]++;
				$V[2]=getMicrotime()+$E[3]['value'];
				logic_setVar($id,1,$V[1]);
				logic_setVar($id,2,$V[2]);
				logic_setOutput($id,2,$V[1]); 	//Zählerstand ausgeben
				logic_setState($id,1,$E[3]['value']*1000);
			}

			//Alarm? (Triggeranzahl erreicht?)
			if ($V[1]>=$E[2]['value']) {
				logic_setOutput($id,1,1);
				logic_setVar($id,1,0); 		//Zähler auf 0
				logic_setOutput($id,2,0); 		//Zählerstand ausgeben
				logic_setState($id,0);
			}

			if (logic_getState($id)==1) {
				//Zeitspanne erreicht?
				if (getMicrotime()>=$V[2]) { 		//Zeit abgelaufen?
					logic_setVar($id,1,0); 	//Zähler auf 0
					logic_setOutput($id,2,0); 	//Zählerstand ausgeben
					logic_setState($id,0);
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
