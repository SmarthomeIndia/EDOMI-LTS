###[DEF]###
[name        =Ausgangsbox: Vergleichswert=&#91;leer&#93;    ]
[titel        =Ausgangsbox            ]

[e#1 TRIGGER=Trigger            ]
[e#2        =Vergleichswert                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus, sofern der Vergleichswert an E2 [leer] ist.

Getriggert wird der Baustein durch ein beliebiges Telegramm &ne;[leer] an E1: Die Befehle werden jedoch nur ausgeführt, wenn E2=[leer] ist.

E1: jedes Telegramm &ne;[leer] triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle, sofern der Wert an E2 =[leer] ist
E2: Vergleichswert: die Befehle werden nur ausgeführt, wenn der Vergleichswert an E2 =[leer] ist (i.d.R. ein leeres KO)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1 && isEmpty($E[2]['value'])) {
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
