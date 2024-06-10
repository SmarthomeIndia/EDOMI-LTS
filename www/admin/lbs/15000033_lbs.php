###[DEF]###
[name		=Filter: 0&#x25B8;0		]

[e#1 TRIGGER=				]

[a#1		=						]
###[/DEF]###


###[HELP]###
Dieser Baustein filtert ein neues Telegramm an E1:

Wenn E1&ne;0 ist bleibt A1 unverändert.
Wenn E1=0 ist wird A1=0 gesetzt.

E1: Signal
A1: gefiltertes Signal (s.o.)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['value']==0 && $E[1]['refresh']==1) {
			logic_setOutput($id,1,0);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
