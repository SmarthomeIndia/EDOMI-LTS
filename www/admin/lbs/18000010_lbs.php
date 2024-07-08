###[DEF]###
[name        =Archiv: Auslesen/Ändern/Löschen    ]

[e#1 TRIGGER=Trigger                ]
[e#2        =Archivtyp                ]
[e#3        =ArchivID                ]
[e#4        =Modus #init=0            ]
[e#5        =Index #init=0            ]
[e#6        =Daten                    ]

[a#1        =Daten        ]
[a#2        =Zeitstempel]
[a#3        =Fehler        ]
###[/DEF]###


###[HELP]###
Dieser Baustein kann einen Archiveintrag auslesen, löschen oder überschreiben. Getriggert wird der Baustein ausschließlich bei einem neuen Telegramm=1/2/3/4 an E1.

Der gewünschte Archiveintrag wird mittels E2, E3, E4 und E5 definiert:
E2 bestimmt den übergeordneten Archivtyp (z.B. Datenarchiv), E3 definiert das gewünschte Archiv (die ArchivID ist z.B. in der Konfiguration hinter dem Namen des Archivs zu finden).
Mit E4 und E5 wird die Art und Weise bestimmt, mit der ein Archiveintrag (Datensatz) bestimmt werden soll: E5 kann eine positive Ganzzahl sein, die den "Abstand" (Offset) zum Anfang (E4=0) oder zum Ende (E4=1) des Archivs angibt.
Alternativ kann E5 den Zeitstempel des gewünschten Archiveintrags definieren. Der Zeitstempel muss entweder exakt übereinstimmen (E4=3) oder es wird der Archiveintrag gewählt, der dem Zeitstempel am nächsten kommt (E4=2).

Im Erfolgsfall wird A1 auf den Wert des Archiveintrags gesetzt, A2 auf den entsprechenden Zeitstempel. A3 wird auf 0 (kein Fehler) gesetzt.
Im Fehlerfall wird A3=1 gesetzt, A1 und A2 bleiben unverändert.

Der Archiveintrag kann zusätzlich gelöscht (E1=2) oder überschrieben (E1=3 bzw. E1=4) werden. In diesem Fall muss E6 mit den neuen Daten belegt sein. Achtung: Es erfolgt keinerlei Validierung, d.h. E6 wird unverändert in das Archiv geschrieben!

A1 und A2 werden stets auf den vorhandenen(!) Archiveintrag gesetzt (sofern kein Fehler vorliegt) - auch wenn der Archiveintrag gelöscht oder überschrieben wird.

E1: Ein neues Telegramm mit einem der folgenden Werte triggert den Baustein:
<ul>
    <li>1=Archiveintrag auslesen</li>
    <li>2=Archiveintrag auslesen und löschen (aus dem Archiv entfernen!)</li>
    <li>3=Archiveintrag auslesen und mit Wert an E6 überschreiben (Zeitstempel unverändert lassen)</li>
    <li>4=Archiveintrag auslesen und mit Wert an E6 überschreiben (Zeitstempel aktualisieren)</li>
</ul>

E2: Auswahl des Archivtyps:
<ul>
    <li>0=Datenarchiv</li>
    <li>1=Meldungsarchiv</li>
    <li>2=Anrufarchiv</li>
    <li>3=Kameraarchiv</li>
</ul>

E3: Auswahl der Archiv-ID (wird in der Konfiguration angezeigt)

E4: Modus:
<ul>
    <li>0=Wert an E5 ist bezogen auf den "Anfang" des Archivs (ältester Eintrag)</li>
    <li>1=Wert an E5 ist bezogen auf das "Ende" des Archivs (neuester Eintrag)</li>
    <li>2=Wert an E5 ist ein Zeitstempel in der Form "31.12.2016/13:34:59/123456" - es wird der Archiveintrag ermittelt, der diesem Zeitstempel am nächsten
        kommt
    </li>
    <li>3=Wert an E5 ist ein Zeitstempel in der Form "31.12.2016/13:34:59/123456" - Zeitstempel muss exakt übereinstimmen</li>
</ul>

E5: Index:
<ul>
    <li>bei Modus=0/1: Relative Position des Archiveintrags
        <ul>
            <li>0=erster Eintrag (bzw. letzter Eintrag)</li>
            <li>1=zweiter Eintrag (bzw. vorletzter Eintrag)</li>
            <li>2..oo=etc.</li>
        </ul>
    </li>
    <li>bei Modus=2/3: Zeitstempel
        <ul>
            <li>Zeitstempel in der Form "31.12.2016/13:34:59/123456"</li>
        </ul>
    </li>
</ul>

E6: Diese Daten werden ggf. in das Archiv geschrieben (nur bei Trigger=3/4, nicht möglich bei Kameraarchiven)

A1: Daten: Bei jedem Triggern des Bausteins wird hier der aktuelle(!) Inhalt des gewählten Archiveintrags ausgegeben.
<ul>
    <li>bei Kameraarchiven wird der Dateiname des Kamerabildes (ohne Pfad) ausgegeben</li>
    <li>bei Trigger=2/3/4 wird ebenfalls der im Archiv vorhandene(!) Archiveintrag ausgegeben (also der Archiveintrag vor(!) dem Löschen bzw. Überschreiben)
    </li>
</ul>

A2: Zeitstempel: Bei jedem Triggern des Bausteins wird hier der Zeitstempel des gewählten Archiveintrags ausgegeben (z.B. "31.12.2016/13:34:59/000123")
<ul>
    <li>bei Trigger=2/3/4 wird ebenfalls der im Archiv vorhandene(!) Zeitstempel ausgegeben (also der Zeitstempel vor(!) dem Löschen bzw. Überschreiben)</li>
</ul>

A3: Fehler: Bei jedem Triggern des Bausteins wird A3 auf 0, im Fehlerfall auf 1 gesetzt:
<ul>
    <li>0=kein Fehler</li>
    <li>1=Fehler (Archiv/Archiveintrag nicht vorhanden oder Parameter unvollständig bzw. fehlerhaft)</li>
</ul>
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {

        if ($E[1]['refresh'] == 1 && $E[1]['value'] >= 1 && $E[1]['value'] <= 4) {

            $sqlData = '';
            $sqlDataTs = '';
            $archivDb = '';
            $archivId = 0;
            $error = true;

            for ($t = 1; $t <= 4; $t++) {
                $E[$t]['value'] = abs(intVal($E[$t]['value']));
            }

            if ($E[2]['value'] == 0) {
                $archivDb = 'archivKoData';
            } else if ($E[2]['value'] == 1) {
                $archivDb = 'archivMsgData';
            } else if ($E[2]['value'] == 2) {
                $archivDb = 'archivPhoneData';
            } else if ($E[2]['value'] == 3) {
                $archivDb = 'archivCamData';
            }

            if ($E[3]['value'] > 0) {
                $archivId = $E[3]['value'];
            }

            if ($E[4]['value'] == 0 || $E[4]['value'] == 1) {
                $E[5]['value'] = abs(intVal($E[5]['value']));
            } else if ($E[4]['value'] == 2 || $E[4]['value'] == 3) {
                $tmp = explode('/', $E[5]['value'] . '//');
                $ts_Datetime = date('Y-m-d H:i:s', strtotime($tmp[0] . ' ' . $tmp[1]));
                $ts_Ms = abs(intVal($tmp[2]));
            }

            if (!isEmpty($archivDb) && $archivId > 0 && $E[4]['value'] >= 0 && $E[4]['value'] <= 3) {

                if ($E[4]['value'] == 0) {
                    $ss1 = sql_call("SELECT * FROM edomiLive." . $archivDb . " WHERE (targetid=" . $archivId . ") ORDER BY datetime ASC,ms ASC LIMIT " . $E[5]['value'] . ",1");
                } else if ($E[4]['value'] == 1) {
                    $ss1 = sql_call("SELECT * FROM edomiLive." . $archivDb . " WHERE (targetid=" . $archivId . ") ORDER BY datetime DESC,ms DESC LIMIT " . $E[5]['value'] . ",1");
                } else if ($E[4]['value'] == 2) {
                    $ss1 = sql_call("SELECT * FROM edomiLive." . $archivDb . " WHERE (targetid=" . $archivId . ") ORDER BY ABS(CONCAT(UNIX_TIMESTAMP(datetime),LPAD(ms,6,'0'))-CONCAT(UNIX_TIMESTAMP('" . $ts_Datetime . "'),LPAD('" . $ts_Ms . "',6,'0'))) ASC LIMIT 0,1");
                } else if ($E[4]['value'] == 3) {
                    $ss1 = sql_call("SELECT * FROM edomiLive." . $archivDb . " WHERE (targetid=" . $archivId . " AND datetime='" . $ts_Datetime . "' AND ms='" . $ts_Ms . "') LIMIT 0,1");
                }

                if ($n = sql_result($ss1)) {

                    //Datensatz lesen
                    $error = false;
                    $sqlDataTs = date('d.m.Y/H:i:s', strtotime($n['datetime'])) . '/' . sprintf("%06d", $n['ms']);
                    if ($E[2]['value'] == 0) {
                        $sqlData = $n['gavalue'];
                    } else if ($E[2]['value'] == 1) {
                        $sqlData = $n['msg'];
                    } else if ($E[2]['value'] == 2) {
                        $sqlData = $n['phone'];
                    } else if ($E[2]['value'] == 3) {
                        $sqlData = getArchivCamFilename($n['targetid'], $n['camid'], $n['datetime'], $n['ms']) . '.jpg';
                    }

                    if ($E[1]['value'] == 2) {
                        //Datensatz löschen
                        if ($E[2]['value'] == 0) {
                            executeCmd(array('cmd' => 50, 'cmdid1' => $archivId, 'cmdoption1' => 3), array($n['datetime'], $n['ms']));
                        } else if ($E[2]['value'] == 1) {
                            executeCmd(array('cmd' => 51, 'cmdid1' => $archivId, 'cmdoption1' => 3), array($n['datetime'], $n['ms']));
                        } else if ($E[2]['value'] == 2) {
                            executeCmd(array('cmd' => 53, 'cmdid1' => $archivId, 'cmdoption1' => 3), array($n['datetime'], $n['ms']));
                        } else if ($E[2]['value'] == 3) {
                            executeCmd(array('cmd' => 52, 'cmdid1' => $archivId, 'cmdoption1' => 3), array($n['datetime'], $n['ms']));
                        }
                        //Kameraarchiv: Bilddatei löschen
                        if ($E[2]['value'] == 3) {
                            $fn = getArchivCamFilename($n['targetid'], $n['camid'], $n['datetime'], $n['ms']);
                            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/' . $fn . '.jpg');
                        }
                    } else if ($E[1]['value'] == 3 || $E[1]['value'] == 4) {
                        //Datensatz updaten
                        $sqlTS = '';
                        if ($E[1]['value'] == 4) {
                            //Timestamp updaten
                            $ts = getTimestamp();
                            $sqlTS = "datetime='" . $ts[0] . "',ms='" . $ts[1] . "',";
                        }
                        if ($E[2]['value'] == 0) {
                            sql_call("UPDATE edomiLive." . $archivDb . " SET " . $sqlTS . "gavalue='" . sql_encodeValue($E[6]['value']) . "' WHERE (targetid=" . $archivId . " AND datetime='" . $n['datetime'] . "' AND ms='" . $n['ms'] . "')");
                        } else if ($E[2]['value'] == 1) {
                            sql_call("UPDATE edomiLive." . $archivDb . " SET " . $sqlTS . "msg='" . sql_encodeValue($E[6]['value']) . "' WHERE (targetid=" . $archivId . " AND datetime='" . $n['datetime'] . "' AND ms='" . $n['ms'] . "')");
                        } else if ($E[2]['value'] == 2) {
                            sql_call("UPDATE edomiLive." . $archivDb . " SET " . $sqlTS . "phone='" . sql_encodeValue($E[6]['value']) . "' WHERE (targetid=" . $archivId . " AND datetime='" . $n['datetime'] . "' AND ms='" . $n['ms'] . "')");
                        } else if ($E[2]['value'] == 3) {
                            $error = true;
                        }    //Kameraarchiv kann nicht überschrieben werden
                    }

                }
                sql_close($ss1);
            }

            if ($error) {
                logic_setOutput($id, 3, 1);
            } else {
                logic_setOutput($id, 1, $sqlData);
                logic_setOutput($id, 2, $sqlDataTs);
                logic_setOutput($id, 3, 0);
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
