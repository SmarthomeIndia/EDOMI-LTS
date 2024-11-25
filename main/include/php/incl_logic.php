<?
require(MAIN_PATH . "/main/include/php/incl_logicmonitor.php");
function callLogicFunction($functionid, $elementid, $init, $trigger)
{
    $callFunction = 'LB_' . $functionid;
    if ($functionid > 0 && function_exists($callFunction)) {
        logicMonitor_callLogicFunction($elementid, $functionid, $init, $trigger);
        $callFunction($elementid);
        logicMonitor_callLogicFunctionReturn($elementid);
    }
}

function mainLogicRunElements()
{
    $c = 0;
    do {
        $run = false;
        $ss1 = sql_call("SELECT elementid,functionid FROM edomiLive.RAMlogicLink WHERE (refresh=1) GROUP BY elementid");
        while ($n = sql_result($ss1)) {
            callLogicFunction($n['functionid'], $n['elementid'], false, 0);
            resetLogicLinkRefresh($n['elementid']);
            $c++;
            if (global_logicLoopMax == 0 || $c < global_logicLoopMax) {
                $run = true;
            }
        }
        sql_close($ss1);
    } while ($run);
    return $c;
}

function mainLogicRefreshElements()
{
    $c = 0;
    $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicElement WHERE status>=1");
    while ($n = sql_result($ss1)) {
        if ($n['status'] == 1) {
            callLogicFunction($n['functionid'], $n['id'], false, $n['status']);
            mainLogicRunElements();
        } else {
            $ts = intVal(getMicrotime() * 1000);
            if (intVal($n['status'] + $n['statusref']) <= $ts) {
                if ($n['statusint'] == 1) {
                    sql_call("UPDATE edomiLive.RAMlogicElement SET statusref='" . $ts . "' WHERE id=" . $n['id']);
                    callLogicFunction($n['functionid'], $n['id'], false, $n['status']);
                    mainLogicRunElements();
                } else {
                    sql_call("UPDATE edomiLive.RAMlogicElement SET status=1,statusref=0 WHERE id=" . $n['id']);
                    callLogicFunction($n['functionid'], $n['id'], false, $n['status']);
                    mainLogicRunElements();
                }
            }
        }
        $c++;
    }
    sql_close($ss1);
    return $c;
}

function resetLogicLinkRefresh($elementid)
{
    sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=0 WHERE (elementid=" . $elementid . ")");
}

function mainLogicExec_onStart($elementid)
{
    sql_connect();
    logicMonitor_execEvent($elementid, true);
    sql_call("UPDATE edomiLive.RAMlogicElement SET statusexec=2 WHERE id=" . $elementid);
    sql_disconnect();
}

function mainLogicExec_onExit($elementid)
{
    sql_connect();
    logicMonitor_execEvent($elementid, false);
    sql_call("UPDATE edomiLive.RAMlogicElement SET statusexec=0 WHERE id=" . $elementid);
    logic_deleteInputsQueued($elementid, true);
    sql_disconnect();
}

function mainLogicExecuteCmdList($elementid, $e1)
{
    logicMonitor_lbsCall_executeCmdList($elementid, $e1);
    executeLogicCmdList($elementid, $e1);
}

function logic_debugVar($elementid, $varName, $value)
{
    logicMonitor_debugVar($elementid, $varName, $value);
}

function logic_callExec($functionid, $elementid, $multiTask = false, $initStart = false)
{
    global $logic_globalStartExec, $logic_globalStartExecQueue;
    if ($multiTask || logic_getStateExec($elementid, false) == 0) {
        $fnExe = MAIN_PATH . '/www/data/liveproject/lbs/EXE' . $functionid . '.php';
        if (file_exists($fnExe)) {
            sql_call("UPDATE edomiLive.RAMlogicElement SET statusexec=1 WHERE id=" . $elementid);
            if ($logic_globalStartExec || $initStart) {
                logicMonitor_lbsCall_callExec($elementid, $functionid, $multiTask, 1);
                exec('php ' . $fnExe . ' ' . $elementid . ' > /dev/null 2>&1 &');
            } else {
                logicMonitor_lbsCall_callExec($elementid, $functionid, $multiTask, 2);
                $logic_globalStartExecQueue[] = array($elementid, $functionid);
            }
        } else {
            logicMonitor_lbsCall_callExec($elementid, $functionid, $multiTask, -1);
        }
    } else {
        logicMonitor_lbsCall_callExec($elementid, $functionid, $multiTask, 0);
    }
}

