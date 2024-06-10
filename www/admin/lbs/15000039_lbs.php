###[DEF]###
[name		=Vergleicher =&#91;Konstante&#93; 5-fach		]
[titel		==&#91;K1..5&#93;?				]

[e#1 TRIGGER= 					]
[e#2 		=K1 				]
[e#3 		=K2 				]
[e#4 		=K3 				]
[e#5 		=K4 				]
[e#6 		=K5 				]

[a#1		=&ne;K1..5			]
[a#2		==K1				]
[a#3		==K2				]
[a#4		==K3				]
[a#5		==K4				]
[a#6		==K5				]
###[/DEF]###


###[HELP]###
Dieser Baustein vergleicht einen Wert an E1 mit den Konstanten K1..K5 (E2..E6). Getriggert wird der Baustein ausschließlich durch ein neues Telegramm &ne;[leer] an E1.

Wenn der Wert an E1 einer der Konstanten K1..K5 entspricht, wird der entsprechende Ausgang A2..A6 auf 1 gesetzt, andernfalls auf 0.

Entspricht der Wert an E1 <i>keiner</i> der Konstanten K1..K5, wird A1 auf den Wert von E1 gesetzt.

Wird ein Eingang E2..E6 auf [leer] gesetzt, wird der entsprechende Eingang ignoriert (d.h. nicht zum Vergleich herangezogen)


E1: ein neues Telegramm &ne;[leer] triggert den Baustein, der Wert an E1 wird mit den Konstanten K1..K5 verglichen
E2..E6: &ne;[leer]: Vergleichswerte (Konstanten, d.h. eine Änderung von E2..E6 triggert den Baustein <i>nicht</i>)

A1: wird auf den Wert an E1 gesetzt, wenn der Wert an E1 <i>keiner</i> der Konstanten K1..K5 entspricht
A2..A6: wird auf 1 gesetzt, wenn der Wert an E1 einer der Konstanten K1..K5 entspricht (andernfalls auf 0, sofern der entsprechende Eingang E2..E6 nicht deaktiviert ist)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1 && !isEmpty($E[1]['value'])) {
			$tmp=false;
			for ($t=2;$t<7;$t++) {
				if ($E[1]['value']==$E[$t]['value'] && !isEmpty($E[$t]['value'])) {
					$tmp=true;
					logic_setOutput($id,$t,1);
				} else if (!isEmpty($E[$t]['value'])) {
					logic_setOutput($id,$t,0);
				}
			}		
			if (!$tmp) {
				logic_setOutput($id,1,$E[1]['value']);
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
