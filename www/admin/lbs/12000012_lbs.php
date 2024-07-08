###[DEF]###
[name        =Ausgangsbox: &ne;&#91;leer&#93;    ]
[titel        =Ausgangsbox            ]

[e#1 TRIGGER=&ne;&#91;leer&#93;                ]
###[/DEF]###


###[HELP]###
Dieser Baustein führt beliebige Befehle aus und schließt somit i.d.R. eine Verkettung von Logikbausteinen ab.

Getriggert wird der Baustein durch ein Telegramm &ne;[leer] an E1.

E1: jedes Telegramm &ne;[leer] triggert den Baustein und führt zur Ausführung der zugewiesenen Befehle

<b>Hinweis:</b>
Die Darstellung dieser Ausgangsbox kann in der
<link>Basis-Konfiguration***a-1</link> modifiziert werden (z.B. kann diese Ausgangsbox auf Wunsch einzeilig dargestellt werden). In der Vorschau werden diese Darstellungsoptionen jedoch ignoriert.
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {
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
