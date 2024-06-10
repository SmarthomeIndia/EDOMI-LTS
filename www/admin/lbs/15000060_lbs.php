###[DEF]###
[name		=Addition A+Konstante			]
[titel		=A+K					]

[e#1 TRIGGER=A 				]
[e#2 		=K  				]

[a#1		=					]
###[/DEF]###


###[HELP]###
Dieser Baustein addiert einen Wert an E1 mit einer Konstante an E2. Nur jedes neue Telegramm an E1 triggert den Baustein, eine Änderung des Wertes an E2 (Konstante) führt nicht zu einer Neuberechnung. Erst wenn ein neues Telegramm an E1 eintrifft, wird der aktuelle Wert an E2 zur Berechnung herangezogen.

Wenn E1 oder E2 keine Zahlen sind, entspricht dies der Zahl 0. Es wird also immer ein gültiges Ergebnis an A1 ausgegeben, sobald an E1 ein neues Telegramm eintrifft.

E1: Trigger: Wert A
E2: Konstante K
A1: E1 + E2 (A+K)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {

			$A=$E[1]['value'];
			if (!is_numeric($A)) {$A=0;}
			$B=$E[2]['value'];
			if (!is_numeric($B)) {$B=0;}

			logic_setOutput($id,1,($A+$B));
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
