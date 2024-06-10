###[DEF]###
[name		=Strings verbinden 10-fach		]
[titel		=Strings verbinden				]

[e#1 TRIGGER	=	]
[e#2 TRIGGER	= 	]
[e#3 TRIGGER	= 	]
[e#4 TRIGGER	=	]
[e#5 TRIGGER	= 	]
[e#6 TRIGGER	= 	]
[e#7 TRIGGER	= 	]
[e#8 TRIGGER	=	]
[e#9 TRIGGER	=	]
[e#10 TRIGGER	=	]
[e#11 TRIGGER	=(Trigger) #init=0			]
[e#12 			=Modus 	 #init=0				]
[e#13 			=Separator						]

[a#1		=String		]
[a#2		=Trigger	]
###[/DEF]###


###[HELP]###
Dieser Baustein fügt die Eingangswerte E1..E10 (A..J) zu einem String zusammen.

Optional kann an E13 eine Zeichenkette (oder einzelnes Zeichen) angelegt werden, durch diese werden die einzelnen Eingangswerte abgetrennt. Leere Eingangswerte an E1..E10 werden dabei ignoriert!

Die Verknüpfung zu einem String wird entweder bei jedem neuen Telegramm &ne;[leer] an einem Eingang E1..10 berechnet (Modus E12=0), oder die Verknüpfung erfolgt erst bei einem Telegramm &ne;0 an E11 (Modus E12=1).

E1..E10: Wert (&ne;[leer] Triggert den Baustein, sofern Modus=0 (E12) gesetzt ist)
E11: Trigger (&ne;0 Triggert den Baustein, sofern Modus=1 (E12) gesetzt ist)
E12: Modus: 0=bei jedem Telegramm an E1..10 einen String an A1 ausgeben, 1=String erst an A1 ausgeben wenn an E11 ein Telegramm &ne;0 anliegt
E13: optionale Trenn-Zeichenkette (oder einzelnes Zeichen) zum Trennen der einzelnen Teil-Strings

A1: Ergebnis als String
A2: Trigger: wird auf 1 (Modus=0) bzw. E11 (Modus=1) gesetzt sobald A1 auf einen neuen String gesetzt wurde (z.B. zur Kaskadierung)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
	
		$trigger=false;
		if ($E[12]['value']==0) {
			for ($t=1;$t<=10;$t++) {
				if (!isEmpty($E[$t]['value']) && $E[$t]['refresh']==1) {
					$trigger=true;
					break;
				}
			}
		} else if ($E[11]['value']!=0 && $E[11]['refresh']==1) {
			$trigger=true;
		}
		
		if ($trigger) {
			$n='';
			for ($t=1;$t<=10;$t++) {
				if (!isEmpty($E[$t]['value'])) {$n.=$E[$t]['value'].$E[13]['value'];}
			}
			if (!isEmpty($E[13]['value'])) {$n=rtrim($n,$E[13]['value']);}
			logic_setOutput($id,1,$n);
			if ($E[12]['value']==0) {
				logic_setOutput($id,2,1);
			} else {
				logic_setOutput($id,2,$E[11]['value']);
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
