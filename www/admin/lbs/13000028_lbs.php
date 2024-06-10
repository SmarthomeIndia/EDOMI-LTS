###[DEF]###
[name		=Auswahlschalter]

[e#1 TRIGGER=Trigger  	]
[e#2 	=Initial-Status	]

[a#1		=Status	]
[a#2		=Flanke	]

[v#1		=		]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet das Verhalten sogenannter "Radio-Buttons" nach: Ein Trigger &ne;[leer] an E1 setzt den Ausgang A1 auf den entsprechenden Wert an E1.
Wird E1 erneut auf den gleichen Wert wie zuvor gesetzt, bleiben alle Ausgänge unverändert. Eine Änderung findet also nur dann statt, wenn an E1 unterschiedliche Werte eintreffen.

An A2 werden zwei Werte (getrennt durch "/") ausgegeben:
Der erste Wert gibt den vorherigen Status an, der zweite Wert den aktuellen Status.
Ist der interne Status noch nicht definiert (z.B. beim ersten Start ohne Belegung von E2), wird der vorherige Status durch ein "?" repräsentiert (z.B. "?/3").
A2 kann z.B. mit mehreren Vergleichern ausgewertet werden und ermöglicht so eine Reaktion auf unterschiedliche Reihenfolgen der eintreffenden Trigger an E1.

Beispiel:
Der aktuelle Status ist "3". Nun wird E1 auf "1" gesetzt - die Ausgabe an A2 ist dann "3/1" (also ein Wechsel von Status "3" auf Status "1").
Wird nun E1 auf "0" gesetzt, wird A2 auf den Wert "1/0" gesetzt. Wird nun E1 auf "2" gesetzt, wird A2 auf "0/2" gesetzt.


Beim Start von EDOMI ist der interne Statuswert =[leer], d.h. jeder Trigger &ne;[leer] an E1 führt zu einer entsprechenden Reaktion des Bausteins.
An E2 kann bei Bedarf ein Statuswert angelegt werden: Dieser Statuswert wird beim Start von EDOMI vom Baustein einmalig(!) übernommen. Die Ausgänge werden dabei nicht(!) verändert, der Statuswert wird lediglich intern als Vergleichswert abgelegt. 
Beispielsweise kann an E2 ein remanentes KO angelegt werden, das zugleich über A1 stets auf den aktuellen Status gesetzt wird. Bei einem Neustart von EDOMI wird dieser Statuswert dann intern übernommen.


E1: Trigger (jedes neue Telegramm &ne;[leer] triggert den Baustein)
E2: Statuswert-Vorgabe beim Start von EDOMI (bzw. bei leerem internen Statuswert)
A1: aktueller Statuswert
A2: Flanken-Status (s.o.)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if ((!isEmpty($E[1]['value'])) && $E[1]['refresh']==1) {
			$V1=logic_getVar($id,1);

			if ((string)$E[1]['value']!==(string)$V1) {
				logic_setOutput($id,1,$E[1]['value']);
				logic_setOutput($id,2,((isEmpty($V1))?'?':$V1).'/'.$E[1]['value']);			
				logic_setVar($id,1,$E[1]['value']);
			}
		}

		//Status intern beim Start setzen (z.B. durch remanentes KO)
		if ((!isEmpty($E[2]['value'])) && $E[2]['refresh']==1) {
			$V1=logic_getVar($id,1);
			if (isEmpty($V1)) {
				logic_setVar($id,1,$E[2]['value']);
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
