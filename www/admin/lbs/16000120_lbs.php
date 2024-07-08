###[DEF]###
[name        =Zeitdifferenz            ]

[e#1 TRIGGER=                        ]

[a#1        =&Delta;T                ]
[a#2        =AVG                    ]

[v#1        =                        ]
[v#2        =                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet die Zeitdifferenz (und deren Durchschnittswert) von nacheinander eintreffenden Telegrammen.

Das erste Telegramm &ne;[leer] an E1 startet die Messung, jedes weitere Telegramm &ne;[leer] an E1 führt zur Ausgabe der Zeitdifferenz (bzw. Durchschnittswert) zum vorherigen Telegramm.

E1: jedes Telegramm &ne;[leer] führt zur Ausgabe der Zeitdifferenz (bzw. Durchschnittswert) zum vorherigen Telegramm (mit Ausnahme des ersten Telegramms nach einem Neustart)
A1: Zeitdifferenz (Sekunden, FLOAT)
A2: durchschnittliche Zeitdifferenz (Sekunden, FLOAT)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{

    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[1]['refresh'] == 1 && !isEmpty($E[1]['value'])) {
            if (!isEmpty($V[1])) {
                $tmp = getMicrotime() - $V[1];
                logic_setOutput($id, 1, $tmp);

                if (isEmpty($V[2])) {
                    $V[2] = $tmp;
                } else {
                    $V[2] = ($V[2] + $tmp) / 2;
                }
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 2, $V[2]);
            }

            $V[1] = getMicrotime();
            logic_setVar($id, 1, $V[1]);
        }

    }

}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
