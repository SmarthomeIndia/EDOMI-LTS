###[DEF]###
[name		=Oszillator				]

[e#1 TRIGGER=Trigger/Stop #init=0		]
[e#2		=Dauer 1 (ms) #init=500	]
[e#3		=Dauer 0 (ms) #init=500	]
[e#4		=Zyklen  #init=0	]

[a#1		=					]

[v#1		=0						]
[v#2		=0						]
[v#3		=0						]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 abwechselnd auf 1 und 0.

Ein neues Telegramm =1 an E1 startet den Baustein, ein neues Telegramm=0 stoppt den Baustein. Ein Zyklus beginnt immer mit A1=1, gefolgt von A1=0.
E2 bestimmt die Dauer in ms für A1=1, E3 bestimmt die Dauer für A1=0. 

Die Anzahl der Zyklen wird mit E4 definiert, E4=0 läßt den Baustein solange arbeiten, bis E1=0 wird. Ein Zyklus dauert E2+E3 Millisekunden.

Wichtig: A1 wird beim Stoppen des Bausteins bzw. nach Ablauf der Zyklen immer(!) auf 0 gesetzt.

E1: 1=Start, 0=Stop
E2: Dauer für A1=1 (ms)
E3: Dauer für A1=0 (ms)
E4: Anzahl der Zyklen: 0=unendlich (bis E1=0 ist), 1..&infin;=Anzahl (1 Zyklus = E2+E3)
A1: wechselt zwischen 1 und 0 (entspricht einem Zyklus). Beendet wird der Baustein immer mit A1=0 (auch wenn Zyklus-Anzahl "unendlich" ist)
###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id) {

	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[2]['value']<1) {$E[2]['value']=1;}
		if ($E[3]['value']<1) {$E[3]['value']=1;}

		if (logic_getState($id)==0) {
		
			if ($E[1]['value']==1 && $E[1]['refresh']==1) { //Start
				logic_setVar($id,1,getMicrotime()+($E[2]['value']/1000)); //Timer (für High und Low)
				logic_setVar($id,2,1); //High- oder Low-Periode
				logic_setVar($id,3,1); //Anzahl der bereits abgespielten Zyklen
				logic_setOutput($id,1,1);
				logic_setState($id,1,$E[2]['value']);
			}
			
		} else {

			if (($V[2]==1) && (getMicrotime()>=$V[1])) {		//"High"-Periode fertig
			
				logic_setOutput($id,1,0);
				$V[1]=getMicrotime()+($E[3]['value']/1000);
				logic_setVar($id,1,$V[1]);
				$V[2]=0;
				logic_setVar($id,2,0);
				logic_setState($id,1,$E[3]['value']);
				
			} else if (($V[2]==0) && (getMicrotime()>=$V[1])) {	//"Low"-Periode fertig
			
				$V[1]=getMicrotime()+($E[2]['value']/1000);
				logic_setVar($id,1,$V[1]);
				logic_setVar($id,2,1);
				logic_setState($id,1,$E[2]['value']);

				if ($E[4]['value']>0) {
					//Zyklus-Anzahl prüfen
					if ($V[3]<$E[4]['value']) {
						//Anzahl noch nicht erreicht
						logic_setVar($id,3,($V[3]+1));
						logic_setOutput($id,1,1);
					} else {
						//Anzahl der Zyklen erledigt => Ende
						//logic_setOutput($id,1,0); //überflüssig, da ohnehin schon auf 0 gesetzt von der letzten High-Periode...
						logic_setState($id,0);
					}
				} else {
					//Zyklus-Anzahl ist unendlich
					logic_setOutput($id,1,1);
				}
				
			}

			if ($E[1]['value']==0) { 	//Stop (also quasi Abbruch => Ausgang auf 0)
				logic_setOutput($id,1,0);
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