function logic_getStateExec($elementid, $logicMonitor = true)
{
    $tmp = sql_getValue('edomiLive.RAMlogicElement', 'statusexec', 'id=' . $elementid);
    if (!isEmpty($tmp)) {
        if ($logicMonitor) {
            logicMonitor_lbsCall_getStateExec($elementid, intVal($tmp));
        }
        return intVal($tmp);
    }
    if ($logicMonitor) {
        logicMonitor_lbsCall_getStateExec($elementid, '');
    }
    return null;
}

function logic_setInputsQueued($elementid, $E, $refreshed = false, $list = false)
{
    $n = "";
    $ts = getTimestampId();
    if ($list === false) {
        foreach ($E as $id => $input) {
            if ($refreshed === false || ($refreshed === true && $input['refresh'] == 1)) {
                $n .= "('" . $ts . "','" . $elementid . "','" . $id . "','" . sql_encodeValue($input['refresh']) . "','" . sql_encodeValue($input['value']) . "'),";
            }
        }
    } else if (is_array($list)) {
        foreach ($list as $void => $listInput) {
            if (array_key_exists($listInput, $E) && ($refreshed === false || ($refreshed === true && $E[$listInput]['refresh'] == 1))) {
                $n .= "('" . $ts . "','" . $elementid . "','" . $listInput . "','" . sql_encodeValue($E[$listInput]['refresh']) . "','" . sql_encodeValue($E[$listInput]['value']) . "'),";
            }
        }
    }
    if (!isEmpty($n)) {
        sql_call("INSERT INTO edomiLive.logicExecQueue (ts,elementid,inputid,refresh,value) VALUES " . rtrim($n, ','));
        logicMonitor_lbsCall_setInputsQueued($elementid, $ts);
    } else {
        logicMonitor_lbsCall_setInputsQueued($elementid, false);
    }
}

function logic_getInputsQueued($elementid, $fallback = false, $sync = false)
{
    $r = false;
    $ts = sql_getValue('edomiLive.logicExecQueue', 'ts', 'elementid=' . $elementid . ' ORDER BY ts ASC LIMIT 0,1');
    if (!isEmpty($ts)) {
        if ($sync) {
            $r = logic_getInputs($elementid);
            foreach ($r as $k => $v) {
                $r[$k]['queue'] = 0;
            }
        }
        $ss1 = sql_call("SELECT inputid,refresh,value,inputid>0 AS queue FROM edomiLive.logicExecQueue WHERE elementid=" . $elementid . " AND ts='" . $ts . "'");
        while ($n = sql_result($ss1)) {
            $r[$n['inputid']] = $n;
        }
        sql_close($ss1);
        sql_call("DELETE FROM edomiLive.logicExecQueue WHERE elementid=" . $elementid . " AND ts='" . $ts . "'");
    } else if ($fallback || $sync) {
        $r = logic_getInputs($elementid);
        foreach ($r as $k => $v) {
            $r[$k]['queue'] = 0;
        }
    }
    logicMonitor_lbsCall_getInputsQueued($elementid, $r);
    return $r;
}

function logic_deleteInputsQueued($elementid, $intern = false)
{
    logicMonitor_lbsCall_deleteInputsQueued($elementid, $intern);
    sql_call("DELETE FROM edomiLive.logicExecQueue WHERE elementid=" . $elementid);
}

function logic_setOutputQueued($elementid, $ausgang, $value)
{
    logicMonitor_lbsCall_setOutputQueued($elementid, $ausgang, $value);
    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gaid,gatyp,value) VALUES (6," . $elementid . ",'" . sql_encodeValue($ausgang) . "','" . sql_encodeValue($value) . "')");
}

function logic_getEdomiState()
{
    return ((getSysInfo(1) >= 1) ? 1 : 0);
}

function logic_dbKeepalive()
{
    $ss1 = sql_call("SELECT COUNT(*) FROM edomiLive.RAMsysInfo");
    sql_close($ss1);
}

function logic_setOutput($elementid, $ausgang, $value)
{
    logicMonitor_lbsCall_setOutput($elementid, $ausgang, $value);
    sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value='" . sql_encodeValue($value) . "' WHERE (linktyp=1 AND linkid=" . $elementid . " AND ausgang='" . sql_encodeValue($ausgang) . "')");
}

