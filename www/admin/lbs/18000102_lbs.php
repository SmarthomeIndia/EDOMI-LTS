###[DEF]###
[name		=Prozess-Status: SYSINFO	]

[e#1 TRIGGER=Trigger 			]

[a#1		=CPU						]
[a#2		=Last						]
[a#3		=RAM						]
[a#4		=HDD						]
[a#5		=PHP						]
[a#6		=HTTP						]
[a#7		=DB-Tables					]
[a#8		=PROC-RAM					]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur <link>Statusseite***0-0</link> entnommen werden.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: CPU-Auslastung in Prozent
A2: Systemlast (Load) als FLOAT-Wert
A3: RAM-Auslastung in Prozent
A4: HDD-Auslastung in Prozent
A5: Anzahl der aktuellen PHP-Prozesse
A6: Anzahl der aktuellen HTTP-Prozesse
A7: Anzahl der aktuell geöffneten Datenbanken
A8: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {

			$r=procStatus_getData(2);
			logic_setOutput($id,1,$r[0]);
			logic_setOutput($id,2,$r[3]);
			logic_setOutput($id,3,$r[1]);
			logic_setOutput($id,4,$r[2]);
			logic_setOutput($id,5,$r[6]);
			logic_setOutput($id,6,$r[7]);
			logic_setOutput($id,7,$r[8]);
			logic_setOutput($id,8,$r[20]);

		}			
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
