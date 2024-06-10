###[DEF]###
[name		=Entprellen				]

[e#1 TRIGGER=Trigger	]
[e#2		=Dauer (ms) #init=500		]

[a#1		=				]

[v#1		=500					]
###[/DEF]###


###[HELP]###
Dieser Baustein entprellt ein Signal an E1.

Jedes Telegramm &ne;[leer] an E1 triggert den Baustein. Der Wert an E1 wird unmittelbar an A1 ausgegeben, der Baustein ist anschließend jedoch für die Dauer an E2 gesperrt (währenddessen werden alle Telegramme an E1 ignoriert).

E1: &ne;[leer] = Signal. Jedes weitere Telegramm während der Laufzeit wird ignoriert!
E2: Entprellzeit (ms)
A1: Wert von E1
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if (logic_getState($id)==0) {
		
			if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {
				if ($E[2]['value']<=0) {$E[2]['value']=0;}
				logic_setVar($id,1,(getMicrotime()+($E[2]['value']/1000)));
				logic_setOutput($id,1,$E[1]['value']);
				logic_setState($id,1,$E[2]['value']);
			}
			
		} else {
		
			if (getMicrotime()>=logic_getVar($id,1)) {
				logic_setState($id,0);
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
