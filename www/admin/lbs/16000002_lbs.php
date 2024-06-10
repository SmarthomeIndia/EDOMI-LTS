###[DEF]###
[name			=	Ein-Verlängerung]

[e#1	TRIGGER	=	Trigger										] 
[e#2	OPTION	=	Dauer (s)				#init=10			]
[e#3	TRIGGER	=	Reset										]

[a#1			=	]

[v#1			=0						]
[v#2			=0						]
###[/DEF]###


###[HELP]###
Dieser Baustein verlängert ein Telegramm &ne;0 an E1 um die Dauer an E2.

Ein Telegramm &ne;0 an E1 triggert den Baustein, A1 wird auf 1 gesetzt. Jedes neue Telegramm &ne;0 an E1 wird nun ignoriert. Erst wenn ein Telegramm =0 an E1 eintrifft, wird der Timer (E2) gestartet.
Trifft während des laufenden Timers ein neues Telegramm &ne;0 an E1 ein, wird der Timer gestoppt und A1 bleibt auf 1. Erst nach Ablauf des Timers (E1 muss =0 sein) wird A1 auf 0 gesetzt.

Ein Telegramm &ne;0 an E3 setzt den Baustein zurück: Sofern A1=1 ist wird A1=0 gesetzt und ein laufender Timer ggf. abgebrochen. Anschließend wartet der Baustein erneut auf ein Telegramm &ne;0 an E1.

Dieser Baustein kann z.B. verwendet werden, um das Signal eines Bewegungsmelders zu verlängern: Solange der BWM eine 1 sendet, bleibt A1 auf 1. Erst wenn das Signal auf 0 abfällt (fallende Flanke) wird der Timer gestartet und A1 wird nach Ablauf des Timers auf 0 gesetzt.


E1: ein neues Telegramm &ne;0 triggert den Baustein, ein <i>anschließendes</i> Telegramm =0 startet den Timer
E2: Dauer der Verlängerung in Sekunden
E3: ein neues Telegramm &ne;0 setzt den Baustein zurück, A1 wird ggf. auf den Wert 0 gesetzt und der Timer ggf. abgebrochen

A1: 1=Baustein wurde getriggert bzw. der Timer läuft, 0=Timer ist abgelaufen
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {

		if ($E[1]['refresh']==1 && $E[1]['value']!=0) {
			if ($V[1]!=1) {
				$V[1]=1;
				logic_setVar($id,1,$V[1]);
				logic_setOutput($id,1,1);
			}
			logic_setState($id,0);

		} else if ($E[1]['refresh']==1 && $E[1]['value']==0 && $V[1]==1) {
			$V[2]=getMicrotime()+$E[2]['value'];
			logic_setVar($id,2,$V[2]);
			logic_setState($id,1,$E[2]['value']*1000);
		}

		if ($E[3]['refresh']==1 && $E[3]['value']!=0) {
			if ($V[1]!=0) {
				logic_setOutput($id,1,0);
			}
			$V[1]=0;
			logic_setVar($id,1,$V[1]);
			logic_setState($id,0);
		}

		if (logic_getState($id)==1 && $V[1]==1 && getMicrotime()>=$V[2]) {
			$V[1]=0;
			logic_setVar($id,1,$V[1]);
			logic_setOutput($id,1,0);
			logic_setState($id,0);
		}

	}
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
