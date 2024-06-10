###[DEF]###
[name		= HSV-Einschalttelegramm		]

[e#1 TRIGGER= HSV							]
[e#2 OPTION	= Verzögerung (ms)	#init=0		]

[a#1		=								]

[v#1		=						]
[v#2		=						]
###[/DEF]###


###[HELP]###
Dieser Baustein generiert ggf. einen modifierten HSV-Wert und gibt diesen unmittelbar aus, gefolgt von dem gewünschten HSV-Wert.

Einige LED-Controller reagieren beim Einschalten mittels eines HSV-Wertes offenbar nicht wie erwartet, d.h. es wird nicht der korrekt Farbwert eingestellt. Erst wenn ein weiteres HSV-Telegramm (bei nunmehr eingeschalteter RGB-Leuchte) eintrifft, wird der Farbwert korrekt eingestellt.

Der Baustein kompensiert dieses Fehlverhalten dadurch, dass bei einer steigenden Flanke an E1 (im Kontext des HSV-Wertes) zunächst ein geringfügig modifierter HSV-Wert ausgegeben wird und nach einer Verzögerung (E2) der eigentliche HSV-Wert (E1) ausgegeben wird. Dadurch wird der LED-Controller zunächst "eingeschaltet" (mit einem abweichenden Farbwert) und anschließend auf den gewünschten Farbwert eingestellt.
Treffen in der Folge weitere HSV-Werte an E1 ein, werden diese unmittelbar an A1 ausgegeben (der LED-Controller ist ja bereits "eingeschaltet"). Erst wenn ein HSV-Wert mit einem V-Anteil "00" eintrifft (Aus), wird der Baustein zurückgesetzt und wartet erneut auf eine steigende Flanke.

Treffen während der Verzögerung HSV-Werte an E1 ein, bleibt A1 stets unverändert. Erst nach Ablauf der Verzögerung wird der letzte (aktuellste) HSV-Wert (E1) an A1 ausgegeben.

Hinweis:
Der generierte HSV-Wert weicht stets geringfügig (Hue +/- 1) vom gewünschten HSV-Wert ab, da der LED-Controller auf gleiche Telegramme in Folge i.d.R. nicht reagiert. Dieser generierte HSV-Wert wird jedoch nur zum "Einschalten" des LED-Controllers genutzt - anschließend (nach der Verzögerung) wird der tatsächlich gewünschte HSV-Wert (E1) ausgegeben.


E1: HSV-Wert (000000..FFFFFF)
E2: Verzögerungszeit beim Einschalten (der zeitliche Abstand des generierten und des gewünschten HSV-Wertes): 0=schnellstmöglich, 1..oo=Millisekunden

A1: HSV-Wert (000000..FFFFFF) zur direkten Ansteuerung des LED-Controllers
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {
		if (logic_getState($id)==0) {
			if ($E[1]['refresh']==1) {
				if (LB_LBSID_isOnValue($E[1]['value'])) {
					if ($V[1]==0 || isEmpty($V[1])) {
					
						//Dummy-HSV-Wert: Hue modifizieren (+1)
						$tmp=hexdec(substr($E[1]['value'],0,2));
						$tmp=(($tmp>0)?$tmp-1:$tmp+1);
						$tmp=sprintf('%02X',$tmp).substr($E[1]['value'],2,2).substr($E[1]['value'],4,2);
						logic_setOutput($id,1,$tmp);	//Dummy-Wert

						logic_setVar($id,1,1);
						logic_setVar($id,2,(getMicrotime()+($E[2]['value']/1000)));
						logic_setState($id,1,$E[2]['value']); 

					} else {
						logic_setOutput($id,1,$E[1]['value']);	//E1-Wert
					}

				} else {
					logic_setVar($id,1,0);
					logic_setOutput($id,1,$E[1]['value']);		//AUS (E1-Wert)
				}
			}

		} else {
			if (getMicrotime()>=$V[2]) {
				logic_setOutput($id,1,$E[1]['value']);			//E1-Wert
				logic_setState($id,0);
				if (!LB_LBSID_isOnValue($E[1]['value'])) {logic_setVar($id,1,0);}
			}
		}
	}
}

function LB_LBSID_isOnValue($value) {
	if (strlen($value)==6 && hexdec(substr($value,4,2))>0) {return true;}
	return false;
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
