
<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_logic.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
$n = glob(MAIN_PATH . "/www/data/liveproject/lbs/LBS????????.php");
foreach ($n as $pathFn) {
    if (is_file($pathFn)) {
        require($pathFn);
    }
}
$logic_globalStartExec = false;
$logic_globalStartExecQueue = array();
$main = new procLogic();
$main->exitSelf();

class procLogic
{
    private $procControl;
    private $procTrigger;
    private $procData;
    private $flagSequence = true;
    private $waitMin = 0;
    private $flagAWS = false;
    private $flagZSU = false;
    private $flagTSU = false;
    private $flagSEQ = false;
    private $mainTimerStart = 0;
    private $mainTimerOld1 = 0;
    private $mainTimerOld5 = 0;
    private $mainTimerOldMinute = -1;
    private $mainTimerOldProc = 0;

    public function __construct()
    {
        writeToLog(4, true, 'process LOGIC started');
        logicMonitor_init(true);
        sql_connect();
        $this->flagAWS = ((sql_getCount('edomiLive.aws', 'gaid>0') > 0) ? true : false);
        $this->flagZSU = ((sql_getCount('edomiLive.timer', 'gaid>0') > 0) ? true : false);
        $this->flagTSU = ((sql_getCount('edomiLive.agenda', 'gaid>0') > 0) ? true : false);
        $this->flagSEQ = ((sql_getCount('edomiLive.sequence', 'id>0') > 0) ? true : false);
        $this->procControl = null;
        $this->procTrigger = procStatus_getTrigger();
        $this->procData = procStatus_getData(4);
        $this->procData[19] = 1;
        $this->procData[0] = 0;
        $this->procData[2] = 0;
        $this->procData[4] = 0;
        $this->procData[5] = 0;
        $this->procData[6] = 0;
        if ($this->logicMode_wait()) {
            if ($this->logicMode_init()) {
                $this->logicMode_run();
            }
        }
        $this->logicMode_terminate();
    }

    private function proc_check()
    {
        $this->procTrigger = procStatus_getTrigger($this->procTrigger[1]);
        if ($this->procTrigger[0]) {
            $this->procControl = procStatus_getControl(4);
            if ($this->procControl == 1) {
                writeToLog(4, true, 'stopping process LOGIC...');
            } else {
                return true;
            }
        }
        return false;
    }

    private function loop_begin()
    {
        $this->mainTimerStart = getMicrotime();
        $this->waitMin = global_logicWaitMax;
        $this->proc_check();
    }

    private function loop_end()
    {
        procStatus_getProcValues($this->procData, $this->mainTimerOldProc);
        if ($this->procData[0] > $this->procData[10] || is_null($this->procData[10])) {
            $this->procData[10] = $this->procData[0];
        }
        if ($this->procData[2] > $this->procData[12] || is_null($this->procData[12])) {
            $this->procData[12] = $this->procData[2];
        }
        if ($this->procData[4] > $this->procData[14] || is_null($this->procData[14])) {
            $this->procData[14] = $this->procData[4];
        }
        if ($this->procData[5] > $this->procData[15] || is_null($this->procData[15])) {
            $this->procData[15] = $this->procData[5];
        }
        if ($this->procData[6] > $this->procData[16] || is_null($this->procData[16])) {
            $this->procData[16] = $this->procData[6];
        }
        if ($this->procTrigger[0]) {
            procStatus_setData(4, $this->procData);
            $this->procData[0] = 0;
            $this->procData[2] = 0;
            $this->procData[4] = 0;
        }
        $wait = $this->waitMin - intVal((getMicrotime() - $this->mainTimerStart) * 1000);
        if ($wait > 0) {
            usleep($wait * 1000);
        } else {
            usleep(10);
        }
    }

