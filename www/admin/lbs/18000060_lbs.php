###[DEF]###
[name        =R/G/B &#x25B8; RGB/HSV        ]

[e#1 TRIGGER=R #init=0                ]
[e#2 TRIGGER=G #init=0                ]
[e#3 TRIGGER=B #init=0                ]

[a#1        =RGB                ]
[a#2        =HSV                ]
[a#3        =R                    ]
[a#4        =G                    ]
[a#5        =B                    ]
[a#6        =H                    ]
[a#7        =S                    ]
[a#8        =V                    ]
###[/DEF]###


###[HELP]###
Konvertiert R/G/B-Werte (z.B. Status von RGB-LEDs) in das interne RGB- und HSV-Format.

E1: R (Byte)
E2: G (Byte)
E3: B (Byte)

A1: RGB-Wert im hex-Format ("RRGGBB")
A2: HSV-Wert im hex-Format ("HHSSVV")
A3: R (Rot: 0..255)
A4: G (Grün: 0..255)
A5: B (Blau: 0..255)
A6: H (Farbe: 0..255)
A7: S (Sättigung: 0..255)
A8: V (Helligkeit: 0..255)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 || $E[2]['refresh'] == 1 || $E[3]['refresh'] == 1) {
            if (is_numeric($E[1]['value']) && is_numeric($E[2]['value']) && is_numeric($E[3]['value'])) {

                $r = round($E[1]['value']);
                $g = round($E[2]['value']);
                $b = round($E[3]['value']);

                if ($r >= 0 && $r <= 255 && $g >= 0 && $g <= 255 && $b >= 0 && $b <= 255) {
                    $rgb = ($r * 256 * 256) + ($g * 256) + $b;
                    logic_setOutput($id, 1, sprintf("%06X", $rgb));
                    logic_setOutput($id, 3, $r);
                    logic_setOutput($id, 4, $g);
                    logic_setOutput($id, 5, $b);

                    $hsv = convertRGBtoHSV($r, $g, $b);
                    if ($hsv !== false) {
                        $hsvhex = (round($hsv[0]) * 256 * 256) + (round($hsv[1]) * 256) + round($hsv[2]);
                        logic_setOutput($id, 2, sprintf("%06X", $hsvhex));
                        logic_setOutput($id, 6, round($hsv[0]));
                        logic_setOutput($id, 7, round($hsv[1]));
                        logic_setOutput($id, 8, round($hsv[2]));
                    }
                }

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
