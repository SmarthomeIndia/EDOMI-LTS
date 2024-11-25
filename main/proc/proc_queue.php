
<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
require(MAIN_PATH . "/main/include/php/incl_telnet.php");
require(MAIN_PATH . "/main/include/php/incl_fritzbox.php");
writeToLog(5, true, 'process QUEUE started');
sql_connect();
$procControl = null;
$procTrigger = procStatus_getTrigger();
$procData = procStatus_getData(5);
$procData[19] = 1;
$mainTimerOldProc = 0;
do {
    $mainTimerStart = getMicrotime();
    $procTrigger = procStatus_getTrigger($procTrigger[1]);
    if ($procTrigger[0]) {
        $procControl = procStatus_getControl(5);
        if ($procControl == 0) {
            $procData[19] = 1;
        } else if ($procControl == 1) {
            writeToLog(5, true, 'Stopping process QUEUE...');
        } else if ($procControl == 2) {
            $procData[19] = 2;
        }
    }
    $queueCount = 0;
    $ss1 = sql_call("SELECT SUM(status=0) AS anz0,SUM(status=1) AS anz1,COUNT(*) AS anz2 FROM edomiLive.RAMcmdQueue");
    if ($n = sql_result($ss1)) {
        $procData[0] = intval($n['anz0']);
        $procData[1] = intval($n['anz1']);
        $queueCount = intval($n['anz2']);
    }
    sql_close($ss1);
    if ($procData[0] > $procData[10] || is_null($procData[10])) {
        $procData[10] = $procData[0];
    }
    if ($procData[1] > $procData[11] || is_null($procData[11])) {
        $procData[11] = $procData[1];
    }
    if ($procControl == 2) {
        if ($queueCount > 0) {
            checkQueueEntry(1, false);
            checkQueueEntry(100, false);
            checkQueueEntry(110, false);
            checkQueueEntry(130, false);
            checkQueueEntry(120, true);
            checkQueueEntry(140, true);
        }
    }
    procStatus_getProcValues($procData, $mainTimerOldProc);
    if ($procTrigger[0]) {
        procStatus_setData(5, $procData);
    }
    $wait = (1000 / global_cmdQueueMaxRate) - ((getMicrotime() - $mainTimerStart) * 1000);
    if ($wait > 0) {
        usleep($wait * 1000);
    } else {
        usleep(10);
    }
} while ($procControl != 1);
exitSelf();
function exitSelf()
{
    writeToLog(5, sql_disconnect(), 'Database: closing connection');
    writeToLog(5, true, 'process stopped QUEUE');
    exit();
}

function checkQueueEntry($cmd, $multithread)
{
    if ($multithread) {
        $oldId = 0;
        $ss1 = sql_call("SELECT * FROM edomiLive.RAMcmdQueue WHERE (cmd=" . $cmd . ") ORDER BY cmdvalue ASC,id ASC");
        while ($n = sql_result($ss1)) {
            if ($n['cmdvalue'] != $oldId) {
                if ($n['status'] == 0) {
                    $ts = getTimestamp();
                    sql_call("UPDATE edomiLive.RAMcmdQueue SET status=1,runts='" . $ts[0] . "' WHERE (id=" . $n['id'] . ")");
                    launchProcess('php ' . MAIN_PATH . '/main/queuecmd/cmd' . $n['cmd'] . '.php ' . $n['id'] . ' 0 ' . $n['cmd'] . ' ' . $n['cmdid'] . ' "' . $n['cmdvalue'] . '"');
                    usleep(1000);
                }
                $oldId = $n['cmdvalue'];
            }
        }
        sql_close($ss1);
    } else {
        $ss1 = sql_call("SELECT * FROM edomiLive.RAMcmdQueue WHERE (cmd=" . $cmd . ") ORDER BY id ASC LIMIT 0,1");
        if ($n = sql_result($ss1)) {
            if ($n['status'] == 0) {
                $ts = getTimestamp();
                sql_call("UPDATE edomiLive.RAMcmdQueue SET status=1,runts='" . $ts[0] . "' WHERE (id=" . $n['id'] . ")");
                launchProcess('php ' . MAIN_PATH . '/main/queuecmd/cmd' . $n['cmd'] . '.php ' . $n['id'] . ' 0 ' . $n['cmd'] . ' ' . $n['cmdid'] . ' "' . $n['cmdvalue'] . '"');
                usleep(1000);
            }
        }
        sql_close($ss1);
    }
} ?>

