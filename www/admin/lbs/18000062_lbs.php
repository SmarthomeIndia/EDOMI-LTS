###[DEF]###
[name        =HSV &#x25B8; RGB                ]

[e#1 TRIGGER=HSV                    ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Konvertiert einen HSV-Wert in einen RGB-Wert.

E1: HSV-Wert im hex-Format ("HHSSVV")
A1: RGB-Wert im hex-Format ("RRGGBB")
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if ($E[1]['refresh'] == 1 && strlen($E[1]['value']) == 6) {
            $h = hexdec(substr($E[1]['value'], 0, 2));
            $s = hexdec(substr($E[1]['value'], 2, 2));
            $v = hexdec(substr($E[1]['value'], 4, 2));
            if ($h >= 0 && $h <= 255 && $s >= 0 && $s <= 255 && $v >= 0 && $v <= 255) {
                $rgb = convertHSVtoRGB($h, $s, $v);
                if ($rgb !== false) {
                    logic_setOutput($id, 1, sprintf("%02X", round($rgb[0])) . sprintf("%02X", round($rgb[1])) . sprintf("%02X", round($rgb[2])));
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
