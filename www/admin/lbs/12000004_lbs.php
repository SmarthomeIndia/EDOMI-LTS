###[DEF]###
[name		=Eingangsbox 12-fach		]
[titel		=Eingangsbox			]

[e#1		=					]
[e#2		=					]
[e#3		=					]
[e#4		=					]
[e#5		=					]
[e#6		=					]
[e#7		=					]
[e#8		=					]
[e#9		=					]
[e#10		=					]
[e#11		=					]
[e#12		=					]

[a#1		=					]
[a#2		=					]
[a#3		=					]
[a#4		=					]
[a#5		=					]
[a#6		=					]
[a#7		=					]
[a#8		=					]
[a#9		=					]
[a#10		=					]
[a#11		=					]
[a#12		=					]
###[/DEF]###


###[HELP]###
Eingangsboxen können zur Strukturierung einer Logik eingesetzt werden, sind jedoch nicht erforderlich (die Eingänge anderer Bausteine können auch direkt mit einem KO belegt werden).
Eine Eingangsbox kann aber eine bessere Übersichtlichkeit bzw. mehr Komfort bereitstellen, z.B. falls ein KO an diversen Eingängen anliegt, kann das KO ggf. bequemer ausgetauscht werden.

Jeder Eingang einer Eingangsbox kann mit einem KO belegt werden. Dieses KO steht am entsprechenden Ausgang zur Verfügung und kann mit den Eingängen anderer Bausteine verbunden werden.

Ein mit einem Ausgang einer Eingangsbox verbundener Eingang (eines anderen Bausteins) verhält sich exakt so, als ob das KO direkt mit diesem Eingang verknüpft wäre.

<b>Hinweis:</b>
Im Live-Projekt werden intern keine Eingangsboxen verwendet und die KOs direkt mit den Eingängen der entsprechenden Bausteine verknüpft. Es macht also technisch keinen Unterschied, ob Eingangsboxen verwendet werden oder nicht. Jedoch ist das Setzen eines Wertes in der Live-Ansicht des Logikeditors u.U. nur eingeschränkt möglich.

Eingänge (ohne Beschriftung): KO
A1..A12: stellt den KO-Wert des entsprechenden Eingangs bereit
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {logic_setOutput($id,1,$E[1]['value']);}
		if ($E[2]['refresh']==1) {logic_setOutput($id,2,$E[2]['value']);}
		if ($E[3]['refresh']==1) {logic_setOutput($id,3,$E[3]['value']);}
		if ($E[4]['refresh']==1) {logic_setOutput($id,4,$E[4]['value']);}
		if ($E[5]['refresh']==1) {logic_setOutput($id,5,$E[5]['value']);}
		if ($E[6]['refresh']==1) {logic_setOutput($id,6,$E[6]['value']);}
		if ($E[7]['refresh']==1) {logic_setOutput($id,7,$E[7]['value']);}
		if ($E[8]['refresh']==1) {logic_setOutput($id,8,$E[8]['value']);}
		if ($E[9]['refresh']==1) {logic_setOutput($id,9,$E[9]['value']);}
		if ($E[10]['refresh']==1) {logic_setOutput($id,10,$E[10]['value']);}
		if ($E[11]['refresh']==1) {logic_setOutput($id,11,$E[11]['value']);}
		if ($E[12]['refresh']==1) {logic_setOutput($id,12,$E[12]['value']);}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