function logic_setState($elementid, $status, $delay = 0, $interval = false)
{
    if (intVal($status) > 0 && intVal($delay) > 1) {
        logicMonitor_lbsCall_setState($elementid, $delay);
        $ts = intVal(getMicrotime() * 1000);
        if ($interval) {
            sql_call("UPDATE edomiLive.RAMlogicElement SET status='" . intVal($delay) . "',statusref='" . $ts . "',statusint=1 WHERE (id=" . $elementid . ")");
        } else {
            sql_call("UPDATE edomiLive.RAMlogicElement SET status='" . intVal($delay) . "',statusref='" . $ts . "',statusint=0 WHERE (id=" . $elementid . ")");
        }
    } else {
        if (intVal($status) > 0) {
            logicMonitor_lbsCall_setState($elementid, 1);
            sql_call("UPDATE edomiLive.RAMlogicElement SET status=1 WHERE (id=" . $elementid . ")");
        } else {
            logicMonitor_lbsCall_setState($elementid, 0);
            sql_call("UPDATE edomiLive.RAMlogicElement SET status=0 WHERE (id=" . $elementid . ")");
        }
    }
}

function logic_getState($elementid)
{
    $tmp = sql_getValue('edomiLive.RAMlogicElement', 'status', 'id=' . $elementid);
    if (!isEmpty($tmp)) {
        if (intVal($tmp) >= 1) {
            logicMonitor_lbsCall_getState($elementid, 1);
            return 1;
        } else {
            logicMonitor_lbsCall_getState($elementid, 0);
            return 0;
        }
    }
    logicMonitor_lbsCall_getState($elementid, false);
    return null;
}

function logic_getInputs($elementid)
{
    logicMonitor_lbsCall_getInputs($elementid);
    $r = false;
    $ss1 = sql_call("SELECT value,refresh,eingang FROM edomiLive.RAMlogicLink WHERE (elementid=" . $elementid . ")");
    while ($n = sql_result($ss1)) {
        $r[$n['eingang']] = $n;
    }
    sql_close($ss1);
    return $r;
}

function logic_setInputKoValue($elementid, $eingang, $value)
{
    $linkId = sql_getValue('edomiLive.RAMlogicLink', 'linkid', 'elementid=' . $elementid . ' AND eingang=' . $eingang . ' AND linktyp=0 AND linkid>0');
    if (!isEmpty($linkId)) {
        logicMonitor_lbsCall_setInputKoValue($elementid, $linkId, $eingang, $value);
        writeGA($linkId, $value);
        return true;
    }
    logicMonitor_lbsCall_setInputKoValue($elementid, false, $eingang, $value);
    return false;
}

function logic_setVar($elementid, $varid, $value)
{
    logicMonitor_lbsCall_setVar($elementid, $varid, $value);
    sql_call("UPDATE edomiLive.RAMlogicElementVar SET value=" . sql_encodeValue($value, true) . " WHERE (elementid=" . $elementid . " AND varid='" . sql_encodeValue($varid) . "')");
    sql_call("UPDATE edomiLive.logicElementVar SET value=" . sql_encodeValue($value, true) . " WHERE (elementid=" . $elementid . " AND varid='" . sql_encodeValue($varid) . "' AND remanent=1)");
}

function logic_getVar($elementid, $varid)
{
    logicMonitor_lbsCall_getVar($elementid, $varid);
    $ss1 = sql_call("SELECT value FROM edomiLive.RAMlogicElementVar WHERE (elementid=" . $elementid . " AND varid='" . sql_encodeValue($varid) . "')");
    if ($n = sql_result($ss1)) {
        sql_close($ss1);
        return $n['value'];
    } else {
        sql_close($ss1);
        return NULL;
    }
}

function logic_getVars($elementid)
{
    logicMonitor_lbsCall_getVars($elementid);
    $r = false;
    $ss1 = sql_call("SELECT value,varid FROM edomiLive.RAMlogicElementVar WHERE (elementid=" . $elementid . ")");
    while ($n = sql_result($ss1)) {
        $r[$n['varid']] = $n['value'];
    }
    sql_close($ss1);
    return $r;
}

function callLogicFunctionExec($functionid, $elementid, $multiTask = false, $initStart = true)
{
    logic_callExec($functionid, $elementid, $multiTask, $initStart);
}

function setLogicLinkAusgang($elementid, $ausgang, $value)
{
    logic_setOutput($elementid, $ausgang, $value);
}

