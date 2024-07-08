<?
/*
*/
?><? if (global_logLogicEnabled > 0) {
    require(MAIN_PATH . "/main/include/php/logicmonitor_config.php");
}
function logicMonitor_init($sender)
{
    if (global_logLogicEnabled == 0) {
        define('logicMonitor_enabled', false);
        return;
    }
    if (!logicMonitor_enabled) {
        return;
    }
    define('logicMonitor_isLogic', $sender);
}

function logicMonitor_log($lbsid, $msg, $list, $raw, $css = 0)
{
    if (logicMonitor_isLogic) {
        writeToLogicLog(logicMonitor_fileName, $lbsid, $msg, $list, $raw, $css);
    } else {
        writeToLogicLog(logicMonitor_fileName, $lbsid, $msg, $list, $raw, (($css != 0) ? $css : 3));
    }
}

function logicMonitor_logElements($lbsid)
{
    global $logicMonitor_elements;
    if ($logicMonitor_elements === false) {
        return false;
    }
    if (in_array('+L' . $lbsid, $logicMonitor_elements)) {
        return true;
    }
    if (in_array('-L' . $lbsid, $logicMonitor_elements)) {
        return false;
    }
    $pageid = sql_getValue('edomiLive.RAMlogicElement', 'pageid', 'id=' . $lbsid);
    if (!isEmpty($pageid) && in_array('+P' . $pageid, $logicMonitor_elements)) {
        return true;
    }
    if (!isEmpty($pageid) && in_array('-P' . $pageid, $logicMonitor_elements)) {
        return false;
    }
    if (in_array('+ALL', $logicMonitor_elements)) {
        return true;
    }
    return false;
}

function logicMonitor_mode($mode)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if ($mode == 1) {
        logicMonitor_log('', 'LBS-INITIALISIERUNG', '', '', -1);
    } else if ($mode == 2) {
        logicMonitor_log('', 'LOGIKENGINE GESTARTET', '', '', -2);
    } else if ($mode == 3) {
        logicMonitor_log('', 'LOGIKENGINE BEENDET', '', '', -3);
    }
}

function logicMonitor_callLogicFunction($lbsid, $functionid, $init, $trigger)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $tmp = '[' . sql_getValue('edomiLive.RAMlogicElement', 'pageid', 'id=' . $lbsid) . '] ' . $functionid . ' ' . sql_getValue('edomiProject.editLogicElementDef', 'name', 'id=' . $functionid);
    if ($init) {
        logicMonitor_log($lbsid, 'INIT', '', $tmp, 1);
    } else if ($trigger == 0) {
        logicMonitor_log($lbsid, 'TRIGGER', '', $tmp, 1);
    } else {
        logicMonitor_log($lbsid, 'INTERVAL [' . (($trigger == 1) ? 'SHORTEST' : $trigger . 'ms') . ']', '', $tmp, 1);
    }
}

function logicMonitor_callLogicFunctionReturn($lbsid)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    logicMonitor_log($lbsid, 'RETURN', '', '', 2);
}

function logicMonitor_execEvent($lbsid, $status)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($status) {
        $functionid = sql_getValue('edomiLive.RAMlogicElement', 'functionid', 'id=' . $lbsid);
        if (!isEmpty($functionid)) {
            $tmp = '[' . sql_getValue('edomiLive.RAMlogicElement', 'pageid', 'id=' . $lbsid) . '] ' . $functionid . ' ' . sql_getValue('edomiProject.editLogicElementDef', 'name', 'id=' . $functionid);
        } else {
            $tmp = '[?] (NOT FOUND)';
        }
        logicMonitor_log($lbsid, 'EXEC STARTED', '', $tmp);
    } else {
        logicMonitor_log($lbsid, 'EXEC TERMINATED', '', '');
    }
}

function logicMonitor_debugVar($lbsid, $varName, $value)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $list = explode("\n", var_export($value, true));
    $tmp = array();
    foreach ($list as $k => $v) {
        $tmp[] = $v;
    }
    logicMonitor_log($lbsid, 'VARDUMP [' . $varName . ']', $tmp, 'logic_debugVar()');
}

