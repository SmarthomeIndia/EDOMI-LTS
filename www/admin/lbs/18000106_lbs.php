###[DEF]###
[name        =Prozess-Status: PHONE    ]

[e#1 TRIGGER=Trigger            ]

[a#1        =Status                        ]
[a#2        =Anrufe: eingehend            ]
[a#3        =Anrufe: ausgehend            ]
[a#4        =PROC-RAM                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur
<link>Statusseite***0-0</link> entnommen werden.

<b>Hinweis:</b>
Wenn das Modul
<link>Anrufmonitor***a-1-3</link> nicht aktiviert ist, erfolgt keine Reaktion an den Ausgängen.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Verbindungsstatus zur Fritzbox (Anrufmonitor): 1=verbunden, 0=nicht verbunden
A2: Anzahl der eingegangenen Anrufe
A3: Anzahl der ausgegangenen Anrufe
A4: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {

            $r = procStatus_getData(6);
            if (!isEmpty($r[0])) {
                logic_setOutput($id, 1, $r[0]);
                logic_setOutput($id, 2, $r[1]);
                logic_setOutput($id, 3, $r[2]);
                logic_setOutput($id, 4, $r[20]);
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
