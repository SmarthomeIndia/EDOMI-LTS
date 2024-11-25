<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
require(MAIN_PATH . "/main/include/php/incl_telnet.php");
restore_error_handler();
error_reporting(0);
writeToLog(6, true, 'Process PHONE started');
sql_connect();
$pingCounter = 0;
$mainTimerOldProc = 0;
$procControl = null;
$procTrigger = procStatus_getTrigger();
$procData = procStatus_getData(6);
do {
    $connected = false;
    $procTrigger = procStatus_getTrigger($procTrigger[1]);
    if ($procTrigger[0]) {
        $procControl = procStatus_getControl(6);
        if ($procControl == 1) {
            writeToLog(6, true, 'Stopping process PHONE...');
        }
        $procData[0] = 0;
        $procData[1] = 0;
        $procData[2] = 0;
        $procData[19] = 1;
        procStatus_setData(6, $procData);
    }
    if ($procControl == 2) {
        if ($con = telnetOpen(global_fbIp, global_fbCallMonPort, 3)) {
            $connected = true;
            $procData[0] = 1;
            $procData[1] = 0;
            $procData[2] = 0;
            $procData[19] = 2;
            procStatus_setData(6, $procData);
            $procTrigger = procStatus_getTrigger();
            while (!feof($con) && $procControl != 1 && $connected) {
                $procTrigger = procStatus_getTrigger($procTrigger[1]);
                if ($procTrigger[0]) {
                    $procControl = procStatus_getControl(6);
                    if ($procControl == 1) {
                        writeToLog(6, true, 'Stopping process PHONE...');
                    }
                }
                $n = fgets($con);
                if ($n !== false && !isEmpty($n)) {
                    $fb_cmd = 0;
                    $ts = getTimestamp();
                    $data = explode(';', $n, -1);
                    if (count($data) > 0) {
                        if (strToUpper($data[1]) == 'RING') {
                            $fb_cmd = 1;
                        }
                        if (strToUpper($data[1]) == 'CALL') {
                            $fb_cmd = 2;
                        }
                        if (strToUpper($data[1]) == 'CONNECT') {
                            $fb_cmd = 3;
                        }
                        if (strToUpper($data[1]) == 'DISCONNECT') {
                            $fb_cmd = 4;
                        }
                    }
                    if ($fb_cmd > 0) {
                        $tmp = sql_getDate($ts[0]) . ';' . sql_getTime($ts[0]) . ';' . $ts[1] . ';' . $fb_cmd . ';';
                        for ($t = 2; $t < count($data); $t++) {
                            $tmp .= $data[$t] . ';';
                        }
                        writeGA(15, trim(rtrim($tmp, ';')));
                    }
                    if ($fb_cmd == 1) {
                        $data[3] = preg_replace('/[^0-9]/', '', $data[3]);
                        $data[4] = preg_replace('/[^0-9]/', '', $data[4]);
                        $ss1 = sql_call("SELECT * FROM edomiLive.phoneCall WHERE typ=0");
                        while ($pCall = sql_result($ss1)) {
                            if ($pCall['phoneid1'] == 0 || trim($data[3]) == getPhoneNumber($pCall['phoneid1'], 1)) {
                                if ($pCall['phoneid2'] == 0 || trim($data[4]) == getPhoneNumber($pCall['phoneid2'], 0)) {
                                    if ($pCall['gaid1'] > 0) {
                                        writeGA($pCall['gaid1'], 1);
                                    }
                                    if ($pCall['gaid2'] > 0) {
                                        if ($nn = getPhoneBookData($data[3], 1)) {
                                            $info = $nn['name'] . ' (' . $nn['id'] . ')';
                                        } else {
                                            $info = $data[3];
                                        }
                                        writeGA($pCall['gaid2'], $info);
                                    }
                                    if ($pCall['gaid3'] > 0) {
                                        if ($nn = getPhoneBookData($data[4], 0)) {
                                            $info = $nn['name'] . ' (' . $nn['id'] . ')';
                                        } else {
                                            $info = $data[4];
                                        }
                                        writeGA($pCall['gaid3'], $info);
                                    }
                                }
                            }
                        }
                        sql_close($ss1);
                        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivPhone");
                        while ($tmp = sql_result($ss1)) {
                            sql_call("INSERT INTO edomiLive.archivPhoneData (datetime,ms,targetid,phone,phoneid,typ,status) VALUES ('" . $ts[0] . "','" . $ts[1] . "'," . $tmp['id'] . ",'" . sql_encodeValue($data[3]) . "',0,0,0)");
                            if ($tmp['outgaid'] > 0) {
                                writeGA($tmp['outgaid'], sql_getCount('edomiLive.archivPhoneData', 'targetid=' . $tmp['id']));
                            }
                        }
                        sql_close($ss1);
                        $procData[1]++;
                    }
                    if ($fb_cmd == 2) {
                        $data[4] = preg_replace('/[^0-9]/', '', $data[4]);
                        $data[5] = preg_replace('/[^0-9]/', '', $data[5]);
                        $ss1 = sql_call("SELECT * FROM edomiLive.phoneCall WHERE typ=1");
                        while ($pCall = sql_result($ss1)) {
                            if ($pCall['phoneid1'] == 0 || trim($data[4]) == getPhoneNumber($pCall['phoneid1'], 0)) {
                                if ($pCall['phoneid2'] == 0 || trim($data[5]) == getPhoneNumber($pCall['phoneid2'], 1)) {
                                    if ($pCall['gaid1'] > 0) {
                                        writeGA($pCall['gaid1'], 1);
                                    }
                                    if ($pCall['gaid2'] > 0) {
                                        if ($nn = getPhoneBookData($data[4], 0)) {
                                            $info = $nn['name'] . ' (' . $nn['id'] . ')';
                                        } else {
                                            $info = $data[4];
                                        }
                                        writeGA($pCall['gaid2'], $info);
                                    }
                                    if ($pCall['gaid3'] > 0) {
                                        if ($nn = getPhoneBookData($data[5], 1)) {
                                            $info = $nn['name'] . ' (' . $nn['id'] . ')';
                                        } else {
                                            $info = $data[5];
                                        }
                                        writeGA($pCall['gaid3'], $info);
                                    }
                                }
                            }
                        }
                        sql_close($ss1);
                        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivPhone");
                        while ($tmp = sql_result($ss1)) {
                            sql_call("INSERT INTO edomiLive.archivPhoneData (datetime,ms,targetid,phone,phoneid,typ,status) VALUES ('" . $ts[0] . "','" . $ts[1] . "'," . $tmp['id'] . ",'" . sql_encodeValue($data[5]) . "',0,1,0)");
                            if ($tmp['outgaid'] > 0) {
                                writeGA($tmp['outgaid'], sql_getCount('edomiLive.archivPhoneData', 'targetid=' . $tmp['id']));
                            }
                        }
                        sql_close($ss1);
                        $procData[2]++;
                    }
                }
                procStatus_getProcValues($procData, $mainTimerOldProc);
                if ($procControl != 1) {
                    procStatus_setData(6, $procData);
                    sleep(1);
                    $pingCounter++;
                    if ($pingCounter >= 30) {
                        $pingCounter = 0;
                        if (!$ping = telnetOpen(global_fbIp, global_fbCallMonPort, 3)) {
                            $connected = false;
                        }
                        telnetClose($ping);
                    }
                }
            }
            telnetClose($con);
        }
        if ($procControl != 1) {
            sleep(3);
        }
    } else {
        if ($procControl != 1) {
            usleep(100000);
        }
    }
} while ($procControl != 1);
exitSelf();
function exitSelf()
{
    procStatus_setVar(6, 0, 0);
    writeToLog(6, sql_disconnect(), 'Database: close connection');
    writeToLog(6, true, 'Process PHONE stopped');
    exit();
}

function getPhoneNumber($id, $mode)
{
    $ss1 = sql_call("SELECT phone1,phone2 FROM edomiLive.phoneBook WHERE (id=" . $id . ")");
    if ($n = sql_result($ss1)) {
        if ($mode == 0) {
            return trim($n['phone2']);
        } else {
            return trim($n['phone1']) . trim($n['phone2']);
        }
    }
    sql_close($ss1);
    return false;
}

function getPhoneBookData($phone, $mode)
{
    if ($mode == 0) {
        $ss1 = sql_call("SELECT * FROM edomiLive.phoneBook WHERE (TRIM(phone2)='" . sql_encodeValue(trim($phone)) . "')");
    }
    if ($mode == 1) {
        $ss1 = sql_call("SELECT * FROM edomiLive.phoneBook WHERE (CONCAT(TRIM(phone1),TRIM(phone2))='" . sql_encodeValue(trim($phone)) . "')");
    }
    if ($n = sql_result($ss1)) {
        return $n;
    }
    sql_close($ss1);
    return false;
}
?>

