###[DEF]###
[name			=	Virtuelle Leuchte 			]

[e#1 TRIGGER	=	Hauptschalter 		#init=1	]
[e#2 TRIGGER	=	A: Alarm					]
[e#3 TRIGGER	=	B: Manuell					]
[e#4 TRIGGER	=	C: Sensor					]
[e#5 TRIGGER	=	D: Sensor					]

[e#6 			=	A: Aktiviert 		#init=1	]
[e#7 			=	B: Aktiviert 		#init=1	]
[e#8 			=	C: Aktiviert 		#init=1	]
[e#9 			=	D: Aktiviert 		#init=1	]
[e#10 			=	B/C/D: Aus	 		#init=0	]

[e#11 OPTION	=	A: Lichtwert				]
[e#12 OPTION	=	B: Lichtwert				]
[e#13 OPTION	=	C: Lichtwert				]
[e#14 OPTION	=	D: Lichtwert				]

[e#15 			=	Sparmodus 			#init=0	]
[e#16 			=	Hell|Dunkel 				]
[e#17 IMPORTANT	=	Typ 				#init=0 ]


[a#1			=	Lichtwert					]
[a#2			=	Status						]
[a#3			=	A: Status					]
[a#4			=	B: Status					]
[a#5			=	C: Status					]
[a#6			=	D: Status					]


[v#1			=								]
[v#2			=0								]
[v#3			=-1								]
[v#4			=								]
###[/DEF]###


###[HELP]###
Dieser Baustein kann auf vielfältige Weise eine Leuchte ansteuern.

Der "Hauptschalter" (E1) hat Priorität vor allen Eingängen E2..E5 und arbeitet wie eine "Sicherung": Sobald E1 auf 0 gesetzt wird, wird A1 ggf. auf einen Lichtwert für "AUS" gesetzt (z.B. "0" oder "000000") und alle Telegramme an E2..E5 werden ignoriert (d.h. A1 bleibt dauerhaft "AUS"). Wird E1 auf 1 gesetzt, wird der ursprüngliche Zustand wiederhergestellt, d.h. alle Eingänge E2..E5 werden erneut verarbeitet und A1..A6 entsprechend gesetzt.

Die Eingänge E2..E5 werden mit absteigender Priorität behandelt, d.h. E2 hat die höchste und E5 die niedrigste Priorität. Wird z.B. E2 auf einen Lichtwert für "EIN" gesetzt (z.B. ein Dimmwert von 255) werden E3..E5 dadurch übersteuert (ignoriert). Erst wenn an E2 ein Lichtwert für "AUS" anliegt (z.B. 0) werden E3..E5 wieder freigegeben.

Grundsätzlich werden 2 Zustände an E2..E5 unterschieden: Ein Lichtwert für "EIN" und ein Lichtwert für "AUS" (bzw. 1/0).
"EIN" führt stets dazu, dass alle nachfolgenden Eingänge E2..E5 blockiert werden, während "AUS" die nachfolgenden Eingänge wieder freigibt und für eine erneute Berechnung des Zustandes der Leuchte sorgt.
"EIN" setzt A1 ggf. auf einen Lichtwert, der eine eingeschaltete Leuchte repräsentiert, während "AUS" das Gegenteil bewirkt (Leuchte wird ausgeschaltet). Der "AUS"-Wert wird stets generiert ("0" bzw. "000000") und kann nicht individuell definiert werden.

An den Eingängen E2..E5 (A/B/C/D) wird entweder 0/1 (Aus/Ein) oder ein "Lichtwert" erwartet. Ein Lichtwert kann im einfachsten Fall 0/1 sein (Aus/Ein), je nach Typ (E17) jedoch auch z.B. ein HSV-Wert wie "A1B2C3".
<ul>
	<li>wenn an E11..E14 ein Lichtwert definiert ist, wird an E2..E5 eine 0 (Aus) bzw. 1 (Ein) erwartet (der Lichtwert an E11..E14 wird dann angewendet)</li>
	<li>wenn E11..E14 [leer] ist, wird direkt an E2..E5 der gewünschte Lichtwert erwartet</li>
</ul>

Als "Lichtwert" kann zudem eine beliebige Zeichenfolge angegeben werden (an E2..E5 bzw. E11.E14), die mit "*" beginnt (z.B. "*EDOMI"): Dieser Zeichenfolge wird als "EIN" interpretiert und unverändert an A1 ausgegeben. Mit Hilfe z.B. einen nachgeschalteten Vergleichers kann diese Zeichenfolge ausgewertet werden und z.B. eine Sonderfunktion (RGB-Sequenz o.ä.) des Aktors auslösen.

Wird E2..E5 auf den Wert 0 (Aus) gesetzt, wird die Leuchte nicht zwangsläufig ausgeschaltet: Der Gesamtzustand (E2..E5) wird erneut berechnet und je nach Status von E2..E5 wird die Leuchte auf den entsprechenden Wert gesetzt. 

Ausschließlich E3 kann zudem auf den Wert -1 gesetzt werden: Dies entspricht einem Ausschalten, jedoch wird der Zustand <i>nicht</i> erneut berechnet: Die Leuchte bleibt solange ausgeschaltet, bis ein neues Telegramm an E1..E10 oder E15 eintrifft (oder E16 von 0 auf 1 wechselt, sofern ein Sparmodus aktiviert ist). E2 hat jedoch auch in diesem Fall Priorität!

<b>Hinweis:</b>
Dieser Baustein kann das Status-KO einer Leuchte <i>nicht</i> auswerten. Wenn die Leuchte zusätzlich über einen anderen Weg (ohne diesen Baustein) geschaltet wird, ist dem Baustein der aktuelle Zustand der Leuchte nicht bekannt - daher werden die Ausgänge A1..A6 u.U. nicht den tatsächlichen Zustand der Leuchte repräsentieren.


<h3>Sparmodus</h3>
Mit E15=1 wird der einfache Energiesparmodus aktiviert: 
Bei Einbruch der Dunkelheit (E16=1) werden E2..E5 erneut berechnet und die Leuchte ggf. eingeschaltet.
Bei Helligkeit (E16=0) wird die Leuchte ausgeschaltet und E2..E5 werden ignoriert. Bei erneutem Einbruch der Dunkelheit wird wieder der aktuelle Zustand E2..E5 ermittelt und die Leuchte ggf. eingeschaltet.

Mit E15=2 wird der komplexe Energiesparmodus aktiviert: 
Bei Einbruch der Dunkelheit (E16=1) werden E2/E4/E5 erneut berechnet und die Leuchte ggf. eingeschaltet. Der Zustand von E3 wird dabei hingegen ignoriert, erst ein <i>neues</i> Telegramm an E3 wird ggf. zu einer Statusänderung der Leuchte führen.
Beispiel: Wurde die Leuchte mittels eines Tastsensors an E3 eingeschaltet (z.B. bei Dunkelheit), wird die Leuchte bei eintretender Helligkeit automatisch ausgeschaltet. Bei erneutem Einbruch der Dunkelheit (z.B. am nächsten Abend) wird E3 u.U. noch immer z.B. auf den Wert 1 gesetzt sein und die Leuchte würde somit eingeschaltet werden (siehe bei E15=1). Mit E15=2 wird genau das verhindert, da erst ein <i>neues</i> Telegramm an E3 bei Dunkelheit ausgewertet wird.
	

<h3>Zwangs-Ausschaltung</h3>
Mit E10 kann die Leuchte zwangsweise und vorübergehend ausgeschaltet werden, jedoch werden nur die Eingänge E3..E5 (nicht E2) übersteuert:
Mit E10=1 wird die Leuchte ausgeschaltet, sofern E2 die Leuchte nicht eingeschaltet hat.
Mit E10=0 wird die Leuchte ggf. wieder eingeschaltet, sofern E3..E5 entsprechend gesetzt sind.
	
	
<h3>Eingänge</h3>
<ul>
	<li>E1: Hauptschalter
		<ul>
			<li>0: die Leuchte wird ggf. dauerhaft ausgeschaltet, E2..E5 werden ignoriert</li>
			<li>1: der aktuelle Zustand des Bausteins wird neuberechnet und die Leuchte ggf. eingeschaltet</li>
		</ul>
	</li>

	<li>E2: Alarm (A)
		<ul>
			<li>Lichtwert "EIN" bzw. 1: setzt A1 ggf. auf den Lichtwert an E2 bzw. E11 und sperrt E3..E5</li>
			<li>Lichtwert "AUS" bzw. 0: setzt A1 ggf. auf den Lichtwert für "AUS", gibt E3..E5 frei und berechnet den Zustand des Bausteins neu</li>
		</ul>
	</li>

	<li>E3: Manuell (B)
		<ul>
			<li>Lichtwert "EIN" bzw. 1: setzt A1 ggf. auf den Lichtwert an E3 bzw. E12 und sperrt E4..E5</li>
			<li>Lichtwert "AUS" bzw. 0: setzt A1 ggf. auf den Lichtwert für "AUS", gibt E4..E5 frei und berechnet den Zustand des Bausteins neu</li>
			<li>-1: setzt A1 ggf. auf den Lichtwert für "AUS", gibt E4..E5 frei und berechnet jedoch den Zustand des Bausteins <i>nicht</i> neu</li>
		</ul>
	</li>

	<li>E4: Sensor (C)
		<ul>
			<li>Lichtwert "EIN" bzw. 1: setzt A1 ggf. auf den Lichtwert an E4 bzw. E13 und sperrt E5</li>
			<li>Lichtwert "AUS" bzw. 0: setzt A1 ggf. auf den Lichtwert für "AUS", gibt E5 frei und berechnet den Zustand des Bausteins neu</li>
		</ul>
	</li>

	<li>E5: Sensor (D)
		<ul>
			<li>Lichtwert "EIN" bzw. 1: setzt A1 ggf. auf den Lichtwert an E5 bzw. E14</li>
			<li>Lichtwert "AUS" bzw. 0: setzt A1 ggf. auf den Lichtwert für "AUS" und berechnet den Zustand des Bausteins neu</li>
		</ul>
	</li>

	<li>E6..E9: Aktivierung von A..D
		<ul>
			<li>0: der entsprechende Kanal (A..D) ist deaktiviert und wird ignoriert</li>
			<li>1: der entsprechende Kanal (A..D) ist aktiviert</li>
			<li>Hinweis: Eine Änderung führt stets zur sofortigen Neuberechnung des Zustandes.</li>
		</ul>
	</li>

	<li>E10: B/C/D Aus (Zwangs-Ausschaltung bzw. Übersteuerung der Eingänge E3..E5)
		<ul>
			<li>1: setzt A1 ggf. auf den Lichtwert für "AUS", sofern die Leuchte nicht durch E2 (Alarm) eingeschaltet ist</li>
			<li>0: setzt A1 ggf. auf einen Lichtwert für "EIN", sofern B/C/D entsprechend gesetzt sind</li>
		</ul>
	</li>

	<li>E11..E14: "EIN"-Lichtwert für A..D (optional)
		<ul>
			<li>&ne;[leer]: die Angabe wird als Lichtwert für "EIN" interpretiert und für E2..E5 wird kein Lichtwert, sondern 0 (Aus) bzw. 1 (Ein) erwartet</li>
			<li>Hinweis: Der Lichtwert für "AUS" wird automatisch generiert.</li>
			<li>Wichtig: Eine Änderung des Lichtwerts an E11..E14 wird erst beim nächsten Einschalten über A..D berücksichtigt.</li>
		</ul>
	</li>

	<li>E15: Sparmodus
		<ul>
			<li>0: deaktiviert, d.h. A1 wird stets unabhängig von E16 (hell/dunkel) gesetzt</li>
			<li>1: Bei Einbruch der Dunkelheit (E16=1) werden E2..E5 erneut berechnet und die Leuchte ggf. eingeschaltet. Bei Helligkeit (E16=0) wird die Leuchte ausgeschaltet und E2..E5 werden ignoriert. Bei erneutem Einbruch der Dunkelheit wird wieder der aktuelle Zustand E2..E5 ermittelt und die Leuchte ggf. eingeschaltet.</li>
			<li>2: Wie zuvor, jedoch wird bei Einbruch der Dunkelheit E3 (Manuell) ignoriert. Erst ein <i>neues</i> Telegramm an E3 wird ggf. zu einer Statusänderung der Leuchte führen.</li>
			<li>Wichtig: Der Energiesparmodus kann nur genutzt werden, wenn an E16 ein entsprechender Wert 0/1 für hell/dunkel anliegt!</li>
		</ul>
	</li>
	
	<li>E16: Helligkeit/Dunkelheit (Status z.B. eines Dämmerungssensors) 
		<ul>
			<li>0 bzw. [leer]: Hell (z.B. Tag)</li>
			<li>1: Dunkel (z.B. Nacht)</li>
			<li>Hinweis: Dieser Status wird für den Sparmodus (E15) benötigt.</li>
		</ul>
	</li>

	<li>E17: Typ sämtlicher Lichtwerte bzw. der Leuchte (diese Angabe ist wichtig!)
		<ul>
			<li>0: Binär (1/0)</li>
			<li>1: Dimmwert (0..255)</li>
			<li>2: RGB-Wert (000000..FFFFFF)</li>
			<li>3: HSV-Wert (000000..FFFFFF)</li>
		</ul>
	</li>
</ul>


<h3>Ausgänge</h3>
<ul>
	<li>A1: Lichtwert: an diesem Ausgang wird der aktuelle Lichtwert zur Übergabe an ein Schalt-/Dimm-KO ausgegeben
		<ul>
			<li>Hinweis: A1 wird nur bei einer Änderung des Lichtwertes gesetzt.</li>
		</ul>
	</li>

	<li>A2: Status: 0=der Lichtwert an A1 repräsentiert einen AUS-Wert, 1=der Lichtwert an A1 repräsentiert einen EIN-Wert
		<ul>
			<li>Hinweis: A2 wird bei <i>jeder</i> Änderung von A1 (also des Lichtwertes) gesetzt.</li>
		</ul>
	</li>

	<li>A3..A6: Status A..D: diese Ausgänge werden in Abhängigkeit von A2 auf 0 oder 1 gesetzt (0=AUS, 1=EIN)
		<ul>
			<li>A3..A6 repräsentieren jeweils den Zustand genau des Kanals A..D, der für den aktuellen Status an A2 verantwortlich ist</li>
			<li>daher kann stets maximal einer der Ausgänge A3..A6 den Wert 1 annehmen, da bedingt durch die Priorisierung stets nur ein Kanal A..D für den aktuellen Status an A2 verantwortlich sein kann</li>
			<li>Hinweis: Die einzelnen Ausgänge A3..A6 werden nur bei einer Änderung des jeweiligen Zustands gesetzt.</li>
			<li>Wichtig: Der Wert 0 an einem der Ausgänge A3..A6 bedeutet nicht zwangsläufig, dass ein AUS-Lichtwert an A1 anliegt - eine 0 bedeutet lediglich, dass der entsprechende Kanal nicht für einen EIN-Lichtwert verantwortlich ist. Nur wenn <i>alle</i> Ausgänge A3..A6 den Wert 0 haben, liegt an A1 ein AUS-Lichtwert an (bzw. A2 ist 0).</li>
		</ul>
	</li>
</ul>
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[15]['value']==2) {
			if ($E[3]['refresh']==1) {
				$V[4]=$E[3]['value'];
				logic_setVar($id,4,$V[4]);
			} else {
				$E[3]['value']=$V[4];
			}
		} 

		//Typ ggf. auf Default=0 setzen
		if (!($E[17]['value']==0 || $E[17]['value']==1 || $E[17]['value']==2 || $E[17]['value']==3)) {$E[17]['value']=0;}

		//Status: Typenkonvertierung
		$V[2]=intval($V[2]);
		$V[3]=intval($V[3]);

		//Soll-Wert für A1 (für SBC)
		$A1value=null;

		//Hauptschalter
		if ($E[1]['value']==1) {

			//B/C/D: Aus
			if ($E[10]['value']==1) {
				$E[7]['value']=0;
				$E[8]['value']=0;
				$E[9]['value']=0;
			}
						
			//Aktivieren/Deaktivieren => Refresh
			if ($E[1]['refresh']==1 || $E[6]['refresh']==1 || $E[7]['refresh']==1 || $E[8]['refresh']==1 || $E[9]['refresh']==1 || $E[10]['refresh']==1 || $E[15]['refresh']==1) {
				$E[2]['refresh']=1;
				$E[3]['refresh']=1;
				$E[4]['refresh']=1;
				$E[5]['refresh']=1;
			}

			//Energiesparmodus
			if ($E[15]['value']>=1) {
				if ($E[16]['value']==1) {
					//Dunkelheit => Freigabe und Refresh
					if ($V[2]&1) {	//Flanke: Hell=>Dunkel
						$E[2]['refresh']=1;
						$E[4]['refresh']=1;
						$E[5]['refresh']=1;
						if ($E[15]['value']==2) {
							$V[4]=0;
							$E[3]['value']=0;
							$E[3]['refresh']=1;
							logic_setVar($id,4,$V[4]);
						} else {
							$E[3]['refresh']=1;
						}
					}
					$V[2]&=~1;
				} else {
					//Helligkeit => Ausschalten und Sperren
					$r=LB_LBSID_switchOnOff($id,$E,0);
					if ($r[0]!==false) {$A1value=$r[1];}
					$V[2]|=1;
				}
			} else {
				$V[2]&=~1;
			}

			//A: Alarm
			if (~$V[2]&1) {
				if ($E[2]['refresh']==1) {
					if ($E[6]['value']==1) {
	
						if (isEmpty($E[11]['value'])) {
							$r=LB_LBSID_switchOnOff($id,$E,$E[2]['value']);
						} else {
							$r=LB_LBSID_switchOnOff($id,$E,(($E[2]['value']==1)?$E[11]['value']:0));
						}
	
						if ($r[0]!==false) {$A1value=$r[1];}
						if ($r[0]==1) {
							$V[2]|=2;
						} else if ($r[0]==0) {
							$V[2]&=~2;
							$E[3]['refresh']=1;
							$E[4]['refresh']=1;
							$E[5]['refresh']=1;
						}
		
					} else {
						$V[2]&=~2;
					}
				}
			}

			//B: Manuell
			if (~$V[2]&1 && ~$V[2]&2) {
				if ($E[3]['refresh']==1) {
					if ($E[7]['value']==1) {

						if (isEmpty($E[12]['value'])) {
							$r=LB_LBSID_switchOnOff($id,$E,$E[3]['value']);
						} else {
							$r=LB_LBSID_switchOnOff($id,$E,(($E[3]['value']==1)?$E[12]['value']:0));
						}
	
						if ($r[0]!==false) {$A1value=$r[1];}
						if ($r[0]==1) {
							$V[2]|=4;
						} else if ($r[0]==0) {
							$V[2]&=~4;
							if ($E[3]['value']==-1) {
								$V[2]&=~8;
								$V[2]&=~16;
							} else {
								$E[4]['refresh']=1;
								$E[5]['refresh']=1;
							}
						}

					} else {
						$V[2]&=~4;
					}
				}
			}

			//C: Sensor 1
			if (~$V[2]&1 && ~$V[2]&2 && ~$V[2]&4) {
				if ($E[4]['refresh']==1) {
					if ($E[8]['value']==1) {
	
						if (isEmpty($E[13]['value'])) {
							$r=LB_LBSID_switchOnOff($id,$E,$E[4]['value']);
						} else {
							$r=LB_LBSID_switchOnOff($id,$E,(($E[4]['value']==1)?$E[13]['value']:0));
						}
	
						if ($r[0]!==false) {$A1value=$r[1];}
						if ($r[0]==1) {
							$V[2]|=8;
						} else if ($r[0]==0) {
							$V[2]&=~8;
							$E[5]['refresh']=1;
						}
	
					} else {
						$V[2]&=~8;
					}
				}
			}

			//D: Sensor 2
			if (~$V[2]&1 && ~$V[2]&2 && ~$V[2]&4 && ~$V[2]&8) {
				if ($E[5]['refresh']==1) {
					if ($E[9]['value']==1) {
	
						if (isEmpty($E[14]['value'])) {
							$r=LB_LBSID_switchOnOff($id,$E,$E[5]['value']);
						} else {
							$r=LB_LBSID_switchOnOff($id,$E,(($E[5]['value']==1)?$E[14]['value']:0));
						}
	
						if ($r[0]!==false) {$A1value=$r[1];}
						if ($r[0]==1) {
							$V[2]|=16;
						} else if ($r[0]==0) {
							$V[2]&=~16;
						}
	
					} else {
						$V[2]&=~16;
					}
				}
			}

		} else if ($E[1]['refresh']==1) {		
			$r=LB_LBSID_switchOnOff($id,$E,0,true);
			if ($r[0]!==false) {$A1value=$r[1];}
		}

		//A1/A2 (SBC)
		if (!isEmpty($A1value) && (string)strtoupper($A1value)!==(string)strtoupper($V[1])) {
			$V[1]=$A1value;
			logic_setVar($id,1,$V[1]);
			
			logic_setOutput($id,1,$V[1]);

			if (LB_LBSID_isOnValue($E[17]['value'],$V[1])!==false) {
				logic_setOutput($id,2,1);
			} else {
				logic_setOutput($id,2,0);
			}

		}

		//A3..A6 (SBC)
		$tmp=LB_LBSID_getStatus($E[17]['value'],$V[1],$V[2]);
		if ($tmp!=$V[3]) {
			if ($V[3]<0 || (($tmp&2)?1:0)!=(($V[3]&2)?1:0)) {logic_setOutput($id,3,(($tmp&2)?1:0));}
			if ($V[3]<0 || (($tmp&4)?1:0)!=(($V[3]&4)?1:0)) {logic_setOutput($id,4,(($tmp&4)?1:0));}
			if ($V[3]<0 || (($tmp&8)?1:0)!=(($V[3]&8)?1:0)) {logic_setOutput($id,5,(($tmp&8)?1:0));}
			if ($V[3]<0 || (($tmp&16)?1:0)!=(($V[3]&16)?1:0)) {logic_setOutput($id,6,(($tmp&16)?1:0));}
			logic_setVar($id,3,$tmp);
		}

		//Status sichern
		logic_setVar($id,2,$V[2]);
	}
}

function LB_LBSID_getStatus($typ,$value,$status) {
	$n=0;
	if (LB_LBSID_isOnValue($typ,$value)!==false) { //nur wenn EIN
		if ($status&2) {$n=2;}
		else if ($status&4) {$n=4;}
		else if ($status&8) {$n=8;}
		else if ($status&16) {$n=16;}
	}
	return $n;
}

function LB_LBSID_isOnValue($typ,$value) {
	//prüft, ob Lichtwert EIN oder AUS repräsentiert und gibt ggf. einen korrigierten Lichtwert für EIN zurück
	//return: Lichtwert=EIN (Lichtwert wird ggf. korrigiert), false=AUS

	//Spezial-Lichtwert
	if (substr(trim($value),0,1)==='*') {
		return $value;
	}

	if ($typ==0) {
		if ($value>=1) {return 1;}

	} else if ($typ==1) {
		if ($value>=1) {
			if ($value>255) {$value=255;}
			return intVal($value);
		}
		
	} else if ($typ==2) {
		if (strlen($value)==6) {
			$r=hexdec(substr($value,0,2));
			$g=hexdec(substr($value,2,2));
			$b=hexdec(substr($value,4,2));
			if (($r+$g+$b)>0) {
				return sprintf("%02X",round($r)).sprintf("%02X",round($g)).sprintf("%02X",round($b));
			}
		}
		
	} else if ($typ==3) {		
		if (strlen($value)==6) {
			$h=hexdec(substr($value,0,2));
			$s=hexdec(substr($value,2,2));
			$v=hexdec(substr($value,4,2));
			if ($v>0) {
				return sprintf("%02X",round($h)).sprintf("%02X",round($s)).sprintf("%02X",round($v));
			}
		}
	}
	return false;
}

function LB_LBSID_getOffValue($typ) {
	//gibt einen gültigen Lichtwert für AUS zurück
	//return: Lichtwert für AUS
	if ($typ==0) {
		return 0;
	} else if ($typ==1) {
		return 0;
	} else if ($typ==2) {
		return '000000';
	} else if ($typ==3) {
		return '000000';
	}
}

function LB_LBSID_switchOnOff($id,$E,$value,$ignoreEnergymode=false) {
	//A1 auf Lichtwert $value setzen (SBC mit V1), Energiesparmodus dabei ggf. berücksichtigen
	//Return: 1=Lampe wurde eingeschaltet (unabhängig von SBC), 0=Lampe wurde ausgeschaltet (unabhängig von SBC), false=Lampe wurde nicht eingeschaltet (Energiesparmodus)

	$r=LB_LBSID_isOnValue($E[17]['value'],$value);
	if ($r!==false) {
		//Einschalten
		if ($ignoreEnergymode || $E[15]['value']==0 || $E[16]['value']==1) {		//Energiesparmodus=aus/ignore oder Dunkelheit?
			return array(1,$r);
		}
	} else {
		//Ausschalten
		$r=LB_LBSID_getOffValue($E[17]['value']);
		return array(0,$r);
	}
	return array(false,false);
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
