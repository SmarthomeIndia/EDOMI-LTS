###[DEF]###
[name		=Binärauslöser			]

[e#1 TRIGGER=Trigger					]

[a#1		=&ne;0		]
[a#2		==0		]
[a#3		=1						]
###[/DEF]###


###[HELP]###
Ein neues Telegramm an E1 wird abhängig von seinem Wert die Ausgänge A1..3 wie folgt setzen: 
Ein Telegramm &ne;0 setzt A1=1, A2 bleibt unverändert. 
Ein Telegramm =0 (oder [leer]) setzt A2=1, A1 bleibt unverändert. 
Jedes beliebige Telegramm setzt unabhängig davon A3=1. 

E1: Signal
A1: 1, wenn E1&ne;0 (A2 bleibt dabei unverändert!)
A2: 1, wenn E1=0 (A1 bleibt dabei unverändert!)
A3: 1, bei jedem Telegramm an E1
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			if ($E[1]['value']!=0) {
				logic_setOutput($id,1,1);
			} else {
				logic_setOutput($id,2,1);
			}
			logic_setOutput($id,3,1);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
