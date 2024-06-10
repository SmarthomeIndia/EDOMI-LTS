###[DEF]###
[name		=Wertauslöser			]

[e#1 TRIGGER=Trigger 				]
[e#2		=Wert					]

[a#1		=			]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 auf den Wert an E2, sobald an E1 ein neues Telegramm &ne;0 eintrifft.

Anwendungsbeispiele:
- es kann z.B. verhindert werden, dass bei einem Vergleich eines KO-Wertes (welcher ggf. durch den Vergleich verändert werden soll) eine Schleife entsteht
- ein Wert (KO oder Logik) kann z.B. in ein Archiv geschrieben werden, sobald ein Ereignis E1 triggert

E1: Trigger &ne;0
E2: auf diesen Wert wird A1 gesetzt

A1: Wert von E2
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
			logic_setOutput($id,1,$E[2]['value']);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