    private function logicMode_wait()
    {
        writeToLog(4, true, 'logic engine: waiting');
        $return = 0;
        do {
            $this->loop_begin();
            $this->procData[19] = 1;
            $ss1 = sql_call("SELECT * FROM edomiLive.RAMknxRead ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $this->waitMin = 0;
                if (($n['mode'] == 1) || ($n['mode'] == 2)) {
                    if ($n['gatyp'] == 1) {
                        if ($n['mode'] == 1 || (global_InitScanWrite && $n['mode'] == 2)) {
                            sql_call("UPDATE edomiLive.RAMko SET initscan=2 WHERE (id=" . $n['gaid'] . " AND initscan=1)");
                        }
                        sql_call("UPDATE edomiLive.RAMko SET value='" . sql_encodeValue($n['value']) . "',visuts='" . getTimestampVisu() . "' WHERE (id=" . $n['gaid'] . ")");
                    }
                    if ($n['gatyp'] == 2) {
                        if ($n['gaid'] == 2 && $n['value'] == 0) {
                            $return = 1;
                        } else if ($n['gaid'] == 2 && $n['value'] == 3) {
                            $return = -1;
                        }
                        sql_call("UPDATE edomiLive.RAMko SET value='" . sql_encodeValue($n['value']) . "',visuts='" . getTimestampVisu() . "' WHERE (id=" . $n['gaid'] . ")");
                        if ($n['remanent'] == 1) {
                            sql_call("UPDATE edomiLive.ko SET value='" . sql_encodeValue($n['value']) . "' WHERE (id=" . $n['gaid'] . " AND gatyp=2 AND remanent=1)");
                        }
                    }
                }
                sql_call("DELETE FROM edomiLive.RAMknxRead WHERE (id=" . $n['id'] . ")");
                usleep(1);
            }
            sql_close($ss1);
            $this->loop_end();
        } while ($this->procControl != 1 && $return == 0);
        if ($this->procControl == 1 || $return == -1) {
            return false;
        }
        return true;
    }

    private function logicMode_init()
    {
        global $logic_globalStartExec, $logic_globalStartExecQueue;
        logicMonitor_mode(1);
        writeToLog(4, true, 'logic engine: initialization');
        $this->procData[19] = 3;
        procStatus_setData(4, $this->procData);
        $ss1 = sql_call("SELECT id,value FROM edomiLive.RAMko WHERE (value IS NOT NULL)");
        while ($n = sql_result($ss1)) {
            sql_call("UPDATE edomiLive.RAMlogicLink SET value='" . sql_encodeValue($n['value']) . "' WHERE (linktyp=0 AND linkid=" . $n['id'] . ")");
        }
        sql_close($ss1);
        sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1 WHERE (linktyp<>1 AND (value IS NOT NULL))");
        sql_call("UPDATE edomiLive.RAMlogicLink SET init=1 WHERE (linktyp<>1)");
        $t = 0;
        $maxElements = sql_getCount('edomiLive.RAMlogicElement', '1=1');
        do {
            $exit = true;
            $ss1 = sql_call("SELECT elementid,functionid,count(id) AS anz1,sum(init=1) AS anz2 FROM edomiLive.RAMlogicLink GROUP BY elementid HAVING anz1=anz2");
            while ($n = sql_result($ss1)) {
                $exit = false;
                callLogicFunction($n['functionid'], $n['elementid'], true, 0);
                resetLogicLinkRefresh($n['elementid']);
                sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1 WHERE (linktyp=1 AND linkid=" . $n['elementid'] . " AND (value IS NOT NULL) AND refresh=0 AND init=0)");
                sql_call("UPDATE edomiLive.RAMlogicLink SET init=1 WHERE (linktyp=1 AND linkid=" . $n['elementid'] . " AND init=0)");
                sql_call("UPDATE edomiLive.RAMlogicLink SET init=2 WHERE (elementid=" . $n['elementid'] . ")");
                $t++;
            }
            sql_close($ss1);
            usleep(10);
        } while (!$exit && $t <= $maxElements);
        if (sql_getCount('edomiLive.RAMlogicLink', 'init<>2') == 0) {
            writeToLog(4, true, 'LBS-initialization: ' . $maxElements . ' from ' . $maxElements . ' LBS initialized');
            $this->procData[1] = $maxElements;
            $this->procData[11] = $maxElements;
        } else {
            writeToLog(4, true, 'LBS-initialization: ' . $t . ' from ' . $maxElements . ' LBS initialized / not initialized LBS:');
            $this->procData[1] = $t;
            $this->procData[11] = $maxElements;
            $ss1 = sql_call("SELECT * FROM edomiLive.RAMlogicLink WHERE init<>2 GROUP BY elementid");
            while ($n = sql_result($ss1)) {
                $nn = sql_getValues('edomiLive.logicElement', '*', 'id=' . $n['elementid']);
                if ($nn !== false) {
                    writeToLog(4, true, 'Logikpage ' . $nn['pageid'] . ': LBS-instance ' . $nn['id'] . ' (' . $nn['functionid'] . ')');
                }
            }
            sql_close($ss1);
            sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1 WHERE (linktyp=1 AND (value IS NOT NULL) AND refresh=0 AND init!=2)");
        }
        return true;
    }

