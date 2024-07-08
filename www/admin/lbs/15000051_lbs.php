###[DEF]###
[name        =Subtraktion A&ndash;B        ]
[titel        =A&ndash;B                    ]

[e#1 TRIGGER=A            ]
[e#2 TRIGGER=B                ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein subtrahiert einen Wert an E2 von einem Wert an E1. Jedes neue Telegramm an E1 oder E2 triggert den Baustein.

Wenn E1 oder E2 keine Zahlen sind, entspricht dies der Zahl 0. Es wird also immer ein gültiges Ergebnis an A1 ausgegeben, sobald an E1 und/oder E2 ein neues Telegramm eintrifft.

E1: Wert A
E2: Wert B
A1: E1 - E2 (A-B)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 || $E[2]['refresh'] == 1) {

            $A = $E[1]['value'];
            if (!is_numeric($A)) {
                $A = 0;
            }
            $B = $E[2]['value'];
            if (!is_numeric($B)) {
                $B = 0;
            }

            logic_setOutput($id, 1, ($A - $B));
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
