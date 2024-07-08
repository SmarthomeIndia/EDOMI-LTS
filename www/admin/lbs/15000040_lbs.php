###[DEF]###
[name        =Vergleicher A=B        ]
[titel        =A=B?                ]

[e#1 TRIGGER=A                ]
[e#2 TRIGGER=B                ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein vergleicht einen Wert an E1 mit einem Wert an E2. Jedes neue Telegramm an E1 oder E2 triggert den Baustein.

E1: Vergleichswert A
E2: Vergleichswert B
A1: Wenn E1 (A) = E2 (B) ist, wird A1=1 gesetzt. Andernfalls wird A1=0 gesetzt.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 || $E[2]['refresh'] == 1) {
            if ($E[1]['value'] == $E[2]['value']) {
                logic_setOutput($id, 1, 1);
            } else {
                logic_setOutput($id, 1, 0);
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
