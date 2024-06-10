###[DEF]###
[name		=Inverter	]

[e#1 TRIGGER=				]

[a#1		=				]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 auf den invertierten Wert an E1.
Jedes neue Telegramm &ne;[leer] an E1 triggert den Baustein.

E1: E1=0: A1 wird auf 1 gesetzt, E1&ne;0: A1 wird auf 0 gesetzt
A1: invertierter Wert
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			if (!isEmpty($E[1]['value'])) {
				if ($E[1]['value']!=0) {
					logic_setOutput($id,1,0);
				} else {
					logic_setOutput($id,1,1);
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
