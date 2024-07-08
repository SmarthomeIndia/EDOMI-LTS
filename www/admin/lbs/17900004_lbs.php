###[DEF]###
[name            =    WC-Spülung]

[e#1    TRIGGER    =    Abspülen                                ]
[e#2    TRIGGER    =    Automatisch abspülen                    ]
[e#3    OPTION    =    Abspülen: max. Anzahl        #init=1        ]
[e#4    OPTION    =    Automatik: Intervall (Tage)    #init=0        ]
[e#5    OPTION    =    Nachladedauer (s)            #init=10    ]
[e#6            =    Aktiviert (E1)                #init=1        ]

[a#1            =    Abspülen                                ]
[a#2            =    Nachladen                                ]
[a#3            =    Abspülen (Auto)                            ]

[v#1            =0                        ]
[v#2            =0                        ]
[v#3    REMANENT=0                        ]
[v#4    REMANENT=0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein steuert eine elektrisch betätigte WC-Spülung.

Sobald ein neues Telegramm &ne;0 an E1 eintrifft (z.B. durch die Drückerplatte), wird die Spülung ausgelöst (A1=1). Anschließend wartet der Baustein (A2=1), bis die Nachladedauer (E5) erreicht worden ist (A2=0).
Trifft <i>während dieser
    Nachladezeit</i> erneut ein Telegramm &ne;0 an E1 ein, wird dieser "Wunsch zum erneuten Abspülen" intern vorgemerkt und nach erfolgtem Nachladen ausgelöst. Die maximale Anzahl dieser Mehrfachspülungen wird an E3 festgelegt.

E4 gibt das Intervall (in Tagen) für ein automatisches Abspülen an, z.B. um ein Austrocknen des Siphons zu vermeiden. Sobald an E2 ein neues Telegramm &ne;0 eintrifft, wird automatisch abgespült sofern das an E4 angegebene Intervall erreicht oder überschritten worden ist.
Der Baustein arbeitet <i>nicht</i> zyklisch, d.h. das automatische Abspülen erfolgt nur wenn E2 entsprechend getriggert wird.
Beim automatischen Abspülen wird A1=1 und A3=1 gesetzt (A3 kann z.B. zur Protokollierung des automatischen Abspülens verwendet werden).

Mit E6=0 kann das manuelle Abspülen (E1) vorübergehend deaktiviert werden, z.B. während der Reinigung der Drückerplatte (um ein versehentliches Auslösen zu verhindern).


E1: &ne;0 = Abspülen (ggf. auch mehrfach hintereinander)
E2: &ne;0 = automatisches Abspülen ggf. durchführen (sofern das Intervall an E4 erreicht/überschritten wurde)
E3: 1..&infin; = maximale Anzahl von Spülungen während der Nachladezeit
E4: Intervall für automatisches Abspülen in Tagen (0=deaktiviert)
E5: Nachladedauer (in Sekunden)
E6: &ne;0 = manuelles Abspülen ist aktiviert, 0 = manuelles Abspülen ist deaktiviert (das automatische Abspülen bleibt ggf. aktiviert)

A1: 1 = Abspülen (manuell oder automatisch)
A2: 1 = lädt gerade nach, 0 = bereit zum Abspülen
A3: 1 = automatisches Abspülen (z.B. für Protokollierung)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($V[3] == 0 || ($E[4]['value'] != $V[4] && $E[4]['refresh'] == 1)) {
            $V[3] = getMicrotime() + ($E[4]['value'] * 86400);
            logic_setVar($id, 3, $V[3]);
            $V[4] = $E[4]['value'];
            logic_setVar($id, 4, $V[4]);
        }

        if (logic_getState($id) == 0) {
            //erste Spülung (Nachladen komplett abgeschlossen)
            if (($E[1]['value'] != 0 && $E[1]['refresh'] == 1 && $E[6]['value'] != 0) || ($E[2]['value'] != 0 && $E[2]['refresh'] == 1 && $E[4]['value'] > 0 && getMicrotime() >= $V[3])) {
                logic_setOutput($id, 1, 1);
                logic_setOutput($id, 2, 1);

                if ($E[1]['refresh'] == 0) {
                    logic_setOutput($id, 3, 1);
                }

                $V[3] = getMicrotime() + ($E[4]['value'] * 86400);
                logic_setVar($id, 3, $V[3]);

                logic_setVar($id, 1, 1);
                logic_setVar($id, 2, (getMicrotime() + $E[5]['value']));
                logic_setState($id, 1, $E[5]['value'] * 1000);
            }

        } else {

            //weitere Spülungen erwünscht? (während des Nachladens)
            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1 && $E[6]['value'] != 0 && $V[1] < $E[3]['value']) {
                $V[1]++;
                logic_setVar($id, 1, $V[1]);
            }

            if (getMicrotime() >= $V[2]) {
                if ($V[1] == 1) {
                    //Spülung(en): fertig
                    logic_setOutput($id, 2, 0);
                    logic_setVar($id, 1, 0);
                    logic_setState($id, 0);

                } else if ($V[1] > 1) {
                    //weiteres mal abspülen
                    logic_setOutput($id, 1, 1);
                    logic_setOutput($id, 2, 1);

                    logic_setVar($id, 1, $V[1] - 1);
                    logic_setVar($id, 2, (getMicrotime() + $E[5]['value']));
                    logic_setState($id, 1, $E[5]['value'] * 1000);
                }
            }

            $V[3] = getMicrotime() + ($E[4]['value'] * 86400);
            logic_setVar($id, 3, $V[3]);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
