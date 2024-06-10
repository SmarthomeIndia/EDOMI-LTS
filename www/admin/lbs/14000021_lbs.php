###[DEF]###
[name		=UND-Gatter 4-fach		]
[titel		=UND					]

[e#1 TRIGGER=#init=0				]
[e#2 TRIGGER=#init=0				]
[e#3 TRIGGER=#init=0				]
[e#4 TRIGGER=#init=0				]

[a#1		=					]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet ein UND-Gatter nach. Wenn alle(!) Eingänge mit einem Wert &ne;0 belegt sind, wird A1=1 gesetzt. Ist einer der Eingänge =0 wird A1=0 gesetzt.

Jedes neue Telegramm an einem Eingang triggert den Baustein und führt dazu, dass A1 entweder =0 oder =1 gesetzt wird.

E1..E4: Signal
A1: Ergebnis der UND-Verknüpfung (0 oder 1)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1 || $E[2]['refresh']==1 || $E[3]['refresh']==1 || $E[4]['refresh']==1) {
			if (($E[1]['value']!=0) && ($E[2]['value']!=0) && ($E[3]['value']!=0) && ($E[4]['value']!=0)) {
				logic_setOutput($id,1,1);
			} else {
				logic_setOutput($id,1,0);
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
