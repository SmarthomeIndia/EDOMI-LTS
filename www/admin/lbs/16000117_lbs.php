###[DEF]###
[name        =Telegrammgenerator    ]

[e#1 TRIGGER=Trigger/Stop #init=0        ]
[e#2        =Wert                    ]
[e#3        =Intervall (ms) #init=500    ]
[e#4        =Zyklen #init=0    ]

[a#1        =                ]

[v#1        =0                        ]
[v#2        =0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 zyklisch auf den Wert von E2, generiert also Telegramme mit dem Inhalt von E2.

Ein neues Telegramm =1 an E1 startet den Baustein, ein neues Telegramm=0 stoppt den Baustein.
Beim Start des Bausteins wird A1 unmittelbar auf den Wert von E2 gesetzt. Anschließend wird A1 alle "Wert an E3" Millisekunden auf den Wert von E2 gesetzt.

Wichtig: Wird E2 während der Laufzeit des Bausteins (E1=1) verändert, wird auch der ausgegebene Wert an A1 entsprechend verändert - allerdings erst beim auf die Änderung folgenden Intervall (also nicht unmittelbar).

Die Anzahl der generierten Telegramme wird mit E4 definiert, E4=0 läßt den Baustein solange arbeiten, bis E1=0 wird.
Wichtig: Das erste Telegramm wird unmittelbar beim Start des Bausteins generiert und wird zur Gesamtanzahl (E4) hinzugerechnet.

E1: 1=Start, 0=Stop
E2: der zyklisch an A1 auszugebende Wert
E3: Intervall (ms)
E4: Anzahl der zu generierenden Telegramme: 0=unendlich (bis E1=0 ist), 1..&infin;=Anzahl
A1: wird zyklisch auf den Wert von E2 gesetzt
###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id)
{

    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[3]['value'] < 1) {
            $E[3]['value'] = 1;
        }

        if (logic_getState($id) == 0) {

            if ($E[1]['value'] == 1 && $E[1]['refresh'] == 1) { //Start
                logic_setVar($id, 1, getMicrotime() + ($E[3]['value'] / 1000)); //Timer (für High und Low)
                logic_setOutput($id, 1, $E[2]['value']);
                if ($E[4]['value'] != 1) {            //nur starten, wenn mehr als 1 Durchgang (oder 0) gewählt wurde
                    logic_setVar($id, 2, 2);    //der Start ist quasi schon der erste Durchgang - daher 2
                    logic_setState($id, 1, $E[3]['value']);
                }
            }

        } else {

            if (getMicrotime() >= $V[1]) {

                $V[1] = getMicrotime() + ($E[3]['value'] / 1000);    //falls das Intervall während der Laufzeit verändert wird
                logic_setVar($id, 1, $V[1]);

                logic_setOutput($id, 1, $E[2]['value']);
                logic_setState($id, 1, $E[3]['value']);

                if ($E[4]['value'] > 0) {
                    //Zyklus-Anzahl prüfen
                    if ($V[2] < $E[4]['value']) {
                        //Anzahl noch nicht erreicht
                        logic_setVar($id, 2, ($V[2] + 1));
                    } else {
                        //Anzahl der Zyklen erledigt => Ende
                        logic_setState($id, 0);
                    }
                }
            }

            if ($E[1]['value'] == 0) {    //Stop
                logic_setState($id, 0);
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
