###[DEF]###
[name		=ODER-Gatter 20-fach	]
[titel		=ODER				]

[e#1 TRIGGER=#init=0				]
[e#2 TRIGGER=#init=0				]
[e#3 TRIGGER=#init=0				]
[e#4 TRIGGER=#init=0				]
[e#5 TRIGGER=#init=0				]
[e#6 TRIGGER=#init=0				]
[e#7 TRIGGER=#init=0				]
[e#8 TRIGGER=#init=0				]
[e#9 TRIGGER=#init=0				]
[e#10 TRIGGER=#init=0				]
[e#11 TRIGGER=#init=0				]
[e#12 TRIGGER=#init=0				]
[e#13 TRIGGER=#init=0				]
[e#14 TRIGGER=#init=0				]
[e#15 TRIGGER=#init=0				]
[e#16 TRIGGER=#init=0				]
[e#17 TRIGGER=#init=0				]
[e#18 TRIGGER=#init=0				]
[e#19 TRIGGER=#init=0				]
[e#20 TRIGGER=#init=0				]

[a#1		=					]
[a#2		=+					]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet ein ODER-Gatter nach.

Wenn mindestens einer der Eingänge mit einem Wert &ne;0 belegt sind, wird A1=1 gesetzt. Sind alle(!) Eingänge =0 wird A1=0 gesetzt.
A2 wird auf die Anzahl der Eingänge gesetzt, die mit einem Wert &ne;0 belegt sind.

Jedes neue Telegramm an einem Eingang triggert den Baustein und führt dazu, dass A1 und A2 auf den entsprechenden Wert gesetzt werden.

E1..E20: Signal
A1: Ergebnis der ODER-Verknüpfung (0 oder 1)
A2: Anzahl der Eingänge &ne;0 (0..20)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		$tmp=0;
		for ($t=1;$t<=20;$t++) {
			$tmp+=(($E[$t]['value']!=0)?1:0);
		}
		logic_setOutput($id,1,(($tmp>0)?1:0));
		logic_setOutput($id,2,$tmp);
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
