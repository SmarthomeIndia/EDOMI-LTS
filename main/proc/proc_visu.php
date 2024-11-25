<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_process.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
require(MAIN_PATH . "/www/visu/include/php/base.php");
restore_error_handler();
error_reporting(0);
writeToLog(7, true, 'Process VISU started');
sql_connect();
$procControl = null;
$procTrigger = procStatus_getTrigger();
$procData = procStatus_getData(7);
resetProcData($procData);
$procData[0] = 0;
$procData[19] = 1;
procStatus_setData(7, $procData);
do {
    $procTrigger = procStatus_getTrigger($procTrigger[1]);
    if ($procTrigger[0]) {
        $procControl = procStatus_getControl(7);
        if ($procControl == 1) {
            writeToLog(7, true, 'stopping process VISU ...');
            writeToVisuLog(true, '', '', 'stopping', 'sE');
        }
        resetProcData($procData);
        $procData[0] = 0;
        $procData[19] = 1;
        procStatus_setData(7, $procData);
    }
    sql_call("UPDATE edomiLive.visuUserList SET online=0,logout=0");
    if ($procControl == 2) {
        writeToVisuLog(true, '', '', 'started', 'sS');
        $socket = new webSocket($procData);
        $socket->logoutClients();
        if ($socket->socketOpen()) {
            $timerStats = 0;
            $mainTimerOldProc = 0;
            resetProcData($procData);
            $procData[0] = 1;
            $procData[19] = 2;
            procStatus_setData(7, $procData);
            do {
                $mainTimerStart = getMicrotime();
                $procTrigger = procStatus_getTrigger($procTrigger[1]);
                if ($procTrigger[0]) {
                    $procControl = procStatus_getControl(7);
                    if ($procControl == 1) {
                        writeToLog(7, true, 'stop process VISU ...');
                        writeToVisuLog(true, '', '', 'stopping', 'sE');
                    }
                    if ($procControl == 3) {
                        writeToLog(7, true, 'pause process VISU and restart...');
                        writeToVisuLog(true, '', '', 'stop and restart', 'sE');
                    }
                }
                if ($procControl != 1 && $procControl != 3) {
                    $socket->socketMain();
                } else {
                    $socket->disconnectAllClients();
                }
                procStatus_getProcValues($procData, $mainTimerOldProc);
                if (($mainTimerStart - $timerStats) >= 1) {
                    $timerStats = $mainTimerStart;
                    procStatus_setData(7, $procData);
                    if ($procData[1] > $procData[11] || is_null($procData[11])) {
                        $procData[11] = $procData[1];
                    }
                    if ($procData[2] > $procData[12] || is_null($procData[12])) {
                        $procData[12] = $procData[2];
                    }
                    if ($procData[3] > $procData[13] || is_null($procData[13])) {
                        $procData[13] = $procData[3];
                    }
                    if ($procData[4] > $procData[14] || is_null($procData[14])) {
                        $procData[14] = $procData[4];
                    }
                    if ($procData[5] > $procData[15] || is_null($procData[15])) {
                        $procData[15] = $procData[5];
                    }
                    if ($procData[6] > $procData[16] || is_null($procData[16])) {
                        $procData[16] = $procData[6];
                    }
                    if ($procData[7] > $procData[17] || is_null($procData[17])) {
                        $procData[17] = $procData[7];
                    }
                    if ($procData[8] > $procData[18] || is_null($procData[18])) {
                        $procData[18] = $procData[8];
                    }
                    $procData[2] = 0;
                    $procData[3] = 0;
                    $procData[4] = 0;
                    $procData[5] = 0;
                    $procData[6] = 0;
                    $procData[7] = 0;
                }
                if ($procControl != 1 && $procControl != 3) {
                    usleep(10000);
                }
            } while ($procControl != 1 && $procControl != 3);
        }
        $socket->logoutClients();
        $socket->socketClose();
        unset($socket);
        if ($procControl != 1 && $procControl != 3) {
            sleep(3);
        }
        writeToVisuLog(true, '', '', 'stopped', 'sX');
    } else {
        if ($procControl != 1) {
            usleep(100000);
        }
    }
} while ($procControl != 1);
sql_call("UPDATE edomiLive.visuUserList SET online=0,logout=0");
exitSelf();
function exitSelf()
{
    procStatus_setVar(7, 0, 0);
    writeToLog(7, sql_disconnect(), 'database: closing connection');
    writeToLog(7, true, 'process VISU stopped');
    exit();
}

function resetProcData(&$procData)
{
    $procData[1] = 0;
    $procData[2] = 0;
    $procData[3] = 0;
    $procData[4] = 0;
    $procData[5] = 0;
    $procData[6] = 0;
    $procData[7] = 0;
    $procData[8] = 0;
    $procData[9] = 0;
    $procData[11] = 0;
    $procData[12] = 0;
    $procData[13] = 0;
    $procData[14] = 0;
    $procData[15] = 0;
    $procData[16] = 0;
    $procData[17] = 0;
    $procData[18] = 0;
}

class webSocket
{
    private $timerSid;
    private $timerKo;
    private $timerEvent;
    private $timerClient;
    private $master;
    private $sockets;
    private $clients = array();
    private $procData;
    private $websocketPing = 5;

    public function __construct(&$procData)
    {
        $this->procData =& $procData;
    }

