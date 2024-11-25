
<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/main/include/php/incl_cmd.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
$main = new procMain($argv);
$r = $main->edomi_Init();
if ($r != 0) {
    $main->exitSelf($r);
}
$r = $main->edomi_Start();
if ($r != 0) {
    $main->exitSelf($r);
}
$r = $main->edomi_Main();
$main->exitSelf($r);

class procMain
{
    private $bashVars;
    private $sys_dls;
    private $pid_proc;
    private $config_proc;
    private $modeFlags = array(false, false, false, null);
    private $errFlags = array(false, false);
    private $edomiStartDateTime;

    public function edomi_Init()
    {
        $r = $this->prepare1();
        if ($r != 0) {
            return $r;
        }
        $r = $this->activation();
        if ($r != 0) {
            return $r;
        }
        $r = $this->prepare2();
        if ($r != 0) {
            return $r;
        }
        $r = $this->pause();
        if ($r != 0) {
            return $r;
        }
    }

    public function edomi_Start()
    {
        $r = $this->initDatabases();
        if ($r != 0) {
            return $r;
        }
        $this->initLogicMonitor();
        $r = $this->initProcesses();
        if ($r != 0) {
            return $r;
        }
        $r = $this->waitKnx();
        if ($r != 0) {
            return $r;
        }
        $r = $this->initialize();
        if ($r != 0) {
            return $r;
        }
    }

    public function edomi_Main()
    {
        $r = $this->run();
        return $r;
    }

    public function __construct($args)
    {
        writeToLog(1, true, 'Process MAIN started', null, 'sL');
        $this->bashVars = $args;
        $this->sys_dls = date('I');
        if ($this->bashVars[1] == 0) {
            $this->modeFlags[0] = true;
        }
        if ($this->bashVars[1] == 12) {
            $this->modeFlags[1] = true;
        }
        if ($this->bashVars[1] == 14 || $this->bashVars[1] == 15) {
            $this->modeFlags[2] = true;
        }
        $this->pid_proc = array(null, getmypid(), false, false, false, false, false, false);
        $this->config_proc = array(null, true, true, global_knxGatewayActive, true, true, ((global_phoneGatewayActive && global_phoneMonitorActive) ? true : false), false, false);
        if (file_exists(MAIN_PATH . '/www/data/tmp/startup.txt')) {
            $this->errFlags[0] = true;
        }
        if (file_exists(MAIN_PATH . '/www/data/tmp/lbserror.txt')) {
            if ($tmp = readInfoFile(MAIN_PATH . '/www/data/tmp/lbserror.txt')) {
                $this->errFlags[1] = $tmp[0];
            }
        }
        if ($this->modeFlags[2]) {
            $this->modeFlags[3] = readInfoFile(MAIN_PATH . '/www/data/tmp/activation_options.txt');
        }
        exec('rm -rf ' . MAIN_PATH . '/www/data/tmp/*');
    }

    private function prepare1()
    {
        writeToLog(1, true, 'EDOMI-Version: ' . global_version);
        writeToLog(1, true, 'EDOMI-ClientId: ' . get_clientId());
        if ($this->modeFlags[0]) {
            writeToLog(1, true, 'server was rebooted');
        }
        if (writeToLog(1, (PHP_INT_SIZE === 8), 'checking: 64-Bit-System', 'FATALERROR') === false) {
            return 22;
        }
        if ($this->sys_dls == 0) {
            writeToLog(1, true, 'PHP-time zone: ' . date_default_timezone_get() . ' (winter time)');
        } else {
            writeToLog(1, true, 'PHP-time zone: ' . date_default_timezone_get() . ' (summer time)');
        }
        if (writeToLog(1, sql_connect(), 'Database: establish connection', 'FATALERROR') === false) {
            return 13;
        }
        $tmp = checkAllDatabases(true, global_dbAutoRepair);
        writeToLog(1, ($tmp[1] == 0), 'Database: ' . $tmp[0] . ' database was checked  (' . $tmp[1] . ' Fehler)', 'ERROR');
        if (writeToLog(1, sql_call("SET max_heap_table_size=" . global_mySqlMaxRAMperDB), 'Database: reserve RAM  (' . (global_mySqlMaxRAMperDB / (1024 * 1024)) . ' MB per RAM-DB)', 'FATALERROR') === false) {
            return 13;
        }
        if (!sql_dbExists('edomiAdmin')) {
            if (writeToLog(1, init_DB_Admin(), 'Database: create edomiAdmin ', 'FATALERROR') === false) {
                return 13;
            }
        }
        if (writeToLog(1, init_DB_Live(), 'Database: create edomiLive ', 'FATALERROR') === false) {
            return 13;
        }
        procStatus_setVar(1, 1, $this->pid_proc[1]);
        return 0;
    }

    private function activation()
    {
        if ($this->modeFlags[2]) {
            writeToLog(1, true, 'project activation...');
            setSysInfo(1, -3);
            require(MAIN_PATH . "/www/admin/include/php/base.php");
            require(MAIN_PATH . "/main/include/php/incl_activation.php");
            $tmp = new class_projectActivation();
            $prjId = $tmp->start($this->modeFlags[3]);
            if ($prjId !== false) {
                writeToLog(1, true, 'project activation: working project (' . $prjId . ') activated');
            } else {
                writeToLog(1, false, 'project activation: no working project available!');
            }
            $tmp = null;
        }
        return 0;
    }

