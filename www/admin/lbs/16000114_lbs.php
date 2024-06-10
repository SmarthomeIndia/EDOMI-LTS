###[DEF]###
[name		=Kurz/Lang				]

[e#1 TRIGGER=Trigger	 				]
[e#2		=Dauer: lang (ms) #init=500	]

[a#1		=kurz				]
[a#2		=lang				]

[v#1		=500					]
###[/DEF]###


###[HELP]###
Dieser Baustein unterscheidet zwischen einem langen und einem kurzen Signal &ne;0 an E1.

Wird E1 auf einen Wert &ne;0 gesetzt, beginnt die Zeitmessung. Wird anschließend E1=0 gesetzt, wird entweder A1=1 gesetzt (kurz) oder A2=1 gesetzt (lang).
Wird die Dauer an E2 erreicht, ohne(!) dass E1=0 gesetzt wird, wird dennoch A2=1 gesetzt (lang).

E1: 0=Aus, &ne;0=Ein
E2: Dauer in Millisekunden für eine lange Signaldauer
A1: 1=Kurzes Signal
A2: 1=Langes Signal
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		//Triggern oder Retriggern
		if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
			$V[1]=getMicrotime()+($E[2]['value']/1000);
			logic_setVar($id,1,$V[1]);
			logic_setState($id,1,$E[2]['value']); 
		}

		if (logic_getState($id)==1) {

			if ($E[1]['value']==0 && $E[1]['refresh']==1 && getMicrotime()<$V[1]) { //Zeit ist noch nicht abgelaufen => kurz
				logic_setOutput($id,1,1);
				logic_setState($id,0);
			} else {
				if (getMicrotime()>=$V[1]) { //Zeit ist abgelaufen => lang (E1 ist egal, Zeit ist rum)
					logic_setOutput($id,2,1);
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