    private function trace($n, $error = false)
    {
        if (global_logVisuWebsocket == 4) {
            writeToCustomLog('PROC_VISU', (($error) ? 'ERROR' : 'OK'), $n);
        } else if ($error) {
            if (global_logVisuWebsocket == 1 || global_logVisuWebsocket == 3) {
                writeToLog(7, false, $n);
            }
            if (global_logVisuWebsocket == 2 || global_logVisuWebsocket == 3) {
                writeToCustomLog('PROC_VISU', 'ERROR', $n);
            }
        }
    }

    private function visuLog($clientId, $logMsg, $ok = true)
    {
        if (global_logVisuEnabled > 0) {
            if (!isEmpty($clientId)) {
                $tmp1 = sql_getValue('edomiLive.visu', 'name', 'id=' . $this->clients[$clientId]['vid']) . ' (' . $this->clients[$clientId]['vid'] . ')';
                $tmp2 = sql_getValue('edomiLive.visuUser', 'login', 'id=' . $this->clients[$clientId]['userdata']['targetid']) . ' (' . $this->clients[$clientId]['userdata']['targetid'] . ')';
            } else {
                $tmp1 = '';
                $tmp2 = '';
            }
            writeToVisuLog($ok, $tmp1, $tmp2, $logMsg);
        }
    }

    public function socketOpen()
    {
        $this->timerKo = 0;
        $this->timerEvent = 0;
        $this->timerClient = 0;
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        if (is_resource($socket)) {
            if (socket_bind($socket, global_visuIP, global_visuWebsocketPort)) {
                if (socket_listen($socket, 20)) {
                    $this->trace('Socket opened at port ' . global_visuWebsocketPort);
                    $this->master = $socket;
                    return true;
                }
            }
        }
        $this->trace('Socket failed to open at port ' . global_visuWebsocketPort . ': ' . socket_strerror(socket_last_error()), true);
        return false;
    }

    public function disconnectAllClients()
    {
        for ($t = 0; $t < count($this->clients); $t++) {
            if ($this->clients[$t]['connected']) {
                $this->sendToClient($t, 'CLOSE');
            }
        }
    }

    public function socketClose()
    {
        if (is_resource($this->master)) {
            $this->trace('Socket closed');
            socket_close($this->master);
        }
    }

