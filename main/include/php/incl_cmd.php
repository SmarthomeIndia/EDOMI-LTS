<? 
function executeLogicCmdList($id, $e1)
{
    $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicCmdList WHERE (targetid=" . $id . ") ORDER BY id ASC");
    while ($n = sql_result($ss1)) {
        executeCmd($n, $e1);
    }
    sql_close($ss1);
}

function executeVisuCmdList($id, $visuid)
{
    $ss1 = sql_call("SELECT * FROM edomiLive.visuCmdList WHERE (targetid=" . $id . ") ORDER BY id ASC");
    while ($n = sql_result($ss1)) {
        executeCmd($n, $visuid);
    }
    sql_close($ss1);
}

function executeMacroCmdList($id)
{
    $ss1 = sql_call("SELECT * FROM edomiLive.macroCmdList WHERE (targetid=" . $id . ") ORDER BY id ASC");
    while ($n = sql_result($ss1)) {
        executeCmd($n, null);
    }
    sql_close($ss1);
}

function executeCmd($cmd, $e1)
{
    if ($cmd['cmd'] == 1) {
        writeGA($cmd['cmdid1'], $e1);
    } else if ($cmd['cmd'] == 2) {
        writeGA($cmd['cmdid1'], $cmd['cmdvalue1']);
    } else if ($cmd['cmd'] == 3) {
        if ($n = getGADataFromID($cmd['cmdid2'], 0, 'value')) {
            writeGA($cmd['cmdid1'], $n['value']);
        }
    } else if ($cmd['cmd'] == 4) {
        if ($n = getGADataFromID($cmd['cmdid1'], 0, 'value')) {
            if (!is_numeric($n['value']) || $n['value'] == 0) {
                writeGA($cmd['cmdid1'], $cmd['cmdvalue1']);
            } else {
                writeGA($cmd['cmdid1'], 0);
            }
        }
    } else if ($cmd['cmd'] == 5) {
        if ($n = getGADataFromID($cmd['cmdid1'], 0, 'value,vstep')) {
            if (isEmpty($n['value'])) {
                $n['value'] = 0;
            }
            if (!is_numeric($n['vstep'])) {
                $n['vstep'] = 1;
            }
            if (is_numeric($n['value'])) {
                if ($cmd['cmdoption1'] == 1) {
                    writeGA($cmd['cmdid1'], $n['value'] + $n['vstep']);
                }
                if ($cmd['cmdoption1'] == -1) {
                    writeGA($cmd['cmdid1'], $n['value'] - $n['vstep']);
                }
            }
        }
    } else if ($cmd['cmd'] == 6) {
        if ($n = getGADataFromID($cmd['cmdid2'], 0, 'value')) {
            if (!is_numeric($n['value']) || $n['value'] == 0) {
                writeGA($cmd['cmdid1'], $cmd['cmdvalue1']);
            } else {
                writeGA($cmd['cmdid1'], 0);
            }
        }
    } else if ($cmd['cmd'] == 19) {
        if ($n = getGADataFromID($cmd['cmdid2'], 0, 'value')) {
            if (!is_numeric($n['value']) || $n['value'] == 1) {
                writeGA($cmd['cmdid1'], $cmd['cmdvalue1']);
            } else {
                writeGA($cmd['cmdid1'], 1);
            }
        }
    } else if ($cmd['cmd'] == 7) {
        if ($n = getGADataFromID($cmd['cmdid1'], 0, 'value')) {
            if (isEmpty($n['value'])) {
                $n['value'] = 0;
            }
            if (is_numeric($n['value']) && is_numeric($cmd['cmdvalue1'])) {
                writeGA($cmd['cmdid1'], $n['value'] + $cmd['cmdvalue1']);
            }
        }
    } else if ($cmd['cmd'] == 8) {
        if ($n = getGADataFromID($cmd['cmdid1'], 1, 'id')) {
            requestGA($cmd['cmdid1']);
        }
    } else if ($cmd['cmd'] == 9) {
        if ($n = getGADataFromID($cmd['cmdid1'], 0, 'value,vcsv')) {
            $r = formatGaValueFromCsv($n['value'], $n['vcsv'], $cmd['cmdvalue1']);
            writeGA($cmd['cmdid1'], $r);
        }
    } else if ($cmd['cmd'] == 10) {
        if ($cmd['cmdoption1'] == 0) {
            $ss1 = sql_call("SELECT gaid,gavalue FROM edomiLive.sceneList WHERE (targetid=" . $cmd['cmdid1'] . ") ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                writeGA($n['gaid'], $n['gavalue']);
            }
            sql_close($ss1);
        } else if ($cmd['cmdoption1'] == 1) {
            $ss1 = sql_call("SELECT id,learngaid,valuegaid FROM edomiLive.sceneList WHERE (targetid=" . $cmd['cmdid1'] . " AND learngaid>0) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                if ($nn = getGADataFromID($n['learngaid'], 0, 'value')) {
                    sql_call("UPDATE edomiLive.sceneList SET gavalue='" . sql_encodeValue($nn['value']) . "' WHERE (id=" . $n['id'] . ")");
                    if ($n['valuegaid'] > 0) {
                        writeGA($n['valuegaid'], $nn['value']);
                    }
                }
            }
            sql_close($ss1);
        }
    } else if ($cmd['cmd'] == 11) {
        if ($cmd['cmdoption1'] == 0) {
            sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (11,2," . $cmd['cmdid1'] . ",0)");
        } else if ($cmd['cmdoption1'] == 1) {
            sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (11,2," . $cmd['cmdid1'] . ",1)");
        }
    } else if ($cmd['cmd'] == 12) {
        $ss1 = sql_call("SELECT camid FROM edomiLive.archivCam WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            queueCmd(120, $cmd['cmdid1'], $n['camid']);
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 13) {
        $ss1 = sql_call("SELECT outgaid,delay FROM edomiLive.archivKo WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if (checkArchivDelay('archivKoData', $cmd['cmdid1'], $n['delay'])) {
                $ts = getTimestampRound($cmd['cmdoption1']);
                sql_call("INSERT INTO edomiLive.archivKoData (datetime,ms,gavalue,targetid) VALUES ('" . $ts[0] . "','" . $ts[1] . "','" . sql_encodeValue($e1) . "'," . $cmd['cmdid1'] . ")");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.archivKoData', 'targetid=' . $cmd['cmdid1']));
                }
            }
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 14) {
        $ss1 = sql_call("SELECT outgaid,delay FROM edomiLive.archivMsg WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if (checkArchivDelay('archivMsgData', $cmd['cmdid1'], $n['delay'])) {
                $ts = getTimestampRound($cmd['cmdoption1']);
                if (!($cmd['cmdid2'] >= 1)) {
                    $cmd['cmdid2'] = 0;
                }
                sql_call("INSERT INTO edomiLive.archivMsgData (datetime,ms,msg,targetid,formatid) VALUES ('" . $ts[0] . "','" . $ts[1] . "','" . sql_encodeValue($e1) . "'," . $cmd['cmdid1'] . "," . $cmd['cmdid2'] . ")");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.archivMsgData', 'targetid=' . $cmd['cmdid1']));
                }
            }
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 15) {
        $ss1 = sql_call("SELECT iptyp FROM edomiLive.ip WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            queueCmd(140, $cmd['cmdid1'], $n['iptyp']);
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 16) {
        queueCmd(130, $cmd['cmdid1'], $cmd['cmdoption1']);
    } else if ($cmd['cmd'] == 17) {
        executeMacroCmdList($cmd['cmdid1']);
    } else if ($cmd['cmd'] == 18 && $e1 >= 1) {
        $visuId = sql_getValue('edomiLive.visuPage', 'visuid', 'id=' . $e1 . ' AND visuid=' . $cmd['cmdid1']);
        if (!isEmpty($visuId)) {
            if ($cmd['cmdid2'] == 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",2," . $e1 . ")");
                }
                sql_close($ss1);
            } else if ($cmd['cmdid2'] > 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND targetid=" . $cmd['cmdid2'] . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",2," . $e1 . ")");
                }
                sql_close($ss1);
            }
        }
    } else if ($cmd['cmd'] == 20) {
        queueCmd(100, $cmd['cmdid1']);
    } else if ($cmd['cmd'] == 21) {
        $visuId = sql_getValue('edomiLive.visuPage', 'visuid', 'id=' . $cmd['cmdid1']);
        if (!isEmpty($visuId)) {
            if ($cmd['cmdid2'] == 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",2," . $cmd['cmdid1'] . ")");
                }
                sql_close($ss1);
            } else if ($cmd['cmdid2'] > 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND targetid=" . $cmd['cmdid2'] . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",2," . $cmd['cmdid1'] . ")");
                }
                sql_close($ss1);
            }
        }
    } else if ($cmd['cmd'] == 22) {
        if (!$cmd['cmdoption1'] > 0) {
            $cmd['cmdoption1'] = 0;
        }
        queueCmd(110, $cmd['cmdid1'], $cmd['cmdoption1']);
    } else if ($cmd['cmd'] == 23) {
        if ($cmd['cmdid1'] == 0 && $cmd['cmdid2'] == 0) {
            sql_call("UPDATE edomiLive.visuUserList SET logout=1 WHERE (online=1)");
        } else if ($cmd['cmdid1'] == 0 && $cmd['cmdid2'] > 0) {
            sql_call("UPDATE edomiLive.visuUserList SET logout=1 WHERE (targetid=" . $cmd['cmdid2'] . " AND online=1)");
        } else if ($cmd['cmdid1'] > 0 && $cmd['cmdid2'] == 0) {
            sql_call("UPDATE edomiLive.visuUserList SET logout=1 WHERE (visuid=" . $cmd['cmdid1'] . " AND online=1)");
        } else if ($cmd['cmdid1'] > 0 && $cmd['cmdid2'] > 0) {
            sql_call("UPDATE edomiLive.visuUserList SET logout=1 WHERE (visuid=" . $cmd['cmdid1'] . " AND targetid=" . $cmd['cmdid2'] . " AND online=1)");
        }
    } else if ($cmd['cmd'] == 24) {
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $cmd['cmdid1'] . " AND online=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",10," . $cmd['cmdid2'] . ")");
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 25) {
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (targetid=" . $cmd['cmdid1'] . " AND online=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",10," . $cmd['cmdid2'] . ")");
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 26) {
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $cmd['cmdid1'] . " AND online=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdvalue) VALUES (" . $n['id'] . ",11,'" . sql_encodeValue(parseGAValues($cmd['cmdvalue1'])) . "')");
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 27) {
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (targetid=" . $cmd['cmdid1'] . " AND online=1) ORDER BY id ASC");
        while ($n = sql_result($ss1)) {
            sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdvalue) VALUES (" . $n['id'] . ",11,'" . sql_encodeValue(parseGAValues($cmd['cmdvalue1'])) . "')");
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 28) {
        if ($cmd['cmdid2'] == 0) {
            $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $cmd['cmdid1'] . " AND online=1) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd) VALUES (" . $n['id'] . ",4)");
            }
            sql_close($ss1);
        } else if ($cmd['cmdid2'] > 0) {
            $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $cmd['cmdid1'] . " AND targetid=" . $cmd['cmdid2'] . " AND online=1) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd) VALUES (" . $n['id'] . ",4)");
            }
            sql_close($ss1);
        }
    } else if ($cmd['cmd'] == 29) {
        $visuId = sql_getValue('edomiLive.visuPage', 'visuid', 'id=' . $cmd['cmdid1'] . ' AND pagetyp=1');
        if (!isEmpty($visuId)) {
            if ($cmd['cmdid2'] == 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",3," . $cmd['cmdid1'] . ")");
                }
                sql_close($ss1);
            } else if ($cmd['cmdid2'] > 0) {
                $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND targetid=" . $cmd['cmdid2'] . " AND online=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiLive.visuQueue (targetid,cmd,cmdid) VALUES (" . $n['id'] . ",3," . $cmd['cmdid1'] . ")");
                }
                sql_close($ss1);
            }
        }
    } else if ($cmd['cmd'] == 30) {
        if ($cmd['cmdoption1'] == 1) {
            setSysInfo(2, 12);
        }
        if ($cmd['cmdoption1'] == 2) {
            setSysInfo(2, 13);
        }
        if ($cmd['cmdoption1'] == 3) {
            setSysInfo(2, 23);
        }
        if ($cmd['cmdoption1'] == 4) {
            setSysInfo(2, 10);
        }
        if ($cmd['cmdoption1'] == 9) {
            queueCmd(1, 2, 0);
        }
    } else if ($cmd['cmd'] == 40) {
        $ss1 = sql_call("SELECT outgaid,delay FROM edomiLive.archivKo WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if (checkArchivDelay('archivKoData', $cmd['cmdid1'], $n['delay'])) {
                $ts = getTimestampRound($cmd['cmdoption1']);
                sql_call("INSERT INTO edomiLive.archivKoData (datetime,ms,gavalue,targetid) VALUES ('" . $ts[0] . "','" . $ts[1] . "','" . sql_encodeValue($cmd['cmdvalue1']) . "'," . $cmd['cmdid1'] . ")");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.archivKoData', 'targetid=' . $cmd['cmdid1']));
                }
            }
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 41) {
        $ss1 = sql_call("SELECT outgaid,delay FROM edomiLive.archivMsg WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if (checkArchivDelay('archivMsgData', $cmd['cmdid1'], $n['delay'])) {
                $ts = getTimestampRound($cmd['cmdoption1']);
                if (!($cmd['cmdid2'] >= 1)) {
                    $cmd['cmdid2'] = 0;
                }
                sql_call("INSERT INTO edomiLive.archivMsgData (datetime,ms,msg,targetid,formatid) VALUES ('" . $ts[0] . "','" . $ts[1] . "','" . sql_encodeValue(parseGAValues($cmd['cmdvalue1'])) . "'," . $cmd['cmdid1'] . "," . $cmd['cmdid2'] . ")");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.archivMsgData', 'targetid=' . $cmd['cmdid1']));
                }
            }
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 42) {
        $ss1 = sql_call("SELECT outgaid,delay FROM edomiLive.archivKo WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if (checkArchivDelay('archivKoData', $cmd['cmdid1'], $n['delay'])) {
                $ts = getTimestampRound($cmd['cmdoption1']);
                if ($ko = getGADataFromID($cmd['cmdid2'], 0, 'value')) {
                    sql_call("INSERT INTO edomiLive.archivKoData (datetime,ms,gavalue,targetid) VALUES ('" . $ts[0] . "','" . $ts[1] . "','" . sql_encodeValue($ko['value']) . "'," . $cmd['cmdid1'] . ")");
                    if ($n['outgaid'] > 0) {
                        writeGA($n['outgaid'], sql_getCount('edomiLive.archivKoData', 'targetid=' . $cmd['cmdid1']));
                    }
                }
            }
        }
        sql_close($ss1);
    } else if ($cmd['cmd'] == 50 || $cmd['cmd'] == 51 || $cmd['cmd'] == 52 || $cmd['cmd'] == 53) {
        if ($cmd['cmd'] == 50) {
            $db = 'archivKo';
        } else if ($cmd['cmd'] == 51) {
            $db = 'archivMsg';
        } else if ($cmd['cmd'] == 52) {
            $db = 'archivCam';
        } else if ($cmd['cmd'] == 53) {
            $db = 'archivPhone';
        }
        $ss1 = sql_call("SELECT outgaid FROM edomiLive." . $db . " WHERE (id=" . $cmd['cmdid1'] . ")");
        if ($n = sql_result($ss1)) {
            if ($cmd['cmdoption1'] == 0 || $cmd['cmdoption1'] == 1) {
                if ($cmd['cmdoption1'] == 0) {
                    $ss2 = sql_call("SELECT datetime,ms FROM edomiLive." . $db . "Data WHERE (targetid=" . $cmd['cmdid1'] . ") ORDER BY datetime DESC,ms DESC LIMIT 0,1");
                }
                if ($cmd['cmdoption1'] == 1) {
                    $ss2 = sql_call("SELECT datetime,ms FROM edomiLive." . $db . "Data WHERE (targetid=" . $cmd['cmdid1'] . ") ORDER BY datetime ASC,ms ASC LIMIT 0,1");
                }
                if ($nn = sql_result($ss2)) {
                    sql_call("DELETE FROM edomiLive." . $db . "Data WHERE (targetid=" . $cmd['cmdid1'] . " AND datetime='" . $nn['datetime'] . "' AND ms=" . $nn['ms'] . ")");
                    if ($n['outgaid'] > 0) {
                        writeGA($n['outgaid'], sql_getCount('edomiLive.' . $db . 'Data', 'targetid=' . $cmd['cmdid1']));
                    }
                }
                sql_close($ss2);
            } else if ($cmd['cmdoption1'] == 2) {
                sql_call("DELETE FROM edomiLive." . $db . "Data WHERE (targetid=" . $cmd['cmdid1'] . ")");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.' . $db . 'Data', 'targetid=' . $cmd['cmdid1']));
                }
            } else if ($cmd['cmdoption1'] == 3) {
                sql_call("DELETE FROM edomiLive." . $db . "Data WHERE (targetid=" . $cmd['cmdid1'] . " AND datetime='" . $e1[0] . "' AND ms='" . $e1[1] . "')");
                if ($n['outgaid'] > 0) {
                    writeGA($n['outgaid'], sql_getCount('edomiLive.' . $db . 'Data', 'targetid=' . $cmd['cmdid1']));
                }
            }
        }
        sql_close($ss1);
    }
}

function checkArchivDelay($db, $targetId, $delay)
{
    if ($delay > 0) {
        $ss1 = sql_call("SELECT datetime FROM edomiLive." . $db . " WHERE (targetid=" . $targetId . " AND datetime>'" . date('Y-m-d H:i:s', strtotime('now -' . $delay . ' seconds')) . "')");
        if (sql_result($ss1)) {
            return false;
        }
        sql_close($ss1);
    }
    return true;
} 
?>
