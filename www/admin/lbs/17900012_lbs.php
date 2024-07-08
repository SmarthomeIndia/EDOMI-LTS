###[DEF]###
[name            =    Pool: Pegelauswertung]

[e#1    TRIGGER    =    Status: Pegel                                ]
[e#2    OPTION    =    Pegel: zu hoch                                ]
[e#3    OPTION    =    Pegel: normal                                ]
[e#4    OPTION    =    Pegel: niedrig                                ]
[e#5    OPTION    =    Pegel: zu niedrig                            ]


[a#1            =    Status                                ]

[v#1    REMANENT=-1                        ]
[v#2            =-1                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein wertet einen (Pool-)Pegelstand aus, d.h. je nach Pegelstand wird A1 auf einen entsprechenden Wert gesetzt.

An E1 wird der aktuelle Pegel erwartet, ein hoher Wert entspricht einem hohen Pegelstand. Der Baustein wird bei jedem neuen Telegramm &ne;[leer] an E1 getriggert.

E2..E5 legen die jeweiligen Schwellenwerte fest:
<ul>
    <li>zu hoch (E2): dieser Schwellenwert definiert einen zu hohen Pegelstand, d.h. ein Pegel größer/gleich E2 sollte z.B. zu einem Abpumpen führen (A1=3)</li>
    <li>normal (E3): dieser Schwellenwert definiert den normalen Pegelstand, d.h. je nach vorherigem Pegelstand (zu hoch bzw. zu niedrig) wird dieser Pegelstand
        angestrebt (A1=0)
    </li>
    <li>niedrig (E4): dieser Schwellenwert definiert einen niedrigen (jedoch nicht <i>zu niedrigen</i>) Pegelstand, d.h. ein Pegel kleiner/gleich E4 sollte z.B.
        zu einem Nachfüllen führen (A1=2)
    </li>
    <li>zu niedrig (E5): dieser Schwellenwert definiert einen zu niedrigen Pegelstand, d.h. ein Pegel kleiner/gleich E5 sollte z.B. zum Ausschalten der
        Umwälzpumpe (Trocklaufgefahr) und zu einem Nachfüllen führen (A1=1)
    </li>
</ul>

An A1 wird der entsprechende Status (bei Änderung) ausgegeben und kann mittels E4 des
<link>LBS 17900010***lbs_17900010</link> direkt ausgewertet werden.

Der letzte Status wird intern remanent beibehalten, so dass auch nach einem Neustart der letzte Zustand wiederhergestellt wird. A1 wird nach einem Neustart auf den entsprechenden Wert gesetzt, sofern der Baustein durch E1 getriggert wird.
Ist der Pegel z.B. "zu niedrig", wird A1=1 gesetzt und es sollte solange Frischwasser nachgefüllt werden bis der Pegel größer/gleich E3 ist (A1=0). Erreicht der Pegel nun z.B. einen Stand zwischen E3 und E4, würde bei einem Neustart der Pegelstand im Normbereich liegen (A1=0). Durch das remanente Verhalten des LBS wird jedoch der letzte Zustand (A1=1) nach einem Neustart beibehalten, so dass das Nachfüllen fortgesetzt werden kann.

E1: &ne;[leer] = aktueller Pegel
E2..E5: Schwellenwerte (s.o.)

A1: aktueller Status (SBC)
<ul>
    <li>0 = der Pegel ist im Normbereich (liegt also zwischen den Werten an E2 und E4), nach einer erfolgten Pegelanpassung (A1&ne;0) sollte der Pegel in Etwa
        dem Wert an E3 entsprechen
    </li>
    <li>1 = der Pegel ist zu niedrig, d.h. ggf. sollte z.B. eine Umwälzpumpe (Pool) ausgeschaltet werden und Frischwasser nachgefüllt werden</li>
    <li>2 = der Pegel ist niedrig, d.h. Frischwasser sollte nachgefüllt werden</li>
    <li>3 = der Pegel ist zu hoch (z.B. durch Regen), d.h. es sollte überschüssiges Wasser abgepumpt werden</li>
</ul>
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[1]['refresh'] == 1 && !isEmpty($E[1]['value'])) {

            //Neustart?
            if ($V[1] >= 0 && $V[2] < 0) {
                $V[2] = $V[1];
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 1, $V[1]);
            }

            if ($V[1] != 3 && $E[1]['value'] >= $E[2]['value']) {
                //Abpumpen bis normal
                $V[1] = 3;
                $V[2] = 3;
                logic_setVar($id, 1, $V[1]);
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 1, $V[1]);

            } else if ($V[1] != 1 && $E[1]['value'] <= $E[5]['value']) {
                //Auffüllen bis normal
                $V[1] = 1;
                $V[2] = 1;
                logic_setVar($id, 1, $V[1]);
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 1, $V[1]);

            } else if (($V[1] != 2 && $E[1]['value'] <= $E[4]['value'] && $E[1]['value'] > $E[5]['value']) || ($V[1] == 1 && $E[1]['value'] < $E[3]['value'] && $E[1]['value'] >= $E[4]['value'])) {
                //Nachfüllen bis normal
                $V[1] = 2;
                $V[2] = 2;
                logic_setVar($id, 1, $V[1]);
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 1, $V[1]);

            } else if ($V[1] == -1 || ($V[1] == 3 && $E[1]['value'] <= $E[3]['value']) || ($V[1] == 2 && $E[1]['value'] >= $E[3]['value']) || ($V[1] == 1 && $E[1]['value'] >= $E[3]['value'])) {
                //Pegel im Normbereich oder Pegelanpassung beendet
                $V[1] = 0;
                $V[2] = 0;
                logic_setVar($id, 1, $V[1]);
                logic_setVar($id, 2, $V[2]);
                logic_setOutput($id, 1, $V[1]);
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
