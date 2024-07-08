###[DEF]###
[name        =Runden                        ]

[e#1 TRIGGER    =                        ]
[e#2            =Richtung #init=0        ]
[e#3            =Präzision #init=0        ]

[a#1            =                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein rundet einen Wert an E1 auf oder ab.

Mit E2 kann die Richtung des Rundens festgelegt werden:
<ul>
    <li>Ist E2&gt;0 wird der Wert an E1 stets auf die nächstgrößere Ganzzahl aufgerundet (z.B. wird aus 3.4 die Ganzzahl 4). E3 wird ignoriert.</li>
    <li>Ist E2&lt;0 wird der Wert an E1 stets auf die nächstkleinere Ganzzahl abgerundet (z.B. wird aus 3.4 die Ganzzahl 3). E3 wird ignoriert.</li>
    <li>
        Ist E2=0 erfolgt das Runden automatisch, d.h. je nach Wert an E1 wird auf- oder abgerundet. E3 legt dabei die Präzision wie folgt fest:
        <ul>
            <li>E3=0: der Wert an E1 wird auf die nächste Ganzzahl auf- oder abgerundet (z.B. 3.49 &rarr; 3 oder 3.50 &rarr; 4)</li>
            <li>E3&gt;0: der Wert an E1 wird auf diese Anzahl an Nachkommastellen auf- oder abgerundet (z.B. E3=1: 3.49 &rarr; 3.5 oder 3.44 &rarr; 3.4)</li>
            <li>E3&lt;0: der Wert an E1 wird auf diese Zehnerstellen (vor dem Dezimalpunkt) auf- oder abgerundet (z.B. E3=-1: 3.49 &rarr; 0 oder 123.49 &rarr;
                120)
            </li>
        </ul>
    </li>
</ul>

Jedes neue Telegramm an E1 triggert den Baustein. Sofern an E1 ein nummerischer Wert anliegt, wird A1 auf den gerundeten Wert gesetzt.
Der Wert an E1 muss &ne;[leer] und nummerisch sein, ansonsten wird das Telegramm ignoriert.

<b>Hinweis:</b>
Nur E1 triggert ggf. den Baustein (Änderungen an E2 oder E3 führen <i>nicht</i> zur Neuberechnung von A1).


E1: &ne;[leer] und nummerisch = Trigger
E2: Richtung (s.o.)
E3: Präzision (s.o.)

A1: wird bei jedem gültigen Trigger (E1) auf den gerundeten Wert gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if ($E[1]['refresh'] == 1 && !isEmpty($E[1]['value']) && is_numeric($E[1]['value'])) {
            if ($E[2]['value'] == 0) {
                logic_setOutput($id, 1, round($E[1]['value'], $E[3]['value']));

            } else if ($E[2]['value'] > 0) {
                logic_setOutput($id, 1, ceil($E[1]['value']));

            } else if ($E[2]['value'] < 0) {
                logic_setOutput($id, 1, floor($E[1]['value']));
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
