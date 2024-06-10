###[DEF]###
[name		=Eingangsbox 2-fach		]
[titel		=Eingangsbox			]

[e#1		=					]
[e#2		=					]

[a#1		=					]
[a#2		=					]
###[/DEF]###


###[HELP]###
Eingangsboxen können zur Strukturierung einer Logik eingesetzt werden, sind jedoch nicht erforderlich (die Eingänge anderer Bausteine können auch direkt mit einem KO belegt werden).
Eine Eingangsbox kann aber eine bessere Übersichtlichkeit bzw. mehr Komfort bereitstellen, z.B. falls ein KO an diversen Eingängen anliegt, kann das KO ggf. bequemer ausgetauscht werden.

Jeder Eingang einer Eingangsbox kann mit einem KO belegt werden. Dieses KO steht am entsprechenden Ausgang zur Verfügung und kann mit den Eingängen anderer Bausteine verbunden werden.

Ein mit einem Ausgang einer Eingangsbox verbundener Eingang (eines anderen Bausteins) verhält sich exakt so, als ob das KO direkt mit diesem Eingang verknüpft wäre.

<b>Hinweis:</b>
Im Live-Projekt werden intern keine Eingangsboxen verwendet und die KOs direkt mit den Eingängen der entsprechenden Bausteine verknüpft. Es macht also technisch keinen Unterschied, ob Eingangsboxen verwendet werden oder nicht. Jedoch ist das Setzen eines Wertes in der Live-Ansicht des Logikeditors u.U. nur eingeschränkt möglich.

Eingänge (ohne Beschriftung): KO
A1..A2: stellt den KO-Wert des entsprechenden Eingangs bereit
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {logic_setOutput($id,1,$E[1]['value']);}
		if ($E[2]['refresh']==1) {logic_setOutput($id,2,$E[2]['value']);}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
