###[DEF]###
[name		=Datum/Uhrzeit-Formatierung			]

[e#1 TRIGGER	=Trigger 			]
[e#2 OPTION 	=Format 			]

[a#1		=]
###[/DEF]###


###[HELP]###
Dieser Baustein wird durch ein neues Telegramm &ne;0 an E1 getriggert und setzt A1 auf die aktuellen Datum- und Zeitwerte mit einer individuellen Formatierung (E2).

An E2 muss ein spezieller String angegeben werden, der eine individuell formatierte Ausgabe an A1 definiert (die Ausgabe selbst wird jedoch nur durch E1 getriggert). 

Die nachfolgenden Variabeln werden in dem String an E2 entsprechend ersetzt:
<ul>
	<li><b>&lt;DATUM&gt;</b> : wird durch das aktuelle Datum in der Form "01.02.2020" ersetzt</li>
	<li><b>&lt;WOCHENTAG&gt;</b> : wird durch den aktuelle Wochentag in der Form "Montag" ersetzt</li>
	<li><b>&lt;TAG&gt;</b> : wird durch den aktuellen Tag in der Form "08" ersetzt</li>
	<li><b>&lt;MONAT&gt;</b> : wird durch den aktuellen Monat in der Form "03" ersetzt</li>
	<li><b>&lt;JAHR&gt;</b> : wird durch das aktuelle Jahr in der Form "2020" ersetzt</li>
	<li><b>&lt;KW&gt;</b> : wird durch die aktuelle Kalenderwoche in der Form "07" ersetzt (nach ISO-8601: die Woche beginnt am Montag)</li>
	<li><b>&lt;UHRZEIT&gt;</b> : wird durch die aktuelle Uhrzeit in der Form "12:21:03" ersetzt</li>
	<li><b>&lt;STUNDE&gt;</b> : wird durch die aktuelle Stunde in der Form "03" ersetzt</li>
	<li><b>&lt;MINUTE&gt;</b> : wird durch die aktuelle Stunde in der Form "02" ersetzt</li>
	<li><b>&lt;SEKUNDE&gt;</b> : wird durch die aktuelle Stunde in der Form "01" ersetzt</li>
</ul>

Beispiel:
Der Wert "Es ist &lt;WOCHENTAG&gt; der &lt;TAG&gt;." an E2 führt z.B. zur Ausgabe von "Es ist Freitag der 13." an A1.


E1: &ne;0 = Trigger
E2: &ne;[leer] = String zur Formatierung der Ausgabe an A1 (s.o.)

A1: individuell formatierter String (nur wenn E2 &ne;[leer] ist)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['value']!=0 && $E[1]['refresh']==1 && !isEmpty($E[2]['value'])) {

			global $global_weekdays;
			$t=microtime(true);
				
			$tmp=$E[2]['value'];
			$tmp=str_replace('<DATUM>',date("d.m.Y",$t),$tmp);
			$tmp=str_replace('<WOCHENTAG>',$global_weekdays[date("N",$t)-1],$tmp);
			$tmp=str_replace('<TAG>',date("d",$t),$tmp);
			$tmp=str_replace('<MONAT>',date("m",$t),$tmp);
			$tmp=str_replace('<JAHR>',date("Y",$t),$tmp);
			$tmp=str_replace('<KW>',date("W",$t),$tmp);
			$tmp=str_replace('<UHRZEIT>',date("H:i:s",$t),$tmp);
			$tmp=str_replace('<STUNDE>',date("H",$t),$tmp);
			$tmp=str_replace('<MINUTE>',date("i",$t),$tmp);
			$tmp=str_replace('<SEKUNDE>',date("s",$t),$tmp);

			logic_setOutput($id,1,$tmp);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
