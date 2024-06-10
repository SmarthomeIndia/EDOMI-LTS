###[DEF]###
[name		=Vergleicher &ne;&#91;leer&#93;		]
[titel		=&ne;&#91;leer&#93;?				]

[e#1 TRIGGER= 				]

[a#1		=					]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 auf den Wert an E1, sofern der Wert an E1 &ne;[leer] ist.

E1: jedes Telegramm &ne;[leer] triggert den Baustein
A1: wird auf den Wert an E1 gesetzt, sofern der Wert an E1 &ne;[leer] ist
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			if (!isEmpty($E[1]['value'])) {
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