function logicMonitor_lbsCall_setState($lbsid, $delay)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($delay > 1) {
        logicMonitor_log($lbsid, 'SET INTERVAL = ' . $delay . 'ms', '', 'logic_setState()');
    } else if ($delay == 1) {
        logicMonitor_log($lbsid, 'SET INTERVAL = SHORTEST', '', 'logic_setState()');
    } else if ($delay == 0) {
        logicMonitor_log($lbsid, 'UNSET INTERVAL', '', 'logic_setState()');
    }
}

function logicMonitor_lbsCall_getState($lbsid, $status)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($status === false) {
        logicMonitor_log($lbsid, 'GET STATUS', '(ERROR)', 'logic_getState()');
    } else if ($status == 0) {
        logicMonitor_log($lbsid, 'GET STATUS', 'INTERVAL: NO', 'logic_getState()');
    } else if ($status == 1) {
        logicMonitor_log($lbsid, 'GET STATUS', 'INTERVAL: YES', 'logic_getState()');
    }
}

function logicMonitor_lbsCall_setOutput($lbsid, $ausgang, $value)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    logicMonitor_log($lbsid, 'SET A' . $ausgang . ' = ' . $value, '', 'logic_setOutput()');
}

function logicMonitor_lbsCall_setOutputQueued($lbsid, $ausgang, $value)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    logicMonitor_log($lbsid, 'PUSH A' . $ausgang . ' [OUTPUTQUEUE] = ' . $value, '', 'logic_setOutputQueued()');
}

function logicMonitor_lbsCall_setVar($lbsid, $varId, $value)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    logicMonitor_log($lbsid, 'SET V' . $varId . ' = ' . $value, '', 'logic_setVar()');
}

function logicMonitor_lbsCall_setInputKoValue($lbsid, $koId, $eingang, $value)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($koId !== false) {
        logicMonitor_log($lbsid, 'SET KO[' . $koId . '] @ E' . $eingang . ' = ' . $value, '', 'logic_setInputKoValue()');
    } else {
        logicMonitor_log($lbsid, 'SET KO[NONE] @ E' . $eingang . ') = ' . $value, '', 'logic_setInputKoValue()');
    }
}

function logicMonitor_lbsCall_executeCmdList($lbsid, $e)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $tmp = sql_getCount('edomiLive.RAMlogicCmdList', 'targetid=' . $lbsid);
    logicMonitor_log($lbsid, 'DO COMMANDS', $tmp . ' COMMANDS : E = ' . $e, '', 4);
}

function logicMonitor_lbsCall_setInputsQueued($lbsid, $ts)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($ts !== false) {
        $tmp = array();
        $ss1 = sql_call("SELECT inputid,refresh,value FROM edomiLive.logicExecQueue WHERE elementid=" . $lbsid . " AND ts='" . $ts . "' ORDER BY inputid ASC");
        while ($n = sql_result($ss1)) {
            $tmp[] = 'E' . $n['inputid'] . '' . (($n['refresh'] > 0) ? '*' : '') . ' = ' . $n['value'];
        }
        sql_close($ss1);
        logicMonitor_log($lbsid, 'PUSH E [INPUTQUEUE]', $tmp, 'logic_setInputsQueued()');
    } else {
        logicMonitor_log($lbsid, 'PUSH E [INPUTQUEUE]', '(NONE)', 'logic_setInputsQueued()');
    }
}

function logicMonitor_lbsCall_getInputsQueued($lbsid, $list)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if ($list === false) {
        logicMonitor_log($lbsid, 'POP E [INPUTQUEUE]', '(NONE)', 'logic_getInputsQueued()');
    } else {
        $tmp = array();
        foreach ($list as $k => $v) {
            $tmp[] = 'E' . $k . '' . (($v['refresh'] > 0) ? '*' : '') . '' . (($v['queue'] > 0) ? '+' : '') . ' = ' . $v['value'];
        }
        sort($tmp);
        logicMonitor_log($lbsid, 'POP E [INPUTQUEUE]', $tmp, 'logic_getInputsQueued()');
    }
}

