###[DEF]###
[name        =Addition: &ne;&#91;leer&#93; 5-fach            ]

[e#1 TRIGGER=A&ne;&#91;leer&#93;                ]
[e#2 TRIGGER=B&ne;&#91;leer&#93;                ]
[e#3 TRIGGER=C&ne;&#91;leer&#93;                ]
[e#4 TRIGGER=D&ne;&#91;leer&#93;                ]
[e#5 TRIGGER=E&ne;&#91;leer&#93;                ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein addiert maximal 5 Werte (E1..E5), sobald <i>sämtliche</i> Werte an E1..E5 &ne;[leer] sind. Jedes neue Telegramm an E1..E5 triggert den Baustein.

Wenn E1..E5 keine Zahlen sind, entspricht dies dem Wert 0.

Eine Kaskadierung ist ohne Weiteres möglich, da A1 nur gesetzt wird wenn die o.g. Bedingungen erfüllt sind.

Wichtig:
Nicht benötigte Eingänge müssen auf den Wert 0 gesetzt werden (Initialwert), da sonst die o.g. Bedingung niemals erfüllt sein wird.

Beispiel:
Dieser Baustein kann z.B. eingesetzt werden, um die Werte verschiedener GAs (die z.B. bei einem Initscan zu verschiedenen Zeitpunkten eintreffen) zu addieren und das Ergebnis erst dann auszugeben, wenn alle erforderlichen GAs eingelesen wurden.


E1..E5: Wert A..E
A1: E1+E2+E3+E4+E5 (A+B+C+D+E) - sofern die o.g. Bedingungen erfüllt sind (ansonsten wird A1 nicht verändert)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        $ok = 0;
        $tmp = 0;
        for ($t = 1; $t <= 5; $t++) {
            if (!isEmpty($E[$t]['value'])) {
                $ok++;
                $tmp += $E[$t]['value'];
            }
        }

        if ($ok == 5) {
            logic_setOutput($id, 1, $tmp);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
