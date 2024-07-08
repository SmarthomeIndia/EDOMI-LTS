###[DEF]###
[name        =Prozess-Status: LOGIC    ]

[e#1 TRIGGER=Trigger            ]

[a#1        =Queuegröße                    ]
[a#2        =LBS: getriggert            ]
[a#3        =LBS: laufend                ]
[a#4        =EXEC: Queuegröße            ]
[a#5        =EXEC: laufend                ]
[a#6        =PROC-RAM                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein gibt an den Ausgängen bei jedem Triggern (E1) die unten genannten Werte aus. Getriggert wird der Baustein über ein neues Telegramm &ne;[leer] an E1.

<b>Anwendungsbeispiel:</b>
An E1 kann z.B. das System-KO[5] (Systemzeit) angelegt werden, um sekündlich aktuelle Werte an den Ausgängen zu erhalten.
Alternativ kann z.B. das System-KO[26] (Trigger: Minütlich) an E1 angelegt werden, zusätzlich kann ein Initialwert 1 angegeben werden - dies führt dazu, dass der Baustein beim Start (Initialwert) und anschließend zu jeder vollen Minute getriggert wird.

Weitere Informationen können ggf. der Hilfe zur
<link>Statusseite***0-0</link> entnommen werden.


E1: jedes Telegramm &ne;[leer] triggert den Baustein

A1: Anzahl der Logikbefehle (einschl. Visu, Zeitschaltuhren, Sequenzen, etc.) in der Warteschlange
A2: Anzahl der aktuell getriggerten Logikbausteine
A3: Anzahl der aktuell laufenden Logikbausteine
A4: Anzahl der Einträge in der
<link>EXEC-Queue***r-0-2</link> über alle Logikbausteine (unabhängig von der Anzahl der Eingänge)
A5: Anzahl der Logikbausteine, deren EXEC-Script aktuell ausgeführt wird (Mehrfachausführungen werden nicht berücksichtigt)
A6: Speichernutzung des Prozesses in MB
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {

            $r = procStatus_getData(4);
            logic_setOutput($id, 1, $r[2]);
            logic_setOutput($id, 2, $r[0]);
            logic_setOutput($id, 3, $r[4]);
            logic_setOutput($id, 4, $r[5]);
            logic_setOutput($id, 5, $r[6]);
            logic_setOutput($id, 6, $r[20]);

        }
    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