function setLogicElementStatus($elementid, $status, $delay = 0, $interval = false)
{
    logic_setState($elementid, $status, $delay, $interval);
}

function getLogicElementStatus($elementid)
{
    return logic_getState($elementid);
}

function getLogicEingangDataAll($elementid)
{
    return logic_getInputs($elementid);
}

function setLogicElementVar($elementid, $varid, $value)
{
    logic_setVar($elementid, $varid, $value);
}

function getLogicElementVar($elementid, $varid)
{
    return logic_getVar($elementid, $varid);
}

function getLogicElementVarAll($elementid)
{
    return logic_getVars($elementid);
}

function playSequences()
{
    $ok = false;
    $ss1 = sql_call("SELECT * FROM edomiLive.sequence WHERE (playpointer>0)");
    while ($n = sql_result($ss1)) {
        $ok = true;
        $ts = getTimestamp();
        $d1 = strval(substr($ts[0], 0, 4) . substr($ts[0], 5, 2) . substr($ts[0], 8, 2) . substr($ts[0], 11, 2) . substr($ts[0], 14, 2) . substr($ts[0], 17, 2) . $ts[1]);
        $d2 = strval(substr($n['datetime'], 0, 4) . substr($n['datetime'], 5, 2) . substr($n['datetime'], 8, 2) . substr($n['datetime'], 11, 2) . substr($n['datetime'], 14, 2) . substr($n['datetime'], 17, 2) . sprintf("%06d", $n['ms']));
        if (strcmp($d1, $d2) > 0) {
            $ss2 = sql_call("SELECT * FROM edomiLive.sequenceCmdList WHERE (id=" . $n['playpointer'] . ")");
            if ($nn = sql_result($ss2)) {
                executeCmd($nn, null);
                $ss3 = sql_call("SELECT id FROM edomiLive.sequenceCmdList WHERE (targetid=" . $nn['targetid'] . " AND sort>" . $nn['sort'] . ") ORDER BY sort ASC LIMIT 0,1");
                if ($nnn = sql_result($ss3)) {
                    $ts = getTimestamp();
                    sql_call("UPDATE edomiLive.sequence SET datetime=DATE_ADD('" . $ts[0] . "', INTERVAL " . $nn['delay'] . " SECOND),ms='" . $ts[1] . "',playpointer=" . $nnn['id'] . " WHERE (id=" . $n['id'] . ")");
                } else {
                    sql_call("UPDATE edomiLive.sequence SET datetime=NULL,ms=NULL,playpointer=0 WHERE (id=" . $n['id'] . ")");
                }
                sql_close($ss3);
            }
            sql_close($ss2);
        }
    }
    sql_close($ss1);
    return $ok;
}

function checkZSU()
{
    $now = date('d.m.Y H:i:s');
    $date0 = date('d.m.Y', strtotime($now));
    $hour0 = intval(date('H', strtotime($now)));
    $minute0 = intval(date('i', strtotime($now)));
    $weekday0 = intval(date('N', strtotime($now)));
    $ss1 = sql_call("SELECT * FROM edomiLive.timer WHERE (gaid>0)");
    while ($n = sql_result($ss1)) {
        $status = getGADataFromID($n['gaid'], 0, 'value');
        if ($status['value'] == 1) {
            $aux = getGADataFromID($n['gaid2'], 0, 'value');
            if ($aux !== false) {
                $tmp = $aux['value'];
            } else {
                $tmp = false;
            }
            $ss2 = sql_call("SELECT * FROM edomiLive.timerData WHERE (targetid=" . $n['id'] . " AND hour=" . $hour0 . " AND minute=" . $minute0 . " AND d" . ($weekday0 - 1) . "=1)");
            while ($nn = sql_result($ss2)) {
                if ($nn['cmdid'] > 0 && checkZSU_date($date0, array($nn['mode'], $nn['d0'], $nn['d1'], $nn['d2'], $nn['d3'], $nn['d4'], $nn['d5'], $nn['d6'], $nn['day1'], $nn['month1'], $nn['year1'], $nn['day2'], $nn['month2'], $nn['year2'], $nn['cmdid'], $nn['d7']), $tmp)) {
                    executeMacroCmdList($nn['cmdid']);
                }
            }
            sql_close($ss2);
        }
    }
    sql_close($ss1);
}

