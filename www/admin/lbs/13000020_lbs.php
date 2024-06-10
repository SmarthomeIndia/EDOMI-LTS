###[DEF]###
[name		=Toggeln mit Status	]

[e#1 TRIGGER=Trigger	]
[e#2		=Wert A #init=0	]
[e#3		=Wert B #init=1	]
[e#4		=Status			]

[a#1		=	]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 auf den Wert an E2 oder E3 in Abhängigkeit vom Wert z.B. eines Status-KOs an E4:

Ist der Wert des Status-KOs an E4 = E2, wird A1 auf den Wert an E3 gesetzt.
Ist der Wert des Status-KOs an E4 &ne; E2, wird A1 auf den Wert an E2 gesetzt.

Dieses "Toggeln" wird bei jeden neuen Telegram &ne;0 an E1 durchgeführt.

Wichtig: Als Vergleichswert dient ausschließlich E2. Ist z.B. E2=0 und E3=1 und E4=999 (also weder =E2 noch =E3), wird A1 auf E2 gesetzt. 

Eine Änderung der Werte an E2, E3 oder E4 triggert den Baustein nicht(!). Der Baustein wird ausschließlich über ein neues Telegramm &ne;0 an E1 getriggert.

E1: Trigger &ne;0
E2: Wert A
E3: Wert B
E4: Vergleichswert, z.B. ein Status-KO

A1: wird bei jedem Trigger (E1) entweder auf den Wert von E2 oder auf den Wert von E3 gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
	
		if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
		
			if ($E[4]['value']==$E[2]['value']) {
				logic_setOutput($id,1,$E[3]['value']);
			} else {
				logic_setOutput($id,1,$E[2]['value']);
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
