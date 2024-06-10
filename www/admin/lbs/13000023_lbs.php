###[DEF]###
[name		=Triggerfolge 5-fach	]
[titel		=Triggerfolge			]

[e#1 TRIGGER		=A 								]
[e#2				=Wartezeit bis B (s) 	#init=0	]
[e#3 TRIGGER		=B								]
[e#4				=Wartezeit bis C (s) 	#init=0	]
[e#5 TRIGGER		=C								]
[e#6				=Wartezeit bis D (s) 	#init=0	]
[e#7 TRIGGER		=D								]
[e#8				=Wartezeit bis E (s) 	#init=0	]
[e#9 TRIGGER		=E								]
[e#10 TRIGGER		=Stoppbar				#init=0	]

[a#1		=Start		]
[a#2		=Erfolg		]
[a#3		=Abbruch	]

[v#1		=0						]
[v#2		=0						]
###[/DEF]###


###[HELP]###
Der Baustein startet bei einem neuen Telegramm &ne;0 an E1. A1 wird auf 1, A2 wird auf 0 und A3 wird auf 0 gesetzt.
Nun muss innerhalb der Wartezeit an E2 ein neues Telegramm &ne;0 an E3 eintreffen - und so weiter, bis schließlich ein neues Telegramm &ne;0 an Trigger E (E9) eintrifft.
Erst jetzt wird A2(!) auf 1 gesetzt, A1 wird auf 0 gesetzt und A3 bleibt unverändert.

Trifft kein entsprechendes Telegramm innerhalb einer Wartezeit ein, wird der Baustein abgebrochen (Triggerfolge nicht erfolgreich). A3 wird auf 1 gesetzt (Abbruch), A1 wird auf 0 gesetzt und A2 bleibt unverändert auf 0.

Ist E10&ne;0, kann der <i>laufende</i> Baustein durch ein neues Telegramm mit dem Wert 0 an E1 abgebrochen werden. Dann werden A1=0 und A3=1 gesetzt.

Wichtig:
Nicht benötigte Eingänge müssen eine Wartezeit von 0 erhalten (somit ist der entsprechende Triggereingang deaktiviert).
Beispiel: Trigger C wird nicht benötigt, also muss E4 (Wartezeit bis C) auf 0 gesetzt werden.

Falls alle Trigger (B..E) deaktiviert sind, startet der Baustein nicht - der Baustein macht in diesem Fall garnichts, die Ausgänge bleiben unverändert.

Mehrere Bausteine können kaskadiert werden, indem A2 mit E1 eines weiteren Bausteins verbunden wird.

E1: ein neues Telegramm &ne;0 startet den Baustein
E2: Wartezeit in Sekunden, innerhalb derer ein neues Telegramm &ne;0 an E3 (Trigger B) eintreffen muss
E3: ein neues Telegramm &ne;0 lässt den Baustein weiter arbeiten
E4: Wartezeit in Sekunden, innerhalb derer ein neues Telegramm &ne;0 an E5 (Trigger C) eintreffen muss
E5: ein neues Telegramm &ne;0 lässt den Baustein weiter arbeiten
E6: Wartezeit in Sekunden, innerhalb derer ein neues Telegramm &ne;0 an E7 (Trigger D) eintreffen muss
E7: ein neues Telegramm &ne;0 lässt den Baustein weiter arbeiten
E8: Wartezeit in Sekunden, innerhalb derer ein neues Telegramm &ne;0 an E9 (Trigger E) eintreffen muss
E9: ein neues Telegramm &ne;0 schließt die Triggerfolge erfolgreich ab (Hinweis: Auch die Trigger A..D können den Baustein beenden, wenn die entsprechend überflüssigen Trigger deaktivert werden)
E10: 1=Baustein ist stoppbar, 0=Baustein ist nicht stoppbar

A1: wird bei jedem Start des Bausteins auf 1 gesetzt. Nach erfolgreichem Durchlauf der Triggerfolge oder beim Timeout/Abbruch wird A1 wieder auf 0 gesetzt.
A2: wird bei jedem Start des Bausteins auf 0 gesetzt und wird bei erfolgreichem Durchlauf der Triggerfolge auf 1 gesetzt. Ein NICHT erfolgreicher Durchlauf der Triggerfolge belässt A2 unverändert auf 0.
A3: wird bei jedem Start des Bausteins auf 0 gesetzt. Bei einem Timeout/Abbruch (also NICHT erfolgreichem Durchlauf der Triggerfolge) wird A3 auf 1 gesetzt. Ein erfolgreicher Durchlauf der Triggerfolge belässt A3 unverändert auf 0.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {
		if (logic_getState($id)==0) {
			//Start
			if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
				$V[1]=0;
				$V=LB_LBSID_nextTrigger($id,$E,$V);
				if ($V[1]>0) {
					logic_setOutput($id,1,1);
					logic_setOutput($id,2,0);
					logic_setOutput($id,3,0);
				}
			}
				
		} else {
			//Stop
			if ($E[1]['value']==0 && $E[1]['refresh']==1 && $E[10]['value']!=0) {
				logic_setOutput($id,1,0);
				logic_setOutput($id,3,1);
				logic_setState($id,0);
				return;
			}

			//Timeout: Abbruch
			if (getMicrotime()>=$V[2]) {
				logic_setOutput($id,1,0);
				logic_setOutput($id,3,1);
				logic_setState($id,0);

			//auf Trigger B..E warten (und nächsten Trigger B..E aktivieren)
			} else {	
				if ($V[1]==1 && $E[3]['value']!=0 && $E[3]['refresh']==1) {$V=LB_LBSID_nextTrigger($id,$E,$V);}
				else if ($V[1]==2 && $E[5]['value']!=0 && $E[5]['refresh']==1) {$V=LB_LBSID_nextTrigger($id,$E,$V);}
				else if ($V[1]==3 && $E[7]['value']!=0 && $E[7]['refresh']==1) {$V=LB_LBSID_nextTrigger($id,$E,$V);}
				else if ($V[1]==4 && $E[9]['value']!=0 && $E[9]['refresh']==1) {$V=LB_LBSID_nextTrigger($id,$E,$V);}

				//keinen nächsten Trigger gefunden => Erfolg
				if ($V[1]==0) {
					logic_setOutput($id,1,0);
					logic_setOutput($id,2,1);
				}
			}
		}
	}
}

function LB_LBSID_nextTrigger($id,$E,$V) {
	if ($V[1]<1 && $E[2]['value']>0) {$V[1]=1;}
	else if ($V[1]<2 && $E[4]['value']>0) {$V[1]=2;}
	else if ($V[1]<3 && $E[6]['value']>0) {$V[1]=3;}
	else if ($V[1]<4 && $E[8]['value']>0) {$V[1]=4;}
	else {$V[1]=0;}
	logic_setVar($id,1,$V[1]);

	if ($V[1]>0) {
		$V[2]=getMicrotime()+$E[$V[1]*2]['value'];
		logic_setVar($id,2,$V[2]);
		logic_setState($id,1,$E[$V[1]*2]['value']*1000);			
	} else {
		logic_setState($id,0);
	}

	return $V;
}
?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###