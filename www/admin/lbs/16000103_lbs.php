###[DEF]###
[name        =Betriebsstundenzähler    ]

[e#1 TRIGGER=Trigger/Stop #init=0    ]
[e#2        =Reset    #init=0            ]
[e#3 OPTION    =Intervall (s)    #init=1    ]
[e#4        =Abrufen #init=0        ]

[a#1        =h:mm:ss                ]
[a#2        =Stunden                ]
[a#3        =Minuten                ]
[a#4        =Sekunden                ]
[a#5        =Reset: h:mm:ss            ]
[a#6        =Reset: Stunden            ]
[a#7        =Reset: Minuten            ]
[a#8        =Reset: Sekunden        ]

[v#1        =                        ]
[v#2        =                        ]
[v#3 REMANENT=0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet einen Betriebsstundenzähler mit Ausgabe der aktuellen Betriebszeit nach.

Ein Telegramm &ne;0 an E1 startet den Baustein:
Die Ausgänge A1..A4 werden auf die aktuelle Betriebszeit gesetzt und fortlaufend mit dem Intervall an E3 aktualisiert (das kleinste Intervall beträgt 1 Sekunde).

Ein Telegramm =0 an E1 stoppt den Baustein, bzw. der Baustein wird angehalten:
Die Ausgänge A1..A4 werden abschließend auf die aktuelle Betriebszeit gesetzt. Bei einem erneuten Start wird die Betriebszeit entsprechend fortgeführt.

Ein Reset (E2&ne;0) setzt die Betriebszeit auf 0 zurück (auch im laufenden Betrieb des Bausteins) und gibt an A1..A4 den Wert 0 aus (an A1 wird "0:00:00" ausgegeben). An A5..A8 wird hingegen die
<i>bis zum Reset aufgelaufene Betriebszeit</i> ausgegeben (dies erfolgt <i>ausschließlich</i> bei einem Reset).

Mit einem Telegramm &ne;0 an E4 wird die aktuelle Betriebszeit sofort abgerufen und an A1..A4 ausgegeben. Dies kann z.B. nützlich sein, um nach einem EDOMI-Neustart die bis dahin ermittelte Betriebszeit gezielt abzufragen (z.B. mit einem Initialwert von 1 an E4).

Hinweis:
Das Setzen von E3 beeinflusst einen bereits laufenden Baustein nicht. Erst beim nächsten Start wird der Baustein mit diesen Intervall arbeiten.

Wichtig:
Die aktuelle Betriebszeit wird remanent gespeichert und bleibt somit auch bei einem EDOMI-Neustart erhalten. Ein Zurücksetzen ist nur über einen Reset an E2 möglich.
Das dauerhafte Speichern der Betriebszeit erfolgt beim Starten/Stoppen und während der Laufzeit mit dem am E3 festgelegten Intervall: Wird EDOMI (während der Laufzeit des Bausteins) zwischen zwei Intervallen neugestartet, wird die aktuelle Betriebszeit u.U. ungenau (zu gering) sein. Je kleiner das Intervall definiert ist, um so präziser erfolgt also die dauerhafte Speicherung im Falle eines Neustarts zur Laufzeit des Bausteins.


E1: &ne;0 = Trigger (Start), 0 = Stopp (beim Starten oder Stoppen wird die aktuelle Betriebszeit unmittelbar an A1..A4 ausgegeben)
E2: &ne;0 = Reset (die aktuelle Betriebszeit wird auf 0 zurückgesetzt und an A1..A4 ausgegeben)
E3: 1..&infin; = Aktualisierungsintervall für die Ausgänge A1..A4 (in Sekunden)
E4: aktuelle Betriebszeit sofort abrufen

A1: abgerundete(!) Betriebszeit im Format h:mm:ss (z.B. "0:12:34" oder "123:45:00")
A2: Betriebszeit in Stunden, abgerundet auf 4 Nachkommastellen (z.B. "5.1234")
A3: Betriebszeit in Minuten, abgerundet auf 4 Nachkommastellen (z.B. "4.01")
A4: Betriebszeit in Sekunden, abgerundet auf 4 Nachkommastellen (z.B. "3.001")
A5..A8: wie A1..A4, jedoch werden diese Ausgänge nur bei einem Reset auf die bis dahin aufgelaufene Betriebszeit gesetzt (A1..A4 werden bei einem Reset auf 0 gesetzt)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        $tmp = getMicrotime();

        if ($E[2]['value'] != 0 && $E[2]['refresh'] == 1) {            //Reset (auch im laufenden Betrieb)
            $resetValue = $V[2] + ($tmp - $V[1]);    //neu berechnen wegen Intervall (E3)
            $V[1] = $tmp;
            $V[2] = 0;
            $V[3] = 0;
            logic_setVar($id, 1, $V[1]);
            logic_setVar($id, 2, $V[2]);
            logic_setVar($id, 3, $V[3]);
            LB_LBSID_output($id, $V[3], $resetValue);

        } else if ($E[4]['value'] != 0 && $E[4]['refresh'] == 1) {    //Abrufen (auch im laufenden Betrieb)
            LB_LBSID_output($id, $V[3], false);
        }


        if (logic_getState($id) == 0) {
            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {        //Start
                logic_setVar($id, 1, $tmp);
                logic_setVar($id, 2, $V[3]);
                LB_LBSID_output($id, $V[3], false);
                logic_setState($id, 1, ((intVal($E[3]['value']) >= 1) ? intVal($E[3]['value']) : 1) * 1000, true);
            }

        } else {

            $V[3] = $V[2] + ($tmp - $V[1]);
            logic_setVar($id, 3, $V[3]);                            //Stand zyklisch merken (wegen möglichem Neustart im laufenden Betrieb)

            if ($E[1]['value'] == 0 && $E[1]['refresh'] == 1) {        //Stop
                logic_setState($id, 0);
            }

            LB_LBSID_output($id, $V[3], false);

        }
    }
}

function LB_LBSID_output($id, $value, $resetValue)
{
    logic_setOutput($id, 1, sprintf('%01d:%02d:%02d', (intVal($value) / 3600), (intVal($value) / 60 % 60), intVal($value) % 60));
    logic_setOutput($id, 2, round($value / 3600, 4));
    logic_setOutput($id, 3, round($value / 60, 4));
    logic_setOutput($id, 4, round($value, 4));

    if ($resetValue !== false) {
        logic_setOutput($id, 5, sprintf('%01d:%02d:%02d', (intVal($resetValue) / 3600), (intVal($resetValue) / 60 % 60), intVal($resetValue) % 60));
        logic_setOutput($id, 6, round($resetValue / 3600, 4));
        logic_setOutput($id, 7, round($resetValue / 60, 4));
        logic_setOutput($id, 8, round($resetValue, 4));
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
