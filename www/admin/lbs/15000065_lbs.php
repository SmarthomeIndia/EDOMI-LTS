###[DEF]###
[name		=Quantisierung						]

[e#1 TRIGGER	=						]
[e#2 			=Raster #init=1			]

[a#1			=Q						]
[a#2			=(Q)					]
###[/DEF]###


###[HELP]###
Dieser Baustein quantisiert einen Wert an E1.

E2 bestimmt die Schrittweite der Quantisierung. E2 muss stets &gt;0 sein, ansonsten werden die Ausgänge des Bausteins nicht verändert.

Jedes neue Telegramm an E1 triggert den Baustein. Sofern an E1 ein nummerischer Wert anliegt, wird A1 auf den (nächstkleineren) quantisierten Wert gesetzt. A2 wird auf den Wert an E1 gesetzt, jedoch nur wenn der Wert an E1 dem quantisierten Wert entspricht (E1 muss also "ins Raster passen").

Der Wert an E1 muss &ne;[leer] und nummerisch sein, ansonsten wird das Telegramm ignoriert.
Der Wert an E2 muss &gt;0 sein, ansonsten arbeitet der Baustein nicht.


Beispiel:
Ein Wert an E1 soll in ein Raster mit der Schrittweite 5 quantisiert werden (E2=5).
Wird E1=53 gesetzt, wird A1=50 gesetzt und A2 bleibt unverändert.
Wird E1=55 gesetzt, wird A1=55 und A2=55 gesetzt.
Wird E1=57 gesetzt, wird A1=55 gesetzt und A2 bleibt unverändert


Hinweis:
Der Wert 0 an E1 wird stets zur Ausgabe von 0 an A1 und A2 führen (sofern E2 &gt;0 ist).


E1: &ne;[leer] und nummerisch = Trigger
E2: &gt;0 = Schrittweite

A1: wird bei jedem Trigger (E1) auf den quantisierten Wert gesetzt
A2: wird bei jedem Trigger (E1) auf den quantisierten Wert gesetzt, sofern E1 restlos durch E2 teilbar ist

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if ($E[1]['refresh']==1 && !isEmpty($E[1]['value']) && is_numeric($E[1]['value']) && $E[2]['value']>0) {
			$tmp=intval(strval($E[1]['value']/$E[2]['value']))*$E[2]['value'];
			logic_setOutput($id,1,$tmp);		
			if ((string)$tmp===(string)$E[1]['value']) {
				logic_setOutput($id,2,$E[1]['value']);		
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