    public function socketMain()
    {
        $mainTimerStart = getMicrotime();
        $tmpSockets = array($this->master);
        for ($t = 0; $t < count($this->clients); $t++) {
            if (is_resource($this->clients[$t]['socket'])) {
                $tmpSockets[] = $this->clients[$t]['socket'];
            }
        }
        $tmp1 = null;
        $tmp2 = null;
        @socket_select($tmpSockets, $tmp1, $tmp2, 0);
        foreach ($tmpSockets as $socket) {
            if ($socket == $this->master) {
                if (($acceptedSocket = socket_accept($this->master)) < 0) {
                    $this->trace('Client-Socket not accepted: ' . socket_strerror(socket_last_error($acceptedSocket)), true);
                } else {
                    $this->trace('Client-Socket accepted from new Client: ' . $acceptedSocket);
                    $this->connectClient($acceptedSocket);
                }
            } else {
                $clientId = $this->getClientIdFromSocket($socket);
                if ($clientId !== false) {
                    $data = '';
                    while (@socket_recv($socket, $r, 8192, MSG_DONTWAIT)) {
                        $data .= $r;
                    }
                    if (!isEmpty($data)) {
                        if (!$this->clients[$clientId]['connected']) {
                            if ($this->handshake($clientId, $data)) {
                                $this->trace('Handshake with Client-ID ' . $clientId);
                                $this->clients[$clientId]['connected'] = true;
                                $this->sendToClient($clientId, 'LOGIN');
                            } else {
                                $this->trace('Handshake failed with Client-ID ' . $clientId, true);
                                $this->disconnectClient($clientId);
                            }
                        } else {
                            $msg = $this->decode($data);
                            if ($msg === false) {
                                $this->trace('Receiving invalid data-frame from Client-ID ' . $clientId, true);
                            } else {
                                foreach ($msg as $arr) {
                                    if (!$this->checkMessage($clientId, $arr)) {
                                        $this->disconnectClient($clientId);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (($mainTimerStart - $this->timerSid) >= 1) {
            $this->timerSid = $mainTimerStart;
            $this->getUserdata(null, null);
        }
        if (($mainTimerStart - $this->timerKo) >= (global_visuWebsocketKo / 1000)) {
            $this->timerKo = $mainTimerStart;
            $this->checkKos();
        }
        if (($mainTimerStart - $this->timerEvent) >= (global_visuWebsocketEvent / 1000)) {
            $this->timerEvent = $mainTimerStart;
            $this->checkEvents();
        }
        if (($mainTimerStart - $this->timerClient) >= $this->websocketPing) {
            $this->timerClient = $mainTimerStart;
            $this->checkClients();
        }
    }

    private function getUserdata($vid, $sid, $clientId = false)
    {
        if ($clientId !== false) {
            $this->clients[$clientId]['userdata'] = checkVisuSid($vid, $sid, true, true);
        } else {
            foreach ($this->clients as $clientId => &$client) {
                if ($client['started']) {
                    $client['userdata'] = checkVisuSid($client['vid'], $client['sid'], true, true);
                    if ($client['userdata'] === false) {
                        $this->disconnectClient($clientId);
                    } else if ($client['userdata']['logout'] == 1) {
                        $this->disconnectClient($clientId);
                    }
                }
            }
        }
    }

    private function checkMessage($clientId, &$arr)
    {
        $this->trace('Receive from Client-ID ' . $clientId . ': ' . implode('/', $arr));
        if ($arr[0] == 'LOGIN') {
            $sid = preg_replace("/[^A-F0-9]+/i", '', $arr[1]);
            $vid = preg_replace("/[^\.0-9]+/i", '', $arr[2]);
            $this->getUserdata($vid, $sid, $clientId);
            if ($this->clients[$clientId]['userdata'] !== false) {
                $this->clients[$clientId]['loggedin'] = true;
                $this->clients[$clientId]['sid'] = $sid;
                $this->clients[$clientId]['vid'] = $vid;
                $this->setStatusKo1($clientId);
                $this->sendToClient($clientId, 'START');
                $this->procData[1] = count($this->clients);
                $this->visuLog($clientId, 'LOGIN: Client-Id=' . $clientId . ' / SID=' . $this->clients[$clientId]['sid'] . ' / IP=' . $this->clients[$clientId]['userdata']['loginip']);
                return true;
            }
        } else if ($arr[0] == 'INITPAGE') {
            if ($this->clients[$clientId]['loggedin'] && $this->clients[$clientId]['userdata'] !== false && $this->initPage($clientId, $arr[2])) {
                $this->clients[$clientId]['started'] = true;
                if ($arr[3] == 1) {
                    $this->setStatusKo3($clientId);
                }
                $this->procData[5]++;
                return true;
            }
        } else if ($arr[0] == 'CLOSEPOPUP') {
            if ($this->clients[$clientId]['started'] && $this->clients[$clientId]['userdata'] !== false && $this->closePopup($clientId, $arr[2])) {
                if ($arr[3] == 1) {
                    $this->setStatusKo3($clientId);
                }
                return true;
            }
        } else if ($arr[0] == 'EXECCMDLIST') {
            if ($this->clients[$clientId]['started'] && $this->clients[$clientId]['userdata'] !== false) {
                sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (3,0," . $arr[2] . ",'" . $this->clients[$clientId]['vid'] . "')");
                $this->setStatusKo3($clientId);
                $this->procData[4]++;
                return true;
            }
        } else if ($arr[0] == 'SETKOVALUE') {
            if ($this->clients[$clientId]['started'] && $this->clients[$clientId]['userdata'] !== false) {
                writeGA($arr[2], $arr[3]);
                $this->setStatusKo3($clientId);
                $this->procData[4]++;
                return true;
            }
        } else if ($arr[0] == 'PONG') {
            if ($this->clients[$clientId]['loggedin'] && $this->clients[$clientId]['userdata'] !== false) {
                $this->clients[$clientId]['ping'] = null;
                return true;
            }
        } else if ($arr[0] == 'CLOSE') {
            if ($this->clients[$clientId]['userdata'] !== false) {
                $this->disconnectClient($clientId);
                return true;
            }
        } else {
            $this->trace('Receiving invalid message from Client-ID ' . $clientId . ': ' . implode('/', $arr), true);
            return true;
        }
        return false;
    }

    private function checkKos()
    {
        $this->procData[9] = 0;
        for ($clientId = 0; $clientId < count($this->clients); $clientId++) {
            if ($this->clients[$clientId]['loggedin'] && $this->clients[$clientId]['userdata'] !== false) {
                $newItems = '';
                $newValues = '';
                $koList = array();
                foreach ($this->clients[$clientId]['page'] as $pageId => $page) {
                    foreach ($page as $itemId => $item) {
                        if (isset($item['ko1']) && array_search($item['ko1'][0], $koList) === false) {
                            $koList[] = $item['ko1'][0];
                        }
                        if (isset($item['ko2']) && array_search($item['ko2'][0], $koList) === false) {
                            $koList[] = $item['ko2'][0];
                        }
                        if (isset($item['ko3']) && array_search($item['ko3'][0], $koList) === false) {
                            $koList[] = $item['ko3'][0];
                        }
                    }
                }
                $kos = array();
                $koSql = implode(',', $koList);
                $ss1 = sql_call("SELECT id,visuts,value,valuetyp FROM edomiLive.RAMko WHERE id IN (" . $koSql . ")");
                while ($ko = sql_result($ss1)) {
                    $kos[$ko['id']] = array(intval($ko['visuts']), $ko['value'], $ko['valuetyp']);
                }
                sql_close($ss1);
                $this->procData[9] = count($kos);
                foreach ($this->clients[$clientId]['page'] as $pageId => $page) {
                    foreach ($page as $itemId => $item) {
                        $ko1 = false;
                        $ko1updated = false;
                        $ko2 = false;
                        $ko2updated = false;
                        $ko3 = false;
                        $ko3updated = false;
                        $ko1value = '';
                        $ko2value = '';
                        $ko3value = '';
                        if (isset($item['ko1']) && isset($kos[$item['ko1'][0]])) {
                            $ko1 = true;
                            $ko1value = $kos[$item['ko1'][0]][1];
                            $ko1dpt = $kos[$item['ko1'][0]][2];
                            if ($kos[$item['ko1'][0]][0] > $item['ko1'][1]) {
                                $ko1updated = true;
                                $this->clients[$clientId]['page'][$pageId][$itemId]['ko1'][1] = $kos[$item['ko1'][0]][0];
                            }
                        }
                        if (isset($item['ko2']) && isset($kos[$item['ko2'][0]])) {
                            $ko2 = true;
                            $ko2value = $kos[$item['ko2'][0]][1];
                            $ko2dpt = $kos[$item['ko2'][0]][2];
                            if ($kos[$item['ko2'][0]][0] > $item['ko2'][1]) {
                                $ko2updated = true;
                                $this->clients[$clientId]['page'][$pageId][$itemId]['ko2'][1] = $kos[$item['ko2'][0]][0];
                            }
                        }
                        if (isset($item['ko3']) && isset($kos[$item['ko3'][0]])) {
                            $ko3 = true;
                            $ko3value = $kos[$item['ko3'][0]][1];
                            $ko3dpt = $kos[$item['ko3'][0]][2];
                            if ($kos[$item['ko3'][0]][0] > $item['ko3'][1]) {
                                $ko3updated = true;
                                $this->clients[$clientId]['page'][$pageId][$itemId]['ko3'][1] = $kos[$item['ko3'][0]][0];
                            }
                        }
                        if ($ko2updated && !$ko1updated && !$ko3updated) {
                            if (!$ko1updated) {
                                $ko1value = '';
                            }
                            if (!$ko2updated) {
                                $ko2value = '';
                            }
                            if (!$ko3updated) {
                                $ko3value = '';
                            }
                            $newValues .= '{id:"' . $itemId . '",kovalues:{update1:"' . (($ko1updated) ? 1 : 0) . '",update2:"' . (($ko2updated) ? 1 : 0) . '",update3:"' . (($ko3updated) ? 1 : 0) . '",kovalue1:"' . ajaxValue($ko1value, false) . '",kovalue2:"' . ajaxValue($ko2value, false) . '",kovalue3:"' . ajaxValue($ko3value, false) . '"}},';
                            $this->procData[3]++;
                        } else if ($ko1updated || $ko3updated) {
                            if ($ko3updated || (isset($item['ko3']) && $ko1updated)) {
                                $newItems .= '{id:"' . $itemId . '",design:' . $this->getElementDesignData($clientId, $pageId, $item['dsm'], $itemId, $ko3value, $ko3dpt, false) . ',kovalues:{update1:"' . (($ko1updated) ? 1 : 0) . '",update2:"' . (($ko2updated) ? 1 : 0) . '",update3:"' . (($ko3updated) ? 1 : 0) . '",kovalue1:"' . ajaxValue($ko1value, false) . '",kovalue2:"' . ajaxValue($ko2value, false) . '",kovalue3:"' . ajaxValue($ko3value, false) . '"}},';
                            } else {
                                $newItems .= '{id:"' . $itemId . '",design:' . $this->getElementDesignData($clientId, $pageId, $item['dsm'], $itemId, $ko1value, $ko1dpt, false) . ',kovalues:{update1:"' . (($ko1updated) ? 1 : 0) . '",update2:"' . (($ko2updated) ? 1 : 0) . '",update3:"' . (($ko3updated) ? 1 : 0) . '",kovalue1:"' . ajaxValue($ko1value, false) . '",kovalue2:"' . ajaxValue($ko2value, false) . '",kovalue3:"' . ajaxValue($ko3value, false) . '"}},';
                            }
                            $this->procData[3]++;
                        }
                    }
                }
                if (!isEmpty($newItems)) {
                    $this->sendToClient($clientId, 'CMD', 'that.response_refreshItems([' . rtrim($newItems, ',') . ']);');
                    $this->procData[2]++;
                }
                if (!isEmpty($newValues)) {
                    $this->sendToClient($clientId, 'CMD', 'that.response_refreshValues([' . rtrim($newValues, ',') . ']);');
                    $this->procData[2]++;
                }
            }
        }
    }

    private function checkEvents()
    {
        $warn = sql_getValue('edomiLive.RAMko', "1,GROUP_CONCAT(value ORDER BY id ASC SEPARATOR ',')", '(id>=7 AND id<=10) GROUP BY 1');
        $this->procData[8] = sql_getCount('edomiLive.visuQueue', '1=1');
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE online=0");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiLive.visuQueue WHERE targetid=" . $n['id']);
        }
        sql_close($ss1);
        for ($clientId = 0; $clientId < count($this->clients); $clientId++) {
            if ($this->clients[$clientId]['started'] && $this->clients[$clientId]['userdata'] !== false) {
                if (!isEmpty($warn) && $warn != $this->clients[$clientId]['warnings']) {
                    $this->clients[$clientId]['warnings'] = $warn;
                    $this->sendToClient($clientId, 'CMD', 'showWarnings([' . $warn . ']);');
                }
                $ss1 = sql_call("SELECT a.* FROM edomiLive.visuQueue AS a,edomiLive.visuUserList AS b WHERE (a.targetid=" . $this->clients[$clientId]['userdata']['id'] . " AND a.targetid=b.id AND b.online=1) ORDER BY a.id ASC");
                while ($n = sql_result($ss1)) {
                    sql_call("DELETE FROM edomiLive.visuQueue WHERE (id='" . $n['id'] . "')");
                    if ($n['cmd'] == 1) {
                        $this->sendToClient($clientId, 'CMD', 'serverNotReady("' . $this->clients[$clientId]['vid'] . '","");');
                        break;
                    } else if ($n['cmd'] == 2) {
                        $this->sendToClient($clientId, 'CMD', 'openPage("' . $n['cmdid'] . '");initScreensaverTimer();');
                    } else if ($n['cmd'] == 3) {
                        $this->sendToClient($clientId, 'CMD', 'closePopupById("' . $n['cmdid'] . '");initScreensaverTimer();');
                    } else if ($n['cmd'] == 4) {
                        foreach ($this->clients[$clientId]['page'] as $pageId => $page) {
                            $pagetyp = sql_getValue('edomiLive.visuPage', 'pagetyp', 'id=' . $pageId);
                            if ($pagetyp == 1) {
                                unset($this->clients[$clientId]['page'][$pageId]);
                            }
                        }
                        $this->sendToClient($clientId, 'CMD', 'closeAllPopups();initScreensaverTimer();');
                    } else if ($n['cmd'] == 10) {
                        if ($n['cmdid'] > 0 && file_exists(MAIN_PATH . '/www/data/liveproject/visu/etc/snd-' . $n['cmdid'] . '.mp3')) {
                            $this->sendToClient($clientId, 'CMD', 'visuSoundPlay("../data/liveproject/visu/etc/snd-' . $n['cmdid'] . '.mp3",false);');
                        } else if ($n['cmdid'] == 0) {
                            $this->sendToClient($clientId, 'CMD', 'visuSoundStop();');
                        }
                    } else if ($n['cmd'] == 11) {
                        if (!isEmpty($n['cmdvalue'])) {
                            $para = array('', '', '');
                            $msg = $n['cmdvalue'];
                            $tmp = explode('***', $msg);
                            if (count($tmp) > 1) {
                                $para = explode('/', $tmp[0]);
                                $msg = $tmp[1];
                            }
                            $this->sendToClient($clientId, 'CMD', 'visuTextToSpeechPlay("","' . ajaxValue($para[0], false) . '","' . ajaxValue($para[1], false) . '","' . ajaxValue($para[2], false) . '","' . ajaxValue($msg, false) . '");');
                        }
                    }
                }
                sql_close($ss1);
            }
        }
    }

    private function checkClients()
    {
        foreach ($this->clients as $clientId => &$client) {
            if ($client['loggedin'] && $client['userdata'] !== false) {
                if (is_null($client['ping'])) {
                    $client['ping'] = getMicrotime();
                    $this->sendToClient($clientId, 'PING');
                } else if ((getMicrotime() - $client['ping']) >= $this->websocketPing) {
                    $this->disconnectClient($clientId);
                }
            }
        }
        $this->procData[1] = count($this->clients);
    }

    private function connectClient($socket)
    {
        $this->clients[] = array('socket' => $socket, 'vid' => 0, 'sid' => '', 'ping' => null, 'userdata' => false, 'warnings' => '0,0,0,0,0', 'connected' => false, 'loggedin' => false, 'started' => false);
    }

    private function handshake($clientId, $headers)
    {
        if (preg_match("/Sec-WebSocket-Version: (.*)\r\n/i", $headers, $match)) {
            $version = $match[1];
            if ($version == 13) {
                $sid = '';
                if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/i", $headers, $match)) {
                    $key = $match[1];
                }
                $acceptKey = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
                socket_write($this->clients[$clientId]['socket'], "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: " . $acceptKey . "\r\n\r\n");
                return true;
            }
        }
        return false;
    }

    private function getClientIdFromSocket($socket)
    {
        for ($t = 0; $t < count($this->clients); $t++) {
            if ($this->clients[$t]['socket'] == $socket) {
                return $t;
            }
        }
        return false;
    }

    private function sendToClient($clientId, $mode, $data = null)
    {
        if ($mode == 'START') {
            $tmp = $this->encode(1, json_encode(array('START')));
        } else if ($mode == 'LOGIN') {
            $tmp = $this->encode(1, json_encode(array('LOGIN')));
        } else if ($mode == 'CMD') {
            $tmp = $this->encode(1, json_encode(array('CMD', $data)));
        } else if ($mode == 'PING') {
            $tmp = $this->encode(1, json_encode(array('PING')));
        } else if ($mode == 'CLOSE') {
            $tmp = $this->encode(8, '');
        }
        $this->procData[6] += strlen($tmp);
        if (@socket_write($this->clients[$clientId]['socket'], $tmp, strlen($tmp)) !== false) {
            $this->trace('Send to Client-ID ' . $clientId . ': ' . $mode . '/' . $data);
        } else {
            $this->disconnectClient($clientId);
        }
    }

    private function encode($mode, $data)
    {
        $l = strlen($data);
        $bytesHeader = array();
        $bytesHeader[0] = 128 | $mode;
        if ($l <= 125) {
            $bytesHeader[1] = $l;
        } else if ($l >= 126 && $l <= 65535) {
            $bytesHeader[1] = 126;
            $bytesHeader[2] = ($l >> 8) & 255;
            $bytesHeader[3] = ($l) & 255;
        } else {
            $bytesHeader[1] = 127;
            $bytesHeader[2] = ($l >> 56) & 255;
            $bytesHeader[3] = ($l >> 48) & 255;
            $bytesHeader[4] = ($l >> 40) & 255;
            $bytesHeader[5] = ($l >> 32) & 255;
            $bytesHeader[6] = ($l >> 24) & 255;
            $bytesHeader[7] = ($l >> 16) & 255;
            $bytesHeader[8] = ($l >> 8) & 255;
            $bytesHeader[9] = ($l) & 255;
        }
        $r = implode(array_map("chr", $bytesHeader)) . $data;
        return $r;
    }

    private function decode($data)
    {
        $this->procData[7] += strlen($data);
        $arr = array();
        while (strlen($data) > 0) {
            $payloadLength = '';
            $mask = '';
            $unmaskedPayload = '';
            $decodedData = '';
            $firstByteBinary = sprintf('%08b', ord($data[0]));
            $secondByteBinary = sprintf('%08b', ord($data[1]));
            $opcode = bindec(substr($firstByteBinary, 4, 4));
            $isMasked = ($secondByteBinary[0] == '1') ? true : false;
            $payloadLength = ord($data[1]) & 127;
            if ($isMasked === false) {
                return false;
            }
            if ($payloadLength === 126) {
                $mask = substr($data, 4, 4);
                $payloadOffset = 8;
                $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
            } else if ($payloadLength === 127) {
                $mask = substr($data, 10, 4);
                $payloadOffset = 14;
                $tmp = '';
                for ($i = 0; $i < 8; $i++) {
                    $tmp .= sprintf('%08b', ord($data[$i + 2]));
                }
                $dataLength = bindec($tmp) + $payloadOffset;
                unset($tmp);
            } else {
                $mask = substr($data, 2, 4);
                $payloadOffset = 6;
                $dataLength = $payloadLength + $payloadOffset;
            }
            if ($isMasked === true) {
                for ($i = $payloadOffset; $i < $dataLength; $i++) {
                    $j = $i - $payloadOffset;
                    if (isset($data[$i])) {
                        $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
                    }
                }
                $decodedData = $unmaskedPayload;
            } else {
                $payloadOffset = $payloadOffset - 4;
                $decodedData = substr($data, $payloadOffset);
            }
            if ($opcode == 1) {
                $arr[] = json_decode($decodedData);
            } else if ($opcode == 8) {
                $arr[] = array('CLOSE');
            } else if ($opcode != 2 && $opcode != 9 && $opcode != 10) {
                return false;
            }
            $data = substr($data, $dataLength);
        }
        return $arr;
    }

    private function disconnectClient($clientId)
    {
        if ($this->clients[$clientId]['userdata'] !== false) {
            $this->visuLog($clientId, 'LOGOUT: Client-Id=' . $clientId . ' / SID=' . $this->clients[$clientId]['sid'] . ' / IP=' . $this->clients[$clientId]['userdata']['loginip']);
            $ss1 = sql_call("SELECT a.id,b.gaid FROM edomiLive.visuUserList AS a,edomiLive.visuUser AS b WHERE a.id=" . $this->clients[$clientId]['userdata']['id'] . " AND a.targetid=b.id AND ((b.autologout=1 AND (a.sid IS NOT NULL)) OR a.logout=1)");
            if ($n = sql_result($ss1)) {
                $this->trace('Logout Client with visuUserList.id ' . $n['id']);
                if ($n['gaid'] > 0) {
                    writeGA($n['gaid'], 0);
                }
                sql_call("UPDATE edomiLive.visuUserList SET online=0,logout=0,sid=NULL,logoutdate=" . sql_getNow() . " WHERE (id='" . $n['id'] . "')");
            }
            sql_close($ss1);
            sql_call("UPDATE edomiLive.visuUserList SET online=0,logout=0 WHERE id=" . $this->clients[$clientId]['userdata']['id']);
        }
        $this->trace('Disconnect Client-ID ' . $clientId);
        $socket = $this->clients[$clientId]['socket'];
        @socket_shutdown($socket, 2);
        socket_close($socket);
        array_splice($this->clients, $clientId, 1);
        $this->procData[1] = count($this->clients);
    }

    public function logoutClients()
    {
        $ss1 = sql_call("SELECT a.id,b.gaid FROM edomiLive.visuUserList AS a,edomiLive.visuUser AS b WHERE a.targetid=b.id AND ((b.autologout=1 AND (a.sid IS NOT NULL)) OR a.logout=1)");
        while ($n = sql_result($ss1)) {
            $this->trace('Logout Client with visuUserList.id ' . $n['id']);
            if ($n['gaid'] > 0) {
                writeGA($n['gaid'], 0);
            }
            sql_call("UPDATE edomiLive.visuUserList SET online=0,logout=0,sid=NULL,logoutdate=" . sql_getNow() . " WHERE (id='" . $n['id'] . "')");
        }
        sql_close($ss1);
        $this->visuLog(null, 'LOGOUT: alle Accounts');
    }

    private function setStatusKo1($clientId)
    {
        $tmp = sql_getValue('edomiLive.visuUser', 'gaid', 'id=' . $this->clients[$clientId]['userdata']['targetid']);
        if ($tmp >= 1) {
            writeGA($tmp, $this->clients[$clientId]['vid']);
        }
    }

    private function setStatusKo2($clientId, $pageId)
    {
        $tmp = sql_getValue('edomiLive.visuUser', 'gaid2', 'id=' . $this->clients[$clientId]['userdata']['targetid']);
        if ($tmp >= 1) {
            writeGA($tmp, $this->clients[$clientId]['vid'] . ';' . $pageId . ';' . $this->clients[$clientId]['userdata']['targetid'] . ';' . $this->clients[$clientId]['userdata']['loginip']);
        }
    }

    private function setStatusKo3($clientId)
    {
        $tmp = sql_getValue('edomiLive.visuUser', 'gaid3', 'id=' . $this->clients[$clientId]['userdata']['targetid']);
        if ($tmp >= 1) {
            writeGA($tmp, $this->clients[$clientId]['vid']);
        }
    }

    private function closePopup($clientId, $pageId)
    {
        $page = sql_getValues('edomiLive.visuPage', 'id', 'id=' . $pageId . ' AND pagetyp=1');
        if ($page !== false) {
            unset($this->clients[$clientId]['page'][$page['id']]);
            return true;
        }
        return false;
    }

    private function initPage($clientId, $pageId)
    {
        $visuId = $this->clients[$clientId]['vid'];
        $page = sql_getValues('edomiLive.visuPage', '*', 'id=' . $pageId);
        if ($page !== false) {
            if ($page['pagetyp'] == 1) {
                unset($this->clients[$clientId]['page'][$page['id']]);
            } else {
                unset($this->clients[$clientId]['page']);
            }
            $this->setStatusKo2($clientId, $pageId);
            $pageIncludeIDs = array($page['id']);
            if ($page['pagetyp'] == 0) {
                $tmp = $page['includeid'];
                while (!isEmpty($tmp) && is_numeric($tmp) && $tmp > 0 && !in_array($tmp, $pageIncludeIDs)) {
                    array_unshift($pageIncludeIDs, $tmp);
                    $ss2 = sql_call("SELECT includeid FROM edomiLive.visuPage WHERE id=" . $tmp);
                    $tmp = 0;
                    if ($n = sql_result($ss2)) {
                        if (!isEmpty($n['includeid']) && is_numeric($n['includeid']) && $n['includeid'] > 0) {
                            $tmp = $n['includeid'];
                        } else {
                            $tmp = 0;
                        }
                    }
                    sql_close($ss2);
                }
                if ($page['globalinclude'] == 1) {
                    $ss2 = sql_call("SELECT id FROM edomiLive.visuPage WHERE (visuid=" . $visuId . " AND pagetyp=2 AND id<>" . $page['id'] . ") ORDER BY id DESC");
                    while ($n = sql_result($ss2)) {
                        array_unshift($pageIncludeIDs, $n['id']);
                    }
                    sql_close($ss2);
                }
            }
            $currentPageIDs = implode(',', $pageIncludeIDs);
            $tmp1 = '';
            $tmp2 = '';
            $ss2 = sql_call("SELECT bgcolorid,bgimg FROM edomiLive.visuPage WHERE (id IN (" . $currentPageIDs . ")) ORDER BY FIELD(id," . $currentPageIDs . ") DESC");
            while ($nn = sql_result($ss2)) {
                if (isEmpty($tmp1) && $nn['bgimg'] > 0 && ($tmp = sql_getValues('edomiLive.visuImg', '*', 'id=' . $nn['bgimg']))) {
                    $tmp1 = "url('../data/liveproject/visu/img/img-" . $tmp['id'] . "." . $tmp['suffix'] . "?" . $tmp['ts'] . "')";
                }
                if (isEmpty($tmp2) && $nn['bgcolorid'] > 0 && ($tmp = sql_getValues('edomiLive.visuBGcol', '*', 'id=' . $nn['bgcolorid']))) {
                    $tmp2 = $tmp['color'];
                }
                if (!isEmpty($tmp1) && !isEmpty($tmp2)) {
                    break;
                }
            }
            sql_close($ss2);
            $background = trim(($tmp1 . ',' . $tmp2), ',');
            $items = $this->getElements($clientId, $page, $currentPageIDs);
            $this->sendToClient($clientId, 'CMD', 'that.response_initPage({id:"' . $page['id'] . '",pagetyp:"' . $page['pagetyp'] . '",xpos:"' . $page['xpos'] . '",ypos:"' . $page['ypos'] . '",xsize:"' . $page['xsize'] . '",ysize:"' . $page['ysize'] . '",autoclose:"' . $page['autoclose'] . '",bgmodal:"' . $page['bgmodal'] . '",bganim:"' . $page['bganim'] . '",bgdark:"' . $page['bgdark'] . '",bgshadow:"' . $page['bgshadow'] . '"},[' . $currentPageIDs . '],"' . ajaxEncode($background, false) . '",[' . rtrim($items, ',') . ']);');
            return true;
        }
        return false;
    }

    private function getElements($clientId, $page, $currentPageIDs)
    {
        $r = '';
        $ss1 = sql_call("SELECT * FROM edomiLive.visuElement WHERE (pageid IN (" . $currentPageIDs . ")) ORDER BY FIELD(pageid," . $currentPageIDs . ") ASC,id ASC");
        while ($item = sql_result($ss1)) {
            $koValue1 = null;
            $koValue2 = null;
            $koValue3 = null;
            $koDptDesign = null;
            $koValueDesign = null;
            $this->clients[$clientId]['page'][$page['id']][$item['id']]['dsm'] = intval($item['dynstylemode']);
            if ($item['gaid3'] > 0) {
                $ko = sql_getValues('edomiLive.RAMko', 'visuts,value,valuetyp', 'id=' . $item['gaid3']);
                if ($ko !== false) {
                    $koValue3 = $ko['value'];
                    $koDptDesign = $ko['valuetyp'];
                    $koValueDesign = $ko['value'];
                    $this->clients[$clientId]['page'][$page['id']][$item['id']]['ko3'] = array(intval($item['gaid3']), intval($ko['visuts']));
                }
            }
            if ($item['gaid2'] > 0) {
                $ko = sql_getValues('edomiLive.RAMko', 'visuts,value,valuetyp', 'id=' . $item['gaid2']);
                if ($ko !== false) {
                    $koValue2 = $ko['value'];
                    $this->clients[$clientId]['page'][$page['id']][$item['id']]['ko2'] = array(intval($item['gaid2']), intval($ko['visuts']));
                }
            }
            if ($item['gaid'] > 0) {
                $ko = sql_getValues('edomiLive.RAMko', 'visuts,value,valuetyp', 'id=' . $item['gaid']);
                if ($ko !== false) {
                    if (!isset($this->clients[$clientId]['page'][$page['id']][$item['id']]['ko3'])) {
                        $koDptDesign = $ko['valuetyp'];
                        $koValueDesign = $ko['value'];
                    }
                    $koValue1 = $ko['value'];
                    $this->clients[$clientId]['page'][$page['id']][$item['id']]['ko1'] = array(intval($item['gaid']), intval($ko['visuts']));
                }
            }
            $design = $this->getElementDesignData($clientId, $page['id'], $item['dynstylemode'], $item['id'], $koValueDesign, $koDptDesign, true);
            $kovalues = '{update1:1,update2:1,update3:1,kovalue1:"' . ajaxValue($koValue1, false) . '",kovalue2:"' . ajaxValue($koValue2, false) . '",kovalue3:"' . ajaxValue($koValue3, false) . '"}';
            $groupTag = '';
            if ($item['controltyp'] == 0) {
                $groupTag = ',grouptag:"' . ajaxValue($item['text'], false) . '"';
            }
            $r .= '{id:"' . $item['id'] . '",controltyp:"' . $item['controltyp'] . '",groupid:"' . (($item['groupid'] >= 1) ? $item['groupid'] : '0') . '",linkid:"' . (($item['linkid'] >= 1) ? $item['linkid'] : '0') . '",initonly:"' . $item['initonly'] . '",xpos:"' . $item['xpos'] . '",ypos:"' . $item['ypos'] . '",var1:"' . ajaxValue($item['var1'], false) . '",var2:"' . ajaxValue($item['var2'], false) . '",var3:"' . ajaxValue($item['var3'], false) . '",var4:"' . ajaxValue($item['var4'], false) . '",var5:"' . ajaxValue($item['var5'], false) . '",var6:"' . ajaxValue($item['var6'], false) . '",var7:"' . ajaxValue($item['var7'], false) . '",var8:"' . ajaxValue($item['var8'], false) . '",var9:"' . ajaxValue($item['var9'], false) . '",var10:"' . ajaxValue($item['var10'], false) . '",var11:"' . ajaxValue($item['var11'], false) . '",var12:"' . ajaxValue($item['var12'], false) . '",var13:"' . ajaxValue($item['var13'], false) . '",var14:"' . ajaxValue($item['var14'], false) . '",var15:"' . ajaxValue($item['var15'], false) . '",var16:"' . ajaxValue($item['var16'], false) . '",var17:"' . ajaxValue($item['var17'], false) . '",var18:"' . ajaxValue($item['var18'], false) . '",var19:"' . ajaxValue($item['var19'], false) . '",var20:"' . ajaxValue($item['var20'], false) . '",gotopageid:"' . (($item['gotopageid'] >= 1) ? $item['gotopageid'] : '0') . '",closepopupid:"' . (($item['closepopupid'] >= 1) ? $item['closepopupid'] : '0') . '",closepopup:"' . $item['closepopup'] . '",hascmd:"' . $item['hascmd'] . '",livepreview:"' . $item['galive'] . '",koid1:"' . (($item['gaid'] > 0) ? $item['gaid'] : 0) . '",koid2:"' . (($item['gaid2'] > 0) ? $item['gaid2'] : 0) . '",koid3:"' . (($item['gaid3'] > 0) ? $item['gaid3'] : 0) . '",design:' . $design . ',kovalues:' . $kovalues . $groupTag . '},';
        }
        sql_close($ss1);
        return $r;
    }

    private function getElementDesignData($clientId, $pageId, $mode, $elementId, $koValue, $koDpt, $pageInit)
    {
        $style = visu_getElementDesignData(true, $mode, $elementId, $koValue, $koDpt);
        if ($style !== false) {
            return '{rcss:' . (($pageInit || $style['styletyp'] == 1 || $style['initonly'] == 0) ? 1 : 0) . ',css:"' . ajaxEncode($style['css']) . '",text:"' . ajaxValueHTML($style['text']) . '"}';
        }
        return null;
    }
}
?>
