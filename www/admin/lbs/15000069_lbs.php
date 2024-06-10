###[DEF]###
[name		=Byte &#x25B8; 8-Bit		]

[e#1 TRIGGER=					]

[a#1		=Bit<sub>0</sub> 		]
[a#2		=Bit<sub>1</sub> 		]
[a#3		=Bit<sub>2</sub> 		]
[a#4		=Bit<sub>3</sub>		]
[a#5		=Bit<sub>4</sub>		]
[a#6		=Bit<sub>5</sub>		]
[a#7		=Bit<sub>6</sub>		]
[a#8		=Bit<sub>7</sub>		]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet aus einem Byte 8 einzelne Bits.

E1: Byte (0..255)
A1..A8: Ergebnis: Bit0..Bit7 (1,2,4,8,...128)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		if ($E[1]['refresh']==1) {
			$E[1]['value']=intval($E[1]['value']);
			if ($E[1]['value']<0) {$E[1]['value']=0;}
			if ($E[1]['value']>255) {$E[1]['value']=255;}
			logic_setOutput($id,1,(($E[1]['value']&1)?1:0));
			logic_setOutput($id,2,(($E[1]['value']&2)?1:0));
			logic_setOutput($id,3,(($E[1]['value']&4)?1:0));
			logic_setOutput($id,4,(($E[1]['value']&8)?1:0));
			logic_setOutput($id,5,(($E[1]['value']&16)?1:0));
			logic_setOutput($id,6,(($E[1]['value']&32)?1:0));
			logic_setOutput($id,7,(($E[1]['value']&64)?1:0));
			logic_setOutput($id,8,(($E[1]['value']&128)?1:0));
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
