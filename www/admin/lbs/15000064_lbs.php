###[DEF]###
[name        =Differenz zum Vorgängerwert        ]
[titel        =Differenz Vorgänger                ]

[e#1 TRIGGER    =                        ]
[e#2 TRIGGER    =Reset                    ]
[e#3            =Modus #init=0            ]

[a#1            =                        ]

[v#1    REMANENT=                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet die Differenz zwischen dem aktuellen Wert an E1 und dem vorherigen Wert an E1.

Beim allerersten Start (bzw. nach einem Reset durch E2 oder Zurücksetzen der remanenten Variablen) existiert noch kein Vorgängerwert. Erst wenn ein neues gültiges Telegramm an E1 eintrifft, wird dieser Wert intern remanent gespeichert und beim nächsten Telegramm an E1 entsprechend abgezogen.

Der Wert an E1 muss &ne;[leer] und nummerisch sein, ansonsten wird das Telegramm vollständig ignoriert.

Wird E2 auf einen Wert &ne;0 gesetzt, wird der Baustein zurückgesetzt (d.h. der intern gespeicherte Vorgängerwert wird gelöscht). A1 wird dabei nicht verändert.

Mit E3 kann optional ein Vergleicher aktiviert werden:
<ul>
    <li>0: Vergleicher ist deaktiviert</li>
    <li>1: das Ergebnis muss &gt;0 sein, sonst wird A1 nicht verändert</li>
    <li>2: das Ergebnis muss &gt;=0 sein, sonst wird A1 nicht verändert</li>
    <li>3: das Ergebnis muss &lt;0 sein, sonst wird A1 nicht verändert</li>
    <li>4: das Ergebnis muss &lt;=0 sein, sonst wird A1 nicht verändert</li>
</ul>

Hinweis:
Auch bei aktiviertem Vergleicher wird der aktuelle Wert (E1) stets unabhängig vom Vergleicher-Ergebnis als Vorgängerwert gespeichert.


E1: &ne;[leer] und nummerisch = Trigger
E2: &ne;0 = Reset (s.o.)
E3: 1..4=Vergleicher aktivieren (s.o.), 0=kein Vergleicher

A1: Differenz von E1 und Vorgängerwert (sofern die o.g. Bedingungen erfüllt sind)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if (($E = logic_getInputs($id)) && ($V = logic_getVars($id))) {

        if ($E[2]['refresh'] == 1 && $E[2]['value'] != 0) {
            $V[1] = null;
            logic_setVar($id, 1, $V[1]);
        }

        if ($E[1]['refresh'] == 1 && !isEmpty($E[1]['value']) && is_numeric($E[1]['value'])) {
            if (!isEmpty($V[1])) {
                $tmp = $E[1]['value'] - $V[1];
                if ($E[3]['value'] == 0 || ($E[3]['value'] == 1 && $tmp > 0) || ($E[3]['value'] == 2 && $tmp >= 0) || ($E[3]['value'] == 3 && $tmp < 0) || ($E[3]['value'] == 4 && $tmp <= 0)) {
                    logic_setOutput($id, 1, $tmp);
                }
            }
            logic_setVar($id, 1, $E[1]['value']);
        }

    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
