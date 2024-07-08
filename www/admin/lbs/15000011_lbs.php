###[DEF]###
[name        =Mittelwert/Abweichung: &ne;&#91;leer&#93; 10-fach            ]

[e#1 TRIGGER=A&ne;&#91;leer&#93;                ]
[e#2 TRIGGER=B&ne;&#91;leer&#93;                ]
[e#3 TRIGGER=C&ne;&#91;leer&#93;                ]
[e#4 TRIGGER=D&ne;&#91;leer&#93;                ]
[e#5 TRIGGER=E&ne;&#91;leer&#93;                ]
[e#6 TRIGGER=F&ne;&#91;leer&#93;                ]
[e#7 TRIGGER=G&ne;&#91;leer&#93;                ]
[e#8 TRIGGER=H&ne;&#91;leer&#93;                ]
[e#9 TRIGGER=I&ne;&#91;leer&#93;                ]
[e#10 TRIGGER=J&ne;&#91;leer&#93;                ]

[a#1        =Mittelwert                    ]
[a#2        =Abweichung                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet den Mittelwert aus maximal 10 Werten (E1..E10), sobald
<i>sämtliche</i> Werte an E1..E10 &ne;[leer] sind. Jedes neue Telegramm an E1..E10 triggert den Baustein.
Zudem wird die mittlere absolute Abweichung vom Mittelwert an A2 ausgegeben.

Wenn E1..E10 keine Zahlen sind, entspricht dies dem Wert 0.

Eine Kaskadierung ist ohne Weiteres möglich, da A1/A2 nur gesetzt werden wenn die o.g. Bedingungen erfüllt sind.

Wichtig:
Nicht benötigte Eingänge müssen auf den Wert "*" gesetzt werden (Initialwert), da sonst die o.g. Bedingung niemals erfüllt sein wird.

Beispiel:
Dieser Baustein kann z.B. eingesetzt werden, um den Mittelwert verschiedener Temperatursensoren (die z.B. bei einem Initscan zu verschiedenen Zeitpunkten eintreffen) zu bilden und das Ergebnis erst dann auszugeben, wenn alle erforderlichen Werte eingelesen wurden.
Mit Hilfe von A2 (Abweichung vom Mittelwert) kann z.B. festgestellt werden, ob die Temperatur eines Sensors ungewöhnlich hoch ist (im Vergleich zum Mittelwert) - dies könnte z.B. auf ein Feuer hinweisen.


E1..E10: Wert A..J

A1: Mittelwert aus A..J, sofern die o.g. Bedingungen erfüllt sind (ansonsten wird A1 nicht verändert)
A2: mittlere absolute Abweichung vom Mittelwert aus A..J (sofern ein Mittelwert berechnet und ausgegeben wurde)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        $ok = 0;
        $anz = 0;
        $tmp = 0;
        for ($t = 1; $t <= 10; $t++) {
            if (!isEmpty($E[$t]['value'])) {
                $ok++;
                if ($E[$t]['value'] != '*') {
                    $anz++;
                    $tmp += $E[$t]['value'];
                }
            }
        }

        if ($ok == 10) {
            $avg = $tmp / $anz;
            $avg2 = 0;
            for ($t = 1; $t <= 10; $t++) {
                if (!isEmpty($E[$t]['value']) && $E[$t]['value'] != '*') {
                    $avg2 += abs($E[$t]['value'] - $avg);
                }
            }
            logic_setOutput($id, 1, $avg);
            logic_setOutput($id, 2, $avg2 / $anz);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
