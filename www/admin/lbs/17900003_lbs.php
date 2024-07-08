###[DEF]###
[name            =    Bewässerung]

[e#1    TRIGGER    =    Bewässern                                    ]
[e#2    TRIGGER    =    Manuell                                        ]
[e#3    OPTION    =    Schwelle: Aus                    #init=0        ]
[e#4    OPTION    =    Schwelle: Ein                    #init=0        ]
[e#5    OPTION    =    min. Bewässerungszeit (Minuten)    #init=0        ]
[e#6    OPTION    =    max. Bewässerungszeit (Minuten)    #init=0        ]
[e#7    OPTION    =    min. Bewässerungsmenge (Liter)    #init=0        ]
[e#8    OPTION    =    max. Bewässerungsmenge (Liter)    #init=0        ]
[e#9            =    Bodenfeuchte                                ]
[e#10            =    Literimpuls                                    ]

[a#1            =    Ventil                            ]
[a#2            =    Bewässerungszeit                ]
[a#3            =    Bewässerungsmenge                ]
[a#4            =    Status                            ]

[v#1            =0                        ]
[v#2            =0                        ]
[v#3            =0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein regelt z.B. die Gartenbewässerung über ein entsprechendes Ventil und einem Bodenfeuchtesensor.

<h3>Bedingte Bewässerung (E1)</h3>
Mit E1&ne;0 wird die Bewässung gestartet, sofern die Bodenfeuchte (E9) kleiner/gleich dem Ein-Schwellenwert (E4) ist und nicht bereits eine durch E2 getriggerte Bewässerung stattfindet (s.u.). Andernfalls wird das Telegramm ignoriert.

Nachdem die Bewässerung erfolgreich gestartet wurde, kann diese mit E1=0 jederzeit beendet werden. Im Normalfall wird die Bewässerung jedoch automatisch beendet, sobald folgende Bedingungen erfüllt sind:
<ul>
    <li>die Mindest-Bewässerungzeit (E5) oder die Mindest-Bewässerungsmenge (E7) ist erreicht worden und die Bodenfeuchte (E9) ist größer/gleich dem
        Aus-Schwellenwert (E3)
    </li>
    <li>die Maximal-Bewässerungzeit (E6) oder die Maximal-Bewässerungsmenge (E8) ist erreicht worden</li>
</ul>

Nach dem Beenden der Bewässerung wird A2 auf die Dauer der erfolgten Bewässerung gesetzt (Minuten), A3 wird ggf. auf die Menge der erfolgten Bewässerung gesetzt (Liter).
Erst jetzt kann ggf. eine erneute Bewässung (E1/E2) getriggert werden.


<h3>Erzwungene Bewässerung (E2)</h3>
Mit E2&ne;0 wird die Bewässung zwangsweise gestartet (unabhängig von der Bodenfeuchte), sofern nicht bereits eine durch E1 getriggerte Bewässerung stattfindet (s.o.). Andernfalls wird das Telegramm ignoriert.
Nachdem die erzwungene Bewässerung erfolgreich gestartet wurde, kann diese mit E2=0 jederzeit beendet werden. Die Bewässerung wird automatisch beendet, sobald die folgende Bedingung erfüllt ist:
<ul>
    <li>die Maximal-Bewässerungzeit (E6) oder die Maximal-Bewässerungsmenge (E8) ist erreicht worden</li>
</ul>

Nach dem Beenden der erzwungenen Bewässerung wird A2 auf die Dauer der erfolgten Bewässerung gesetzt (Minuten), A3 wird ggf. auf die Menge der erfolgten Bewässerung gesetzt (Liter).
Erst jetzt kann ggf. eine erneute Bewässung (E1/E2) getriggert werden.


<h3>Bodenfeuchte und Literimpuls</h3>
An E9 wird ein analoger Bodenfeuchtesensor erwartet, der bei geringer Feuchtigkeit einen kleinen Wert ausgibt und bei hoher Feuchtigkeit einen großen Wert.
An E10 wird ggf. (sofern die Mengenbeschränkungen benötigt werden) ein Literimpuls erwartet, d.h. bei jedem verbrauchten Liter muss an E10 eine 1 anliegen.


<h3>Ein-/Ausgänge</h3>
E1: &ne;0 = starten, 0 = beenden
E2: &ne;0 = starten, 0 = beenden
E3: Ausschalt-Schwelle für die Bodenfeuchte
E4: Einschalt-Schwelle für die Bodenfeuchte
E5: Mindest-Bewässerungszeit (0=deaktiviert)
E6: Maximal-Bewässerungszeit (0=deaktiviert)
E7: Mindest-Bewässerungsmenge (0=deaktiviert)
E8: Maximal-Bewässerungsmenge (0=deaktiviert)
E9: Bodenfeuchte
E10: Literimpuls

A1: Ventilsteuerung (1=öffnen, 0=schließen)
A2: letzte Bewässerungsdauer (Minuten mit einer Nachkommastelle)
A3: letzte Bewässerungsmenge
A4: aktueller Status: 0=keine Bewässerung, 1=bedingte Bewässerung (E1&ne;0), 2=manuelle Bewässerung (E2&ne;0)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[1]['refresh'] == 1) {
            if ($E[1]['value'] != 0) {
                if ($V[3] == 0 && $E[9]['value'] <= $E[4]['value']) {
                    $V = LB_LBSID_switchOn($id, $V, 1);
                }
            } else if ($V[3] == 1) {
                $V = LB_LBSID_switchOff($id, $V);
            }
        }

        if ($E[2]['refresh'] == 1) {
            if ($E[2]['value'] != 0) {
                if ($V[3] == 0) {
                    $V = LB_LBSID_switchOn($id, $V, 2);
                }
            } else if ($V[3] == 2) {
                $V = LB_LBSID_switchOff($id, $V);
            }
        }

        if (logic_getState($id) == 1) {
            //Literimpuls
            if ($E[10]['value'] != 0 && $E[10]['refresh'] == 1) {
                $V[2]++;
                logic_setVar($id, 2, $V[2]);
            }

            //Maximalzeit oder(!) Maximalmenge erreicht?
            if (($E[6]['value'] > 0 && (getMicrotime() - $V[1]) / 60 >= $E[6]['value']) || ($E[8]['value'] > 0 && $V[2] >= $E[8]['value'])) {
                $V = LB_LBSID_switchOff($id, $V);

                //nur Automatik: Aus-Schwellenwert erreicht und Mindestzeit oder(!) Mindestmenge erreicht (oder beides deaktiviert)?
            } else if ($V[3] == 1 && $E[9]['value'] >= $E[3]['value'] && (($E[5]['value'] == 0 && $E[7]['value'] == 0) || ($E[5]['value'] > 0 && (getMicrotime() - $V[1]) / 60 >= $E[5]['value']) || ($E[7]['value'] > 0 && $V[2] >= $E[7]['value']))) {
                $V = LB_LBSID_switchOff($id, $V);
            }
        }

    }
}

function LB_LBSID_switchOn($id, $V, $mode)
{
    $V[1] = getMicrotime();
    $V[2] = 0;
    $V[3] = $mode;
    logic_setVar($id, 1, $V[1]);
    logic_setVar($id, 2, $V[2]);
    logic_setVar($id, 3, $V[3]);

    logic_setOutput($id, 1, 1);
    logic_setOutput($id, 4, $mode);
    logic_setState($id, 1, 1000, true);

    return $V;
}

function LB_LBSID_switchOff($id, $V)
{
    $V[3] = 0;
    logic_setVar($id, 3, $V[3]);

    logic_setOutput($id, 1, 0);
    logic_setOutput($id, 2, round((getMicrotime() - $V[1]) / 60, 1));
    logic_setOutput($id, 3, $V[2]);
    logic_setOutput($id, 4, 0);
    logic_setState($id, 0);

    return $V;
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
