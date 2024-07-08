###[DEF]###
[name        =Ein-/Ausgangsmatrix 10-fach]
[titel        =Ein-/Ausgangsmatrix        ]

[e#1 TRIGGER=                ]
[e#2 TRIGGER=                ]
[e#3 TRIGGER=                ]
[e#4 TRIGGER=                ]
[e#5 TRIGGER=                ]
[e#6 TRIGGER=                ]
[e#7 TRIGGER=                ]
[e#8 TRIGGER=                ]
[e#9 TRIGGER=                ]
[e#10 TRIGGER=                ]
[e#11        =Eingang                ]
[e#12        =Ausgang                ]

[a#1        =            ]
[a#2        =            ]
[a#3        =            ]
[a#4        =            ]
[a#5        =            ]
[a#6        =            ]
[a#7        =            ]
[a#8        =            ]
[a#9        =            ]
[a#10        =            ]
###[/DEF]###


###[HELP]###
Dieser Baustein übergibt einen Eingangswert an einen Ausgang. Der Eingang wird mit E11 ausgewählt, der Ausgang mit E12.

Jedes neue(!) Telegramm &ne;[leer] am mittels E11 gewählten Eingang E1..E10 wird an den mittels E12 gewählten Ausgang A1..A10 unverändert weitergegeben.
Die Werte an E11 und E12 müssen jeweils im Bereich 1..10 liegen, ansonsten bleiben alle Ausgänge unverändert.
Eine Änderung von E11 bzw. E12 hat solange keine Auswirkung, bis ein neues(!) Telegramm &ne;[leer] am gewählten Eingang eintrifft.

E1..E10: Signal: Jedes neue(!) Telegramm &ne;[leer] triggert den Baustein
E11: Wahl des Eingangs (1..10)
E12: Wahl des Ausgangs (1..10)

A1..A10: Wird auf den Wert des gewählten Eingangs gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        $E[11]['value'] = intVal($E[11]['value']);
        $E[12]['value'] = intVal($E[12]['value']);
        if ($E[11]['value'] >= 1 && $E[11]['value'] <= 10 && $E[12]['value'] >= 1 && $E[12]['value'] <= 10) {
            if (!isEmpty($E[$E[11]['value']]['value']) && $E[$E[11]['value']]['refresh'] == 1) {
                logic_setOutput($id, $E[12]['value'], $E[$E[11]['value']]['value']);
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
