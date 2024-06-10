###[DEF]###
[name		=RGB &#x25B8; HSV				]

[e#1 TRIGGER=RGB					]

[a#1		=					]
###[/DEF]###


###[HELP]###
Konvertiert einen RGB-Wert in einen HSV-Wert.

E1: RGB-Wert im hex-Format ("RRGGBB")
A1: HSV-Wert im hex-Format ("HHSSVV")
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
				$hsv=convertRGBtoHSV($r,$g,$b);
				if ($hsv!==false) {
					logic_setOutput($id,1,sprintf("%02X",round($hsv[0])).sprintf("%02X",round($hsv[1])).sprintf("%02X",round($hsv[2])));
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