function logicMonitor_lbsCall_deleteInputsQueued($lbsid, $intern)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    if (!$intern) {
        logicMonitor_log($lbsid, 'DELETE INPUTQUEUE', '', 'logic_deleteInputsQueued()');
    }
}

function logicMonitor_lbsCall_getInputs($lbsid)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $n2 = array();
    $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicLink WHERE elementid=" . $lbsid . " ORDER BY eingang ASC");
    while ($n = sql_result($ss1)) {
        if ($n['linktyp'] == 0) {
            $n2[] = 'E' . $n['eingang'] . '' . (($n['refresh'] > 0) ? '*' : '') . ' : KO[' . $n['linkid'] . '] = ' . $n['value'];
        } else if ($n['linktyp'] == 1) {
            $n2[] = 'E' . $n['eingang'] . '' . (($n['refresh'] > 0) ? '*' : '') . ' : A' . $n['ausgang'] . ' @ LBS[' . $n['linkid'] . '] = ' . $n['value'];
        } else if ($n['linktyp'] == 2) {
            $n2[] = 'E' . $n['eingang'] . '' . (($n['refresh'] > 0) ? '*' : '') . ' = ' . $n['value'];
        }
    }
    sql_close($ss1);
    logicMonitor_log($lbsid, 'GET E', $n2, 'logic_getInputs()');
}

function logicMonitor_lbsCall_getVars($lbsid)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $n2 = array();
    $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicElementVar WHERE elementid=" . $lbsid . " ORDER BY varid ASC");
    while ($n = sql_result($ss1)) {
        $n2[] = 'V' . $n['varid'] . '' . (($n['remanent'] > 0) ? '#' : '') . ' = ' . $n['value'];
    }
    sql_close($ss1);
    logicMonitor_log($lbsid, 'GET V', $n2, 'logic_getVars()');
}

function logicMonitor_lbsCall_getVar($lbsid, $varid)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicElementVar WHERE (elementid=" . $lbsid . " AND varid='" . sql_encodeValue($varid) . "')");
    if ($n = sql_result($ss1)) {
        logicMonitor_log($lbsid, 'GET V' . $varid, 'V' . $n['varid'] . '' . (($n['remanent'] > 0) ? '#' : '') . ' = ' . $n['value'], 'logic_getVar()');
    } else {
        logicMonitor_log($lbsid, 'GET V' . $varid, '(NONE)', 'logic_getVar()');
    }
    sql_close($ss1);
}

function logicMonitor_lbsCall_callExec($lbsid, $functionid, $multiTask, $status)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $tmp = '';
    if ($status == 2) {
        $tmp = 'STARTING (QUEUED)';
    }
    if ($status == 1) {
        $tmp = 'STARTING';
    }
    if ($status == 0) {
        $tmp = 'ALREADY RUNNING';
    }
    if ($status == -1) {
        $tmp = 'EXEC-FILE NOT FOUND';
    }
    logicMonitor_log($lbsid, 'START EXEC' . (($multiTask) ? ' [Multitask]' : ''), $tmp, 'logic_callExec()');
}

function logicMonitor_lbsCall_getStateExec($lbsid, $status)
{
    if (!logicMonitor_enabled) {
        return;
    }
    if (!logicMonitor_logElements($lbsid)) {
        return;
    }
    $tmp = '';
    if (isEmpty($status)) {
        $tmp = 'ERROR';
    } else if ($status == 0) {
        $tmp = 'NOT RUNNING';
    } else if ($status == 1) {
        $tmp = 'STARTING';
    } else if ($status == 2) {
        $tmp = 'RUNNING';
    }
    logicMonitor_log($lbsid, 'GET EXEC-STATE', $tmp, 'logic_getStateExec()');
} ?>
