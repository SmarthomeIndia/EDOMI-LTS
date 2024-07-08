###[DEF]###
[name        =Sperre    mit Halten        ]

[e#1        =Entsperrt #init=0        ]
[e#2 TRIGGER=Trigger                ]
[e#3 OPTION    =Modus #init=0            ]

[a#1        =E<sub>2</sub>                        ]

[v#1        =0                                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein sperrt zunächst alle eintreffenden Telegramme an E2, solange E1=0 ist.

Ist die Sperre entsperrt (E1&ne;0) werden alle Telegramme an E2 unverändert an A1 ausgegeben.
Wird nun die Sperre gesperrt (E1=0) werden ebenfalls alle Telegramme an E2 unverändert an A1 ausgegeben, bis ein Telegramm =0 an E2 eintrifft: Erst dann wird die Sperre tatsächlich gesperrt.

Eine einmal entsperrte Sperre bleibt also solange entsperrt, bis ein Telegramm mit dem Wert 0 an E2 eintrifft oder E2 beim Sperren (E1=0) bereits den Wert 0 hat.

Je nach Modus (E3) verhält sich die Sperre wie folgt:
<ul>
    <li>E3=0: Beim Entsperren (E1&ne;0) wird A1 nicht(!) verändert - erst beim Eintreffen eines neuen(!) Telegramms an E2 wird dieses an A1 durchgereicht. Beim
        anschließenden Sperren (E1=0) wird das Telegramm an E2 an A1 durchgereicht, sobald E2=0 wird oder ist.
    </li>
    <li>E3=1: Beim Sperren (E1=0) bzw. Entsperren (E1&ne;0) wird bereits der Wert an E2 an A1 ausgegeben - beim Eintreffen eines neuen Telegramms an E2 wird
        dieses ebenfalls an A1 durchgereicht.
    </li>
    <li>Hinweis: wenn E3=1 ist, wird A1 auch dann aktualisiert wenn z.B. E1 erneut auf 1 gesetzt wird (obgleich E1 bereits 1 war)</li>
</ul>

E1: 0 = gesperrt, &ne;0 = entsperrt
E2: Signal (neue Telegramme an E2 werden an A1 durchgereicht, wenn E1 &ne;0 ist - Details siehe oben)
E3: Modus (siehe oben)

A1: wird auf den Wert an E2 gesetzt, sofern E1&ne;0 (entsperrt) ist oder E1 im Anschluss =0 (gesperrt) wird, jedoch noch kein Telegramm =0 an E2 eingetroffen ist
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        $V1 = logic_getVar($id, 1);

        if ($E[1]['value'] != 0 && $V1 != 1) {
            $V1 = 1;
            logic_setVar($id, 1, $V1);

        } else if ($E[1]['value'] == 0 && $V1 == 1 && $E[2]['value'] == 0) {
            $V1 = 0;
            logic_setVar($id, 1, $V1);
            logic_setOutput($id, 1, $E[2]['value']);
        }

        if ($V1 != 0) {
            if ($E[2]['refresh'] == 1 || $E[3]['value'] == 1) {
                logic_setOutput($id, 1, $E[2]['value']);
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
