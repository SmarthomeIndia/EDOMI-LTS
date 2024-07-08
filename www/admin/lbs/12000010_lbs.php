###[DEF]###
[name        =Ausgangsbox            ]

[e#1 TRIGGER=&ne;0                    ]
[e#2 TRIGGER=&ne;&#91;leer&#93;        ]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus und schließt somit i.d.R. eine Verkettung von Logikbausteinen ab.

Getriggert wird der Baustein entweder durch ein Telegramm &ne;0 an E1 oder(!) durch ein beliebiges Telegramm &ne;[leer] an E2.

E2 wird i.d.R. verwendet, um z.B. ein KO auf den Eingangswert des Bausteins zu setzen. Würde für diesen Zweck E1 verwendet, würde das KO nur dann auf den Eingangswert gesetzt werden, wenn der Eingangswert ungleich der Zahl 0 ist.

<b>Wichtig:</b>
E1 hat Priorität gegenüber E2: Die Befehle werden nur einmalig ausgeführt - entweder getriggert durch E1 oder(!) E2. Es ist daher i.d.R. sinnvoll entweder E1 oder(!) E2 zu belegen, aber nicht beide Eingänge!

E1: jedes Telegramm &ne;0 triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle
oder(!)
E2: jedes Telegramm &ne;[leer] triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['value'] != 0 && $E[1]['refresh'] == 1) {
            mainLogicExecuteCmdList($id, $E[1]['value']);
        } else if (!isEmpty($E[2]['value']) && $E[2]['refresh'] == 1) {
            mainLogicExecuteCmdList($id, $E[2]['value']);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
