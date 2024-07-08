###[DEF]###
[name        =Prozess-Status: KNX    ]

[e#1 TRIGGER=Trigger            ]

[a#1        =Status                        ]
[a#2        =Queuegröße                    ]
[a#3        =GAs: gesendet                ]
[a#4        =GAs: empfangen                ]
[a#5        =Senderate                    ]
[a#6        =Empfangsrate                ]
[a#7        =Fehler: unbekannte GAs        ]
[a#8        =Fehler: Senden/Empfangen    ]
[a#9        =Fehler: Verbindung            ]
[a#10        =PROC-RAM                    ]
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
<link>KNX-Gateway***a-1-1</link> nicht aktiviert ist, erfolgt keine Reaktion an den Ausgängen.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Verbindungsstatus: 1=verbunden, 0=nicht verbunden
A2: Anzahl der auf den KNX-Bus zu schreibenden Telegramme in der Warteschlange
A3: Gesamtanzahl aller gesendeten Telegramme (EDOMI > KNX)
A4: Gesamtanzahl aller empfangenen Telegramme (KNX > EDOMI)
A5: aktuelle KNX-Senderate (EDOMI > KNX)
A6: aktuelle KNX-Empfangsrate (KNX > EDOMI)
A7: Gesamtanzahl aller unbekannten Gruppenadressen (GAs, die EDOMI nicht bekannt sind)
A8: Gesamtanzahl aller Fehler beim Senden/Empfangen von Gruppenadressen
A9: Anzahl der fehlgeschlagenen Verbindungsversuche zur KNX-Schnittstelle
A10: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {

            $r = procStatus_getData(3);
            if (!isEmpty($r[9])) {
                logic_setOutput($id, 1, $r[9]);
                logic_setOutput($id, 2, $r[0]);
                logic_setOutput($id, 3, $r[3]);
                logic_setOutput($id, 4, $r[5]);
                logic_setOutput($id, 5, $r[13]);
                logic_setOutput($id, 6, $r[15]);
                logic_setOutput($id, 7, $r[2]);
                logic_setOutput($id, 8, $r[6]);
                logic_setOutput($id, 9, $r[7]);
                logic_setOutput($id, 10, $r[20]);
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
