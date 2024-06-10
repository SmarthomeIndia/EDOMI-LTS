###[DEF]###
[name		=Alarmanlage 			]

[e#1 TRIGGER	=Extern 1: Alarmtrigger						]
[e#2 TRIGGER	=Intern 2: Alarmtrigger						]
[e#3 TRIGGER	=Panik 3: Alarmtrigger						]
[e#4			=Extern 1: Ein/Aus 							]
[e#5			=Intern 2: Ein/Aus	 						]
[e#6			=Panik 3: Ein/Aus 	  						]
[e#7 OPTION		=Extern 1: Scharfschaltung (s)	#init=30	]
[e#8 OPTION		=Intern 2: Scharfschaltung (s)	#init=10	]
[e#9 OPTION		=Panik 3: Scharfschaltung (s)	#init=0		]
[e#10 OPTION	=Extern 1: Voralarmdauer (s)	#init=0		]
[e#11 OPTION	=Intern 2: Voralarmdauer (s)	#init=10	]
[e#12 OPTION	=Panik 3: Voralarmdauer (s)		#init=0		]
[e#13 OPTION	=Alarmdauer (s)	#init=3600					]
[e#14 TRIGGER	=Quittierung								]
[e#15 OPTION	=Logging						#init=0		]

++++++++++
	Voralarm Extern (Dauer wie bei Intern?)
	oder einfach für alle: Voralarm, Scharfverzögerung...



[a#1		=Alarm				 			]
[a#2		=Voralarm	 					]
[a#3		=Alarmanzahl		 			]
[a#4		=Extern 1: Aktiviert	 		]
[a#5		=Intern 2: Aktiviert	 		]
[a#6		=Panik 3: Aktiviert		 		]
[a#7		=Extern 1: Scharf		 		]
[a#8		=Intern 2: Scharf					]
[a#9		=Panik 3: Scharf					]

[v#1			=0						] Status (Extern: bit1=ein/aus, bit2=Verzögerung/Scharf // Intern: bit3/4 // Panik: bit5/6)
[v#2			=0						] Alarm (Extern: bit1=Voralarm, bit2=Alarm // Intern: bit3/4 // Panik: bit5/6)
[v#3 REMANENT	=0						] Alarmzähler
[v#4			=0						] Voralarm/Alarm: Timer
[v#5			=0						] Scharf Extern: Timer 
[v#6			=0						] Scharf Intern: Timer 
[v#7			=0						] Scharf Panik: Timer 
###[/DEF]###


###[HELP]###
Dieser Baustein bildet eine einfache Alarmanlage nach.

Unterschieden werden 3 verschiedene Auslöser eines Alarms: "Extern" (1), "Intern" (2) und "Panik" (3).
Sobald ein Alarm ausgelöst wurde (A1=1/2/3), kann kein erneuter Alarm eines anderen Auslösers ausgelöst werden, bis der Alarm quittiert worden ist (E14=1) oder der Auslöser deaktiviert wird.

Grundsätzlich wird mit E4..E6 der entsprechende Auslöser aktiviert oder deaktiviert und A4..A6 entsprechend auf 1 oder 0 gesetzt. Der entsprechende Auslöser ist jedoch erst "scharfgeschaltet", wenn die Scharfschalt-Verzögerung (E7..E9) abgelaufen ist (A7..A9 wird dann auf 1 gesetzt).

"Panik" kann stets unabhängig von "Extern" und "Intern" mittels E6 aktiviert/deaktiviert werden.
"Intern" wird hingegen zusätzlich abhängig von "Extern" deaktiviert bzw. aktiviert: Wurde "Intern" aktiviert (E5=1) und anschließend "Extern" ebenfalls aktiviert (E4=1), wird "Intern" sofort deaktiviert. Erst wenn "Extern" deaktiviert wird, wird "Intern" ggf. wieder aktiviert.


<h3>Alarm auslösen</h3>
Ausgelöst wird ein Alarm, sobald an E1..E3 ein Telegramm =1 eintrifft (sofern der entsprechende Auslöser bereits "scharfgeschaltet" ist). 
Je nach E10..E12 wird nun zunächst A2 (Voralarm) auf die ID des auslösenden Alarms gesetzt ("Extern"=1, "Intern"=2, "Panik"=3), bis die Voralarmdauer abgelaufen ist.
Anschließend wird A1 (Alarm) auf die ID des auslösenden Alarms gesetzt und A3 wird um den Wert 1 erhöht (Alarmanzahl).
Sobald ein Voralarm (A2) oder Alarm (A1) ausgelöst wurde, kann kein weiterer Alarm ausgelöst werden bis der aktuelle Alarm quittiert oder beendet worden ist.


<h3>Alarm beenden</h3>
Beendet wird ein Alarm automatisch nach Ablauf der an E13 angegebenen Alarmdauer. Der Zustand vor dem Alarm wird dann unmittelbar wieder hergestellt, d.h. die entsprechend aktivierten Auslöser sind sofort wieder "scharfgeschaltet".
Ein Alarm bzw. Voralarm kann zudem vorzeitig beendet werden, indem der Alarm quittiert wird (E14=1) oder der Auslöser des Alarms deaktiviert wird (E4..E6=0).
Nur bei einer Quittierung mittels E14 wird der Alarmzähler (A3) zurückgesetzt, andernfalls wird der Alarmzähler bei jedem Alarm (jedoch nicht bei einem Voralarm) um den Wert 1 erhöht.


<h3>Verhalten bei einem Neustart</h3>
Nach einem Neustart wird der Baustein vollständig zurückgesetzt (mit Ausnahme des Alarmzählers). Wurde also vor einem Neustart z.B. ein Alarm ausgelöst, wird dieser nach dem Neustart nicht erneut ausgelöst.


<h3>Ein- und Ausgänge</h3>
E1..E3: 1 = Alarm 1..3 auslösen
E4..E6: &ne;0 = Auslöser 1..3 aktivieren, 0 = Auslöser 1..3 deaktivieren
E7..E9: Scharfschaltverzögerung für Auslöser 1..3 (in Sekunden), 0 = Scharfschaltverzögerung für Auslöser 1..3 deaktiviert
E10..E12: Voralarmdauer für Auslöser 1..3 (in Sekunden), 0 = Voralarm für Auslöser 1..3 deaktiviert
E13: Alarmdauer für alle 3 Auslöser gleichermaßen (in Sekunden)
E14: 1 = ausgelösten Alarm quittieren (Alarmzähler wird zurückgesetzt)
E15: 1 = Protokollierung aktivieren (im Individual-Log "LBS17000225-&lt;Instanz-ID&gt;")
	
A1: 0 = Alarm beendet/quittiert, 1..3 = Alarm durch Auslöser 1..3
A2: 0 = Voralarm beendet/quittiert, 1..3 = Voralarm durch Auslöser 1..3
A3: Alarmzähler (nicht Voralarm): Anzahl der ausgelösten Alarme seit Quittierung (dieser Wert wird intern remanent gespeichert)
A4..A6: 1 = Auslöser 1..3 ist aktiviert, 0 = Auslöser 1..3 ist deaktiviert
A7..A9: 1 = Auslöser 1..3 ist "scharfgeschaltet", 0 = Auslöser 1..3 ist nicht "scharfgeschaltet"
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		//erforderlich für Bit-Operationen
		$V[1]=intval($V[1]);	
		$V[2]=intval($V[2]);

		//Extern: Aktivierung/Deaktivierung
		if ($E[4]['refresh']==1) {
			if ($E[4]['value']!=0 && ~$V[1]&1) {
				$V[1]|=1;
				//Extern: aktivieren und Intern ggf. deaktivieren
				logic_setOutput($id,4,1);
				logic_setOutput($id,7,0);
				LB_LBSID_log($id,$E,'Ein','Extern');

//### Funktion?
				if ($V[1]&4) {
					$V[1]&=~4;
					$V[1]&=~8;
					//Intern: deaktivieren
					logic_setOutput($id,5,0);
					logic_setOutput($id,8,0);
					$V=LB_LBSID_alarmOff($id,$E,$V,2);
					LB_LBSID_log($id,$E,'Aus (durch Extern)','Intern');
				}
				
				//Scharfschaltverzögerung
				if ($E[7]['value']>0) {
					$V[1]&=~2;
					$V[5]=getMicrotime()+$E[7]['value'];
					logic_setVar($id,5,$V[5]);
					if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}
				} else {
					LB_LBSID_log($id,$E,'Scharf','Extern');
					$V[1]|=2;
					logic_setOutput($id,7,1);
				}

			} else if ($E[4]['value']==0 && $V[1]&1) {
				$V[1]&=~1;
				$V[1]&=~2;
				//Extern: deaktivieren und Intern ggf. aktivieren
				logic_setOutput($id,4,0);
				logic_setOutput($id,7,0);
				$V=LB_LBSID_alarmOff($id,$E,$V,1);
				LB_LBSID_log($id,$E,'Aus','Extern');
//### unsauber... besser hierhin kopieren bzw. Funktion (Intern ggf. aktivieren)
				$E[5]['refresh']=1;
			}
		}

		//Intern: Aktivierung/Deaktivierung
		if (~$V[1]&1 && $E[5]['refresh']==1) {
			if ($E[5]['value']!=0 && ~$V[1]&4) {
				$V[1]|=4;
				//Intern: aktivieren
				logic_setOutput($id,5,1);
				logic_setOutput($id,8,0);
				LB_LBSID_log($id,$E,'Ein','Intern');

				//Scharfschaltverzögerung
				if ($E[8]['value']>0) {
					$V[1]&=~8;
					$V[6]=getMicrotime()+$E[8]['value'];
					logic_setVar($id,6,$V[6]);
					if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}
				} else {
					LB_LBSID_log($id,$E,'Scharf','Intern');
					$V[1]|=8;
					logic_setOutput($id,8,1);
				}

			} else if ($E[5]['value']==0 && $V[1]&4) {
				$V[1]&=~4;
				$V[1]&=~8;
				//Intern: deaktivieren
				logic_setOutput($id,5,0);
				logic_setOutput($id,8,0);
				$V=LB_LBSID_alarmOff($id,$E,$V,2);
				LB_LBSID_log($id,$E,'Aus','Intern');
			}
		}

		//Panik: Aktivierung/Deaktivierung
		if ($E[6]['refresh']==1) {
			if ($E[6]['value']!=0 && ~$V[1]&16) {
				$V[1]|=16;
				//Panik: aktivieren
				logic_setOutput($id,6,1);
				logic_setOutput($id,9,0);
				LB_LBSID_log($id,$E,'Ein','Panik');

				//Scharfschaltverzögerung
				if ($E[9]['value']>0) {
					$V[1]&=~32;
					$V[7]=getMicrotime()+$E[9]['value'];
					logic_setVar($id,7,$V[7]);
					if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}
				} else {
					LB_LBSID_log($id,$E,'Scharf','Panik');
					$V[1]|=32;
					logic_setOutput($id,9,1);
				}

			} else if ($E[6]['value']==0 && $V[1]&16) {
				$V[1]&=~16;
				$V[1]&=~32;
				//Panik: deaktivieren
				logic_setOutput($id,6,0);
				logic_setOutput($id,9,0);
				$V=LB_LBSID_alarmOff($id,$E,$V,3);
				LB_LBSID_log($id,$E,'Aus','Panik');
			}
		}

		//Quittierung
		if ($E[14]['value']==1 && $E[14]['refresh']==1) {
			if ($V[2]>0) {
				if ($V[2]==1) {LB_LBSID_log($id,$E,'Voralarm quittiert','Extern');}
				if ($V[2]==2) {LB_LBSID_log($id,$E,'Alarm quittiert','Extern');}
				if ($V[2]==4) {LB_LBSID_log($id,$E,'Voralarm quittiert','Intern');}
				if ($V[2]==8) {LB_LBSID_log($id,$E,'Alarm quittiert','Intern');}
				if ($V[2]==16) {LB_LBSID_log($id,$E,'Voralarm quittiert','Panik');}
				if ($V[2]==32) {LB_LBSID_log($id,$E,'Alarm quittiert','Panik');}
				$V=LB_LBSID_alarmOff($id,$E,$V);
			}
			$V[3]=0;
			logic_setOutput($id,3,$V[3]);
			logic_setVar($id,3,$V[3]);
		}

		//Alarmtrigger
		if ($V[2]==0) {
			if ($V[1]&1 && $V[1]&2 && $E[1]['value']==1 && $E[1]['refresh']==1) {
				if ($E[10]['value']>0) {
					$V[2]=1;
					LB_LBSID_log($id,$E,'Vorlarm ausgelöst','Extern');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				} else {
					$V[2]=2;
					LB_LBSID_log($id,$E,'Alarm ausgelöst','Extern');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				}

			} else if ($V[1]&4 && $V[1]&8 && $E[2]['value']==1 && $E[2]['refresh']==1) {
				if ($E[11]['value']>0) {
					$V[2]=4;
					LB_LBSID_log($id,$E,'Vorlarm ausgelöst','Intern');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				} else {
					$V[2]=8;
					LB_LBSID_log($id,$E,'Alarm ausgelöst','Intern');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				}

			} else if ($V[1]&16 && $V[1]&32 && $E[3]['value']==1 && $E[3]['refresh']==1) {
				if ($E[12]['value']>0) {
					$V[2]=16;
					LB_LBSID_log($id,$E,'Vorlarm ausgelöst','Panik');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				} else {
					$V[2]=32;
					LB_LBSID_log($id,$E,'Alarm ausgelöst','Panik');
					$V=LB_LBSID_alarmOn($id,$E,$V);
				}
			}
		}

		//Timer läuft
		if (logic_getState($id)==1) {
		
			//Extern: Scharfschaltverzögerung
			if ($V[1]&1 && ~$V[1]&2) {
				if (getMicrotime()>=$V[5]) {
					LB_LBSID_log($id,$E,'Scharf (verzögert)','Extern');
					$V[1]|=2;
					logic_setOutput($id,7,1);
				}
			}

			//Intern: Scharfschaltverzögerung
			if ($V[1]&4 && ~$V[1]&8) {
				if (getMicrotime()>=$V[6]) {
					LB_LBSID_log($id,$E,'Scharf (verzögert)','Intern');
					$V[1]|=8;
					logic_setOutput($id,8,1);
				}
			}

			//Panik: Scharfschaltverzögerung
			if ($V[1]&16 && ~$V[1]&32) {
				if (getMicrotime()>=$V[7]) {
					$V[1]|=32;
					logic_setOutput($id,9,1);
					LB_LBSID_log($id,$E,'Scharf (verzögert)','Panik');
				}
			}

			//Alarm: Timeout
			if ($V[2]>0 && getMicrotime()>=$V[4]) {
				if ($V[2]==1) {
					LB_LBSID_log($id,$E,'Voralarm beenden (automatisch)','Extern');
					$V=LB_LBSID_alarmOff($id,$E,$V);
					$V[2]=2;
					$V=LB_LBSID_alarmOn($id,$E,$V);

				} else if ($V[2]==2) {
					LB_LBSID_log($id,$E,'Alarm beenden (automatisch)','Extern');
					$V=LB_LBSID_alarmOff($id,$E,$V);

				} else if ($V[2]==4) {
					LB_LBSID_log($id,$E,'Voralarm beenden (automatisch)','Intern');
					$V=LB_LBSID_alarmOff($id,$E,$V);
					$V[2]=8;
					$V=LB_LBSID_alarmOn($id,$E,$V);

				} else if ($V[2]==8) {
					LB_LBSID_log($id,$E,'Alarm beenden (automatisch)','Intern');
					$V=LB_LBSID_alarmOff($id,$E,$V);

				} else if ($V[2]==16) {
					LB_LBSID_log($id,$E,'Voralarm beenden (automatisch)','Panik');
					$V=LB_LBSID_alarmOff($id,$E,$V);
					$V[2]=32;
					$V=LB_LBSID_alarmOn($id,$E,$V);

				} else if ($V[2]==32) {
					LB_LBSID_log($id,$E,'Alarm beenden (automatisch)','Panik');
					$V=LB_LBSID_alarmOff($id,$E,$V);
				}				
			}
						
			//Timer ggf. beenden
			if ($V[2]==0 && (~$V[1]&1 || $V[1]&2) && (~$V[1]&4 || $V[1]&8) && (~$V[1]&16 || $V[1]&32)) {
				logic_setState($id,0);
			}	
		}
		
		logic_setVar($id,1,$V[1]);
		logic_setVar($id,2,$V[2]);
	}
}

function LB_LBSID_log($id,$E,$msg,$level='') {
	if ($E[15]['value']==1) {
		writeToCustomLog('LBS17000225-'.$id,$level,$msg);
	}
}

function LB_LBSID_alarmOn($id,$E,$V) {
	if ($V[2]==1) {
		LB_LBSID_log($id,$E,'VORALARM','Extern');
		logic_setOutput($id,2,1);

		$V[4]=getMicrotime()+$E[10]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}

	} else if ($V[2]==2) {
		LB_LBSID_log($id,$E,'ALARM','Extern');
		logic_setOutput($id,1,1);

		$V[3]++;
		logic_setOutput($id,3,$V[3]);
		logic_setVar($id,3,$V[3]);

		$V[4]=getMicrotime()+$E[13]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}

	} else if ($V[2]==4) {
		LB_LBSID_log($id,$E,'VORALARM','Intern');
		logic_setOutput($id,2,2);

		$V[4]=getMicrotime()+$E[11]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}

	} else if ($V[2]==8) {
		LB_LBSID_log($id,$E,'ALARM','Intern');
		logic_setOutput($id,1,2);

		$V[3]++;
		logic_setOutput($id,3,$V[3]);
		logic_setVar($id,3,$V[3]);
		
		$V[4]=getMicrotime()+$E[13]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}

	} else if ($V[2]==16) {
		LB_LBSID_log($id,$E,'VORALARM','Panik');
		logic_setOutput($id,2,3);

		$V[4]=getMicrotime()+$E[12]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}

	} else if ($V[2]==32) {
		LB_LBSID_log($id,$E,'ALARM','Panik');
		logic_setOutput($id,1,3);

		$V[3]++;
		logic_setOutput($id,3,$V[3]);
		logic_setVar($id,3,$V[3]);

		$V[4]=getMicrotime()+$E[13]['value'];
		logic_setVar($id,4,$V[4]);
		if (logic_getState($id)!=1) {logic_setState($id,1,1000,true);}
	}
	return $V;
}

function LB_LBSID_alarmOff($id,$E,$V,$offId=0) {
	if ($V[2]>0) {
		if ($offId==0 || ($offId==1 && ($V[2]==1 || $V[2]==2)) || ($offId==2 && ($V[2]==4 || $V[2]==8)) || ($offId==3 && ($V[2]==16 || $V[2]==32))) {
			if ($V[2]==1) {
				LB_LBSID_log($id,$E,'Voralarm beendet','Extern');
				logic_setOutput($id,2,0);

			} else if ($V[2]==2) {
				LB_LBSID_log($id,$E,'Alarm beendet','Extern');
				logic_setOutput($id,1,0);

			} else if ($V[2]==4) {
				LB_LBSID_log($id,$E,'Voralarm beendet','Intern');
				logic_setOutput($id,2,0);

			} else if ($V[2]==8) {
				LB_LBSID_log($id,$E,'Alarm beendet','Intern');
				logic_setOutput($id,1,0);

			} else if ($V[2]==16) {
				LB_LBSID_log($id,$E,'Voralarm beendet','Panik');
				logic_setOutput($id,2,0);

			} else if ($V[2]==32) {
				LB_LBSID_log($id,$E,'Alarm beendet','Panik');
				logic_setOutput($id,1,0);
			}
			$V[2]=0;
		}
	}
	return $V;
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
