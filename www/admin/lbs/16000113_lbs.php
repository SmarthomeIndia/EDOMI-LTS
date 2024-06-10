###[DEF]###
[name		=Ein/Aus-Verzögerung			]

[e#1 TRIGGER=Trigger					]
[e#2		=Verzögerung 1 (ms) #init=500	]
[e#3		=Verzögerung 0 (ms) #init=500	]

[a#1		=					]

[v#1		=500					]
[v#2		=-1					] (Hilfsvariable)
###[/DEF]###


###[HELP]###
Dieser Baustein verzögert ein neues Telegramm an E1. Dabei wird zwischen Telegrammen =0 und &ne;0 unterschieden:

Trifft ein neues Telegramm &ne;0 an E1 ein, wird nach Ablauf der Verzögerungszeit an E2 der Ausgang A1=1 gesetzt.
Trifft ein neues Telegramm =0 an E1 ein, wird nach Ablauf der Verzögerungszeit an E3 der Ausgang A1=0 gesetzt.

Wichtig: Trifft während der Verzögerung eines Telegramms ein weiteres Telegramm an E1 ein, startet der Baustein quasi neu. Das vorherige Telegramm wird verworfen!

E1: 0=Aus, &ne;0=Ein
E2: Verzögerung für Ein (&ne;0) in Millisekunden (Änderungen an E2 werden während(!) einer laufenden Verzögerung ignoriert)
E3: Verzögerung für Aus (0) in Millisekunden (Änderungen an E3 werden während(!) einer laufenden Verzögerung ignoriert)
A1: nach Ablauf der Verzögerungszeit wird 0 oder 1 ausgegeben
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[1]['value']!=0 && $E[1]['refresh']==1) {

			$V[1]=getMicrotime()+($E[2]['value']/1000);
			$V[2]=1;
			logic_setVar($id,1,$V[1]);
			logic_setVar($id,2,$V[2]);	//Modus: Ein-Verzögerung
			logic_setState($id,1,$E[2]['value']);

		} else if ($E[1]['value']==0 && $E[1]['refresh']==1) {

			$V[1]=getMicrotime()+($E[3]['value']/1000);
			$V[2]=0;
			logic_setVar($id,1,$V[1]);
			logic_setVar($id,2,$V[2]);	//Modus: Aus-Verzögerung
			logic_setState($id,1,$E[3]['value']); 

		}

		if (logic_getState($id)==1) {

			if ($V[2]==1 && getMicrotime()>=$V[1]) { //Zeit 1 abgelaufen?
				logic_setOutput($id,1,1);
				logic_setState($id,0);
			}

			if ($V[2]==0 && getMicrotime()>=$V[1]) { //Zeit 0 abgelaufen?
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
