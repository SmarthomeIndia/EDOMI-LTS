###[DEF]###
[name        =Datenarchiv: Statistikdaten]

[e#1 TRIGGER=Trigger                ]
[e#2        =DatenarchivID                ]
[e#3        =Modus             #init=0    ]

[a#1        =AVG        ]
[a#2        =MIN        ]
[a#3        =MAX        ]
[a#4        =SUM        ]
[a#5        =CNT        ]
[a#6        =Fehler        ]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet den Mittelwert, den Minimum-/Maximumwert, die Summe und die Anzahl der Einträge innerhalb eines Intervalls in einem Datenarchiv.

E3 (Modus) definiert die Art des Intervalls: Es kann entweder eine bestimmte Anzahl von Einträgen bestimmt werden (E3=0) oder ein definierter Zeitraum in Sekunden/Minuten/Stunden/Tagen (E3=1..4).
Das Intervall bezieht sich dabei stets auf das "Ende" des Datenarchivs, d.h. es wird vom aktuellsten Eintrag ausgegangen. Ale Referenzzeit für ein Zeitintervall (E3=1..4) wird stets das aktuelle Datum bzw. Uhrzeit verwendet.

Die gewünschte Anzahl der Einträge bzw. die Anzahl der Zeitintervalle werden mit E1 bestimmt, jedes neue Telegramm &gt;0 an E1 triggert den Baustein.

Sind keine Einträge im gewählten Intervall vorhanden wird A6=1 gesetzt (Fehler), A5 (Anzahl) wird auf 0 gesetzt und A1..A4 bleiben unverändert.


E1: Ein neues Telegramm &gt;0 triggert den Baustein (je nach Modus bestimmt E1 die Anzahl der Einträge oder einen Zeitraum)

E2: Auswahl der Datenarchiv-ID (wird in der Konfiguration angezeigt)

E3: Modus:
<ul>
    <li>0=E1 bestimmt die Anzahl der Einträge (ausgehend vom aktuellsten/letzten Eintrag)</li>
    <li>1=E1 bestimmt den Zeitraum in Sekunden (die Referenzzeit ist die aktuelle Zeit)</li>
    <li>2=E1 bestimmt den Zeitraum in Minuten (die Referenzzeit ist die aktuelle Zeit)</li>
    <li>3=E1 bestimmt den Zeitraum in Stunden (die Referenzzeit ist die aktuelle Zeit)</li>
    <li>4=E1 bestimmt den Zeitraum in Tagen (die Referenzzeit ist die aktuelle Zeit)</li>
    <li>5=die Berechnung wird auf alle verfügbaren Einträge angewendet (der Wert an E1 ist unerheblich, muss jedoch &gt;0 sein)</li>
</ul>

A1: AVG (Mittelwert): Bei jedem Triggern des Bausteins wird hier ggf. der Mittelwert der Werte innerhalb des Intervalls ausgegeben.
A2: MIN (Minimum): Bei jedem Triggern des Bausteins wird hier ggf. der kleinste Wert innerhalb des Intervalls ausgegeben.
A3: MAX (Maximum): Bei jedem Triggern des Bausteins wird hier ggf. der größte Wert innerhalb des Intervalls ausgegeben.
A4: SUM (Summe): Bei jedem Triggern des Bausteins wird hier ggf. die Summe der Werte innerhalb des Intervalls ausgegeben.
A5: CNT (Anzahl): Bei jedem Triggern des Bausteins wird hier die Anzahl der Einträge innerhalb des Intervalls ausgegeben.

A6: Fehler: Bei jedem Triggern des Bausteins wird A6 auf 0, im Fehlerfall auf 1 gesetzt:
<ul>
    <li>0=kein Fehler</li>
    <li>1=Fehler (Archiv nicht vorhanden, keine Einträge im gewählten Intervall vorhanden oder Parameter unvollständig bzw. fehlerhaft)</li>
</ul>
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if ($E[1]['refresh'] == 1 && $E[1]['value'] > 0) {

            $E[1]['value'] = abs(intVal($E[1]['value']));
            $E[2]['value'] = abs(intVal($E[2]['value']));
            $E[3]['value'] = abs(intVal($E[3]['value']));

            if ($E[1]['value'] > 0 && $E[2]['value'] > 0 && $E[3]['value'] >= 0 && $E[3]['value'] <= 5) {

                if ($E[3]['value'] == 0) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM (SELECT gavalue FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . ") ORDER BY datetime DESC,ms DESC LIMIT 0," . $E[1]['value'] . ") void");
                } else if ($E[3]['value'] == 1) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM (SELECT gavalue FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . " AND UNIX_TIMESTAMP(datetime)>=" . strtotime('now -' . $E[1]['value'] . ' seconds') . ")) void");
                } else if ($E[3]['value'] == 2) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM (SELECT gavalue FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . " AND UNIX_TIMESTAMP(datetime)>=" . strtotime('now -' . $E[1]['value'] . ' minutes') . ")) void");
                } else if ($E[3]['value'] == 3) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM (SELECT gavalue FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . " AND UNIX_TIMESTAMP(datetime)>=" . strtotime('now -' . $E[1]['value'] . ' hours') . ")) void");
                } else if ($E[3]['value'] == 4) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM (SELECT gavalue FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . " AND UNIX_TIMESTAMP(datetime)>=" . strtotime('now -' . $E[1]['value'] . ' days') . ")) void");
                } else if ($E[3]['value'] == 5) {
                    $ss1 = sql_call("SELECT COUNT(gavalue) AS vanz,AVG(CAST(gavalue AS DECIMAL(20,4))) AS vavg,MIN(CAST(gavalue AS DECIMAL(20,4))) AS vmin,MAX(CAST(gavalue AS DECIMAL(20,4))) AS vmax,SUM(CAST(gavalue AS DECIMAL(20,4))) AS vsum FROM edomiLive.archivKoData WHERE (targetid=" . $E[2]['value'] . ")");
                }

                if ($n = sql_result($ss1)) {
                    if ($n['vanz'] > 0) {
                        logic_setOutput($id, 1, $n['vavg']);
                        logic_setOutput($id, 2, $n['vmin']);
                        logic_setOutput($id, 3, $n['vmax']);
                        logic_setOutput($id, 4, $n['vsum']);
                        logic_setOutput($id, 5, $n['vanz']);
                        logic_setOutput($id, 6, 0);
                    } else {
                        logic_setOutput($id, 5, 0);
                        logic_setOutput($id, 6, 1);
                    }
                } else {
                    logic_setOutput($id, 6, 1);
                }
                sql_close($ss1);

            } else {
                logic_setOutput($id, 6, 1);
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
