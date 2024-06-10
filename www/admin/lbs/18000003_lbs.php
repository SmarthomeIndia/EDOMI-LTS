###[DEF]###
[name		=String zerteilen 10-fach		]
[titel		=String zerteilen				]

[e#1 TRIGGER=Trigger 	]
[e#2		=Separator #init=; 	]

[a#1		=String 1		]
[a#2		=String 2 		]
[a#3		=String 3		]
[a#4		=String 4		]
[a#5		=String 5		]
[a#6		=String 6		]
[a#7		=String 7		]
[a#8		=String 8		]
[a#9		=String 9		]
[a#10		=String 10		]
[a#11		=Reststring		]
###[/DEF]###


###[HELP]###
Dieser Baustein zerteilt einen String in maximal 10 einzelne Teil-Strings (Werte) und legt diese auf die Ausgänge A1..A10 (und ggf. A11).

Die Teil-Strings müssen durch ein oder mehrere Zeichen (E2) voneinander abgetrennt sein, standardmäßig wird ein Semikolon erwartet (z.B. "Wert1;Wert2;Wert3").

Enthält der String an E1 mehr als 10 Teil-Strings, wird der restliche String unverändert an A11 ausgegeben.

Es werden stets nur die erforderlichen Ausgänge gesetzt (z.B. A1..A3, wenn E1="a;b;c") - alle anderen Ausgänge bleiben unverändert.

<h3>Beispiele</h3>
E1="a;b;c" führt zu A1="a", A2="b", A3="c"
E1="a;b;c;" führt zu A1="a", A2="b", A3="c", A4=""
E1="1;2;3;4;5;6;7;8;9;10;a;b;c" führt zu A1="1", A2="2"...A10="10", A11="a;b;c" (Restwert)


<h2>Ein- und Ausgänge</h2>
E1: Ein String &ne;[leer] triggert den Baustein
E2: Trenn-Zeichenkette (oder einzelnes Zeichen) der einzelnen Teil-Strings (darf nicht [leer] sein)

A1..A10: die ermittelten Teil-Strings 
A11: verbleibender Rest-String, falls mehr als 10 Teil-Strings vorhanden sind
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
	
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1 && !isEmpty($E[2]['value'])) {
			$n=explode($E[2]['value'],$E[1]['value'],11);
			for ($t=0;$t<count($n);$t++) {
				logic_setOutput($id,($t+1),$n[$t]);
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
