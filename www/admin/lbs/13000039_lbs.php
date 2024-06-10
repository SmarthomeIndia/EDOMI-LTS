###[DEF]###
[name		=Flankendetektor		]

[e#1 TRIGGER=			#init=0		]

[a#1		=positiv					]
[a#2		=negativ					]

[v#1		=						] Vergleichswert
[v#2		=1						] 1=erster Start
###[/DEF]###


###[HELP]###
Ein neues Telegramm an E1 wird mit dem vorherigen Zustand von E1 verglichen. Ist das Telegramm größer, wird A1=1 gesetzt. Ist das Telegramm kleiner, wird A2=1 gesetzt.

Beim ersten Start des Bausteins liegt noch kein Vergleichswert vor, daher kann auch keine Flanke bestimmt werden. 
Ein Initialwert an E1 wird nicht(!) als erster Vergleichswert herangezogen, sondern dient ggf. als Vergleichswert für das als nächstes eintreffende Telegramm an E1.
Der erste Wert an E1 (auch ein Initialwert) dient immer(!) als Vergleichswert für das nachfolgende Telegramm an E1. 

E1: Signal
A1: 1=steigende (positive) Flanke
A2: 1=fallende (negative) Flanke
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {


		if ($E[1]['refresh']==1) {

			if ($V[2]==1) {

				//erster Start: Vergleichswert = E1 setzen
				$V[1]=$E[1]['value'];
				logic_setVar($id,1,$V[1]);
				logic_setVar($id,2,0);

			} else {

				if ($E[1]['value']>$V[1]) {

					logic_setVar($id,1,$E[1]['value']);
					logic_setOutput($id,1,1);

				} else if ($E[1]['value']<$V[1]) {

					logic_setVar($id,1,$E[1]['value']);
					logic_setOutput($id,2,1);

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
