###[DEF]###
[name		=RGB/HSV &#x25B8; R/G/B/H/S/V		]

[e#1 TRIGGER=RGB 				]
[e#2 TRIGGER=HSV 				]

[a#1		=R						]
[a#2		=G						]
[a#3		=B						]
[a#4		=H					]
[a#5		=S					]
[a#6		=V					]
###[/DEF]###


###[HELP]###
Konvertiert das interne RGB- oder(!) HSV-Format in R/G/B/H/S/V-Werte (z.B. zur Ansteuerung von RGB-LEDs).
Entweder E1 mit RGB belegen oder(!) E2 mit HSV belegen!

E1: RGB-Wert im hex-Format ("RRGGBB")
E2: oder(!) HSV-Wert im hex-Format ("HHSSVV")
A1: R (Rot: 0..255)
A2: G (Grün: 0..255)
A3: B (Blau: 0..255)
A4: H (Farbe: 0..255)
A5: S (Sättigung: 0..255)
A6: V (Helligkeit: 0..255)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {

		if ($E[1]['refresh']==1 && strlen($E[1]['value'])==6) {
			$r=hexdec(substr($E[1]['value'],0,2));
			$g=hexdec(substr($E[1]['value'],2,2));
			$b=hexdec(substr($E[1]['value'],4,2));
			if ($r>=0 && $r<=255 && $g>=0 && $g<=255 && $b>=0 && $b<=255) {
				logic_setOutput($id,1,$r);
				logic_setOutput($id,2,$g);
				logic_setOutput($id,3,$b);
				$hsv=convertRGBtoHSV($r,$g,$b);
				if ($hsv!==false) {
					logic_setOutput($id,4,intVal($hsv[0]));
					logic_setOutput($id,5,intVal($hsv[1]));
					logic_setOutput($id,6,intVal($hsv[2]));
				}
			}
		}

		if ($E[2]['refresh']==1 && strlen($E[2]['value'])==6) {
			$h=hexdec(substr($E[2]['value'],0,2));
			$s=hexdec(substr($E[2]['value'],2,2));
			$v=hexdec(substr($E[2]['value'],4,2));
			if ($h>=0 && $h<=255 && $s>=0 && $s<=255 && $v>=0 && $v<=255) {
				logic_setOutput($id,4,$h);
				logic_setOutput($id,5,$s);
				logic_setOutput($id,6,$v);
				$rgb=convertHSVtoRGB($h,$s,$v);
				if ($rgb!==false) {
					logic_setOutput($id,1,intVal($rgb[0]));
					logic_setOutput($id,2,intVal($rgb[1]));
					logic_setOutput($id,3,intVal($rgb[2]));
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
