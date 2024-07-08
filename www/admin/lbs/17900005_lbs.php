###[DEF]###
[name            =    Ventil-Festsetzschutz/Leckagealarm]

[e#1    TRIGGER    =    Leckagealarm                                    ]
[e#2    TRIGGER    =    Festsetzschutz                                    ]
[e#3    TRIGGER    =    Leckagealarm: Reset                                ]
[e#4    OPTION    =    Festsetzschutz: Intervall (Tage)    #init=0        ]
[e#5    OPTION    =    Ventil: Verfahrzeit (s)                #init=10    ]

[a#1            =    Ventil                                    ]
[a#2            =    Alarm                                    ]
[a#3            =    Festsetzschutz                            ]

[v#1            =0                        ]
[v#2    REMANENT=0                        ]
[v#3    REMANENT=0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein bewegt regelmäßig ein Motorventil (Festsetzschutz) und wertet zudem einen Leckagesensor aus (z.B. um die Hauptwasserleitung im Falle einer Leckage abzusperren).

E4 gibt das Intervall (in Tagen) für den Festsetzschutz an. Sobald an E2 ein neues Telegramm &ne;0 eintrifft, wird der Festsetzschutz durchgeführt sofern das an E4 angegebene Intervall erreicht oder überschritten worden ist.
Der Baustein arbeitet <i>nicht</i> zyklisch, d.h. der Festsetzschutz wird ggf. nur durchgeführt wenn E2 entsprechend getriggert wird.
Der Festsetzschutz schließt das Ventil (A1=0) und öffnet es unmittelbar danach wieder (A1=1). Die Verfahrzeit (E5) bestimmt dabei die Dauer der Öffnungs- bzw. Schließvorgangs.

Sobald an E1 ein neues Telegramm &ne;0 eintrifft, wird dies als "Leckage" interpretiert: Das Ventil wird geschlossen (A1=0) und A2 (Alarm) wird auf 1 gesetzt. Der Festsetzschutz ist nun deaktiviert.
Dieser Alarmzustand bleibt solange bestehen, bis ein neues Telegramm &ne;0 an E3 (Reset) eintrifft: Erst dann wird das Ventil wieder geöffnet und der Festsetzschutz freigegeben.

E1: &ne;0 = Leckagealarm auslösen
E2: &ne;0 = Festsetzschutz ggf. durchführen (sofern das Intervall an E4 erreicht/überschritten wurde)
E3: &ne;0 = Leckagealarm zurücksetzen
E4: Festsetzschutz-Intervall in Tagen
E5: Verfahrzeit des Ventils für einen Öffnungs- bzw. Schließvorgang (in Sekunden)

A1: 1 = Ventil öffnen, 0 = Ventil schließen
A2: 1 = Leckagealarm, 0 = Lackagealarm wurde mittels E3 zurückgesetzt
A3: 1 = Festsetzschutz wird gerade durchgeführt, 0 = Durchführung beendet (ggf. verfährt das Ventil aber noch)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($V[2] == 0 || ($E[4]['value'] != $V[3] && $E[4]['refresh'] == 1)) {
            $V[2] = getMicrotime() + ($E[4]['value'] * 86400);
            logic_setVar($id, 2, $V[2]);
            $V[3] = $E[4]['value'];
            logic_setVar($id, 3, $V[3]);
        }

        //Alarm ausgelöst
        if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1 && $V[1] >= 0) {
            logic_setOutput($id, 1, 0);    //Ventil schliessen
            logic_setOutput($id, 2, 1);
            logic_setOutput($id, 3, 0);
            logic_setState($id, 0);

            $V[1] = -1;
            logic_setVar($id, 1, $V[1]);

        } else if ($V[1] >= 0) {
            if (logic_getState($id) == 0) {
                //Festsetzschutz
                if ($E[2]['value'] != 0 && $E[2]['refresh'] == 1 && $E[4]['value'] > 0 && getMicrotime() >= $V[2]) {
                    logic_setOutput($id, 1, 0);    //Ventil schliessen
                    logic_setOutput($id, 3, 1);

                    $V[2] = getMicrotime() + ($E[4]['value'] * 86400);
                    logic_setVar($id, 2, $V[2]);

                    logic_setVar($id, 1, (getMicrotime() + $E[5]['value']));
                    logic_setState($id, 1, $E[5]['value'] * 1000);
                }

            } else {
                if (getMicrotime() >= $V[1]) {
                    logic_setOutput($id, 1, 1);    //Ventil öffnen
                    logic_setOutput($id, 3, 0);
                    logic_setState($id, 0);
                }

                $V[2] = getMicrotime() + ($E[4]['value'] * 86400);
                logic_setVar($id, 2, $V[2]);
            }
        }

        //Alarm (warten auf Reset)
        if ($E[3]['value'] != 0 && $E[3]['refresh'] == 1 && $V[1] < 0) {
            logic_setOutput($id, 1, 1);    //Ventil öffnen
            logic_setOutput($id, 2, 0);

            $V[1] = 0;
            logic_setVar($id, 1, $V[1]);

            $V[2] = getMicrotime() + ($E[4]['value'] * 86400);
            logic_setVar($id, 2, $V[2]);
        }

    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
