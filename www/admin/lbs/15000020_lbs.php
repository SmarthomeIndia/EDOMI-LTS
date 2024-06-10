###[DEF]###
[name		=Schwellenwert			]

[e#1 TRIGGER=Trigger				]
[e#2		=untere Schwelle		]
[e#3		=obere Schwelle			]

[a#1		=					]
###[/DEF]###


###[HELP]###
Dieser Baustein vergleicht einen Ist-Wert (E1) mit zwei Soll-Werten (E2 und E3). Erreicht (bzw. über- oder unterschreitet) der Ist-Wert einen der Soll-Werte, wird A1 auf 1 bzw. 0 gesetzt.

Wichtig: A1 wird bei jedem(!) Erreichen oder Über- bzw. Unterschreiten der Schwellenwerte gesetzt! Ggf. ist an A1 also ein "Send-By-Change"-Baustein einzusetzen.

E1: Signal
E2: untere Schwelle (ist E1 <= E2, wird A1=0 gesetzt)
E3: obere Schwelle (ist E1 >= E3, wird A1=1 gesetzt)

A1: 0=untere Schwelle erreicht, 1=obere Schwelle erreicht
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1 || $E[2]['refresh']==1 || $E[3]['refresh']==1) {

			if (is_numeric($E[1]['value']) && is_numeric($E[2]['value']) && $E[1]['value']<=$E[2]['value'] && $E[1]['refresh']==1) {
				logic_setOutput($id,1,0);
			}

			if (is_numeric($E[1]['value']) && is_numeric($E[3]['value']) && $E[1]['value']>=$E[3]['value'] && $E[1]['refresh']==1) {
				logic_setOutput($id,1,1);
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
