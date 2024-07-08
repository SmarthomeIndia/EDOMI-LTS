###[DEF]###
[name        =Sperre                    ]

[e#1        =Entsperrt #init=0        ]
[e#2 TRIGGER=Trigger                ]
[e#3 OPTION    =Modus #init=0            ]

[a#1        =E<sub>2</sub>                        ]
[a#2        =(E<sub>2</sub>)                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein sperrt alle eintreffenden Telegramme an E2, solange E1=0 ist.

Ist die Sperre entsperrt (E1&ne;0) werden Telegramme an E2 unverändert an A1 ausgegeben.
Ist die Sperre gesperrt (E1=0) werden Telegramme an E2 unverändert an A2 ausgegeben.

Je nach Modus (E3) verhält sich die Sperre wie folgt:
<ul>
    <li>E3=0: Beim Sperren (E1=0) bzw. Entsperren (E1&ne;0) werden A1 bzw. A2 nicht(!) verändert - erst beim Eintreffen eines neuen(!) Telegramms an E2 wird
        dieses an A1 bzw. A2 durchgereicht.
    </li>
    <li>E3=1: Beim Sperren (E1=0) bzw. Entsperren (E1&ne;0) wird bereits der Wert an E2 an A1 bzw. A2 ausgegeben - beim Eintreffen eines neuen Telegramms an E2
        wird dieses ebenfalls an A1 bzw. A2 durchgereicht.
    </li>
    <li>Hinweis: wenn E3=1 ist, werden A1 bzw. A2 auch dann aktualisiert wenn z.B. E1 erneut auf 1 gesetzt wird (obgleich E1 bereits 1 war)</li>
</ul>

E1: 0 = gesperrt, &ne;0 = entsperrt
E2: Signal (neue Telegramme an E2 werden an A1 bzw. A2 durchgereicht, wenn E1 &ne;0 bzw. =0 ist)
E3: Modus (siehe oben)

A1: wird auf den Wert an E2 gesetzt, sofern E1&ne;0 (entsperrt) ist
A2: wird auf den Wert an E2 gesetzt, sofern E1=0 (gesperrt) ist
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['value'] != 0) {
            if ($E[2]['refresh'] == 1 || $E[3]['value'] == 1) {
                logic_setOutput($id, 1, $E[2]['value']);
            }
        } else {
            if ($E[2]['refresh'] == 1 || $E[3]['value'] == 1) {
                logic_setOutput($id, 2, $E[2]['value']);
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
