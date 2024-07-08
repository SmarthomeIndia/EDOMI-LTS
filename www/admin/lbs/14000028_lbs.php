###[DEF]###
[name        =FlipFlop                ]

[e#1 TRIGGER=Trigger                ]
[e#2        =Reset                    ]

[a#1        =0|E<sub>1</sub>        ]
[a#2        =0|1                    ]

[v#1        =0                        ]
###[/DEF]###


###[HELP]###
Dieser Baustein bildet ein FlipFlop nach.

Ein neues Telegramm &ne;0 an E1 triggert das FlipFlop, A1 wird auf den Wert an E1 gesetzt und A2 wird =1 gesetzt. Treffen nun weitere Telegramme an E1 ein, werden diese ignoriert.
Erst wenn ein neues Telegramm &ne;0 an E2 eintrifft, wird das FlipFlop zurückgesetzt: A1 und A2 werden =0 gesetzt.

E1: Signal &ne;0: Ein Telegramm &ne;0 setzt A1 solange auf E1 (und A2 auf 1), bis der Baustein resettet wird (E2). Weitere Telegramme werden bis zum Reset ignoriert!
E2: Reset &ne;0: Ein Telegramm &ne;0 resettet den Baustein und setzt A1 und A2 auf 0
A1: bei getriggertem FlipFlop wird A1 auf den Wert an E1 gesetzt, bei einem Reset (E2) wird A1=0 gesetzt
A2: bei getriggertem FlipFlop wird A2=1 gesetzt, bei einem Reset (E2) wird A2=0 gesetzt
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if (logic_getVar($id, 1) == 0) {
            if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {
                //triggern
                logic_setOutput($id, 1, $E[1]['value']);
                logic_setOutput($id, 2, 1);
                logic_setVar($id, 1, 1);
            }
        } else {
            if ($E[2]['value'] != 0 && $E[2]['refresh'] == 1) {
                //Reset
                logic_setOutput($id, 1, 0);
                logic_setOutput($id, 2, 0);
                logic_setVar($id, 1, 0);
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
