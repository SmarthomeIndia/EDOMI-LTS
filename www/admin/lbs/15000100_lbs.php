###[DEF]###
[name        =Entropie (Unruhe)        ]
[titel        =Entropie                ]

[e#1 TRIGGER=Trigger            ]
[e#2        =Intervall (s) #init=30    ]
[e#3        =Reduktion #init=1        ]
[e#4        =Reset                    ]

[a#1        =                ]

[v#1        =0                        ]    Timer
[v#2        =0                        ]    Zählerstand
[v#3        =0                        ]    SBC
###[/DEF]###


###[HELP]###
Dieser Baustein ermittelt ein Maß für die "Unruhe" (Entropie) eines Signals.

Jedes neues Telegramm &gt;0 an E1 startet den Baustein, bzw. triggert den Baustein erneut.
Der Wert an E1 gibt dabei die Gewichtung an: Je größer der Wert, desto "unruhiger" wird das Ergebnis sein.
Nach Ablauf des Intervalls an E2 wird die Summe aller bislang aufgelaufenen Werte um E3 reduziert, d.h. das Ergebnis (A1) wird im Laufe der Zeit immer kleiner ("ruhiger").

Erreicht das Ergebnis den Wert 0, wird der Baustein gestoppt (wird jedoch bei einem erneuten Trigger automatisch wieder gestartet).

Während der Baustein arbeitet, kann über E4 ein Reset ausgelöst werden: Der Baustein stoppt unmittelbar und setzt das Ergbnis auf 0.

Anwendungsbeispiel:
Ein (oder mehrere) Bewegungsmelder triggert E1 bei jeder Bewegung und erhöht somit das Maß der Unruhe. Wird für einen definierten Zeitraum keine Bewegung mehr registriert, erreicht die "Unruhe" den Wert 0. Dies kann z.B. als "Abwesenheit" interpretiert werden.

Hinweis:
Mehrere Trigger (z.B. Bewegungsmelder) mit unterschiedlichen Gewichtungen können z.B. mit einem Baustein "Multiplikation" (Gewichtung) über einen Baustein "Klemme" mit E1 verbunden werden.

E1: Jedes neue Telegramm &gt;0 triggert den LBS. Der Wert an E1 bestimmt zudem die Gewichtung.
E2: Intervall (s): Nach Ablauf dieses Intervalls wird das Ergebnis ("Unruhe") um 1 reduziert
E3: Dieser Wert wird zu jedem Intervall (E2) vom internen Zählerstand abgezogen
E4: Ein neues Telegramm &ne;0 setzt den (laufenden) Baustein zurück (A1=0)
A1: Ergebnis 0..oo als Maß der "Unruhe": Je größer der Wert, desto "unruhiger". Achtung: A1 wird nur bei Änderung gesetzt (SBC)!
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[1]['value'] > 0 && $E[1]['refresh'] == 1) {
            $V[2] += $E[1]['value'];
            if ($V[2] != $V[3]) {
                logic_setOutput($id, 1, $V[2]);
            }
            $V[3] = $V[2];

            if (logic_getState($id) == 0) {
                $V[1] = getMicrotime() + $E[2]['value'];
                logic_setState($id, 1, $E[2]['value'] * 1000);
            }
        }

        if (logic_getState($id) != 0) {

            if ($E[4]['value'] != 0 && $E[4]['refresh'] == 1) {
                $V[1] = 0;
                $V[2] = 0;
            }

            if (getMicrotime() >= $V[1]) {
                $V[1] = getMicrotime() + $E[2]['value'];
                $V[2] -= $E[3]['value'];
                if ($V[2] > 0) {
                    logic_setState($id, 1, $E[2]['value'] * 1000);
                } else {
                    $V[2] = 0;
                    logic_setState($id, 0);
                }
                if ($V[2] != $V[3]) {
                    logic_setOutput($id, 1, $V[2]);
                }
                $V[3] = $V[2];
            }

            logic_setVar($id, 1, $V[1]);
            logic_setVar($id, 2, $V[2]);
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
