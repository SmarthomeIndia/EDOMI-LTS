###[DEF]###
[name        =Logging            ]

[e#1 TRIGGER=Logeintrag            ]
[e#2        =Loglevel            ]
[e#3 IMPORTANT=Dateiname            ]
###[/DEF]###


###[HELP]###
Dieser Baustein schreibt alle Werte an E1 in eine Logdatei. Der Dateiname des Individual-Logs muss an E3 angegeben werden.

Optional kann an E2 ein beliebiger Wert als "Loglevel" angegeben werden. Der Baustein wird jedoch nur durch E1 getriggert!

<b>Wichtig:</b>
Individual-Logs müssen ggf. zunächst in der
<link>Basis-Konfiguration***0-1-1</link> aktiviert werden.


E1: jedes(!) Telegramm triggert den Baustein und wird in die Logdatei geschrieben
E2: (optional) ein beliebiger Wert
E3: beliebiger Dateiname (Individual-Log)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if ($E[1]['refresh'] == 1 && !isEmpty($E[3]['value'])) {
            writeToCustomLog($E[3]['value'], $E[2]['value'], $E[1]['value']);
        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
