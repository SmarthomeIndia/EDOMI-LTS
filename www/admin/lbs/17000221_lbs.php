###[DEF]###
[name		=Alarmtrigger (Pause)	]

[e#1 TRIGGER=Trigger				]
[e#2 TRIGGER=Pause					]
[e#3		=Dauer (s)#init=30]
[e#4		=Aktiviert				]
[e#5 OPTION	=Modus	#init=0			]

[a#1		=Alarm			 		]
[a#2		=Pause		 			]

[v#1		=0						] Pausentimer
[v#2		=0						] Flankendetektion
[v#3		=-1						] Pause aktiv? (und SBC für A2)
###[/DEF]###


###[HELP]###
Ein Signal &ne;0 an E1 triggert einen Alarm an A1. Dies kann jedoch temporär unterdrückt werden, wenn an E2 ein Signal &ne;0 anliegt. Dann wird für die Pausendauer (E3) jedes Signal an E1 ignoriert.

Jeder Alarm setzt A1 erneut auf 1. Der Ausgang A1 wird niemals wieder auf 0 gesetzt.

Am Ausgang A2 liegt eine 1 an, wenn gerade eine Pause aktiv ist (z.B. zur Signalisierung per LED). Ist die Pause beendet, wird A2 wieder auf 0 gesetzt.

Der Modus (E5) bestimmt das Verhalten des Eingangs E2:
<ul>
	<li>E5=0: Jedes Signal &ne;0 an E2 aktiviert die Pause für die Pausendauer (E3) erneut (der interne Timer wird neugestartet), ein Signal E2=0 wird stets ignoriert.</li>
	<li>E5=1: Solange E2&ne;0 ist, wird die Pause <i>dauerhaft</i> aktiviert. Erst wenn anschließend ein Signal E2=0 eintrifft (fallende Flanke), wird die Pause für die Pausendauer (E3) aktiviert (der interne Timer wird gestartet).</li>
</ul>

<b>Wichtig:</b>
Nach einer Alarmauslösung (A1=1) wird das Verhalten (Pausieren, etc.) des Bausteins unbeeindruckt fortgesetzt, d.h. eine Alarmauslösung bewirkt keine interne Zustandsänderung o.d.G. (eine Alarmauslösung ist kein Zustand, sondern ein Ereignis).

Typischer Anwendungsfall:
Reed-Kontakte an E1, BWMs im entsprechenden Raum an E2. Die BWMs pausieren dann den Baustein beim Öffnen eines Fensters von innen(!). Beim Öffnen von außen sprechen die BWMs hingegen nicht an (diese befinden sich im innern des Hauses) und der Alarm wird ausgelöst.

E1: &ne;0 = Alarm auslösen (A1=1), falls der Baustein aktiv ist und nicht durch E2 pausiert wird
E2: 0/&ne;0 = Pausieren (je nach Modus, s.o.)
E3: Dauer des Pausierens in Sekunden
E4: &ne;0 = Baustein wird aktiviert (A2 wird ggf. bereits beim Aktivieren aktualisiert), 0 = Baustein wird vollständig deaktiviert (A2 wird auf 0 gesetzt)
E5: Modus für E2: 0 = Triggern, 1 = Flanke (s.o.)

A1: wird bei Alarm auf 1 gesetzt (niemals jedoch auf 0)
A2: 1 = Pausieren aktiv (eine Alarmauslösung ist nicht möglich), 0 = Pausieren inaktiv (eine Alarmauslösung ist möglich)

<b>Hinweis:</b>
A2 wird nur bei einer Änderung des internen Zustandes gesetzt (SBC).

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[4]['refresh']==1) {

			//aktivieren?
			if ($E[4]['value']!=0) {

				//Pause während des Aktivierens schon aktiv
				if ($E[2]['value']!=0) {

					if ($E[5]['value']==0) {
						//Pausieren und Timer starten
						if ($V[3]!=1) {logic_setOutput($id,2,1);}

						$V[1]=getMicrotime()+$E[3]['value'];
						$V[2]=1;
						$V[3]=1;
						logic_setVar($id,1,$V[1]);
						logic_setVar($id,2,$V[2]);
						logic_setVar($id,3,$V[3]);
						logic_setState($id,1,$E[3]['value']*1000);

					} else {
						//Pausieren, aber Timer noch nicht starten
						if ($V[3]!=1) {logic_setOutput($id,2,1);}

						$V[2]=1;
						$V[3]=1;
						logic_setVar($id,2,$V[2]);
						logic_setVar($id,3,$V[3]);
						logic_setState($id,0);
					}

				//Pause während des Aktivierens nicht aktiv
				} else {
					if ($V[3]!=0) {logic_setOutput($id,2,0);}

					$V[2]=0;
					$V[3]=0;
					logic_setVar($id,2,$V[2]);
					logic_setVar($id,3,$V[3]);
					logic_setState($id,0);
				}

			//deaktivieren
			} else {
				if ($V[3]!=0) {logic_setOutput($id,2,0);}

				$V[2]=0;
				$V[3]=0;
				logic_setVar($id,2,$V[2]);
				logic_setVar($id,3,$V[3]);
				logic_setState($id,0);
			}
		}
		
		
		
		//aktiviert?
		if ($E[4]['value']!=0) {

			if ($E[5]['value']==0) {
				//Pause retriggern
				if ($E[2]['refresh']==1 && $E[2]['value']!=0) {
					if ($V[3]!=1) {logic_setOutput($id,2,1);}

					$V[1]=getMicrotime()+$E[3]['value'];
					$V[2]=1;
					$V[3]=1;
					logic_setVar($id,1,$V[1]);
					logic_setVar($id,2,$V[2]);
					logic_setVar($id,3,$V[3]);
					logic_setState($id,1,$E[3]['value']*1000);
				}

			} else {
				//steigende Flanke: Pausieren, aber Timer nicht starten
				if ($E[2]['refresh']==1 && $E[2]['value']!=0 && $V[2]==0) {
					if ($V[3]!=1) {logic_setOutput($id,2,1);}

					$V[2]=1;
					$V[3]=1;
					logic_setVar($id,2,$V[2]);
					logic_setVar($id,3,$V[3]);
					logic_setState($id,0);

				//fallende Flanke: Pausieren und Timer starten
				} else if ($E[2]['refresh']==1 && $E[2]['value']==0 && $V[2]==1) {
					if ($V[3]!=1) {logic_setOutput($id,2,1);}

					$V[1]=getMicrotime()+$E[3]['value'];
					$V[2]=0;
					$V[3]=1;
					logic_setVar($id,1,$V[1]);
					logic_setVar($id,2,$V[2]);
					logic_setVar($id,3,$V[3]);
					logic_setState($id,1,$E[3]['value']*1000);
				}
			}


			//Pausenzeit abgelaufen?
			if (logic_getState($id)==1) {
				if (getMicrotime()>=$V[1]) {
					if ($V[3]!=0) {logic_setOutput($id,2,0);}

					$V[2]=0;
					$V[3]=0;
					logic_setVar($id,2,$V[2]);
					logic_setVar($id,3,$V[3]);
					logic_setState($id,0);
				}
			}

			//Alarm?
			if ($V[3]==0 && $E[1]['refresh']==1 && $E[1]['value']!=0) {
				logic_setOutput($id,1,1);
				if ($V[3]!=0) {logic_setOutput($id,2,0);}
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