###[DEF]###
[name		=Klemme 16-fach			]
[titel		=Klemme					]

[e#1 TRIGGER=						]
[e#2 TRIGGER=						]
[e#3 TRIGGER=						]
[e#4 TRIGGER=						]
[e#5 TRIGGER=						]
[e#6 TRIGGER=						]
[e#7 TRIGGER=						]
[e#8 TRIGGER=						]
[e#9 TRIGGER=						]
[e#10 TRIGGER=						]
[e#11 TRIGGER=						]
[e#12 TRIGGER=						]
[e#13 TRIGGER=						]
[e#14 TRIGGER=						]
[e#15 TRIGGER=						]
[e#16 TRIGGER=						]

[a#1		=					]
###[/DEF]###


###[HELP]###
Dieser Baustein dient zum Beschalten eines(!) Baustein-Eingangs mit mehreren KOs oder Ausgängen.

Ein neues(!) Telegramm &ne;[leer] an einem Eingang wird 1:1 an A1 durchgereicht.
Treffen gleichzeitig Telegramme an verschiedenen Eingängen ein, wird der Ausgang stets auf den Wert des Eingangs mit der kleinsten ID gesetzt (z.B. hat E1 Priorität gegenüber E5). 

Hinweis: Im Gegensatz zu einem Oder-Gatter wird nur der Eingang ausgewertet, an dem ein neues(!) Telegramm eingetroffen ist. Die Zustände der anderen Eingänge werden dabei ignoriert.

E1..E16: Signal
A1: Eingangswert
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (!isEmpty($E[16]['value']) && $E[16]['refresh']==1) {logic_setOutput($id,1,$E[16]['value']);}
		if (!isEmpty($E[15]['value']) && $E[15]['refresh']==1) {logic_setOutput($id,1,$E[15]['value']);}
		if (!isEmpty($E[14]['value']) && $E[14]['refresh']==1) {logic_setOutput($id,1,$E[14]['value']);}
		if (!isEmpty($E[13]['value']) && $E[13]['refresh']==1) {logic_setOutput($id,1,$E[13]['value']);}
		if (!isEmpty($E[12]['value']) && $E[12]['refresh']==1) {logic_setOutput($id,1,$E[12]['value']);}
		if (!isEmpty($E[11]['value']) && $E[11]['refresh']==1) {logic_setOutput($id,1,$E[11]['value']);}
		if (!isEmpty($E[10]['value']) && $E[10]['refresh']==1) {logic_setOutput($id,1,$E[10]['value']);}
		if (!isEmpty($E[9]['value']) && $E[9]['refresh']==1) {logic_setOutput($id,1,$E[9]['value']);}
		if (!isEmpty($E[8]['value']) && $E[8]['refresh']==1) {logic_setOutput($id,1,$E[8]['value']);}
		if (!isEmpty($E[7]['value']) && $E[7]['refresh']==1) {logic_setOutput($id,1,$E[7]['value']);}
		if (!isEmpty($E[6]['value']) && $E[6]['refresh']==1) {logic_setOutput($id,1,$E[6]['value']);}
		if (!isEmpty($E[5]['value']) && $E[5]['refresh']==1) {logic_setOutput($id,1,$E[5]['value']);}
		if (!isEmpty($E[4]['value']) && $E[4]['refresh']==1) {logic_setOutput($id,1,$E[4]['value']);}
		if (!isEmpty($E[3]['value']) && $E[3]['refresh']==1) {logic_setOutput($id,1,$E[3]['value']);}
		if (!isEmpty($E[2]['value']) && $E[2]['refresh']==1) {logic_setOutput($id,1,$E[2]['value']);}
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {logic_setOutput($id,1,$E[1]['value']);}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
