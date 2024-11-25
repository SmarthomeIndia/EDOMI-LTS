
<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_camera.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
$main = new procDvr();
$main->exitSelf();

class procDvr
{
    private $procControl;
    private $procTrigger;
    private $procData;
    private $mainTimerOld1 = 0;
    private $mainTimerOld3 = 0;
    private $isMounted = true;

    public function __construct()
    {
        writeToLog(8, true, 'process DVR started');
        sql_connect();
        $this->procControl = null;
        $this->procTrigger = procStatus_getTrigger();
        $this->procData = procStatus_getData(8);
        $this->procData[0] = 0;
        $this->procData[1] = 0;
        $this->procData[2] = 0;
        $this->procData[3] = 0;
        $this->procData[4] = 0;
        $this->procData[5] = 0;
        $this->procData[6] = 0;
        $this->proc_wait();
    }

    private function proc_check()
    {
        $this->procTrigger = procStatus_getTrigger($this->procTrigger[1]);
        if ($this->procTrigger[0]) {
            $this->procControl = procStatus_getControl(8);
            if ($this->procControl == 1) {
                writeToLog(8, true, 'stopping process DVRCAM...');
            } else {
                return true;
            }
        }
        return false;
    }

    private function proc_wait()
    {
        do {
            if ($this->proc_check()) {
                $this->procData[19] = 1;
                procStatus_setData(8, $this->procData);
            }
            if ($this->procControl == 2) {
                $this->procData[19] = 2;
                procStatus_setData(8, $this->procData);
                $this->proc_run();
            } else if ($this->procControl != 1) {
                usleep(100000);
            }
        } while ($this->procControl != 1);
    }

