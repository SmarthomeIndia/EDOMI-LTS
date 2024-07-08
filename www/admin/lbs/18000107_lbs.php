###[DEF]###
[name        =Prozess-Status: VISU    ]

[e#1 TRIGGER=Trigger            ]

[a#1        =Status                        ]
[a#2        =Online                        ]
[a#3        =Queuegröße                    ]
[a#4        =Aktualisierungen/s            ]
[a#5        =Elemente/s                    ]
[a#6        =Trigger/s                    ]
[a#7        =Seitenaufrufe/s            ]
[a#8        =Senden (kb/s)                ]
[a#9        =Empfangen (kb/s)            ]
[a#10        =PROC-RAM                    ]
[a#11        =KO                            ]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur
<link>Statusseite***0-0</link> entnommen werden.

<b>Hinweis:</b>
Wenn keine Visualisierungen verfügbar sind, erfolgt keine Reaktion an den Ausgängen.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Websocket-Verbindungsstatus: 1=verbunden, 0=nicht verbunden
A2: Anzahl der Visualisierungen, die aktuell auf allen Endgeräten angezeigt werden
A3: Anzahl der Visubefehle in der Warteschlange (z.B. Sprachausgabe, Visualarm, etc.) über alle Visualisierungen und Accounts
A4: Gesamtanzahl der Aktualisierungen pro Sekunde (unabhängig von der Anzahl der aktualisierten Visuelemente)
A5: Gesamtanzahl der Aktualisierungen von Visuelementen pro Sekunde
A6: Gesamtanzahl der aktuellen Trigger (z.B. Klick auf eine Schaltfläche)
A7: Gesamtanzahl der Seitenaufrufe pro Sekunde
A8: Gesamtdatenmenge pro Sekunde (kb), die vom Server an die Clients übermittelt wird
A9: Gesamtdatenmenge pro Sekunde (kb), die von den Clients an den Server übermittelt wird
A10: Speichernutzung des Prozesses in MB
A11: Gesamtanzahl der auf Änderung überwachten Kommunikationsobjekte
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {

            $r = procStatus_getData(7);
            if (!isEmpty($r[0])) {
                logic_setOutput($id, 1, $r[0]);
                logic_setOutput($id, 2, $r[1]);
                logic_setOutput($id, 3, $r[8]);
                logic_setOutput($id, 4, $r[2]);
                logic_setOutput($id, 5, $r[3]);
                logic_setOutput($id, 6, $r[4]);
                logic_setOutput($id, 7, $r[5]);
                logic_setOutput($id, 8, round($r[6] / 1024, 2));
                logic_setOutput($id, 9, round($r[7] / 1024, 2));
                logic_setOutput($id, 10, $r[20]);
                logic_setOutput($id, 11, $r[9]);
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
