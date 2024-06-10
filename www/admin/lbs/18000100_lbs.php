###[DEF]###
[name		=Systemauslastung	]

[e#1 TRIGGER=Trigger 			]
[e#2		=SBC #init=1		]

[a#1		=CPU				]
[a#2		=Last				]
[a#3		=RAM				]
[a#4		=HDD				]

[v#1		=					] SBC A1
[v#2		=					] SBC A2
[v#3		=					] SBC A3
[v#4		=					] SBC A4
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an A1..A4 zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.


E1: jedes Telegramm &ne;[leer] triggert den Baustein
E2: SBC (Send by Change): legt für A1..A4 fest, ob der Ausgang nur bei einer Wertänderung gesetzt werden soll: 0=Ausgang immer setzen, 1=Ausgang nur bei Änderung setzen

A1: CPU-Auslastung in Prozent (ggf. SBC)
A2: Systemlast (Load) als FLOAT-Wert (ggf. SBC)
A3: RAM-Auslastung in Prozent (ggf. SBC)
A4: HDD-Auslastung in Prozent (ggf. SBC)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {
			$r=procStatus_getData(2);
			if ($r!==false) {
				if ($E[2]['value']==0) {
					logic_setOutput($id,1,$r[0]);
					logic_setOutput($id,2,$r[3]);
					logic_setOutput($id,3,$r[1]);
					logic_setOutput($id,4,$r[2]);
				} else {
					if ($V=logic_getVars($id)) {
						if ($r[0]!=$V[1]) {
							logic_setOutput($id,1,$r[0]);
							logic_setVar($id,1,$r[0]);
						}
						if ($r[3]!=$V[2]) {
							logic_setOutput($id,2,$r[3]);
							logic_setVar($id,2,$r[3]);
						}
						if ($r[1]!=$V[3]) {
							logic_setOutput($id,3,$r[1]);
							logic_setVar($id,3,$r[1]);
						}
						if ($r[2]!=$V[4]) {
							logic_setOutput($id,4,$r[2]);
							logic_setVar($id,4,$r[2]);
						}
					}
				}
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