function checkTSU()
{
    $tmp = date('YmdHi');
    $date0 = substr($tmp, 0, 4) . '-' . substr($tmp, 4, 2) . '-' . substr($tmp, 6, 2);
    $hour0 = intval(substr($tmp, 8, 2));
    $minute0 = intval(substr($tmp, 10, 2));
    $ss1 = sql_call("SELECT * FROM edomiLive.agenda WHERE (gaid>0)");
    while ($n = sql_result($ss1)) {
        $status = getGADataFromID($n['gaid'], 0);
        if ($status['value'] == 1) {
            $aux = '';
            $tmp = getGADataFromID($n['gaid2'], 0, 'value');
            if ($tmp !== false) {
                if ($tmp['value'] != 1) {
                    $aux = 'AND (d7=0 OR d7=2)';
                }
                if ($tmp['value'] == 1) {
                    $aux = 'AND (d7=0 OR d7=1)';
                }
            }
            $ss2 = sql_call("SELECT cmdid FROM edomiLive.agendaData WHERE (targetid=" . $n['id'] . " AND hour=" . $hour0 . " AND minute=" . $minute0 . ") AND (step=0 OR date2='' OR date2 IS NULL OR date2>='" . $date0 . "') " . $aux . " AND
				(
					(step=0 AND date1='" . $date0 . "') 
				OR
					(step>0 AND unit=0 AND date1<='" . $date0 . "' AND (DATEDIFF('" . $date0 . "',date1) MOD step)=0)
				OR
					(step>0 AND unit=1 AND date1<='" . $date0 . "' AND (DATEDIFF('" . $date0 . "',date1) MOD (step*7))=0)
				OR
					(step>0 AND unit=2 AND date1<='" . $date0 . "' AND ((TIMESTAMPDIFF(MONTH,date1,'" . $date0 . "') MOD step=0) AND (DATE_ADD(date1,INTERVAL TIMESTAMPDIFF(MONTH,date1,'" . $date0 . "') MONTH)='" . $date0 . "')))
				OR
					(step>0 AND unit=3 AND date1<='" . $date0 . "' AND ((TIMESTAMPDIFF(YEAR,date1,'" . $date0 . "') MOD step=0) AND (DATE_ADD(date1,INTERVAL TIMESTAMPDIFF(YEAR,date1,'" . $date0 . "') YEAR)='" . $date0 . "')))
				)
				ORDER BY id ASC
			");
            while ($nn = sql_result($ss2)) {
                if ($nn['cmdid'] > 0) {
                    executeMacroCmdList($nn['cmdid']);
                }
            }
            sql_close($ss2);
        }
    }
    sql_close($ss1);
}

function checkAWS($gaid = 0, $value = null)
{
    $ts = getTimestampIdWdayTime();
    $ss1 = sql_call("SELECT a.id,a.playpointer,a.recordpointer,b.id AS ko_id,b.value AS ko_value FROM edomiLive.aws AS a,edomiLive.RAMko AS b WHERE a.gaid>0 AND a.gaid=b.id");
    while ($aws = sql_result($ss1)) {
        if ($aws['ko_value'] == 1) {
            if (isEmpty($aws['playpointer'])) {
                $aws['playpointer'] = $ts;
                $ss2 = sql_call("SELECT gaid,gavalue1 FROM edomiLive.awsList WHERE targetid=" . $aws['id'] . " AND gavalue1 IS NOT NULL");
                while ($event = sql_result($ss2)) {
                    writeGA($event['gaid'], $event['gavalue1']);
                }
                sql_close($ss2);
            }
            if (intval($aws['playpointer']) > intval($ts)) {
                $ss2 = sql_call("SELECT gaid,gavalue FROM edomiLive.awsData WHERE targetid=" . $aws['id'] . " AND (timestamp>=" . $aws['playpointer'] . " OR timestamp<" . $ts . ") ORDER BY timestamp ASC");
            } else {
                $ss2 = sql_call("SELECT gaid,gavalue FROM edomiLive.awsData WHERE targetid=" . $aws['id'] . " AND (timestamp>=" . $aws['playpointer'] . " AND timestamp<" . $ts . ") ORDER BY timestamp ASC");
            }
            while ($event = sql_result($ss2)) {
                writeGA($event['gaid'], $event['gavalue']);
            }
            sql_close($ss2);
            sql_call("UPDATE edomiLive.aws SET playpointer=" . $ts . " WHERE (id=" . $aws['id'] . ")");
        } else if ($aws['ko_value'] == 2) {
            if (isEmpty($aws['recordpointer'])) {
                $aws['recordpointer'] = $ts;
            }
            if (intval($aws['recordpointer']) > intval($ts)) {
                sql_call("DELETE FROM edomiLive.awsData WHERE targetid=" . $aws['id'] . " AND (timestamp>" . $aws['recordpointer'] . " OR timestamp<=" . $ts . ")");
            } else {
                sql_call("DELETE FROM edomiLive.awsData WHERE targetid=" . $aws['id'] . " AND (timestamp>" . $aws['recordpointer'] . " AND timestamp<=" . $ts . ")");
            }
            if ($gaid > 0) {
                $tmp = sql_getValues('edomiLive.awsList', 'id,gaid', 'targetid=' . $aws['id'] . ' AND gaid>0 AND (((gaid2=0 OR gaid2 IS NULL) AND gaid=' . $gaid . ') OR (gaid2=' . $gaid . '))');
                if ($tmp !== false) {
                    sql_call("INSERT INTO edomiLive.awsData (timestamp,targetid,gaid,gavalue) VALUES (" . $ts . "," . $aws['id'] . "," . $tmp['gaid'] . ",'" . sql_encodeValue($value) . "')");
                }
            }
            sql_call("UPDATE edomiLive.aws SET recordpointer=" . $ts . " WHERE (id=" . $aws['id'] . ")");
        }
        if ($aws['ko_value'] != 1 && !isEmpty($aws['playpointer'])) {
            sql_call("UPDATE edomiLive.aws SET playpointer=NULL WHERE (id=" . $aws['id'] . ")");
            $ss2 = sql_call("SELECT gaid,gavalue2 FROM edomiLive.awsList WHERE targetid=" . $aws['id'] . " AND gavalue2 IS NOT NULL");
            while ($event = sql_result($ss2)) {
                writeGA($event['gaid'], $event['gavalue2']);
            }
            sql_close($ss2);
        }
        if ($aws['ko_value'] != 2 && !isEmpty($aws['recordpointer'])) {
            sql_call("UPDATE edomiLive.aws SET recordpointer=NULL WHERE (id=" . $aws['id'] . ")");
        }
    }
    sql_close($ss1);
}

function recordAWS($gaid, $value)
{
    if ($gaid > 0) {
        $tmp = sql_getValue('edomiLive.awsList', 'id', 'gaid=' . $gaid . ' OR gaid2=' . $gaid . ' LIMIT 0,1');
        if (!isEmpty($tmp)) {
            checkAWS($gaid, $value);
        }
    }
}

function convertHSVtoRGB($h, $s, $v)
{
    $h /= 255;
    $s /= 255;
    $v /= 255;
    $i = intVal($h * 6);
    $f = $h * 6 - $i;
    $p = $v * (1 - $s);
    $q = $v * (1 - $f * $s);
    $t = $v * (1 - (1 - $f) * $s);
    if ($i % 6 == 0) {
        $r = $v;
        $g = $t;
        $b = $p;
    }
    if ($i % 6 == 1) {
        $r = $q;
        $g = $v;
        $b = $p;
    }
    if ($i % 6 == 2) {
        $r = $p;
        $g = $v;
        $b = $t;
    }
    if ($i % 6 == 3) {
        $r = $p;
        $g = $q;
        $b = $v;
    }
    if ($i % 6 == 4) {
        $r = $t;
        $g = $p;
        $b = $v;
    }
    if ($i % 6 == 5) {
        $r = $v;
        $g = $p;
        $b = $q;
    }
    if ($r >= 0 && $r <= 1 && $g >= 0 && $g <= 1 && $b >= 0 && $b <= 1) {
        return array(($r * 255), ($g * 255), ($b * 255));
    }
    return false;
}

function convertRGBtoHSV($r, $g, $b)
{
    $r /= 255;
    $g /= 255;
    $b /= 255;
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $v = $max;
    $d = $max - $min;
    $s = $max == 0 ? 0 : $d / $max;
    if ($max == $min) {
        $h = 0;
    } else {
        if ($max == $r) {
            $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
        }
        if ($max == $g) {
            $h = ($b - $r) / $d + 2;
        }
        if ($max == $b) {
            $h = ($r - $g) / $d + 4;
        }
        $h /= 6;
    }
    if ($h >= 0 && $h <= 1 && $s >= 0 && $s <= 1 && $v >= 0 && $v <= 1) {
        return array(($h * 255), ($s * 255), ($v * 255));
    }
    return false;
}
?>