    private function logicMode_run()
    {
        global $logic_globalStartExec, $logic_globalStartExecQueue;
        logicMonitor_mode(2);
        writeToLog(4, true, 'Logikengine: started');
        writeGA(2, 1);
        $return = 0;
        writeToLog(4, true, 'LBS-initialization: LBS-EXEC-scripts will be started (Queue)');
        $logic_globalStartExec = true;
        foreach ($logic_globalStartExecQueue as $k => $v) {
            exec('php ' . MAIN_PATH . '/www/data/liveproject/lbs/EXE' . $v[1] . '.php' . ' ' . $v[0] . ' > /dev/null 2>&1 &');
        }
        $logic_globalStartExecQueue = null;
        do {
            $this->loop_begin();
            $this->procData[19] = 2;
            $ss1 = sql_call("SELECT * FROM edomiLive.RAMknxRead ORDER BY id ASC LIMIT 0,1");
            if ($n = sql_result($ss1)) {
                $this->waitMin = 0;
                if ($n['mode'] == 0) {
                    if ($n['local'] != 1) {
                        if ($gaData = getGADataFromID($n['gaid'], 1)) {
                            if ($gaData['requestable'] == 1 || $gaData['requestable'] == 3 || $gaData['requestable'] == 4 || $gaData['requestable'] == 6) {
                                sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value=1 WHERE (linktyp=0 AND linkid=" . $n['gaid'] . " AND functionid=12000008)");
                            }
                            if ($gaData['requestable'] == 2 || $gaData['requestable'] == 3 || $gaData['requestable'] == 5 || $gaData['requestable'] == 6) {
                                writeGA($n['gaid'], $gaData['value']);
                            }
                        }
                    }
                }
                if (($n['mode'] == 1) || ($n['mode'] == 2)) {
                    if ($n['gatyp'] == 1) {
                        sql_call("UPDATE edomiLive.RAMko SET value='" . sql_encodeValue($n['value']) . "',visuts='" . getTimestampVisu() . "' WHERE (id=" . $n['gaid'] . ")");
                    }
                    if ($n['gatyp'] == 2) {
                        if ($n['gaid'] == 2 && $n['value'] == 3) {
                            $return = -1;
                        }
                        sql_call("UPDATE edomiLive.RAMko SET value='" . sql_encodeValue($n['value']) . "',visuts='" . getTimestampVisu() . "' WHERE (id=" . $n['gaid'] . ")");
                        if ($n['remanent'] == 1) {
                            sql_call("UPDATE edomiLive.ko SET value='" . sql_encodeValue($n['value']) . "' WHERE (id=" . $n['gaid'] . " AND gatyp=2 AND remanent=1)");
                        }
                    }
                    if ($this->flagAWS) {
                        recordAWS($n['gaid'], $n['value']);
                    }
                    sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value='" . sql_encodeValue($n['value']) . "' WHERE (linktyp=0 AND linkid=" . $n['gaid'] . " AND functionid<>12000008)");
                }
                if ($n['mode'] == 3) {
                    executeVisuCmdList($n['gaid'], $n['value']);
                }
                if ($n['mode'] == 4) {
                    writeGA($n['gaid'], $n['value']);
                }
                if ($n['mode'] == 5) {
                    requestGA($n['gaid']);
                }
                if ($n['mode'] == 6) {
                    sql_call("UPDATE edomiLive.RAMlogicLink SET refresh=1,value='" . sql_encodeValue($n['value']) . "' WHERE (linktyp=1 AND linkid=" . $n['gaid'] . " AND ausgang=" . $n['gatyp'] . ")");
                }
                if ($this->flagSEQ && $n['mode'] == 11) {
                    if ($n['value'] == 0) {
                        $ss2 = sql_call("SELECT id FROM edomiLive.sequenceCmdList WHERE (targetid=" . $n['gaid'] . ") ORDER BY sort ASC LIMIT 0,1");
                        if ($nn = sql_result($ss2)) {
                            $ts = getTimestamp();
                            sql_call("UPDATE edomiLive.sequence SET datetime='" . $ts[0] . "',ms='" . $ts[1] . "',playpointer=" . $nn['id'] . " WHERE (id=" . $n['gaid'] . " AND NOT(playpointer<>0))");
                            $this->flagSequence = true;
                        }
                        sql_close($ss2);
                    }
                    if ($n['value'] == 1) {
                        sql_call("UPDATE edomiLive.sequence SET datetime=NULL,ms=NULL,playpointer=0 WHERE (id=" . $n['gaid'] . ")");
                        $this->flagSequence = true;
                    }
                }
                sql_call("DELETE FROM edomiLive.RAMknxRead WHERE (id=" . $n['id'] . ")");
            }
            sql_close($ss1);
            $tmp = sql_getCount('edomiLive.RAMknxRead', '1=1');
            if ($tmp > $this->procData[2]) {
                $this->procData[2] = $tmp;
            }
            $tmp = mainLogicRunElements();
            if ($tmp > $this->procData[0]) {
                $this->procData[0] = $tmp;
            }
            $tmp = mainLogicRefreshElements();
            if ($tmp > $this->procData[4]) {
                $this->procData[4] = $tmp;
            }
            if ($this->waitMin > 0 && $tmp > 0) {
                $ss1 = sql_call("SELECT COUNT(*) AS anz1 FROM edomiLive.RAMlogicElement WHERE (status>0 AND status<" . global_logicWaitMax . ")");
                if ($n = sql_result($ss1)) {
                    if ($n['anz1'] > 0) {
                        $this->waitMin = global_logicWaitMin;
                    } else {
                        $this->waitMin = global_logicWaitMax;
                    }
                }
                sql_close($ss1);
            }
            if ($this->flagSequence) {
                if ($this->flagSEQ) {
                    $this->flagSequence = playSequences();
                }
            }
            if ((intval($this->mainTimerStart) - intval($this->mainTimerOld1)) >= 1) {
                $this->mainTimerOld1 = intval($this->mainTimerStart);
                if ($this->flagAWS) {
                    checkAWS();
                }
                $this->procData[5] = sql_getValue('edomiLive.logicExecQueue', 'COUNT(DISTINCT ts)', '1=1');
                $this->procData[6] = sql_getCount('edomiLive.RAMlogicElement', 'statusexec=2');
            }
            if ((intval($this->mainTimerStart) - intval($this->mainTimerOld5)) >= 5) {
                $this->mainTimerOld5 = intval($this->mainTimerStart);
            }
            $tmp = intval(date('i'));
            if ($this->mainTimerOldMinute != $tmp) {
                $this->mainTimerOldMinute = $tmp;
                if ($this->flagZSU) {
                    checkZSU();
                }
                if ($this->flagTSU) {
                    checkTSU();
                }
            }
            $this->loop_end();
        } while ($this->procControl != 1 && $return == 0);
        return false;
    }

    private function logicMode_terminate()
    {
        logicMonitor_mode(3);
        writeToLog(4, true, 'Logikengine: Beendet');
        $this->procData[19] = 4;
        procStatus_setData(4, $this->procData);
    }

    public function exitSelf()
    {
        writeToLog(4, sql_disconnect(), 'Database: closing connection');
        writeToLog(4, true, 'stopped process LOGIC ');
        exit();
    }
} ?>
