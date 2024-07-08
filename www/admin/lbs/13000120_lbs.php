###[DEF]###
[name        =Zähler                    ]

[e#1 TRIGGER=+1  #init=0            ]
[e#2 TRIGGER=-1 #init=0                ]
[e#3        =Reset #init=0            ]
[e#4        =Reset-Stand #init=0    ]
[e#5        =Aktiviert #init=1        ]
[e#6        =Abrufen #init=0        ]

[a#1        =                        ]
[a#2        =Reset                    ]

[v#1 REMANENT=0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein zählt mit jedem neuen Telegramm &ne;0 an E1 bzw. E2 vorwärts bzw. rückwärts. Die Schrittweite beträgt dabei stets +1 bzw. -1.

Der Zählerstand ist remanent, d.h. nach einem EDOMI-Neustart bleibt der Zählerstand erhalten. A1 wird jedoch nach einem Neustart nicht automatisch auf den aktuellen Wert gesetzt, erst nach dem Eintreffen eines Telegramms &ne;0 an E1..E3 oder E6 wird der entsprechende Zählerstand ausgegeben.

Mit einem Telegramm &ne;0 an E6 wird der aktuelle Zählerstand sofort abgerufen und an A1 ausgegeben. Dies kann z.B. nützlich sein, um nach einem EDOMI-Neustart den bis dahin ermittelten Zählerstand gezielt abzufragen (z.B. mit einem Initialwert von 1 an E6).

Hinweis:
Auch negative Zählerstände sind möglich!


E1: &ne;0 = Zähler +1
E2: &ne;0 = Zähler -1
E3: &ne;0 = Zähler zurücksetzen: der Zähler (und A1) wird auf den Wert an E4 gesetzt, A2 wird auf den aktuellen Zählerstand (vor dem Reset) gesetzt
E4: auf diesen Wert wird der Zähler bei einem Reset (E3) gesetzt
E5: &ne;0 = Aktiviert, 0 = Deaktiviert (der Zählerstand wird dabei NICHT verändert - weder beim Aktivieren, noch beim Deaktivieren)
E6: aktuellen Zählerstand sofort abrufen

A1: aktueller Zählerstand (wird gesetzt, wenn an E1/E2/E3 oder E6 ein Telegramm &ne;0 eintrifft)
A2: Zählerstand bei einem Reset (wird auf den aktuellen Zählerstand gesetzt, wenn der Zähler zurückgesetzt wird)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{

    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[5]['value'] != 0) {

            $output = false;

            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {
                $V[1]++;
                logic_setVar($id, 1, $V[1]);
                $output = true;
            }
            if ($E[2]['value'] != 0 && $E[2]['refresh'] == 1) {
                $V[1]--;
                logic_setVar($id, 1, $V[1]);
                $output = true;
            }

            if ($E[3]['value'] != 0 && $E[3]['refresh'] == 1) {
                logic_setOutput($id, 2, $V[1]);
                $V[1] = $E[4]['value'];
                if (isEmpty($V[1]) || !is_numeric($V[1])) {
                    $V[1] = 0;
                }
                logic_setVar($id, 1, $V[1]);
                $output = true;
            }

            if ($E[6]['value'] != 0 && $E[6]['refresh'] == 1) {
                $output = true;
            }

            if ($output) {
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
