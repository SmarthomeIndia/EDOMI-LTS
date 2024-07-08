###[DEF]###
[name        =8-Bit &#x25B8; Byte        ]

[e#1 TRIGGER=Bit<sub>0</sub>        ]
[e#2 TRIGGER=Bit<sub>1</sub>        ]
[e#3 TRIGGER=Bit<sub>2</sub>        ]
[e#4 TRIGGER=Bit<sub>3</sub>        ]
[e#5 TRIGGER=Bit<sub>4</sub>        ]
[e#6 TRIGGER=Bit<sub>5</sub>        ]
[e#7 TRIGGER=Bit<sub>6</sub>        ]
[e#8 TRIGGER=Bit<sub>7</sub>        ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet aus 8 einzelnen Bits ein Byte.

E1..E8: Bit0..Bit7 (1,2,4,8,...128), jeder Wert &ne;0 wird als binär 1 interpretiert
A1: Ergebnis: Byte (0..255)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 || $E[2]['refresh'] == 1 || $E[3]['refresh'] == 1 || $E[4]['refresh'] == 1 || $E[5]['refresh'] == 1 || $E[6]['refresh'] == 1 || $E[7]['refresh'] == 1 || $E[8]['refresh'] == 1) {
            $byte = 0;
            if (is_numeric($E[1]['value']) && $E[1]['value'] != 0) {
                $byte += 1;
            }
            if (is_numeric($E[2]['value']) && $E[2]['value'] != 0) {
                $byte += 2;
            }
            if (is_numeric($E[3]['value']) && $E[3]['value'] != 0) {
                $byte += 4;
            }
            if (is_numeric($E[4]['value']) && $E[4]['value'] != 0) {
                $byte += 8;
            }
            if (is_numeric($E[5]['value']) && $E[5]['value'] != 0) {
                $byte += 16;
            }
            if (is_numeric($E[6]['value']) && $E[6]['value'] != 0) {
                $byte += 32;
            }
            if (is_numeric($E[7]['value']) && $E[7]['value'] != 0) {
                $byte += 64;
            }
            if (is_numeric($E[8]['value']) && $E[8]['value'] != 0) {
                $byte += 128;
            }
            logic_setOutput($id, 1, $byte);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
