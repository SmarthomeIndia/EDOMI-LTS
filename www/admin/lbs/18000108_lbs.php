###[DEF]###
[name		=Prozess-Status: DVR	]

[e#1 TRIGGER=Trigger 			]

[a#1		=Status						]
[a#2		=Kameras					]
[a#3		=Kameras: aufnehmend		]
[a#4		=Bilder/h					]
[a#5		=Fehler: Kameras			]
[a#6		=Fehler: Bilder				]
[a#7		=HDD						]
[a#8		=PROC-RAM					]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur <link>Statusseite***0-0</link> entnommen werden.

<b>Hinweis:</b>
Wenn das Modul <link>Digitaler Videorekorder***a-1-5</link> (DVR) nicht aktiviert bzw. konfiguriert ist, erfolgt keine Reaktion an den Ausgängen.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Status: 1=DVR ist aufnahmebereit, 0=keine Aufnahme möglich
A2: Anzahl der zur Aufnahme verfügbaren Kameras
A3: Anzahl der aktuell aufnehmenden Kameras
A4: Anzahl der aufgenommenen Bilder innerhalb der aktuellen Stunde (seit Start)
A5: Anzahl der Kameras, deren Aufnahme fehlgeschlagen ist (z.B. Verbindungsprobleme)
A6: Gesamtanzahl aller fehlgeschlagenen Bild-Aufnahmen (seit Start)
A7: aktuell genutzte Speicherkapazität (in %) des Aufnahme-Pfads
A8: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {

			$r=procStatus_getData(8);
			if (!isEmpty($r[0])) {
				logic_setOutput($id,1,$r[0]);
				logic_setOutput($id,2,$r[1]);
				logic_setOutput($id,3,$r[2]);
				logic_setOutput($id,4,$r[3]);
				logic_setOutput($id,5,$r[4]);
				logic_setOutput($id,6,$r[5]);
				logic_setOutput($id,7,$r[6]);
				logic_setOutput($id,8,$r[20]);
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
