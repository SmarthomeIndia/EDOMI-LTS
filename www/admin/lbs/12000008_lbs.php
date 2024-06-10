###[DEF]###
[name		=Requestauslöser		]

[e#1 TRIGGER=GA					]

[a#1		=			]
###[/DEF]###


###[HELP]###
Setzt A1=1, wenn ein Read-Request an E1 eintrifft:
E1 muss mit einer KNX-Gruppenadresse (kein interes KO) belegt sein, die die Eigenschaft "Reaktion auf Read-Request: Logik triggern" besitzt (Konfiguration). Sobald diese GA von einem Busteilnehmer abgefragt wird (Read-Request), wird E1 getriggert und A1=1 gesetzt.

Mit Hilfe dieses Bausteins können Read-Request an(!) EDOMI gesendet werden. A1 kann z.B. mit einer Ausgangsbox verbunden werden, die dann die gleiche GA wie an E1 auf einen Antwort-Wert setzt. Eine Logik-Schleife entsteht hierdurch nicht, da der Baustein nur bei eintreffenden Read-Requests getriggert wird.

E1: Hier muss stets eine KNX-Gruppenadresse (kein interes KO) mit der Eigenschaft "Reaktion auf Read-Request: Logik triggern" anliegen (ein Read-Request an(!) EDOMI startet den Baustein)
A1: bei einem eintreffenden Read-Request an die GA (E1) wird dieser Ausgang auf 1 gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['value']!=0 && $E[1]['refresh']==1) {
			logic_setOutput($id,1,1);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
