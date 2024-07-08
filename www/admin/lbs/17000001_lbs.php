###[DEF]###
[name        =    Warmwasser                    ]

[e#1 TRIGGER    =    A: Hygiene                        ]
[e#2 TRIGGER    =    B: Einmalladung                ]
[e#3 TRIGGER    =    C: Einmalladung                ]
[e#4 TRIGGER    =    D: Dauerladung                ]
[e#5 OPTION        =    A: Temperatur        #init=50    ]
[e#6 OPTION        =    B: Temperatur        #init=40    ]
[e#7 OPTION        =    C: Temperatur        #init=45    ]
[e#8 OPTION        =    D: Temperatur        #init=35    ]
[e#9 OPTION        =    Hysterese            #init=2        ]
[e#10 OPTION    =    Timeout (s)            #init=7200    ]
[e#11 TRIGGER    =    Temperatur                        ]
[e#12            =    Aktiviert                        ]

[a#1        =    A: Status        ]
[a#2        =    B: Status        ]
[a#3        =    C: Status        ]
[a#4        =    D: Status        ]
[a#5        =    A: Warm            ]
[a#6        =    B: Warm            ]
[a#7        =    C: Warm            ]
[a#8        =    D: Warm            ]
[a#9        =    Heizen            ]
[a#10        =    Störung            ]

[v#1        =0                ] Modus
[v#2        =-1                ] Timeout-TS
[v#3        =-1                ] A1..8: SBC
[v#4        =-1                ] A9: SBC
###[/DEF]###


###[HELP]###
Dieser Baustein regelt z.B. einen Boiler in Abhängigkeit von der Wassertemperatur.

Der Baustein wird lediglich durch die Ist-Temperatur (E11) und die Eingänge E1..E4 getriggert. Eine Änderung z.B. einer Solltemperatur (E5..E8) wird erst beim nächsten Triggern berücksichtigt, also z.B. wenn die Ist-Temperatur sich geändert hat oder E1..E4 aktualisiert wurden.

Die Eingänge E1..E4 beeinflussen sich z.T. gegenseitig:
<ul>
    <li>E1 (Hygiene) hat stets Prioriät, d.h. E2/E3 werden bei aktivierter Hygienefunktion ignoriert und E4 wird intern vorübergehend deaktiviert</li>
    <li>E2 und E3 (Einmalladungen) toggeln sich gegenseitig, d.h. wenn z.B. E2 aktiviert wird, wird E3 automatisch deaktiviert</li>
    <li>E4 (Dauerladung) aktiviert oder deaktiviert permanent das kontinulierliche Warmhalten des Boilers, wird aber ggf. durch E1..E3 temporär übersteuert</li>
</ul>

E1..E3&ne;0 triggert die entsprechende Funktion, d.h. der entsprechende Vorgang wird ggf. <i>einmalig</i> gestartet und nach Abschluss deaktiviert.
Sind die entsprechenden Bedingungen (Soll-Temperaturen) bei Triggern von E1..E3 nicht erfüllt (Wasser ist bereits warm), wird der entsprechende Vorgang nicht gestartet (E1..E3&ne;0 wird quasi ignoriert).
Mit E1..E3=0 wird ein ggf. eingeleiteter Vorgang vorzeitig abgebrochen.
E4&ne;0 aktiviert hingegen permanent die Dauerladung, während E4=0 die Dauerladung deaktiviert.

E5..E8 geben die Soll-Temperaturen für die entsprechenden Funktionen vor.

E9 legt eine Hysterese für die Soll-Temperaturen fest:
<ul>
    <li>"heizen" (A9=1): Ist-Temperatur &lt; Soll-Temperatur abzüglich Hysterese</li>
    <li>"nicht heizen" (A9=0): Ist-Temperatur &gt;= Soll-Temperatur</li>
    <li>Status "warm" (A5..A8=1): Ist-Temperatur &gt;= Soll-Temperatur abzüglich Hysterese</li>
    <li>Status "kalt" (A5..A8=0): Ist-Temperatur &lt; Soll-Temperatur abzüglich Hysterese</li>
</ul>

Hinweis:
Bei aktivierter Dauerladung (D) wird nach dem Beenden eines Vorgangs A/B/C unter Umständen das (zuvor per Dauerladung aktivierte) Heizen nicht fortgesetzt, wenn die Ist-Temperatur im "Totbereich" liegt (also genau zwischen der Soll-Temperatur und der Soll-Temperatur abzüglich der Hysterese).

An E10 kann optional die maximale Einschaltdauer (in Sekunden) angegeben werden (0=deaktiviert): Ist A9 (Heizen) länger als diese Zeitspanne kontinuierlich eingeschaltet, wird A9=0 und A10=1 gesetzt (Störung).
Bei jedem Einschalten des Heizens (A9=1) wird der interne Timer neu gestartet: Die Einschaltdauer wird immer dann neu berechnet, wenn ein neuer Vorgang aktiviert wird.

Mit E12 kann die Heizfunktion bei Bedarf deaktiviert werden (z.B. während eines Urlaubs): Mit E12=0 wird das Heizen unmittelbar beendet (A9=0) und sämtliche Vorgänge abgebrochen (A1..A4=0). Der Warm-Status (A5..A8) wird jedoch weiterhin ausgegeben.
Beim Aktivieren (E12&ne;0) wird die Dauerladung (D) ggf. wieder aktiviert, ggf. zuvor gestartete Vorgänge A/B/C jedoch nicht.


<h3>Ein-/Ausgänge</h3>
E1: &ne;0 = Hygieneladung A ggf. einmalig starten (und Einmalladungen B/C ggf. abbrechen), 0 = abbrechen
E2: &ne;0 = Einmalladung B ggf. einmalig starten (und Einmalladung C ggf. abbrechen), 0 = abbrechen
E3: &ne;0 = Einmalladung C ggf. einmalig starten (und Einmalladung B ggf. abbrechen), 0 = abbrechen
E4: &ne;0 = Dauerladung D aktivieren, 0 = deaktivieren
E5..E8: Soll-Temperaturen für die entsprechende Funktion A..D
E9: Hysterese (sollte stets >0 sein, um ein "Schwingen" zu vermeiden)
E10: maximale Einschaltdauer, 0 = deaktiviert
E11: Ist-Temperatur
E12: &ne;0 = Heizfunktion aktiviert, 0 = deaktiviert

A1..A4: 1 = die entsprechende Funktion A..D ist aktiviert, 0 = A..D ist deaktiviert
A5..A8: 1 = die Soll-Temperatur (abzüglich der Hysterese) für A..D ist erreicht, 0 = Soll-Temperatur nicht erreicht
A9: 1 = heizen, 0 = nicht heizen
A10: 1 = Störung (maximale Einschaltdauer überschritten) - Achtung: A10 wird <i>nicht</i> wieder auf 0 gesetzt!

Hinweis:
Alle Ausgänge A1..A10 werden nur bei Änderung des entsprechenden Zustandes gesetzt (SBC).
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {
        $V[1] = intval($V[1]);
        $V[3] = intval($V[3]);
        $switch = null;

        //Deaktivieren?
        if ($E[12]['refresh'] == 1 && $E[12]['value'] == 0) {
            $V = LB_LBSID_switchOnOff($id, $V, $E, false);
            logic_setState($id, 0);
        }

        if ($E[12]['value'] != 0 && !isEmpty($E[11]['value'])) {
            if ($E[4]['value'] != 0) {
                $V[1] |= 8;
            } else {
                $V[1] &= ~8;
            }
            if ($E[3]['refresh'] == 1) {
                if ($E[3]['value'] != 0 && ~$V[1] & 1 && $E[11]['value'] < ($E[7]['value'] - $E[9]['value'])) {
                    $V[1] |= 4;
                    $V[1] &= ~2;
                } else {
                    $V[1] &= ~4;
                }
            }
            if ($E[2]['refresh'] == 1) {
                if ($E[2]['value'] != 0 && ~$V[1] & 1 && $E[11]['value'] < ($E[6]['value'] - $E[9]['value'])) {
                    $V[1] |= 2;
                    $V[1] &= ~4;
                } else {
                    $V[1] &= ~2;
                }
            }
            if ($E[1]['refresh'] == 1) {
                if ($E[1]['value'] != 0 && $E[11]['value'] < ($E[5]['value'] - $E[9]['value'])) {
                    $V[1] |= 1;
                    $V[1] &= ~2;
                    $V[1] &= ~4;
                } else {
                    $V[1] &= ~1;
                }
            }

        } else {
            $V[1] = 0;
        }

        //A
        if ($V[1] & 1) {
            if ($E[11]['value'] < ($E[5]['value'] - $E[9]['value'])) {
                $switch = true;
                $V[1] &= ~256;
            } else if ($E[11]['value'] >= $E[5]['value']) {
                $V[1] &= ~1;
                $switch = false;
            }
        } else if ($V[3] & 1) {
            $switch = false;
        }

        //B
        if (~$V[1] & 1 && ~$V[1] & 4) {
            if ($V[1] & 2) {
                if ($E[11]['value'] < ($E[6]['value'] - $E[9]['value'])) {
                    $switch = true;
                    $V[1] &= ~256;
                } else if ($E[11]['value'] >= $E[6]['value']) {
                    $V[1] &= ~2;
                    $switch = false;
                }
            } else if ($V[3] & 2) {
                $switch = false;
            }
        }

        //C
        if (~$V[1] & 1 && ~$V[1] & 2) {
            if ($V[1] & 4) {
                if ($E[11]['value'] < ($E[7]['value'] - $E[9]['value'])) {
                    $switch = true;
                    $V[1] &= ~256;
                } else if ($E[11]['value'] >= $E[7]['value']) {
                    $V[1] &= ~4;
                    $switch = false;
                }
            } else if ($V[3] & 4) {
                $switch = false;
            }
        }

        //D
        if (~$V[1] & 1 && ~$V[1] & 2 && ~$V[1] & 4) {
            if ($V[1] & 8) {
                if ($E[11]['value'] < ($E[8]['value'] - $E[9]['value'])) {
                    $switch = true;
                    $V[1] |= 256;
                } else if ($E[11]['value'] >= $E[8]['value']) {
                    $switch = false;
                    $V[1] &= ~256;
                }
            } else if ($V[3] & 8) {
                $switch = false;
                $V[1] &= ~256;
            }
        }

        //Timeout?
        if (logic_getState($id) == 1 && getMicrotime() >= $V[2]) {
            $switch = false;
            $V[1] &= ~1;
            $V[1] &= ~2;
            $V[1] &= ~4;
            $V[1] &= ~256;
            logic_setState($id, 0);
            logic_setOutput($id, 10, 1);
        }

        //Status: Warm/Kalt?
        if ($E[11]['value'] >= ($E[5]['value'] - $E[9]['value'])) {
            $V[1] |= 16;
        } else {
            $V[1] &= ~16;
        }
        if ($E[11]['value'] >= ($E[6]['value'] - $E[9]['value'])) {
            $V[1] |= 32;
        } else {
            $V[1] &= ~32;
        }
        if ($E[11]['value'] >= ($E[7]['value'] - $E[9]['value'])) {
            $V[1] |= 64;
        } else {
            $V[1] &= ~64;
        }
        if ($E[11]['value'] >= ($E[8]['value'] - $E[9]['value'])) {
            $V[1] |= 128;
        } else {
            $V[1] &= ~128;
        }

        //A1..8 (SBC)
        if ($V[1] & 1 && (~$V[3] & 1 || $V[3] == -1)) {
            logic_setOutput($id, 1, 1);
        } else if (~$V[1] & 1 && ($V[3] & 1 || $V[3] == -1)) {
            logic_setOutput($id, 1, 0);
        }
        if ($V[1] & 2 && (~$V[3] & 2 || $V[3] == -1)) {
            logic_setOutput($id, 2, 1);
        } else if (~$V[1] & 2 && ($V[3] & 2 || $V[3] == -1)) {
            logic_setOutput($id, 2, 0);
        }
        if ($V[1] & 4 && (~$V[3] & 4 || $V[3] == -1)) {
            logic_setOutput($id, 3, 1);
        } else if (~$V[1] & 4 && ($V[3] & 4 || $V[3] == -1)) {
            logic_setOutput($id, 3, 0);
        }
        if ($V[1] & 8 && (~$V[3] & 8 || $V[3] == -1)) {
            logic_setOutput($id, 4, 1);
        } else if (~$V[1] & 8 && ($V[3] & 8 || $V[3] == -1)) {
            logic_setOutput($id, 4, 0);
        }
        if ($V[1] & 16 && (~$V[3] & 16 || $V[3] == -1)) {
            logic_setOutput($id, 5, 1);
        } else if (~$V[1] & 16 && ($V[3] & 16 || $V[3] == -1)) {
            logic_setOutput($id, 5, 0);
        }
        if ($V[1] & 32 && (~$V[3] & 32 || $V[3] == -1)) {
            logic_setOutput($id, 6, 1);
        } else if (~$V[1] & 32 && ($V[3] & 32 || $V[3] == -1)) {
            logic_setOutput($id, 6, 0);
        }
        if ($V[1] & 64 && (~$V[3] & 64 || $V[3] == -1)) {
            logic_setOutput($id, 7, 1);
        } else if (~$V[1] & 64 && ($V[3] & 64 || $V[3] == -1)) {
            logic_setOutput($id, 7, 0);
        }
        if ($V[1] & 128 && (~$V[3] & 128 || $V[3] == -1)) {
            logic_setOutput($id, 8, 1);
        } else if (~$V[1] & 128 && ($V[3] & 128 || $V[3] == -1)) {
            logic_setOutput($id, 8, 0);
        }

        //Timeout: starten bei Ein-Flanke (A..D)
        if (($V[1] & 1 && (~$V[3] & 1 || $V[3] == -1)) || ($V[1] & 2 && (~$V[3] & 2 || $V[3] == -1)) || ($V[1] & 4 && (~$V[3] & 4 || $V[3] == -1)) || ($V[1] & 256 && (~$V[3] & 256 || $V[3] == -1))) {
            if ($E[10]['value'] > 0) {
                $V[2] = getMicrotime() + $E[10]['value'];
                logic_setVar($id, 2, $V[2]);
                logic_setState($id, 1, $E[10]['value'] * 1000);
            }
        }

        $V = LB_LBSID_switchOnOff($id, $V, $E, $switch);

        logic_setVar($id, 1, $V[1]);
        logic_setVar($id, 3, $V[1]);
    }
}

function LB_LBSID_switchOnOff($id, $V, $E, $mode)
{
    if ($mode === true) {
        if ($V[4] == 0 || $V[4] == -1) {
            $V[4] = 1;
            logic_setVar($id, 4, $V[4]);
            logic_setOutput($id, 9, 1);
        }

    } else if ($mode === false) {
        if ($V[4] == 1 || $V[4] == -1) {
            $V[4] = 0;
            logic_setVar($id, 4, $V[4]);
            logic_setOutput($id, 9, 0);
            logic_setState($id, 0);
        }
    }
    return $V;
}

?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###
