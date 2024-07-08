###[DEF]###
[name        =Torsteuerung    ]

[e#1 TRIGGER=Aktion                                    ]
[e#2        =Status: Tor                            ]
[e#3        =Schließvorgang sperren            #init=0    ]
[e#4 OPTION    =Dauer: Öffnen (s)                #init=20]
[e#5 OPTION    =Dauer: Schließen (s)            #init=20]

[a#1        =Torsteuerung            ]
[a#2        =Status                    ]
[a#3        =Störung                ]

[v#1        =0                ]
[v#2        =                ]
[v#3        =                ]
###[/DEF]###


###[HELP]###
Dieser Baustein steuert z.B. einen Garagentorantrieb.

Der Torantrieb muss über einen(!) Steuereingang verfügen, der das Tor nach dem Schema "Auf-Stop-Zu-Stop" verfahren lässt.
Erwartet wird zudem ein(!) (Reed-)Kontakt, der den Zustand des Tors an E2 repräsentiert (0=Tor geschlossen, &ne;0=Tor geöffnet).

Getriggert wird der Baustein mit einem neuen Telegramm (0 oder 1) an E1.

Die (Mindest-)Laufzeiten für das Öffnen und Schließen des Tores sind an E4 bzw. E5 anzugeben. Während dieser Laufzeiten wird A2 (Status) entsprechend gesetzt. Nach Ablauf der Laufzeiten wird A2 stets auf den Status "offen" (1) oder "geschlossen" (0) gesetzt - ausschlaggebend ist hierbei der Status des Kontaktes an E2.
Entspricht der Kontakt-Status (E2) nicht den Erwartungen (z.B. ist E2=0, das Tor sollte jedoch geöffnet werden), wird A3 auf 1 gesetzt ("Störung").

Ist E3&ne;0 wird das Schließen des Tores verhindert werden (z.B. durch eine Lichtschranke), das Öffnen ist jedoch weiterhin möglich.

<b>Hinweis:</b>
Ein bereits <i>eingeleiteter</i> Schließvorgang wird durch E3 nicht(!) abgebrochen!

<b>Wichtig:</b>
A1 wird ausschließlich auf 1 gesetzt (niemals auf 0). In der Regel ist daher die Nachschaltung eines
<link>Impuls/Trigger-LBS***lbs_16000110</link> erforderlich, dessen Ausgang schließlich das KO des Torantriebs nach dem Schema "Auf-Stop-Zu-Stop" triggert.

<b>Achtung:</b>
Prinzipbedingt kann dieser Baustein nicht ermitteln, in welchem Zustand sich der Torantrieb
<i>tatsächlich</i> befindet. Daher wird auf Grundlage von E2 (Kontakt) und den Laufzeiten versucht, den aktuellen (logischen) Zustand zu ermitteln. Sofern der Torantrieb zusätzlich auf anderem Wege beeinflusst wird (z.B. Funksender oder Not-Stopp durch ein Hindernis), kann dies zu unerwarteten Ergebnissen führen.


<h3>Ein- und Ausgänge</h3>
E1: triggert den Baustein mit der gewünschten Aktion: 0=Schließen, 1=Öffnen
E2: Türkontakt (z.B. Reed): &ne;0=Tor ist geöffnet, 0=Tor ist geschlossen
E3: Sperren: &ne;0=Schließen verhindern, 0=Schließen erlauben
E4: Laufzeit in Sekunden für den Öffnungsvorgang (muss mindestens(!) der tatsächlichen Laufzeit des Tores entsprechen)
E5: Laufzeit in Sekunden für den Schließvorgang (muss mindestens(!) der tatsächlichen Laufzeit des Tores entsprechen)

A1: 1=Tor Öffnen oder Schließen bzw. stoppen (Triggerausgang), A1 wird NIEMALS 0!
A2: 0=Tor ist geschlossen, 1=Tor ist geöffnet (bzw. nicht geschlossen), -1=Tor öffnet gerade, -2=Tor schließt gerade
A3: Störung: 1=Störung (s.o.)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{

    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {


        if ($E[1]['refresh'] == 1 && $V[1] == 0) {

            //Öffnen
            if ($E[1]['value'] == 1) {
                //Reed=zu?
                if ($E[2]['value'] == 0) {
                    $V[1] = 1;
                    logic_setVar($id, 1, $V[1]);
                    $V[2] = getMicrotime() + $E[4]['value'];
                    logic_setVar($id, 2, $V[2]);
                    logic_setState($id, 1, $E[4]['value'] * 1000);
                    logic_setOutput($id, 1, 1);
                    $V = LB_LBSID_setStatus($id, $E, $V, -1);
                }
            }

            //Schließen (wenn nicht gesperrt)
            if ($E[1]['value'] == 0 && $E[3]['value'] == 0) {
                //Reed=auf?
                if ($E[2]['value'] == 1) {
                    $V[1] = 2;
                    logic_setVar($id, 1, $V[1]);
                    $V[2] = getMicrotime() + $E[5]['value'];
                    logic_setVar($id, 2, $V[2]);
                    logic_setState($id, 1, $E[5]['value'] * 1000);
                    logic_setOutput($id, 1, 1);
                    $V = LB_LBSID_setStatus($id, $E, $V, -2);
                }
            }
        }

        if ($E[2]['refresh'] == 1 && $V[1] == 0) {
            $V = LB_LBSID_getStatus($id, $E, $V);
        }

        if (logic_getState($id) == 1) {

            //Öffnen: warten auf Timeout (und Reed)
            if ($V[1] == 1) {
                if (getMicrotime() >= $V[2]) {
                    //Reed=auf?
                    if ($E[2]['value'] != 0) {
                        $V = LB_LBSID_setStatus($id, $E, $V, 1);
                    } else {
                        $V = LB_LBSID_getStatus($id, $E, $V);
                        logic_setOutput($id, 3, 1);
                    }
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                }
            }

            //Schließen: warten auf Timeout (und Reed)
            if ($V[1] == 2) {
                if (getMicrotime() >= $V[2]) {
                    //Reed=zu?
                    if ($E[2]['value'] == 0) {
                        $V = LB_LBSID_setStatus($id, $E, $V, 0);
                    } else {
                        $V = LB_LBSID_getStatus($id, $E, $V);
                        logic_setOutput($id, 3, 1);
                    }
                    $V[1] = 0;
                    logic_setVar($id, 1, $V[1]);
                    logic_setState($id, 0);
                }
            }
        }
    }
}

function LB_LBSID_getStatus($id, $E, $V)
{
    if ($E[2]['value'] != 0) {
        $V = LB_LBSID_setStatus($id, $E, $V, 1);
    } else {
        $V = LB_LBSID_setStatus($id, $E, $V, 0);
    }
    return $V;
}

function LB_LBSID_setStatus($id, $E, $V, $status)
{
    if ($V[3] != $status || isEmpty($V[3])) {
        $V[3] = $status;
        logic_setVar($id, 3, $V[3]);
        logic_setOutput($id, 2, $status);
    }
    return $V;
}

?>
###[/LBS]###


###[EXEC]###
<?
?>
###[/EXEC]###