    private function proc_run()
    {
        $mainTimerOldProc = 0;
        $cam = array();
        $ss1 = sql_call("SELECT * FROM edomiLive.cam WHERE dvr=1");
        while ($n = sql_result($ss1)) {
            array_push($cam, array($n['id'], $n['url'], $n['mjpeg'], false, false, 0, 0, $n['dvrrate'], $n['dvrkeep'], $n['dvrgaid'], (($n['dvrgaid'] > 0) ? 0 : 1), 0, 0, 0, $n['dvrgaid2'], 0, false, 0));
        }
        sql_close($ss1);
        $this->mountCheck();
        while ($this->procControl != 1) {
            if ($this->isMounted) {
                $this->procData[0] = 1;
                procStatus_setData(8, $this->procData);
                for ($id = 0; $id < count($cam); $id++) {
                    if ($cam[$id][8] > 0) {
                        exec("find " . global_dvrPath . " -mindepth 1 -maxdepth 1 -type f \( -name 'cam-" . $cam[$id][0] . "-*.edomidvr' \) -ctime +" . ($cam[$id][8] - 1) . " -delete");
                    }
                }
                $ts = strtotime('now');
                $curDate = date('Ymd-H', $ts);
                for ($id = 0; $id < count($cam); $id++) {
                    $tmp = global_dvrPath . '/cam-' . $cam[$id][0] . '-' . $curDate . '-';
                    $cam[$id][3] = fopen($tmp . '1.edomidvr', 'a');
                    $cam[$id][4] = fopen($tmp . '2.edomidvr', 'a');
                    $cam[$id][5] = getFileSize($tmp . '2.edomidvr');
                    $cam[$id][11] = 0;
                    $cam[$id][15] = 0;
                    $cam[$id][16] = $tmp . '0.edomidvr';
                    if (file_exists($cam[$id][16])) {
                        $n = file_get_contents($cam[$id][16]);
                        $tmp = explode(';', trim($n));
                        if (isset($tmp[2]) && $tmp[2] > 0) {
                            $cam[$id][15] = intVal($tmp[2]);
                        }
                    }
                }
                while ($this->procControl != 1 && $curDate == date('Ymd-H', $ts) && $this->isMounted) {
                    if (getMicrotime() >= ($this->mainTimerOld1 + 1)) {
                        $this->mainTimerOld1 = getMicrotime();
                        for ($id = 0; $id < count($cam); $id++) {
                            if ($cam[$id][9] > 0) {
                                if ($tmp = getGADataFromID($cam[$id][9], 0, 'value')) {
                                    $cam[$id][10] = (($tmp['value'] >= 1) ? 1 : 0);
                                    $cam[$id][7] = intval($tmp['value']);
                                    if ($cam[$id][7] < 1) {
                                        $cam[$id][7] = 1;
                                    } else if ($cam[$id][7] > 60) {
                                        $cam[$id][7] = 60;
                                    }
                                } else {
                                    $cam[$id][10] = 0;
                                }
                            }
                            $cam[$id][17] = 0;
                            if ($cam[$id][14] > 0 && ($tmp = getGADataFromID($cam[$id][14], 0, 'value'))) {
                                if ($tmp['value'] != 0) {
                                    $cam[$id][17] = 1;
                                }
                            }
                        }
                    }
                    for ($id = 0; $id < count($cam); $id++) {
                        if ($cam[$id][10] == 1 && getMicrotime() >= ($cam[$id][6] + $cam[$id][7])) {
                            $cam[$id][6] = getMicrotime();
                            $cam[$id][12] = 1;
                            if ($cam[$id][3] && $cam[$id][4]) {
                                $img = getLiveCamImg($cam[$id][0], 0, $cam[$id][1], $cam[$id][2]);
                                if ($img !== false) {
                                    $len = strlen($img);
                                    if (fwrite($cam[$id][4], $img)) {
                                        if (fwrite($cam[$id][3], $ts . ';' . $cam[$id][0] . ';' . $cam[$id][5] . ';' . $len . ';' . $cam[$id][17] . "\n")) {
                                            $cam[$id][11]++;
                                            $cam[$id][12] = 0;
                                            if ($cam[$id][17] == 1) {
                                                $cam[$id][15]++;
                                                file_put_contents($cam[$id][16], $ts . ';' . $cam[$id][0] . ';' . $cam[$id][15] . "\n");
                                            }
                                        }
                                        $cam[$id][5] += $len;
                                    }
                                }
                                $img = null;
                            }
                            if ($cam[$id][12] == 1) {
                                $cam[$id][13]++;
                            }
                        }
                    }
                    $this->proc_check();
                    procStatus_getProcValues($this->procData, $mainTimerOldProc);
                    if ($this->procControl != 1 && (getMicrotime() >= ($this->mainTimerOld3 + 3))) {
                        $this->mainTimerOld3 = getMicrotime();
                        $this->mountCheck();
                        $recCount = 0;
                        $imgCount = 0;
                        $errCount = 0;
                        $imgErrCount = 0;
                        for ($id = 0; $id < count($cam); $id++) {
                            $recCount += $cam[$id][10];
                            $imgCount += $cam[$id][11];
                            $errCount += $cam[$id][12];
                            $imgErrCount += $cam[$id][13];
                        }
                        if ($errCount == count($cam)) {
                            $this->procData[0] = 0;
                        } else {
                            $this->procData[0] = 1;
                        }
                        $this->procData[1] = count($cam);
                        $this->procData[2] = $recCount;
                        $this->procData[3] = $imgCount;
                        $this->procData[4] = $errCount;
                        $this->procData[5] = $imgErrCount;
                        if ($n = getHddSpace(global_dvrPath)) {
                            $this->procData[6] = 100 - $n[2];
                        }
                        procStatus_setData(8, $this->procData);
                    }
                    usleep(1000 * 10);
                    $ts = strtotime('now');
                }
                for ($id = 0; $id < count($cam); $id++) {
                    if ($cam[$id][3]) {
                        fclose($cam[$id][3]);
                    }
                    if ($cam[$id][4]) {
                        fclose($cam[$id][4]);
                    }
                }
            } else {
                $this->proc_check();
                if ($this->procControl != 1 && (getMicrotime() >= ($this->mainTimerOld3 + 3))) {
                    $this->mainTimerOld3 = getMicrotime();
                    $this->procData[0] = 0;
                    procStatus_setData(8, $this->procData);
                    $this->mountCheck();
                }
                sleep(1);
            }
        }
    }

    private function mountCheck()
    {
        if (global_dvrMountcheck) {
            $tmp = isMounted(global_dvrPath);
            if ($tmp === false && $this->isMounted === true) {
                writeToLog(8, false, 'process DVR: target path is not mounted (mount check) - recording is stopped!');
            }
            $this->isMounted = $tmp;
        } else {
            $this->isMounted = true;
        }
    }

    public function exitSelf()
    {
        writeToLog(8, sql_disconnect(), 'Database: close connection');
        writeToLog(8, true, 'process DVR stopped');
        exit();
    }
}
?>
