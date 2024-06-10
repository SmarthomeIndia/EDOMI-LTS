###[DEF]###
[name		=Prozess-Status: QUEUE	]

[e#1 TRIGGER=Trigger 			]

[a#1		=Befehle: wartend			]
[a#2		=Befehle: laufend			]
[a#3		=PROC-RAM					]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur <link>Statusseite***0-0</link> entnommen werden.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Anzahl der wartenden Queue-Befehle
A2: Anzahl der aktuell laufenden Queue-Befehle (z.B. Auto-Backup, Kamerabild-Archivierung, etc.)
A3: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if (!isEmpty($E[1]['value']) && $E[1]['refresh']==1) {

			$r=procStatus_getData(5);
			logic_setOutput($id,1,$r[0]);
			logic_setOutput($id,2,$r[1]);
			logic_setOutput($id,3,$r[20]);
			
		}			
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
