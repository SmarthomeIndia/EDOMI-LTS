###[DEF]###
[name            =    Präsenzstatus]

[e#1    TRIGGER    =    Aktivität                                    ]
[e#2    TRIGGER    =    Schlafen                                    ]
[e#3    TRIGGER    =    Coming/Leaving                                ]
[e#4    OPTION    =    Aktivität: Dauer (s)            #init=1800    ]
[e#5    OPTION    =    Leaving: Dauer (s)                #init=30    ]

[a#1            =    Status                            ]
[a#2            =    Coming                            ]
[a#3            =    Leaving                            ]
[a#4            =    Abbruch                        ]
[a#5            =    Leaved                            ]
[a#6            =    Schlafen                        ]
[a#7            =    Aufwachen                        ]


[v#1    REMANENT=-3                        ]
[v#2            =0                        ]
[v#3    REMANENT=-1                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein ermittelt einen "Präsenzstatus" und triggert ggf. ein "Coming home"- bzw. "Leaving home"-Ereignis.

Es werden ggf. folgende Präsenzzustände (A1) unterschieden:
<ul>
    <li>Anwesend und aktiv (A1=6): E1 kann z.B. durch Bewegungsmelder getriggert werden, um eine Aktivität zu registrieren</li>
    <li>Anwesend aber inaktiv (A1=5): z.B. wenn ein Triggern von E1 (z.B. durch Bewegungsmelder) für eine gewisse Zeit (E4) ausbleibt</li>
    <li>Schlafend (A1=4): mit E2&ne;0 wird der Schlafmodus (bei Anwesenheit) aktiviert</li>
    <li>Abwesend (A1=-1/-2/-3): wird mit E3=1/2/3 aktiviert (es werden 3 Abwesenheits-Modi unterschieden, um z.B. bei einem Urlaub entsprechende Maßnahmen zu
        treffen)
    </li>
    <li>"Gehend" (A1=1/2/3): wird eine Abwesenheit eingeleitet (s.o.), wird zunächst ein Timer gestartet (E5) - der Status "Abwesend" wird erst nach Ablauf des
        Timers gesetzt
    </li>
</ul>

A2..A5 repräsentieren Ereignisse, d.h. diese Ausgänge werden immer dann auf 1/2/3 gesetzt (niemals auf 0), wenn das entsprechende Ereignis getriggert wird:
<ul>
    <li>Coming (A2=1/2/3): wird getriggert, wenn <i>nach einer Abwesenheit</i> E3=0 ("Coming home") gesetzt wird (A2 wird je nach zuvor aktiviertem
        Abwesenheitsmodus auf 1, 2 oder 3 gesetzt)
    </li>
    <li>Leaving (A3=1/2/3): wird getriggert, wenn <i>bei Anwesenheit</i> E3=1/2/3 ("Leaving home") gesetzt wird</li>
    <li>Abbruch (A4=1/2/3): wird getriggert, wenn <i>bei Anwesenheit</i> E3=1/2/3 ("Leaving home") gesetzt wurde und noch vor Ablauf des internen Timers (E5)
        ein "Coming home" (E3=0) ausgelöst wird
    </li>
    <li>Leaved (A5=1/2/3): wird getriggert, nachdem <i>bei Anwesenheit</i> E3=1/2/3 ("Leaving home") gesetzt wurde und der interne Timer (E5) abgelaufen ist
    </li>
</ul>

A6 (Schlafen) repräsentiert ein Ereignis, d.h. dieser Ausgang wird immer dann auf 1 gesetzt (niemals auf 0), wenn das entsprechende Ereignis getriggert wird:
<ul>
    <li>wenn <i>bei Anwesenheit oder während des Verlassens ("Gehend/Leaving")</i> E2=&ne;0 ("schlafen") gesetzt wird und zuvor E2=0 ("nicht schlafen") gesetzt
        war
    </li>
    <li>bei Abwesenheit bleibt der Schlafmodus ggf. solange aktiviert, bis "Coming" aktiviert wird</li>
    <li>Hinweis: bei Abwesenheit wird E2 ignoriert (der Schlafmodus kann nur bei Anwesenheit aktiviert oder deaktiviert werden)</li>
</ul>

A7 (Aufwachen) repräsentiert ein Ereignis, d.h. dieser Ausgang wird immer dann auf 1 gesetzt (niemals auf 0), wenn das entsprechende Ereignis getriggert wird:
<ul>
    <li>wenn <i>bei Anwesenheit oder während des Verlassens ("Gehend/Leaving")</i> E2=0 ("nicht schlafen") gesetzt wird und zuvor E2=&ne;0 ("schlafen") gesetzt
        war
    </li>
    <li>wenn "Coming" aktiviert wird (nach dem vollständigem Verlassen) und zuvor E2=&ne;0 ("schlafen") gesetzt war (bei "Coming" wird also der Schlafmodus
        stets beendet)
    </li>
    <li>Hinweis: bei Abwesenheit wird E2 ignoriert (der Schlafmodus kann nur bei Anwesenheit aktiviert oder deaktiviert werden)</li>
</ul>


<h3>Verhalten der Eingänge</h3>
Solange E1&ne;0 ist, wird dies ggf. als Aktivität (A1=6) gewertet. Erst wenn E1=0 wird, beginnt der interne Timer abzulaufen (E4). Nach Ablauf des Timers wird A1=5 gesetzt.

Bei der Aktivierung des Schlafmodus (E2&ne;0) wird A1=4 gesetzt (nur bei Anwesenheit möglich), E1 wird nun bis zu Deaktivierung (E2=0) ignoriert.
Bei der Deaktivierung des Schlafmodus (nur bei Anwesenheit möglich) wird zunächst A1=6 (anwesend und aktiv) gesetzt.
Bei Abwesenheit wird E2 ignoriert (der Schlafmodus kann nur bei Anwesenheit aktiviert oder deaktiviert werden).

Beim Auslösung eines (gültigen, d.h. bei Abwesenheit) "Coming home"-Ereignisses (E3=0) wird zunächst A1=6 (anwesend und aktiv) gesetzt.


<h3>Wichtige Hinweise</h3>
<ul>
    <li>Beim allerersten Start (bzw. nach dem Zurücksetzen der remanenten Variablen) wird von Anwesenheit ausgegangen (A1=4/5/6), bis ein entsprechender Eingang
        getriggert wird.
    </li>
    <li>Bei einem Neustart bleibt der aktuelle Status <i>intern</i> erhalten, d.h. die Ausgänge werden <i>nicht</i> erneut gesetzt. A1 sollte daher ein
        remanentes KO befüllen.
    </li>
    <li>An E2 sollte ein remanentes KO angelegt werden, damit der Schlafmodus ggf. nach einem Neutstart erhalten bleibt.</li>
</ul>


<h3>Ein-/Ausgänge</h3>
E1: &ne;0 = Aktivität (z.B. durch Bewegungsmelder), 0=keine Aktivität
E2: &ne;0 = Schlafmodus, 0=normaler Modus
E3: 0=Coming Home, 1/2/3=Leaving Home 1/2/3
E4: legt die Dauer der Aktivität bei fallender Flanke an E1 fest (in Sekunden)
E5: legt die Dauer der Verzögerung (bis zur Auslösung von "Leaved") bei "Leaving Home" fest (in Sekunden)

A1: -1/-2/-3=abwesend ("Leaved"), 1/2/3="gehend" ("Leaving home"), 4=schlafend, 5=anwesend (inaktiv), 6=anwesend (aktiv)
A2: 1/2/3="Coming Home"
A3: 1/2/3="Leaving Home" (interner Timer wird gestartet)
A4: 1/2/3="Leaving Home"-Abbruch (interner Timer wird abgebrochen)
A5: 1/2/3="Leaved" (interner Timer ist abgelaufen)
A6: 1=Schlafen (wenn zuvor nicht schlafend)
A7: 1=Aufwachen (wenn zuvor schlafend)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        //Coming bzw. Leaving-Abbruch
        if ($E[3]['refresh'] == 1 && $E[3]['value'] == 0 && ($V[1] == 1 || $V[1] == 2 || $V[1] == 3 || $V[1] == 11 || $V[1] == 12 || $V[1] == 13)) {
            if ($V[1] == 1) {
                logic_setOutput($id, 2, 1);
            } else if ($V[1] == 2) {
                logic_setOutput($id, 2, 2);
            } else if ($V[1] == 3) {
                logic_setOutput($id, 2, 3);
            } else if ($V[1] == 11) {
                logic_setOutput($id, 4, 1);
            } else if ($V[1] == 12) {
                logic_setOutput($id, 4, 2);
            } else if ($V[1] == 13) {
                logic_setOutput($id, 4, 3);
            }

            //Coming: Aufwachen?
            if ($V[1] == 1 || $V[1] == 2 || $V[1] == 3) {
                if ($V[3] == 1) {
                    $V[3] = 0;
                    logic_setVar($id, 3, $V[3]);
                    logic_setOutput($id, 7, 1);
                }
                $E[2]['refresh'] = 0;
            } else {
                $E[2]['refresh'] = 1;
            }

            //Aktivität?
            if ($E[1]['value'] != 0) {
                $V[1] = -1;
            } else {
                $V[1] = -2;
                logic_setOutput($id, 1, 6);
            }
            logic_setVar($id, 1, $V[1]);
            $E[1]['refresh'] = 1;

            //Leaving
        } else if ($E[3]['refresh'] == 1 && ($E[3]['value'] == 1 || $E[3]['value'] == 2 || $E[3]['value'] == 3) && ($V[1] == 0 || $V[1] == -1 || $V[1] == -2)) {
            if ($E[3]['value'] == 1) {
                $V[1] = 11;
                logic_setOutput($id, 1, 1);
                logic_setOutput($id, 3, 1);
            } else if ($E[3]['value'] == 2) {
                $V[1] = 12;
                logic_setOutput($id, 1, 2);
                logic_setOutput($id, 3, 2);
            } else if ($E[3]['value'] == 3) {
                $V[1] = 13;
                logic_setOutput($id, 1, 3);
                logic_setOutput($id, 3, 3);
            }
            logic_setVar($id, 1, $V[1]);
            $V[2] = getMicrotime() + $E[5]['value'];
            logic_setVar($id, 2, $V[2]);
            logic_setState($id, 1, $E[5]['value'] * 1000);
        }

        //Schlafen: während Leaving ggf. anpassen (nicht bei Leaved)
        if ($E[2]['refresh'] == 1 && ($V[1] == 11 || $V[1] == 12 || $V[1] == 13)) {
            if ($E[2]['value'] != 0 && ($V[3] == 0 || $V[3] < 0)) {
                $V[3] = 1;
                logic_setVar($id, 3, $V[3]);
                logic_setOutput($id, 6, 1);
            } else if ($E[2]['value'] == 0 && ($V[3] == 1 || $V[3] < 0)) {
                $V[3] = 0;
                logic_setVar($id, 3, $V[3]);
                logic_setOutput($id, 7, 1);
            }
        }

        //Schlafen
        if ($E[2]['refresh'] == 1 && $E[2]['value'] != 0 && $V[1] < 0) {
            $V[1] = 0;
            logic_setVar($id, 1, $V[1]);
            logic_setOutput($id, 1, 4);
            logic_setState($id, 0);

            //Schlafengehen
            if ($V[3] == 0 || $V[3] < 0) {
                $V[3] = 1;
                logic_setVar($id, 3, $V[3]);
                logic_setOutput($id, 6, 1);
            }

        } else if ($E[2]['refresh'] == 1 && $E[2]['value'] == 0 && $V[1] == 0) {
            //Aktivität?
            if ($E[1]['value'] != 0) {
                $V[1] = -1;
            } else {
                $V[1] = -2;
                logic_setOutput($id, 1, 6);
            }
            logic_setVar($id, 1, $V[1]);
            $E[1]['refresh'] = 1;

            //Aufwachen
            if ($V[3] == 1 || $V[3] < 0) {
                $V[3] = 0;
                logic_setVar($id, 3, $V[3]);
                logic_setOutput($id, 7, 1);
            }
        }

        //Aktivität
        if ($V[1] < 0) {
            if ($E[1]['refresh'] == 1 && $E[1]['value'] != 0) {
                //Präsenz, aber Timer noch nicht starten
                if ($V[1] != -2) {
                    $V[1] = -2;
                    logic_setVar($id, 1, $V[1]);
                    logic_setOutput($id, 1, 6);
                }
                logic_setState($id, 0);

            } else if ($E[1]['refresh'] == 1 && $E[1]['value'] == 0 && ($V[1] == -2 || $V[1] == -3)) {
                if ($V[1] == -3) {
                    $V[1] = -2;
                    logic_setVar($id, 1, $V[1]);
                    logic_setOutput($id, 1, 6);
                }
                //Timer starten
                $V[2] = getMicrotime() + $E[4]['value'];
                logic_setVar($id, 2, $V[2]);
                logic_setState($id, 1, $E[4]['value'] * 1000);
            }
        }

        if (logic_getState($id) == 1) {
            //Aktivität: Verzögerung abgelaufen?
            if ($V[1] == -2) {
                if (getMicrotime() >= $V[2]) {
                    $V[1] = -1;
                    logic_setVar($id, 1, $V[1]);
                    logic_setOutput($id, 1, 5);
                    logic_setState($id, 0);
                }
            }

            //Leaved
            if ($V[1] == 11 || $V[1] == 12 || $V[1] == 13) {
                if (getMicrotime() >= $V[2]) {
                    if ($V[1] == 11) {
                        $V[1] = 1;
                        logic_setOutput($id, 1, -1);
                        logic_setOutput($id, 5, 1);
                    } else if ($V[1] == 12) {
                        $V[1] = 2;
                        logic_setOutput($id, 1, -2);
                        logic_setOutput($id, 5, 2);
                    } else if ($V[1] == 13) {
                        $V[1] = 3;
                        logic_setOutput($id, 1, -3);
                        logic_setOutput($id, 5, 3);
                    }
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                }
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
