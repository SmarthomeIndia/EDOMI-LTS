<?
/*
*/
?><? require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
$main = new procSysinfo();
$main->exitSelf();

class procSysinfo
{
    private $procControl;
    private $procTrigger;
    private $procData;
    private $cpuInfo = false;
    private $mainTimerOld5 = 0;

    public function __construct()
    {
        writeToLog(2, true, 'Prozess SYSINFO gestartet');
        sql_connect();
        $this->procControl = null;
        $this->procTrigger = procStatus_getTrigger();
        $this->procData = procStatus_getData(2);
        $this->procData[19] = 2;
        $this->proc_run();
    }

    private function proc_check()
    {
        $this->procTrigger = procStatus_getTrigger($this->procTrigger[1]);
        if ($this->procTrigger[0]) {
            $this->procControl = procStatus_getControl(2);
            if ($this->procControl == 1) {
                writeToLog(2, true, 'Prozess SYSINFO beenden...');
            } else {
                return true;
            }
        }
        return false;
    }

    private function proc_run()
    {
        $mainTimerOldProc = 0;
        do {
            $mainTimerStart = getMicrotime();
            $this->proc_check();
            $this->procData[0] = $this->getCpuInfo();
            if ($this->procData[0] > $this->procData[10] || is_null($this->procData[10])) {
                $this->procData[10] = $this->procData[0];
            }
            if ((intval($mainTimerStart) - intval($this->mainTimerOld5)) >= 5) {
                $this->mainTimerOld5 = intval($mainTimerStart);
                if ($n = $this->getRamInfo()) {
                    $this->procData[1] = $n[2];
                }
                if ($this->procData[1] > $this->procData[11] || is_null($this->procData[11])) {
                    $this->procData[11] = $this->procData[1];
                }
                if ($n = getHddSpace('/')) {
                    $this->procData[2] = 100 - $n[2];
                }
                if ($this->procData[2] > $this->procData[12] || is_null($this->procData[12])) {
                    $this->procData[12] = $this->procData[2];
                }
                $this->procData[3] = $this->getSystemloadInfo();
                if ($this->procData[3] > $this->procData[13] || is_null($this->procData[13])) {
                    $this->procData[13] = $this->procData[3];
                }
                $this->procData[6] = countProcesses('php');
                if ($this->procData[6] > $this->procData[16] || is_null($this->procData[16])) {
                    $this->procData[16] = $this->procData[6];
                }
                $this->procData[7] = countProcesses('httpd');
                if ($this->procData[7] > $this->procData[17] || is_null($this->procData[17])) {
                    $this->procData[17] = $this->procData[7];
                }
                $ss1 = sql_call("SHOW OPEN TABLES");
                if ($n = sql_rowCount($ss1)) {
                    $this->procData[8] = $n;
                }
                if ($this->procData[8] > $this->procData[18] || is_null($this->procData[18])) {
                    $this->procData[18] = $this->procData[8];
                }
                sql_close($ss1);
            }
            procStatus_getProcValues($this->procData, $mainTimerOldProc);
            procStatus_setData(2, $this->procData);
            if ($this->procControl != 1) {
                sleep(1);
            }
        } while ($this->procControl != 1);
    }

    private function getCpuInfo()
    {
        if (file_exists('/proc/stat') && $lines = file('/proc/stat')) {
            foreach ($lines as $line) {
                $n = preg_split('/\s+/', $line);
                if (strtoupper(array_shift($n)) == 'CPU') {
                    if ($this->cpuInfo === false) {
                        $this->cpuInfo = $n;
                        return 0;
                    } else {
                        $tmp[0] = abs($n[0] - $this->cpuInfo[0]);
                        $tmp[1] = abs($n[1] - $this->cpuInfo[1]);
                        $tmp[2] = abs($n[2] - $this->cpuInfo[2]);
                        $tmp[3] = abs($n[3] - $this->cpuInfo[3]);
                        $this->cpuInfo = $n;
                        $total = array_sum($tmp);
                        if ($total > 0) {
                            return round((($tmp[0] + $tmp[1] + $tmp[2]) / $total * 100), 1);
                        } else {
                            return 0;
                        }
                    }
                }
            }
        }
    }

    private function getRamInfo()
    {
        if (file_exists('/proc/meminfo') && $lines = file('/proc/meminfo')) {
            foreach ($lines as $line) {
                $n = preg_split('/\s+/', $line);
                if (strtoupper($n[0]) == 'MEMTOTAL:') {
                    $mem[0] = intVal($n[1]);
                }
                if (strtoupper($n[0]) == 'MEMFREE:') {
                    $mem[1] = intVal($n[1]);
                }
                if (strtoupper($n[0]) == 'BUFFERS:') {
                    $mem[2] = intVal($n[1]);
                }
                if (strtoupper($n[0]) == 'CACHED:') {
                    $mem[3] = intVal($n[1]);
                }
            }
            $r[0] = $mem[0];
            $r[1] = $mem[0] - $mem[1] - $mem[2] - $mem[3];
            if ($r[0] > 0) {
                $r[2] = truncFloat(($r[1] * 100) / $r[0]);
            } else {
                $r[2] = 0;
            }
            return $r;
        } else {
            return false;
        }
    }

    private function getSystemloadInfo()
    {
        $n = sys_getloadavg();
        return round($n[0], 2);
    }

    public function exitSelf()
    {
        writeToLog(2, sql_disconnect(), 'Datenbank: Verbindung schließen');
        writeToLog(2, true, 'Prozess SYSINFO beendet');
        exit();
    }
} ?>
