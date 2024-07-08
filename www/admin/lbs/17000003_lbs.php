###[DEF]###
[name        =Türsteuerung    ]

[e#1 TRIGGER=Aktion                                    ]
[e#2        =Status: Tür                            ]
[e#3        =Status: Riegel                            ]
[e#4 OPTION    =Dauer: Entriegeln+Öffnen (s)    #init=5    ]
[e#5 OPTION    =Dauer: Entriegeln (s)            #init=3    ]
[e#6 OPTION    =Dauer: Verriegeln (s)            #init=3    ]

[a#1        =Öffnen                    ]
[a#2        =Aufschließen            ]
[a#3        =Abschließen            ]
[a#4        =Status                    ]
[a#5        =Störung                ]

[v#1                =0                ]
[v#2                =                ]
[v#3 REMANENT        =                ]
###[/DEF]###


###[HELP]###
Dieser Baustein steuert ein Tür-Motorschloss.

Erwartet wird zumindest ein (Reed-)Kontakt, der den Zustand des Türblatts repräsentiert (0=Tür geschlossen, &ne;0=Tür geöffnet).
Optional kann an E3 ein Riegelkontakt anliegen, der den Zustand der Verriegelung des Schlossen repräsentiert (0=Schloss ist verriegelt, &ne;0=Schloss ist entriegelt).

Hinweis:
Wird kein Riegelkontakt verwendet, muss E3=[leer] sein. Der aktuelle Türstatus (s.u.) kann dann u.U. nicht eindeutig ermittelt werden.


Getriggert wird der Baustein mit der gewünschten Aktion an E1. Der Baustein unterscheidet 3 Funktionen, die das Motorschloss umsetzen können sollte:
<ul>
    <li>E1=0: Abschließen (Schloss wird verriegelt)</li>
    <li>E1=1: Aufschließen (Schloss wird entriegelt, die Falle bleibt aber ausgefahren)</li>
    <li>E1=2: Öffnen (Schloss wird entriegelt, die Falle wird kurzfristig zur Türöffnung eingezogen)</li>
</ul>

Die gewünschte Aktion wird nur ausgeführt, wenn die entsprechenden Bedinungen erfüllt sind: z.B. muss die Tür geschlossen sein (E2=0), wenn das Schloss verriegelt werden soll (E1=0).

Hinweis:
Eine laufende Aktion kann
<i>nicht</i> durch eine andere Aktion abgebrochen werden, sondern wird stets für die entsprechende Dauer (E4..E6) ausgeführt (oder vorzeitig beendet, wenn die entsprechenden Bedingungen bereits erfüllt sind).


E4 legt die Dauer des Entriegelungs- und Öffnungsvorgangs (Motorschloss) fest. Anzugeben ist der größte Wert (die Dauer des Entriegelungsvorgangs zuzüglich des Öffnungsvorgang, also dem Einziehen der Falle).
E5 legt die Dauer des Entriegelungsvorgangs (Motorschloss) fest.
E6 legt die Dauer des Verriegelungsvorgangs (Motorschloss) fest.


A1..A3 werden auf 1 gesetzt, wenn die entsprechende Aktion getriggert wird und die erforderlichen Bedingungen erfüllt sind. Das Motorschloss sollte dann die entsprechende Funktion ausführen.

A4 repräsentiert zu jeder Zeit den aktuellen Status der Tür (bzw. des Schlosses) und wird nur bei einer Änderung gesetzt (SBC).
Falls kein Riegelkontakt verwendet wird (E3=[leer]), kann der tatsächliche Status abweichenend sein, da der Zustand "Tür geschlossen und verriegelt oder entriegelt" nicht immer eindeutig bestimmbar ist (sicherheitshalber wird im Zweifel davon ausgegangen, dass die Tür entriegelt ist).
Unabhängig von dem internen Status (der sich aus der Logik ergibt) wird zudem der tatsächliche Status (E2 und E3) mit ausgewertet. A4 repräsentiert also stets den tatsächlichen Status (sofern ein Riegelkontakt verwendet wird).

Wichtig:
Der Türstatus wird intern remanent verwaltet, d.h. nach einem Neustart wird A4 u.U.
<i>nicht</i> gesetzt (SBC). Daher ist es erforderlich mit A4 ein remanentes KO zu befüllen, um auch nach einem Neustart den aktuellen Status beizubehalten (für weitere Logiken oder Visualisierungen).


A5 wird ggf. auf 1 gesetzt (niemals jedoch auf 0 zurückgesetzt), falls eine Störung vorliegt:
<ul>
    <li>wenn beim Abschließen (E1=0) die Ausführungszeit (E6) erreicht ist und: der Riegel noch entriegelt ist (E3&ne;0) oder die Tür (wieder) geöffnet ist/wird
        (E2&ne;0)
    </li>
    <li>wenn beim Aufschließen (E1=1) die Ausführungszeit (E5) erreicht ist und: der Riegel noch verriegelt ist (E3=0) oder die Tür geöffnet ist/wird
        (E2&ne;0)
    </li>
    <li>wenn beim Öffnen (E1=2) die Ausführungszeit (E4) erreicht ist und: die Tür noch geschlossen ist (E2=0)</li>
</ul>


<h3>Ein- und Ausgänge</h3>
E1: triggert den Baustein mit der gewünschten Aktion: 0=Abschließen, 1=Aufschließen, 2=Öffnen
E2: Türkontakt (z.B. Reed): &ne;0=Türblatt ist geöffnet, 0=Türblatt ist geschlossen
E3: Riegelkontakt (optional): &ne;0=Schloss ist entriegelt, 0=Schloss ist verriegelt, [leer]=kein Riegelkontakt vorhanden
E4..E6: Verfahrzeiten des Motorschlosses (s.o.)

A1: Motorschloss: 1=Entriegeln und Öffnen
A2: Motorschloss: 1=Entriegeln
A3: Motorschloss: 1=Verriegeln
A4: Tür-/Schlossstatus (SBC): 0=geschlossen und verriegelt, 1=geschlossen und entriegelt, 2=geöffnet, -1=öffnet gerade, -2=verriegelt gerade, -3=entriegelt gerade
A5: Störung: 1=Störung (s.o.)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[1]['refresh'] == 1 && $V[1] == 0) {

            //Abschließen
            if ($E[1]['value'] == 0) {
                if ($E[2]['value'] == 0) {
                    if (isEmpty($E[3]['value']) || (!isEmpty($E[3]['value']) && $E[3]['value'] != 0)) {
                        $V[1] = 1;
                        logic_setVar($id, 1, $V[1]);
                        $V[2] = getMicrotime() + $E[6]['value'];
                        logic_setVar($id, 2, $V[2]);
                        logic_setState($id, 1, $E[6]['value'] * 1000);
                        logic_setOutput($id, 3, 1);
                        $V = LB_LBSID_setStatus($id, $E, $V, -2);
                    }
                }
            }

            //Aufschließen
            if ($E[1]['value'] == 1) {
                if ($E[2]['value'] == 0 && (isEmpty($E[3]['value']) || (!isEmpty($E[3]['value']) && $E[3]['value'] == 0))) {
                    if (isEmpty($E[3]['value']) || (!isEmpty($E[3]['value']) && $E[3]['value'] == 0)) {
                        $V[1] = 2;
                        logic_setVar($id, 1, $V[1]);
                        $V[2] = getMicrotime() + $E[5]['value'];
                        logic_setVar($id, 2, $V[2]);
                        logic_setState($id, 1, $E[5]['value'] * 1000);
                        logic_setOutput($id, 2, 1);
                        $V = LB_LBSID_setStatus($id, $E, $V, -3);
                    }
                }
            }

            //Öffnen
            if ($E[1]['value'] == 2) {
                if ($E[2]['value'] == 0) {
                    $V[1] = 3;
                    logic_setVar($id, 1, $V[1]);
                    $V[2] = getMicrotime() + $E[4]['value'];
                    logic_setVar($id, 2, $V[2]);
                    logic_setState($id, 1, $E[4]['value'] * 1000);
                    logic_setOutput($id, 1, 1);
                    $V = LB_LBSID_setStatus($id, $E, $V, -1);
                }
            }
        }

        //Status anpassen bei manuellem Eingriff
        if (($E[2]['refresh'] == 1 || $E[3]['refresh'] == 1) && $V[1] == 0) {
            $V = LB_LBSID_newStatus($id, $E, $V, $V[3]);
        }

        if (logic_getState($id) == 1) {
            //Abschließen: warten auf Riegel/Timeout
            if ($V[1] == 1) {
                if ($E[2]['value'] == 0 && ((isEmpty($E[3]['value']) && getMicrotime() >= $V[2]) || (!isEmpty($E[3]['value']) && $E[3]['value'] == 0 && getMicrotime() < $V[2]))) {
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    $V = LB_LBSID_newStatus($id, $E, $V, 0);
                } else if (getMicrotime() >= $V[2]) {
                    //Timeout: Riegel ist noch offen => Störung
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    logic_setOutput($id, 5, 1);
                    $V = LB_LBSID_newStatus($id, $E, $V, $V[3]);
                }
            }

            //Aufschließen: warten auf Riegel/Timeout
            if ($V[1] == 2) {
                if ($E[2]['value'] == 0 && ((isEmpty($E[3]['value']) && getMicrotime() >= $V[2]) || (!isEmpty($E[3]['value']) && $E[3]['value'] != 0 && getMicrotime() < $V[2]))) {
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    $V = LB_LBSID_newStatus($id, $E, $V, 1);
                } else if (getMicrotime() >= $V[2]) {
                    //Timeout: Riegel ist noch zu => Störung
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    logic_setOutput($id, 5, 1);
                    $V = LB_LBSID_newStatus($id, $E, $V, $V[3]);
                }
            }

            //Öffnen: warten auf Reed/Timeout
            if ($V[1] == 3) {
                if ($E[2]['value'] != 0) {
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    $V = LB_LBSID_newStatus($id, $E, $V, 2);
                } else if (getMicrotime() >= $V[2]) {
                    //Timeout: Tür wurde nicht geöffnet (Reed) => Störung
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                    logic_setOutput($id, 5, 1);
                    $V = LB_LBSID_newStatus($id, $E, $V, $V[3]);
                }
            }
        }
    }
}

function LB_LBSID_newStatus($id, $E, $V, $status)
{
    $tmp = false;
    if ($E[2]['value'] != 0) {
        $tmp = 2;
    } else if (!isEmpty($E[2]['value']) && $E[2]['value'] == 0) {
        if (isEmpty($E[3]['value'])) {
            if (isEmpty($status) || $status != 0) {
                $tmp = 1;
            } else {
                $tmp = 0;
            }
        } else if ($E[3]['value'] != 0) {
            $tmp = 1;
        } else if ($E[3]['value'] == 0) {
            $tmp = 0;
        }
    }
    if ($tmp !== false) {
        $V = LB_LBSID_setStatus($id, $E, $V, $tmp);
    }
    return $V;
}

function LB_LBSID_setStatus($id, $E, $V, $status)
{
    if ($V[3] != $status || isEmpty($V[3])) {
        $V[3] = $status;
        logic_setVar($id, 3, $V[3]);
        logic_setOutput($id, 4, $status);
    }
    return $V;
}

?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###