    private function prepare2()
    {
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMcmdQueue"), 'Database: edomiLive.RAMcmdQueue deleting');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMcmdQueue (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			status TINYINT UNSIGNED DEFAULT 0,
			runts datetime DEFAULT NULL,
			cmd SMALLINT UNSIGNED DEFAULT NULL,
			cmdid BIGINT UNSIGNED DEFAULT NULL,
			cmdvalue VARCHAR(500) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (status),
			KEY (runts),
			KEY (cmd),
			KEY (cmdid),
			KEY (cmdvalue)
			) ENGINE=MEMORY DEFAULT CHARSET=latin1"), 'Database: edomiLive.RAMcmdQueue created', 'FATALERROR') === false) {
            return 13;
        }
        $this->pid_proc[5] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_queue.php'), 'starting process QUEUE', 'FATALERROR');
        if ($this->pid_proc[5] === false) {
            return 13;
        }
        procStatus_setControl(5, 2);
        $this->pid_proc[2] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_sysinfo.php'), 'starting process SYSINFO', 'FATALERROR');
        if ($this->pid_proc[2] === false) {
            return 13;
        }
        setSysInfo(1, -2);
        if ($this->modeFlags[0] || $this->modeFlags[1]) {
            setSysInfo(2, 11);
        }
        if ($this->errFlags[0]) {
            setSysInfo(3, 1);
            writeToLog(1, false, 'WARNING: EDOMI was stopped unexpectedly!');
            if ($this->modeFlags[0] && global_mailNotifyOnReboot) {
                queueCmd(100, 1);
            }
            sleep(5);
        }
        if ($this->errFlags[1] !== false) {
            writeToLog(1, false, 'WARNING: EDOMI was stopped unexpectedly: FATALERROR at LBS ' . $this->errFlags[1] . ' | EDOMI is paused');
            setSysInfo(4, $this->errFlags[1]);
            setSysInfo(2, 0);
        }
        createInfoFile(MAIN_PATH . '/www/data/tmp/startup.txt', array($this->bashVars[1]));
        return 0;
    }

    private function pause()
    {
        $showPause = true;
        do {
            $ss1 = sql_call("SELECT COUNT(*) FROM edomiLive.RAMsysInfo");
            if ($ss1 === false) {
                writeToLog(1, false, 'mySQL: connection lost!', 'FATALERROR');
                return 13;
            }
            sql_close($ss1);
            $dbOk = true;
            if (!sql_tableExists('edomiLive.ko')) {
                $dbOk = false;
            }
            if (!sql_tableExists('edomiLive.logicElement')) {
                $dbOk = false;
            }
            if (!sql_tableExists('edomiLive.logicElementVar')) {
                $dbOk = false;
            }
            if (!sql_tableExists('edomiLive.logicLink')) {
                $dbOk = false;
            }
            if (!sql_tableExists('edomiLive.logicCmdList')) {
                $dbOk = false;
            }
            if (!file_exists(MAIN_PATH . '/www/data/liveproject/liveprojectname.txt')) {
                $dbOk = false;
            }
            $n = getSysInfo(2);
            if ($n > 0 && $n != 11) {
                return $n;
            }
            if ($n != 11 || !$dbOk) {
                if ($showPause) {
                    if ($dbOk) {
                        writeToLog(1, true, 'EDOMI: Paused (waiting for start command)', null, 'sP');
                    } else {
                        writeToLog(1, true, 'EDOMI: Paused (no live-project available)', null, 'sP');
                    }
                    $showPause = false;
                }
                $this->consoleInfo('PAUSE', '43');
                usleep(500000);
            }
        } while ($n != 11 || !$dbOk);
        setSysInfo(2, 0);
        return 0;
    }

    private function initDatabases()
    {
        setSysInfo(1, 1);
        setSysInfo(4, 0);
        writeToLog(1, sql_call("DELETE FROM edomiLive.RAMcmdQueue"), 'Database: edomiLive.RAMcmdQueue emptying');
        procStatus_setControl(5, 0);
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMko"), 'Database: edomiLive.RAMko deleting');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMko LIKE edomiLive.ko"), 'Database: edomiLive.RAMko creation', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("INSERT INTO edomiLive.RAMko SELECT * FROM edomiLive.ko"), 'Database: edomiLive.RAMko filling', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("ALTER TABLE edomiLive.RAMko ADD COLUMN visuts BIGINT UNSIGNED DEFAULT NULL AFTER id"), 'Database: edomiLive.RAMko modify', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("ALTER TABLE edomiLive.RAMko ADD KEY (value), ADD KEY (defaultvalue), ADD KEY (initscan), ADD KEY (initsend), ADD KEY (endsend), ADD KEY (visuts)"), 'Database: edomiLive.RAMko index creation', 'FATALERROR') === false) {
            return 13;
        }
        sql_call("ALTER TABLE edomiLive.RAMko DROP text");
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicElement"), 'Database: delete edomiLive.RAMlogicElement ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMlogicElement ENGINE=MEMORY SELECT * FROM edomiLive.logicElement"), 'Database: copy edomiLive.RAMlogicElement ', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("ALTER TABLE edomiLive.RAMlogicElement ADD KEY (id), ADD KEY (functionid), ADD KEY (status)"), 'Datenbank: edomiLive.RAMlogicElement Index erstellen', 'FATALERROR') === false) {
            return 13;
        }
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicElementVar"), 'Datenbank: edomiLive.RAMlogicElementVar löschen');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMlogicElementVar LIKE edomiLive.logicElementVar"), 'Datenbank: edomiLive.RAMlogicElementVar erstellen', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("INSERT INTO edomiLive.RAMlogicElementVar SELECT * FROM edomiLive.logicElementVar"), 'Datenbank: edomiLive.RAMlogicElementVar befüllen', 'FATALERROR') === false) {
            return 13;
        }
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicLink"), 'Database: delete edomiLive.RAMlogicLink ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMlogicLink LIKE edomiLive.logicLink"), 'Database: create edomiLive.RAMlogicLink ', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("INSERT INTO edomiLive.RAMlogicLink SELECT * FROM edomiLive.logicLink"), 'Database: fill edomiLive.RAMlogicLink ', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("ALTER TABLE edomiLive.RAMlogicLink ADD KEY (functionid), ADD KEY (eingang), ADD KEY (init), ADD KEY (ausgang), ADD KEY (value)"), 'Database: create index edomiLive.RAMlogicLink ', 'FATALERROR') === false) {
            return 13;
        }
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicCmdList"), 'Database: delete edomiLive.RAMlogicCmdList ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMlogicCmdList LIKE edomiLive.logicCmdList"), 'Datenbank: create edomiLive.RAMlogicCmdList ', 'FATALERROR') === false) {
            return 13;
        }
        if (writeToLog(1, sql_call("INSERT INTO edomiLive.RAMlogicCmdList SELECT * FROM edomiLive.logicCmdList"), 'Database: fill edomiLive.RAMlogicCmdList ', 'FATALERROR') === false) {
            return 13;
        }
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMknxRead"), 'Database: delete edomiLive.RAMknxRead ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMknxRead (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			mode TINYINT UNSIGNED DEFAULT NULL,
			gatyp INT UNSIGNED DEFAULT NULL,
			pa VARCHAR(11) DEFAULT NULL,
			gaid BIGINT UNSIGNED DEFAULT NULL,
			value VARCHAR(10000) DEFAULT NULL,
			local TINYINT UNSIGNED DEFAULT 0,
			remanent TINYINT UNSIGNED DEFAULT 0,
			PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1"), 'Database: edomiLive.RAMknxRead erstellen', 'FATALERROR') === false) {
            return 13;
        }
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMknxWrite"), 'Database: delete edomiLive.RAMknxWrite ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.RAMknxWrite (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			prio TINYINT UNSIGNED DEFAULT 0,
			mode TINYINT UNSIGNED DEFAULT NULL,
			gaid BIGINT UNSIGNED DEFAULT NULL,
			value VARCHAR(1000) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (prio)
			) ENGINE=MEMORY DEFAULT CHARSET=latin1"), 'Database: create edomiLive.RAMknxWrite', 'FATALERROR') === false) {
            return 13;
        }
        sql_call("UPDATE edomiLive.sequence SET datetime=NULL,ms=NULL,playpointer=0");
        sql_call("UPDATE edomiLive.aws SET recordpointer=NULL,playpointer=NULL");
        sql_call("UPDATE edomiLive.cam SET cachets=NULL");
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.visuQueue"), 'Database: deleted edomiLive.visuQueue ');
        if (writeToLog(1, sql_call("CREATE TABLE edomiLive.visuQueue (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			targetid BIGINT UNSIGNED DEFAULT NULL,
			cmd SMALLINT UNSIGNED DEFAULT NULL,
			cmdid BIGINT UNSIGNED DEFAULT NULL,
			cmdvalue VARCHAR(10000) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY (targetid)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1"), 'Datenbank: edomiLive.visuQueue erstellen', 'FATALERROR') === false) {
            return 13;
        }
        return 0;
    }

    private function initLogicMonitor()
    {
        $fnIni = MAIN_PATH . "/logicmonitor.ini";
        $fnCnf = MAIN_PATH . "/main/include/php/logicmonitor_config.php";
        if (global_logLogicEnabled > 0) {
            $tmp = sql_getValue('edomiAdmin.project', 'id', 'edit=1 AND live=1');
            if (!isEmpty($tmp)) {
                if (file_exists($fnIni)) {
                    $ini = file($fnIni);
                    $lmEnabled = false;
                    $cnf = "<?\n";
                    for ($t = 0; $t < count($ini); $t++) {
                        if (trim($ini[$t]) != '' && substr(trim($ini[$t]), 0, 1) != '#') {
                            $var = explode('=', trim($ini[$t]), 2);
                            if (count($var) == 2) {
                                $var[0] = trim($var[0]);
                                $var[1] = trim($var[1]);
                                if ($var[0] == 'logicMonitor_enabled') {
                                    if ($var[1] == 'true') {
                                        $cnf .= "define('logicMonitor_enabled',true);\n";
                                        $lmEnabled = true;
                                    } else {
                                        $cnf .= "define('logicMonitor_enabled',false);\n";
                                    }
                                }
                                if ($var[0] == 'logicMonitor_fileMode') {
                                    if ($var[1] == '1') {
                                        $cnf .= "define('logicMonitor_fileName','0');\n";
                                    } else if ($var[1] == '2') {
                                        $cnf .= "define('logicMonitor_fileName','" . date('Y-m-d_Hxixs') . "');\n";
                                    } else {
                                        $cnf .= "define('logicMonitor_fileName','0');\n";
                                        if ($lmEnabled) {
                                            deleteFiles(MAIN_PATH . '/www/data/log/LOGICLOG_0.*');
                                        }
                                    }
                                }
                                if ($var[0] == 'logicMonitor_elements') {
                                    $tmp = trim(preg_replace("/[^PALL,\+\-0-9]+/i", '', strToUpper($var[1])));
                                    $tmp = explode(',', $tmp);
                                    $arr = '';
                                    foreach ($tmp as $k => $v) {
                                        if (!isEmpty($v)) {
                                            if (substr($v, 0, 4) == '+ALL') {
                                                $arr .= "'+ALL',";
                                            }
                                            if (substr($v, 0, 2) == '+P') {
                                                $arr .= "'" . $v . "',";
                                            }
                                            if (substr($v, 0, 2) == '-P') {
                                                $arr .= "'" . $v . "',";
                                            }
                                            if (substr($v, 0, 2) == '+L') {
                                                $arr .= "'" . $v . "',";
                                            }
                                            if (substr($v, 0, 2) == '-L') {
                                                $arr .= "'" . $v . "',";
                                            }
                                        }
                                    }
                                    $arr = rtrim($arr, ',');
                                    if (!isEmpty($arr)) {
                                        $cnf .= '$logicMonitor_elements' . "=array(" . $arr . ");\n";
                                    } else {
                                        $cnf .= '$logicMonitor_elements' . "=false;\n";
                                    }
                                }
                            }
                        }
                    }
                    $cnf .= "?>";
                    if (strpos($cnf, 'logicMonitor_enabled') !== false && strpos($cnf, 'logicMonitor_fileName') !== false && strpos($cnf, 'logicMonitor_elements') !== false) {
                        writeToLog(1, true, 'logicmonitor: finished configuration');
                        file_put_contents($fnCnf, $cnf);
                    } else {
                        writeToLog(1, false, 'logicmonitor: file  logicmonitor.ini incomplete! (logicmonitor will be deactivated)', 'ERROR');
                        file_put_contents($fnCnf, "<?define('logicMonitor_enabled',false);?>");
                    }
                } else {
                    writeToLog(1, false, 'logicmonitor: file logicmonitor.ini not found! (logicmonitor will be deactivated)', 'ERROR');
                    file_put_contents($fnCnf, "<?define('logicMonitor_enabled',false);?>");
                }
            } else {
                writeToLog(1, true, 'logicmonitor: working and live-project are not matching (logicmonitor will be deactivated)');
                file_put_contents($fnCnf, "<?define('logicMonitor_enabled',false);?>");
            }
        } else {
            writeToLog(1, true, 'logicmonitor: protocol (Basis-configuration) ist deactivated (logicmonitor will be deactivated)');
            file_put_contents($fnCnf, "<?define('logicMonitor_enabled',false);?>");
        }
    }

    private function initProcesses()
    {
        if ($this->config_proc[3]) {
            $this->pid_proc[3] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_knx.php'), 'starting process KNX', 'FATALERROR');
            if ($this->pid_proc[3] === false) {
                return 13;
            }
        }
        $this->pid_proc[4] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_logic.php'), 'starting process LOGIC', 'FATALERROR');
        if ($this->pid_proc[4] === false) {
            return 13;
        }
        if ($this->config_proc[6]) {
            $this->pid_proc[6] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_phone.php'), 'starting process PHONE', 'FATALERROR');
            if ($this->pid_proc[6] === false) {
                return 13;
            }
        }
        if (sql_getCount('edomiLive.visu', 'id>0') > 0) {
            $this->config_proc[7] = true;
            $this->pid_proc[7] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_visu.php'), 'starting process VISU', 'FATALERROR');
            if ($this->pid_proc[7] === false) {
                return 13;
            }
        } else {
            $this->config_proc[7] = false;
        }
        if (!isEmpty(global_dvrPath) && global_dvrActive && sql_getCount('edomiLive.cam', 'dvr=1') > 0) {
            $this->config_proc[8] = true;
            $this->pid_proc[8] = writeToLog(1, launchProcess('php ' . MAIN_PATH . '/main/proc/proc_dvr.php'), 'starting process DVR', 'FATALERROR');
            if ($this->pid_proc[8] === false) {
                return 13;
            }
        } else {
            $this->config_proc[8] = false;
        }
        $p2 = procStatus_waitReady(2, 10);
        if ($this->config_proc[3]) {
            $p3 = procStatus_waitReady(3, 10);
        }
        $p4 = procStatus_waitReady(4, 10);
        $p5 = procStatus_waitReady(5, 10);
        if ($this->config_proc[6]) {
            $p6 = procStatus_waitReady(6, 10);
        }
        if ($this->config_proc[7]) {
            $p7 = procStatus_waitReady(7, 10);
        }
        if ($this->config_proc[8]) {
            $p8 = procStatus_waitReady(8, 10);
        }
        if (!$this->checkProcesses() || !$p2 || ($this->config_proc[3] && !$p3) || !$p4 || !$p5 || ($this->config_proc[6] && !$p6) || ($this->config_proc[7] && !$p7) || ($this->config_proc[8] && !$p8)) {
            writeToLog(1, false, 'WARNING: at least 1 process is not ready!', 'FATALERROR');
            return 13;
        }
        return 0;
    }

    private function waitKnx()
    {
        if ($this->config_proc[3]) {
            $this->consoleInfo('KNX-connection...', '43');
            writeToLog(1, true, 'setup KNX-connection...');
            $t = getMicrotime();
            do {
                $procTmp = procStatus_getVar(3, 19);
                $n = getSysInfo(2);
                if ($n > 0 && $n != 11) {
                    return $n;
                }
                usleep(500000);
            } while ($procTmp != 2 && ((getMicrotime() - $t) < global_knxConnectionTimeout));
            if ($procTmp != 2) {
                writeToLog(1, false, 'WARNING: no KNX connection possible (timeout after ' . global_knxConnectionTimeout . 's)!', 'FATALERROR');
                return 10;
            }
        }
        return 0;
    }

    private function initialize()
    {
        $this->edomiStartDateTime = date('d.m.Y H:i:s');
        writeToLog(1, true, 'INIT', null, 'sI');
        writeToMonLog(0, 1, null, null, null, null, null, null);
        sql_call("UPDATE edomiLive.RAMko SET value=NULL WHERE (remanent=0)");
        sql_call("UPDATE edomiLive.RAMko SET value=defaultvalue WHERE (gatyp=1 AND initsend=0)");
        sql_call("UPDATE edomiLive.RAMko SET value=defaultvalue WHERE (gatyp=2 AND remanent=0)");
        sql_call("UPDATE edomiLive.RAMko SET value=defaultvalue WHERE (gatyp=2 AND remanent=1 AND (value IS NULL))");
        sql_call("UPDATE edomiLive.ko SET value=defaultvalue WHERE (gatyp=2 AND remanent=1 AND (value IS NULL))");
        if ($this->config_proc[3]) {
            $sendGAs = 0;
            $ss1 = sql_call("SELECT id,defaultvalue FROM edomiLive.RAMko WHERE (gatyp=1 AND initsend=1 AND (defaultvalue IS NOT NULL)) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                writeGA($n['id'], $n['defaultvalue'], true, 0);
                $sendGAs++;
                usleep(10);
            }
            sql_close($ss1);
            writeToLog(1, true, 'KNX: InitSend: ' . $sendGAs . ' GAs');
            $timeout = intVal(sql_getCount('edomiLive.RAMknxWrite', '1=1') * 5 / global_knxMaxSendRate) + 30;
            $t = 0;
            while (sql_getCount('edomiLive.RAMknxWrite', '1=1') > 0 && $t < $timeout) {
                sleep(1);
                $t++;
            }
            if (sql_getCount('edomiLive.RAMknxWrite', '1=1') > 0) {
                writeToLog(1, false, 'KNX: InitSend: send of ' . sql_getCount('edomiLive.RAMknxWrite', '1=1') . ' GAs failed!');
            }
        }
        if ($this->config_proc[3]) {
            $scanTry = global_InitScanTry;
            $statScanTry1 = 0;
            $statScanTry2 = 0;
            $scanOk = false;
            $scanGAs = sql_getCount('edomiLive.RAMko', 'gatyp=1 AND initscan=1');
            do {
                writeToLog(1, true, 'KNX: InitScan (' . $scanGAs . ' GAs), reading ' . ($statScanTry1 + 1) . '...');
                $ss1 = sql_call("SELECT id FROM edomiLive.RAMko WHERE (gatyp=1 AND initscan=1) ORDER BY id ASC");
                while ($n = sql_result($ss1)) {
                    requestGA($n['id'], 0);
                    usleep(10);
                }
                sql_close($ss1);
                $timeout = intVal(sql_getCount('edomiLive.RAMknxWrite', '1=1') * 5 / global_knxMaxSendRate) + 30;
                $t = 0;
                while (sql_getCount('edomiLive.RAMknxWrite', '1=1') > 0 && $t < $timeout) {
                    sleep(1);
                    $t++;
                }
                writeToLog(1, true, 'KNX: InitScan (' . $scanGAs . ' GAs), checking (max. ' . global_InitScanTryCheck . ' loops)...');
                $scanTryCheck = global_InitScanTryCheck;
                do {
                    $ss2 = sql_call("SELECT id FROM edomiLive.RAMko WHERE (gatyp=1 AND initscan=1)");
                    if (!sql_result($ss2)) {
                        $scanOk = true;
                    }
                    sql_close($ss2);
                    if (!$scanOk) {
                        sleep(1);
                    }
                    $scanTryCheck--;
                    $statScanTry2++;
                    usleep(10);
                } while ((!$scanOk) && ($scanTryCheck > 0));
                $scanTry--;
                $statScanTry1++;
                usleep(10);
            } while ((!$scanOk) && ($scanTry > 0));
            $scanFail = sql_getCount('edomiLive.RAMko', 'gatyp=1 AND initscan=1');
            writeToLog(1, $scanOk, 'KNX: InitScan finished: ' . $scanGAs . ' GAs read / reading of ' . $scanFail . ' GAs failed (' . $statScanTry1 . ' readings / ' . $statScanTry2 . ' Checks)', 'TIMEOUT');
            $ss1 = sql_call("SELECT id,name,ga FROM edomiLive.RAMko WHERE (gatyp=1 AND initscan=1) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                writeToLog(1, false, 'KNX: InitScan: no reply from GA ' . $n['ga'] . ' ' . $n['name'] . ' (' . $n['id'] . ')', 'TIMEOUT');
                usleep(10);
            }
            sql_close($ss1);
        }
        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivKo WHERE outgaid>0");
        while ($n = sql_result($ss1)) {
            writeGA($n['outgaid'], sql_getCount('edomiLive.archivKoData', 'targetid=' . $n['id']), true, 0);
            usleep(10);
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivMsg WHERE outgaid>0");
        while ($n = sql_result($ss1)) {
            writeGA($n['outgaid'], sql_getCount('edomiLive.archivMsgData', 'targetid=' . $n['id']), true, 0);
            usleep(10);
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivPhone WHERE outgaid>0");
        while ($n = sql_result($ss1)) {
            writeGA($n['outgaid'], sql_getCount('edomiLive.archivPhoneData', 'targetid=' . $n['id']), true, 0);
            usleep(10);
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id,outgaid FROM edomiLive.archivCam WHERE outgaid>0");
        while ($n = sql_result($ss1)) {
            writeGA($n['outgaid'], sql_getCount('edomiLive.archivCamData', 'targetid=' . $n['id']), true, 0);
            usleep(10);
        }
        sql_close($ss1);
        $ss1 = sql_call("SELECT id,gavalue,valuegaid FROM edomiLive.sceneList WHERE valuegaid>0");
        while ($n = sql_result($ss1)) {
            writeGA($n['valuegaid'], $n['gavalue'], true, 0);
            usleep(10);
        }
        sql_close($ss1);
        writeToLog(1, true, 'initialize logic-modules...');
        writeGA(2, 0);
        procStatus_wait(4, 19, 2, 600, $this->pid_proc[4]);
        procStatus_setControl(5, 2);
        if ($this->config_proc[6]) {
            procStatus_setControl(6, 2);
        }
        if ($this->config_proc[7]) {
            procStatus_setControl(7, 2);
        }
        if ($this->config_proc[8]) {
            procStatus_setControl(8, 2);
        }
        return 0;
    }

    private function run()
    {
        writeToLog(1, true, 'START', null, 'sS');
        writeToMonLog(0, 2, null, null, null, null, null, null);
        setSysInfo(1, 2);
        procStatus_setVar(1, 0, $this->edomiStartDateTime);
        writeGA(1, global_version);
        if ($this->errFlags[0]) {
            writeGA(13, 1);
        }
        $oldDate = '';
        $oldTime = '';
        $dayjobDate = date('d.m.Y');
        $mainTimerOld1 = 0;
        $mainTimerOld5 = 0;
        $mainTimerOld10 = 0;
        $mainTimerOld900 = 0;
        $mainTimerOld3600 = 0;
        $mainTrigger21 = false;
        $mainTrigger22 = false;
        $mainTrigger23 = false;
        $mainTrigger24 = false;
        $mainTrigger25 = false;
        $mainTrigger26 = null;
        $procCheck = $this->checkProcesses();
        queueCmd(1, 3, 0);
        if (global_autoupdate) {
            queueCmd(1, 10, 2);
        }
        do {
            $mainTimerStart = getMicrotime();
            $currentDT = explode('/', date('d.m.Y/H:i:s'));
            $currentDThms = explode(':', $currentDT[1]);
            $tmp = getSysInfo(2);
            if ($tmp != 0) {
                return $tmp;
            }
            if ($procCheck) {
                if ($currentDT[0] != $oldDate) {
                    writeGA(4, $currentDT[0], false);
                    $oldDate = $currentDT[0];
                }
                if ($currentDT[1] != $oldTime) {
                    writeGA(5, $currentDT[1], false);
                    $oldTime = $currentDT[1];
                }
                if (date('I') != $this->sys_dls) {
                    if (date('I') == 0) {
                        writeToLog(1, true, 'clock change (summer time -> daylight saving time)! restart...');
                        writeGA(11, 0);
                    } else {
                        writeToLog(1, true, 'clock change (daylight saving time -> summer time)! restart...');
                        writeGA(11, 1);
                    }
                    $this->sys_dls = date('I');
                    return 12;
                }
                if ((intval($mainTimerStart) - intval($mainTimerOld1)) >= 1) {
                    $mainTimerOld1 = intval($mainTimerStart);
                    $procCheck = $this->checkProcesses();
                    if (global_serverConsoleInterval) {
                        $this->consoleInfo($this->edomiStartDateTime, '42');
                    }
                }
                if ((intval($mainTimerStart) - intval($mainTimerOld5)) >= 5) {
                    $mainTimerOld5 = intval($mainTimerStart);
                    $ss1 = sql_call("SELECT COUNT(*) FROM edomiLive.RAMsysInfo");
                    if ($ss1 === false) {
                        writeToLog(1, false, 'Database: connection lost!', 'FATALERROR');
                        return 1013;
                    }
                    sql_close($ss1);
                }
                if ((intval($mainTimerStart) - intval($mainTimerOld10)) >= 10) {
                    $mainTimerOld10 = intval($mainTimerStart);
                    $this->checkSystemWarnings();
                }
                if ((intval($mainTimerStart) - intval($mainTimerOld900)) >= 900) {
                    $mainTimerOld900 = intval($mainTimerStart);
                    if (global_cmdQueueTimeout > 0) {
                        sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (status=1 AND DATE_ADD(runts,INTERVAL " . global_cmdQueueTimeout . " SECOND)<" . sql_getNow() . ")");
                        if (sql_affectedRows() > 0) {
                            writeToLog(1, false, 'QUEUE: ' . sql_affectedRows() . ' removed entries (runtime >' . global_cmdQueueTimeout . 's)');
                        }
                    }
                }
                if ((intval($mainTimerStart) - intval($mainTimerOld3600)) >= 3600) {
                    $mainTimerOld3600 = intval($mainTimerStart);
                    if (global_serverWANIP != 0) {
                        queueCmd(1, 4, 0);
                    }
                    if (!isEmpty(global_serverHeartbeat)) {
                        queueCmd(1, 8, 0);
                    }
                    if (!global_serverConsoleInterval) {
                        $this->consoleInfo($this->edomiStartDateTime, '42');
                    }
                }
                if ($currentDT[0] != $dayjobDate) {
                    $dayjobDate = $currentDT[0];
                    $procData = procStatus_getData(2);
                    if (date('d.m', strtotime($dayjobDate)) == '01.01') {
                        writeGA(17, 1, false);
                    }
                    if (date('d', strtotime($dayjobDate)) == 1) {
                        writeGA(18, 1, false);
                    }
                    if (date('N', strtotime($dayjobDate)) == 1) {
                        writeGA(19, 1, false);
                    }
                    writeGA(20, 1, false);
                    queueCmd(1, 3, 0);
                    if (global_daylyStats) {
                        writeToLog(1, true, 'Aktuell: CPU ' . $procData[0] . '% / RAM ' . $procData[1] . '% / HDD ' . $procData[2] . '% / LOAD ' . $procData[3] . ' / PHP ' . $procData[6] . ' / HTTP ' . $procData[7]);
                        writeToLog(1, true, 'Peaks  : CPU ' . $procData[10] . '% / RAM ' . $procData[11] . '% / HDD ' . $procData[12] . '% / LOAD ' . $procData[13] . ' / PHP ' . $procData[16] . ' / HTTP ' . $procData[17]);
                    }
                    if (global_autoBackup) {
                        queueCmd(1, 2, 0);
                    }
                    if (global_daylyWarnMail && ($procData[0] > 99 || $procData[1] > 90 || $procData[2] > 90)) {
                        queueCmd(100, 2);
                    }
                    if (global_autoupdate) {
                        queueCmd(1, 10, 2);
                    }
                }
                if ($currentDThms[1] == 0) {
                    if (!$mainTrigger21) {
                        writeGA(21, 1, false);
                        $mainTrigger21 = true;
                    }
                } else {
                    $mainTrigger21 = false;
                }
                if (($currentDThms[1] % 30) == 0) {
                    if (!$mainTrigger22) {
                        writeGA(22, 1, false);
                        $mainTrigger22 = true;
                    }
                } else {
                    $mainTrigger22 = false;
                }
                if (($currentDThms[1] % 15) == 0) {
                    if (!$mainTrigger23) {
                        writeGA(23, 1, false);
                        $mainTrigger23 = true;
                    }
                } else {
                    $mainTrigger23 = false;
                }
                if (($currentDThms[1] % 10) == 0) {
                    if (!$mainTrigger24) {
                        writeGA(24, 1, false);
                        $mainTrigger24 = true;
                    }
                } else {
                    $mainTrigger24 = false;
                }
                if (($currentDThms[1] % 5) == 0) {
                    if (!$mainTrigger25) {
                        writeGA(25, 1, false);
                        $mainTrigger25 = true;
                    }
                } else {
                    $mainTrigger25 = false;
                }
                if ($currentDThms[1] != $mainTrigger26) {
                    if (!is_null($mainTrigger26)) {
                        writeGA(26, 1, false);
                    }
                    $mainTrigger26 = $currentDThms[1];
                }
                $wait = (250 * 1000) - ((getMicrotime() - $mainTimerStart) * 1000000);
                if ($wait > 0) {
                    usleep($wait);
                } else {
                    usleep(1);
                }
            } else {
                if ((!checkProcess($this->pid_proc[4])) && file_exists(MAIN_PATH . '/www/data/tmp/lbserror.txt')) {
                    return 1010;
                } else {
                    return 1013;
                }
            }
        } while (true);
        return 0;
    }

    private function consoleInfo($n, $color)
    {
        echo "\33[2K--------------------------------------------------------------------------------\n";
        echo "\33[2K\33[" . $color . "m\33[30m" . str_pad(substr($n, 0, 19), 19, ' ', STR_PAD_RIGHT) . "\33[0m [EDOMI " . str_pad(substr(global_version, 0, 4), 4, ' ', STR_PAD_RIGHT) . " - Long Term Evolution] \33[47m\33[30m" . str_pad(substr(date('d.m.Y H:i:s'), 0, 19), 19, ' ', STR_PAD_RIGHT) . chr(13);
        echo "\33[1A\33[0m";
    }

    private function checkProcesses()
    {
        if (!checkProcess($this->pid_proc[2])) {
            writeToLog(1, false, 'process SYSINFO is not running anymore!', 'FATALERROR');
            return false;
        }
        if ($this->config_proc[3] && !checkProcess($this->pid_proc[3])) {
            writeToLog(1, false, 'process KNX is not running anymore!', 'FATALERROR');
            return false;
        }
        if (!checkProcess($this->pid_proc[4])) {
            writeToLog(1, false, 'process LOGIC is not running anymore!', 'FATALERROR');
            return false;
        }
        if (!checkProcess($this->pid_proc[5])) {
            writeToLog(1, false, 'process QUEUE is not running anymore!', 'FATALERROR');
            return false;
        }
        if ($this->config_proc[6] && !checkProcess($this->pid_proc[6])) {
            writeToLog(1, false, 'process PHONE is not running anymore!', 'FATALERROR');
            return false;
        }
        if ($this->config_proc[7] && !checkProcess($this->pid_proc[7])) {
            writeToLog(1, false, 'process VISU is not running anymore!', 'FATALERROR');
            return false;
        }
        if ($this->config_proc[8] && !checkProcess($this->pid_proc[8])) {
            writeToLog(1, false, 'process DVR is not running anymore!', 'FATALERROR');
            return false;
        }
        return true;
    }

    private function deleteServerData($mode)
    {
        if ($mode == 1) {
            sql_call("DROP DATABASE edomiLive");
            sql_call("UPDATE edomiAdmin.project SET live=0");
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/live/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/img/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/etc/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/lbs/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/vse/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/*.*');
            if (!isEmpty(global_dvrPath)) {
                deleteFiles(global_dvrPath . '/cam-*.edomidvr');
            }
            writeToLog(1, true, 'Live-Project is deleted.');
        }
        if ($mode == 3) {
            sql_call("DROP DATABASE edomiAdmin");
            sql_call("DROP DATABASE edomiProject");
            sql_call("DROP DATABASE edomiLive");
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/live/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/cam/archiv/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/img/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/visu/etc/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/lbs/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/vse/*.*');
            deleteFiles(MAIN_PATH . '/www/data/liveproject/*.*');
            deleteFiles(MAIN_PATH . '/www/data/tmp/*');
            deleteFiles(MAIN_PATH . '/www/data/log/*');
            if (!isEmpty(global_dvrPath)) {
                deleteFiles(global_dvrPath . '/cam-*.edomidvr');
            }
            deleteFiles(MAIN_PATH . '/www/data/project/visu/img/*.*');
            deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/*.*');
            deleteFiles(MAIN_PATH . '/www/data/project/*.*');
            deleteFiles(MAIN_PATH . '/www/data/projectarchiv/*.*');
            deleteFiles(MAIN_PATH . '/www/admin/vse/vse_include_*.php');
            deleteFiles(MAIN_PATH . '/www/admin/vse/vse_include_*.js');
            deleteFiles(MAIN_PATH . '/www/admin/help/1002-*.htm');
            deleteFiles(MAIN_PATH . '/www/admin/help/lbs_*.htm');
            writeToLog(1, true, 'All data are completly deleted (Factory settings).');
            writeToLog(1, init_DB_Admin(), 'Database: create edomiAdmin ');
            writeToLog(1, init_DB_Project(), 'Database: create edomiProject ');
        }
    }

    private function checkSystemWarnings()
    {
        $procData = procStatus_getData(2);
        if ($procData[0] > 99) {
            $n = getGADataFromID(7, 2);
            if ($n['value'] != 1) {
                writeGA(7, 1);
            }
        }
        if ($procData[0] < 90) {
            $n = getGADataFromID(7, 2);
            if ($n['value'] != 0) {
                writeGA(7, 0);
            }
        }
        if ($procData[1] > 90) {
            $n = getGADataFromID(8, 2);
            if ($n['value'] != 1) {
                writeGA(8, 1);
            }
        }
        if ($procData[1] < 80) {
            $n = getGADataFromID(8, 2);
            if ($n['value'] != 0) {
                writeGA(8, 0);
            }
        }
        if ($procData[2] > 90) {
            $n = getGADataFromID(9, 2);
            if ($n['value'] != 1) {
                writeGA(9, 1);
            }
        }
        if ($procData[2] < 80) {
            $n = getGADataFromID(9, 2);
            if ($n['value'] != 0) {
                writeGA(9, 0);
            }
        }
        $errCount = getFileSize(MAIN_PATH . '/www/data/tmp/errorcount.txt');
        $n = getGADataFromID(10, 2);
        if ($n['value'] != $errCount) {
            writeGA(10, $errCount);
        }
        if (file_exists(MAIN_PATH . '/www/data/tmp/camerror.txt')) {
            $n1 = file_get_contents(MAIN_PATH . '/www/data/tmp/camerror.txt');
            if (!isEmpty($n1)) {
                $n2 = getGADataFromID(16, 2);
                if ($n1 != $n2['value']) {
                    writeGA(16, $n1, false);
                }
            }
        }
    }

    public function exitSelf($sysCmd)
    {
        writeToLog(1, true, 'stopping  MAIN... (Modus ' . $sysCmd . ')', null, 'sE');
        writeToLog(1, true, 'EDOMI: stopping...');
        $procLogic_running = checkProcess($this->pid_proc[4]);
        if ($procLogic_running) {
            writeToLog(1, true, 'EDOMI: waiting for LBS (3 seconds)...');
            writeGA(2, 2);
            if ($sysCmd == 1010 || $sysCmd == 1013) {
                writeGA(13, 1);
            }
            sleep(3);
            writeGA(2, 3);
            procStatus_wait(4, 19, 4, 3, false);
        }
        if ($this->config_proc[3] && checkProcess($this->pid_proc[3])) {
            $sendGAs = 0;
            sql_call("DELETE FROM edomiLive.RAMknxWrite");
            $ss1 = sql_call("SELECT id,endvalue FROM edomiLive.RAMko WHERE (gatyp=1 AND endsend=1 AND (endvalue IS NOT NULL)) ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                writeGA($n['id'], $n['endvalue'], true, 0);
                $sendGAs++;
                usleep(10);
            }
            sql_close($ss1);
            writeToLog(1, true, 'KNX: EndSend: ' . $sendGAs . ' GAs');
            $timeout = intVal(sql_getCount('edomiLive.RAMknxWrite', '1=1') * 5 / global_knxMaxSendRate) + 30;
            $t = 0;
            while (sql_getCount('edomiLive.RAMknxWrite', '1=1') > 0 && $t < $timeout) {
                sleep(1);
                $t++;
            }
            if (sql_getCount('edomiLive.RAMknxWrite', '1=1') > 0) {
                writeToLog(1, false, 'KNX: EndSend: send of ' . sql_getCount('edomiLive.RAMknxWrite', '1=1') . ' GAs failed!');
            }
        }
        setSysInfo(1, -1);
        sql_call("FLUSH TABLES");
        writeToLog(1, procStatus_quit(2, $this->pid_proc[2]), 'process SYSINFO (PID=' . $this->pid_proc[2] . ') stopping');
        if ($this->config_proc[3]) {
            writeToLog(1, procStatus_quit(3, $this->pid_proc[3]), 'process KNX (PID=' . $this->pid_proc[3] . ') stopping');
        }
        writeToLog(1, procStatus_quit(4, $this->pid_proc[4]), 'process LOGIC (PID=' . $this->pid_proc[4] . ') stopping');
        writeToLog(1, procStatus_quit(5, $this->pid_proc[5]), 'process QUEUE (PID=' . $this->pid_proc[5] . ') stopping');
        if ($this->config_proc[6]) {
            writeToLog(1, procStatus_quit(6, $this->pid_proc[6]), 'process PHONE (PID=' . $this->pid_proc[6] . ') stopping');
        }
        if ($this->config_proc[7]) {
            writeToLog(1, procStatus_quit(7, $this->pid_proc[7]), 'process VISU (PID=' . $this->pid_proc[7] . ') stopping');
        }
        if ($this->config_proc[8]) {
            writeToLog(1, procStatus_quit(8, $this->pid_proc[8]), 'process DVR (PID=' . $this->pid_proc[8] . ') stopping');
        }
        writeToMonLog(0, 3, null, null, null, null, null, null);
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMsysInfo"), 'Database: delete edomiLive.RAMsysInfo ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMcmdQueue"), 'Database: delete edomiLive.RAMcmdQueue ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMko"), 'Database: delete edomiLive.RAMko ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicElement"), 'Database: delete edomiLive.RAMlogicElement ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicElementVar"), 'Database: delete edomiLive.RAMlogicElementVar ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicLink"), 'Database: delete edomiLive.RAMlogicLink ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMlogicCmdList"), 'Database: delete edomiLive.RAMlogicCmdList ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.logicExecQueue"), 'Database: delete edomiLive.logicExecQueue ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.visuQueue"), 'Database: delete edomiLive.visuQueue ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMknxRead"), 'Database: delete edomiLive.RAMknxRead ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMknxWrite"), 'Database: delete edomiLive.RAMknxWrite ');
        writeToLog(1, sql_call("DROP TABLE IF EXISTS edomiLive.RAMsysProc"), 'Database: delete edomiLive.RAMsysProc ');
        setSysInfo(1, 0);
        if ($procLogic_running) {
            writeToLog(1, true, 'EDOMI: waiting for EXEC-LBS (3 seconds)...');
            sleep(3);
        }
        if ($sysCmd == 15 || $sysCmd == 31) {
            $this->deleteServerData(1);
        }
        if ($sysCmd == 33) {
            $this->deleteServerData(3);
        }
        writeToLog(1, sql_disconnect(), 'Database: close connection');
        if ($sysCmd == 1010) {
            $sysCmd = 10;
        } else if ($sysCmd == 1013) {
            $sysCmd = 13;
        } else {
            deleteFiles(MAIN_PATH . '/www/data/tmp/startup.txt');
        }
        $exitCode = 13;
        if ($sysCmd == 10) {
            $exitCode = 10;
        }
        if ($sysCmd == 12) {
            $exitCode = 12;
        }
        if ($sysCmd == 13) {
            $exitCode = 13;
        }
        if ($sysCmd == 14) {
            $exitCode = 14;
        }
        if ($sysCmd == 15) {
            $exitCode = 15;
        }
        if ($sysCmd == 21) {
            $exitCode = 21;
        }
        if ($sysCmd == 22) {
            $exitCode = 22;
        }
        if ($sysCmd == 23) {
            $exitCode = 23;
        }
        if ($sysCmd == 24) {
            $exitCode = 24;
        }
        if ($sysCmd == 31) {
            $exitCode = 10;
        }
        if ($sysCmd == 33) {
            $exitCode = 10;
        }
        createInfoFile(MAIN_PATH . '/www/data/tmp/exit.txt', array($exitCode));
        writeToLog(1, true, 'Prozess MAIN stopped (' . $exitCode . ')', null, 'sX');
        exit($exitCode);
    }
} ?>
