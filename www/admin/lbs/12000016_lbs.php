###[DEF]###
[name        =Ausgangsbox: Vergleicher mit Sperre]
[titel        =Ausgangsbox            ]

[e#1 TRIGGER=Trigger                ]
[e#2        =Vergleichswert            ]
[e#3        =Entsperrt #init=1        ]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus, sofern der Wert an E1 dem Vergleichswert an E2 entspricht.

Getriggert wird der Baustein durch ein beliebiges Telegramm &ne;[leer] an E1: Die Befehle werden jedoch nur ausgeführt, wenn E1=E2 ist und der Baustein aktiviert ist (E3).

Bei einer Entsperrung des Bausteins (E3&ne;0) werden die Befehle erst nach einem <i>neuen</i> Telegramm (nach der Entsperrung) an E1 ggf. ausgeführt.


E1: jedes Telegramm &ne;[leer] triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle, sofern der Wert an E1=E2 ist
E2: Vergleichswert: die Befehle werden nur ausgeführt, wenn der Vergleichswert an E2=E1 ist
E3: entsperrt (&ne;0) oder sperrt (0) den Baustein
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1 && $E[3]['value'] != 0 && $E[1]['value'] == $E[2]['value']) {
            mainLogicExecuteCmdList($id, $E[1]['value']);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
