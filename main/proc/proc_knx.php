<?
/*
*/
?><? require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
restore_error_handler();
error_reporting(0);
$main = new procKnx();
$main->exitSelf();

class procKnx
{
    private $procControl;
    private $procTrigger;
    private $procData;
    private $knxRate_timer;
    private $knxRate_refTx;
    private $knxRate_refRx;
    private $statistic_timer = null;
    private $connectionstate;
    private $reconnect;
    private $cE_socket;
    private $cE_serverIp = global_knxIP;
    private $cE_serverPort = global_cEserverPort;
    private $cE_serverIpPort;
    private $cE_routerIp = global_knxRouterIp;
    private $cE_routerPort = global_knxRouterPort;
    private $cE_description_response_timeout;
    private $cE_connect_response_timeout;
    private $cE_connectionstate_request_timer;
    private $cE_connectionstate_response_timeout;
    private $cE_connectionstate_requested;
    private $cE_channelId;
    private $cE_tunnelPA;
    private $dE_socket;
    private $dE_serverIp = global_knxIP;
    private $dE_serverPort = global_dEserverPort;
    private $dE_serverIpPort;
    private $dE_routerIp = global_knxRouterIp;
    private $dE_routerPort = global_knxRouterPort;
    private $dE_tunneling_ack;
    private $dE_tunneling_ack_timeout;
    private $dE_tunneling_request_timer;
    private $dE_tunneling_request_seqCounter_send;
    private $dE_tunneling_request_seqCounter_receive;
    private $dE_tunneling_request_data;
    private $dE_tunneling_request_repeat;

    public function __construct()
    {
        writeToLog(3, true, 'Prozess KNX gestartet');
        sql_connect();
        if (writeToLog(3, $this->cE_serverIpPort = $this->ipPortToRaw($this->cE_serverIp, $this->cE_serverPort), 'KNX-Verbindung: Control-Endpoint: ' . $this->cE_serverIp . ':' . $this->cE_serverPort, 'FATALERROR') === false) {
            exitSelf();
        }
        if (writeToLog(3, $this->dE_serverIpPort = $this->ipPortToRaw($this->dE_serverIp, $this->dE_serverPort), 'KNX-Verbindung: Data-Endpoint: ' . $this->dE_serverIp . ':' . $this->dE_serverPort, 'FATALERROR') === false) {
            exitSelf();
        }
        $this->procControl = null;
        $this->procTrigger = procStatus_getTrigger();
        $this->procData = procStatus_getData(3);
        for ($t = 0; $t < 20; $t++) {
            $this->procData[$t] = 0;
        }
        $this->proc_run();
    }

    private function proc_check()
    {
        $this->procTrigger = procStatus_getTrigger($this->procTrigger[1]);
        if ($this->procTrigger[0]) {
            $this->procControl = procStatus_getControl(3);
            if ($this->procControl == 1) {
                writeToLog(3, true, 'Prozess KNX beenden...');
            } else {
                return true;
            }
        }
        return false;
    }

    private function proc_run()
    {
        do {
            $this->proc_check();
            $this->procData[19] = 1;
            $this->statistic_queueSize();
            $this->cE_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            $this->dE_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($this->cE_socket !== false && $this->dE_socket !== false) {
                if (socket_bind($this->cE_socket, 0, $this->cE_serverPort) && socket_bind($this->dE_socket, 0, $this->dE_serverPort)) {
                    $this->proc_do();
                } else {
                    if (!(global_logTraceLevelKnx == 1 || global_logTraceLevelKnx == 3)) {
                        writeToLog(3, false, 'socket_bind nicht möglich.');
                    }
                    $this->trace(true, 'socket_bind nicht möglich.');
                }
            } else {
                if (!(global_logTraceLevelKnx == 1 || global_logTraceLevelKnx == 3)) {
                    writeToLog(3, false, 'socket_create nicht möglich.');
                }
                $this->trace(true, 'socket_create nicht möglich.');
            }
            socket_close($this->cE_socket);
            socket_close($this->dE_socket);
            if ($this->procControl != 1) {
                if (!(global_logTraceLevelKnx == 1 || global_logTraceLevelKnx == 3)) {
                    writeToLog(3, false, 'KNX-Verbindung verloren.');
                }
                $this->trace(true, 'KNX-Verbindung verloren.');
                $this->procData[7]++;
                sleep(3);
            }
            if ($this->procTrigger[0]) {
                procStatus_setData(3, $this->procData);
            }
        } while ($this->procControl != 1);
    }

    private function proc_do()
    {
        $mainTimerOldProc = 0;
        $this->procData[13] = 0;
        $this->procData[15] = 0;
        $this->knxRate_timer = null;
        $this->knxRate_refTx = 0;
        $this->knxRate_refRx = 0;
        $this->connectionstate = 0;
        $this->reconnect = false;
        $this->cE_description_response_timeout = null;
        $this->cE_connect_response_timeout = null;
        $this->cE_connectionstate_request_timer = null;
        $this->cE_connectionstate_response_timeout = null;
        $this->cE_connectionstate_requested = false;
        $this->cE_channelId = 0;
        $this->cE_tunnelPA = '0.0.0';
        $this->dE_tunneling_ack = true;
        $this->dE_tunneling_ack_timeout = null;
        $this->dE_tunneling_request_timer = null;
        $this->dE_tunneling_request_seqCounter_send = 0;
        $this->dE_tunneling_request_seqCounter_receive = 0;
        $this->dE_tunneling_request_data = false;
        $this->dE_tunneling_request_repeat = false;
        while ($this->procControl != 1 && (!$this->reconnect)) {
            $this->proc_check();
            $this->statistic_telegramRate();
            if ($this->connectionstate == 0) {
                $this->CONTROL_DESCRIPTION_REQUEST();
            } else if ($this->connectionstate == 3) {
                $this->CONTROL_CONNECT_REQUEST();
            } else if ($this->connectionstate == 15) {
                if ((!$this->cE_connectionstate_requested) && (is_null($this->cE_connectionstate_request_timer) || (getMicrotime() >= ($this->cE_connectionstate_request_timer + global_knxHeartbeat)))) {
                    $this->CONTROL_CONNECTIONSTATE_REQUEST();
                    $this->cE_connectionstate_request_timer = getMicrotime();
                }
            }
            if ($this->connectionstate == 15) {
                if ($this->dE_tunneling_request_repeat) {
                    $this->repeat_TUNNELING_REQUEST();
                    $this->dE_tunneling_request_timer = getMicrotime();
                } else if ($this->dE_tunneling_ack && (is_null($this->dE_tunneling_request_timer) || getMicrotime() >= ($this->dE_tunneling_request_timer + (1 / global_knxMaxSendRate)))) {
                    $this->DATA_TUNNELING_REQUEST_SEND();
                    $this->dE_tunneling_request_timer = getMicrotime();
                }
            }
            if ($this->connectionstate == 15) {
                $len = socket_recv($this->dE_socket, $response, 1024 * 100, MSG_DONTWAIT);
                if ($len !== false) {
                    if ($this->word($response, 3) == 0x0421) {
                        $this->DATA_TUNNELING_ACK($response);
                    } else if ($this->word($response, 3) == 0x0420) {
                        $this->DATA_TUNNELING_REQUEST_RECEIVE($response);
                    } else {
                        $this->DATA_UNKNOWN($response);
                    }
                }
            }
            if ($this->connectionstate != 0) {
                $len = socket_recv($this->cE_socket, $response, 1024 * 100, MSG_DONTWAIT);
                if ($len !== false) {
                    if ($this->word($response, 3) == 0x0208) {
                        $this->CONTROL_CONNECTIONSTATE_RESPONSE($response);
                    } else if ($this->word($response, 3) == 0x0202) {
                        $this->CONTROL_SEARCH_RESPONSE($response);
                    } else if ($this->word($response, 3) == 0x0204) {
                        $this->CONTROL_DESCRIPTION_RESPONSE($response);
                    } else if ($this->word($response, 3) == 0x0206) {
                        $this->CONTROL_CONNECT_RESPONSE($response);
                    } else if ($this->word($response, 3) == 0x0209) {
                        $this->CONTROL_DISCONNECT_REQUEST_fromRouter($response);
                    } else if ($this->word($response, 3) == 0x020A) {
                        $this->CONTROL_DISCONNECT_RESPONSE($response);
                    } else {
                        $this->CONTROL_UNKNOWN_RESPONSE($response);
                    }
                }
            }
            if ($this->connectionstate == 1) {
                if (getMicrotime() >= ($this->cE_description_response_timeout + global_knxOpenTimeout)) {
                    $this->trace(true, 'CE > | DESCRIPTION_REQUEST / Timeout nach ' . global_knxOpenTimeout . 's / ErrMsg: Kein DESCRIPTION_RESPONSE vom Router erhalten');
                    $this->reconnect = true;
                    $this->procData[7]++;
                }
            }
            if ($this->connectionstate == 7) {
                if (getMicrotime() >= ($this->cE_connect_response_timeout + global_knxOpenTimeout)) {
                    $this->trace(true, 'CE > | CONNECT_REQUEST / Timeout nach ' . global_knxOpenTimeout . 's / ErrMsg: Kein CONNECT_RESPONSE vom Router erhalten');
                    $this->reconnect = true;
                    $this->procData[7]++;
                }
            }
            if ($this->connectionstate == 15 && $this->cE_connectionstate_requested) {
                if (getMicrotime() >= ($this->cE_connectionstate_response_timeout + global_knxHeartbeatTimeout)) {
                    $this->trace(true, 'CE > | CONNECTIONSTATE_REQUEST / Timeout nach ' . global_knxHeartbeatTimeout . 's / ErrMsg: Kein CONNECTIONSTATE_RESPONSE vom Router erhalten');
                    $this->reconnect = true;
                    $this->procData[7]++;
                }
            }
            if ($this->connectionstate == 15 && (!$this->dE_tunneling_ack)) {
                if (getMicrotime() >= ($this->dE_tunneling_ack_timeout + global_knxWriteTimeout)) {
                    if (!$this->dE_tunneling_request_repeat) {
                        $this->trace(true, 'DE > | TUNNELING_ACK / Timeout nach ' . global_knxWriteTimeout . 's / ErrMsg: Kein TUNNELING_ACK vom Router erhalten / Wiederholen');
                        $this->dE_tunneling_request_repeat = true;
                    } else {
                        $this->trace(true, 'DE > | TUNNELING_ACK / Timeout nach ' . global_knxWriteTimeout . 's / ErrMsg: Kein TUNNELING_ACK vom Router erhalten / Verwerfen');
                        $this->dE_tunneling_request_repeat = false;
                        $this->dE_tunneling_ack = true;
                    }
                    $this->procData[6]++;
                }
            }
            procStatus_getProcValues($this->procData, $mainTimerOldProc);
            if (is_null($this->statistic_timer) || (getMicrotime() >= ($this->statistic_timer + 1))) {
                procStatus_setData(3, $this->procData);
                $this->statistic_timer = getMicrotime();
            }
            usleep(global_knxWait * 1000);
        }
        $this->procData[9] = 0;
        $this->CONTROL_DISCONNECT_REQUEST();
    }

    private function CONTROL_DESCRIPTION_REQUEST()
    {
        $this->send_DESCRIPTION_REQUEST();
        $this->cE_description_response_timeout = getMicrotime();
        $this->connectionstate |= 1;
    }

    private function CONTROL_CONNECT_REQUEST()
    {
        $this->send_CONNECT_REQUEST();
        $this->cE_connect_response_timeout = getMicrotime();
        $this->connectionstate |= 4;
        $this->procData[8]++;
        $this->procData[13] = 0;
        $this->procData[15] = 0;
    }

    private function CONTROL_CONNECTIONSTATE_REQUEST()
    {
        $this->send_CONNECTIONSTATE_REQUEST();
        $this->cE_connectionstate_response_timeout = getMicrotime();
        $this->cE_connectionstate_requested = true;
    }

    private function CONTROL_DISCONNECT_REQUEST()
    {
        $this->send_DISCONNECT_REQUEST();
    }

    private function CONTROL_CONNECTIONSTATE_RESPONSE($response)
    {
        $err = $this->byte($response, 8);
        if ($this->byte($response, 7) == $this->cE_channelId && $err == 0x00) {
            $this->cE_connectionstate_requested = false;
            $this->trace(false, 'CE < | CONNECTIONSTATE_RESPONSE / Raw: ' . $this->bytesToHex($response));
        } else {
            $errMsg = '(unbekannt)';
            if ($err == 0x21) {
                $errMsg = 'E_CONNECTION_ID (The KNXnet/IP Server device cannot find an active data connection with the specified ID)';
            }
            if ($err == 0x26) {
                $errMsg = 'E_DATA_CONNECTION (The KNXnet/IP Server device detects an error concerning the data connection with the specified ID)';
            }
            if ($err == 0x27) {
                $errMsg = 'E_KNX_CONNECTION (The KNXnet/IP Server device detects an error concerning the KNX subnetwork connection with the specified ID)';
            }
            $this->trace(true, 'CE < | CONNECTIONSTATE_RESPONSE / ErrCode: ' . dechex($err) . 'h / ErrMsg: ' . $errMsg . ' / Raw: ' . $this->bytesToHex($response));
            $this->reconnect = true;
            $this->procData[7]++;
        }
    }

    private function CONTROL_SEARCH_RESPONSE($response)
    {
        $this->trace(true, 'CE < | SEARCH_RESPONSE / Raw: ' . $this->bytesToHex($response));
    }

    private function CONTROL_DESCRIPTION_RESPONSE($response)
    {
        writeToLog(3, true, 'KNX-Schnittstelle: Name: ' . trim(str_replace(chr(0), ' ', $this->chars($response, 31, 30))) . ' / PA: ' . $this->rawToPA($this->chars($response, 11, 2)) . ' / MAC: ' . $this->bytesToHex($this->chars($response, 25, 6)));
        if ($this->byte($response, 10) == 0x00) {
            $this->trace(false, 'CE < | DESCRIPTION_RESPONSE / Status: Ok / Name: ' . trim(str_replace(chr(0), ' ', $this->chars($response, 31, 30))) . ' / PA: ' . $this->rawToPA($this->chars($response, 11, 2)) . ' / MAC: ' . $this->bytesToHex($this->chars($response, 25, 6)) . ' / Raw: ' . $this->bytesToHex($response));
        } else {
            $this->trace(true, 'CE < | DESCRIPTION_RESPONSE / Status: Gerät ist im Programmiermodus! / Name: ' . trim(str_replace(chr(0), ' ', $this->chars($response, 31, 30))) . ' / PA: ' . $this->rawToPA($this->chars($response, 11, 2)) . ' / MAC: ' . $this->bytesToHex($this->chars($response, 25, 6)) . ' / Raw: ' . $this->bytesToHex($response));
        }
        $this->connectionstate |= 2;
    }

    private function CONTROL_CONNECT_RESPONSE($response)
    {
        $this->cE_channelId = $this->byte($response, 7);
        $err = $this->byte($response, 8);
        if ($this->cE_channelId >= 0 && $err == 0) {
            $this->cE_tunnelPA = $this->rawToPA($this->chars($response, 19, 2));
            $this->dE_routerIp = $this->byte($response, 11) . '.' . $this->byte($response, 12) . '.' . $this->byte($response, 13) . '.' . $this->byte($response, 14);
            $this->dE_routerPort = ($this->byte($response, 15) * 256) + $this->byte($response, 16);
            writeToLog(3, true, 'KNX-Verbindung: Tunnel-PA: ' . $this->cE_tunnelPA . ' / Channel-ID: ' . $this->cE_channelId);
            $this->trace(false, 'CE < | CONNECT_RESPONSE / CE: ' . $this->cE_serverIp . ':' . $this->cE_serverPort . ' - ' . $this->cE_routerIp . ':' . $this->cE_routerPort . ' / DE: ' . $this->dE_serverIp . ':' . $this->dE_serverPort . ' - ' . $this->dE_routerIp . ':' . $this->dE_routerPort . ' / Tunnel-PA: ' . $this->cE_tunnelPA . ' / ChannelID: ' . $this->cE_channelId . ' / Raw: ' . $this->bytesToHex($response));
            $this->connectionstate |= 8;
            $this->procData[19] = 2;
            $this->procData[9] = 1;
        } else {
            $errMsg = '(unbekannt)';
            if ($err == 0x22) {
                $errMsg = 'E_CONNECTION_TYPE (Verbindungstyp wird nicht unterstützt)';
            }
            if ($err == 0x23) {
                $errMsg = 'E_CONNECTION_OPTION (Verbindungsoptionen werden nicht unterstützt)';
            }
            if ($err == 0x24) {
                $errMsg = 'NO_MORE_CONNECTIONS (Maximale Anzahl verfügbarer Verbindungen erreicht)';
            }
            $this->trace(true, 'CE < | CONNECT_RESPONSE / ErrCode: ' . dechex($err) . 'h / ErrMsg: ' . $errMsg . ' / Raw: ' . $this->bytesToHex($response));
            $this->reconnect = true;
            $this->procData[7]++;
        }
    }

    private function CONTROL_DISCONNECT_REQUEST_fromRouter($response)
    {
        $this->trace(true, 'CE < | DISCONNECT_REQUEST / Raw: ' . $this->bytesToHex($response));
        $this->reconnect = true;
        $this->procData[7]++;
    }

    private function CONTROL_DISCONNECT_RESPONSE($response)
    {
        $this->trace(true, 'CE < | DISCONNECT_RESPONSE / Raw: ' . $this->bytesToHex($response));
    }

    private function CONTROL_UNKNOWN_RESPONSE($response)
    {
        $this->trace(true, 'CE < | UNKNOWN / Unbekannter CRD: ' . dechex($this->word($response, 3)) . 'h / Raw: ' . $this->bytesToHex($response));
    }

    private function DATA_TUNNELING_REQUEST_SEND()
    {
        $this->dE_tunneling_request_data = false;
        $ss1 = sql_call("SELECT * FROM edomiLive.RAMknxWrite ORDER BY prio DESC,id ASC LIMIT 0,1");
        if ($n = sql_result($ss1)) {
            if ($n['mode'] == 0) {
                if ($gaData = getGADataFromID($n['gaid'], 1)) {
                    $this->send_TUNNELING_REQUEST($gaData['ga'], $n['prio'], $n['value'], chr(0x00), 0);
                }
            } else if ($n['mode'] == 1) {
                if ($gaData = getGADataFromID($n['gaid'], 1)) {
                    $data = $this->gaValueToRawData(0x40, $n['value'], $gaData['valuetyp']);
                    $this->send_TUNNELING_REQUEST($gaData['ga'], $n['prio'], $n['value'], $data, 1);
                }
            } else if ($n['mode'] == 2) {
                if ($gaData = getGADataFromID($n['gaid'], 1)) {
                    $data = $this->gaValueToRawData(0x80, $n['value'], $gaData['valuetyp']);
                    $this->send_TUNNELING_REQUEST($gaData['ga'], $n['prio'], $n['value'], $data, 2);
                }
            }
            sql_call("DELETE FROM edomiLive.RAMknxWrite WHERE (id=" . $n['id'] . ")");
        }
        sql_close($ss1);
        $this->statistic_queueSize();
    }

    private function DATA_TUNNELING_REQUEST_RECEIVE($response)
    {
        $ACK = false;
        $INC = false;
        if ($this->byte($response, 8) == $this->cE_channelId) {
            $receiveRtg = $this->byte($response, 9);
            $receiveMsgTyp = $this->byte($response, 11);
            $receivePA = $this->rawToPA($this->chars($response, 15, 2));
            $receiveGA = $this->rawToGA($this->chars($response, 17, 2));
            $receiveSize = $this->byte($response, 19);
            $receiveRawData = $this->chars($response, 21, $receiveSize);
            if (($this->byte($response, 20) & 0x03) || (($this->byte($response, 21) & 0xC0) == 0xC0)) {
                $this->trace(true, 'DE < | TUNNELING_REQUEST / ErrMsg: Unbekannter Fehler / Raw: ' . $this->bytesToHex($response));
                $this->procData[6]++;
            } else {
                if ($receiveRtg == $this->dE_tunneling_request_seqCounter_receive) {
                    $ACK = true;
                    $INC = true;
                    if ($receiveMsgTyp == 0x2E) {
                        if (($this->byte($response, 21) & 0xC0) == 0x00) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid,local) VALUES (0,1,'" . $receivePA . "'," . $gaData['id'] . ",1)");
                                writeToMonLog(1, 0, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], '(Read)', true);
                                $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.con / Typ: Read / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' / Raw: ' . $this->bytesToHex($response));
                            } else {
                                writeToMonLog(1, 0, 1, 0, $receivePA, $receiveGA, '', '(Read)', true);
                            }
                            $this->procData[4]++;
                        }
                        if (($this->byte($response, 21) & 0xC0) == 0x40) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                $gaValueRaw = $this->rawDataToGaValue($receiveRawData, $gaData['valuetyp']);
                                $gaValue = verifyGaValue($gaValueRaw, $gaData['valuetyp'], $gaData['vmin'], $gaData['vmax'], $gaData['vstep'], $gaData['vlist'], $gaData['vcsv']);
                                if ($gaValue !== false) {
                                    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid,value,local) VALUES (1,1,'" . $receivePA . "'," . $gaData['id'] . ",'" . sql_encodeValue($gaValue) . "',1)");
                                    writeToMonLog(1, 1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValue, true);
                                    $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.con / Typ: Response / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValue . ' / Raw: ' . $this->bytesToHex($response));
                                } else {
                                    sql_call("UPDATE edomiLive.RAMko SET visuts='" . getTimestampVisu() . "' WHERE (id=" . $gaData['id'] . ")");
                                    writeToMonLog(1, -1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValueRaw, true);
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.con / Typ: Response / ErrMsg: Ungültiger Wert / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValueRaw . ' / Raw: ' . $this->bytesToHex($response));
                                }
                            } else {
                                writeToMonLog(1, 1, 1, 0, $receivePA, $receiveGA, '', 'HEX_' . $this->bytesToHex($receiveRawData));
                            }
                            $this->procData[4]++;
                        }
                        if (($this->byte($response, 21) & 0xC0) == 0x80) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                $gaValueRaw = $this->rawDataToGaValue($receiveRawData, $gaData['valuetyp']);
                                $gaValue = verifyGaValue($gaValueRaw, $gaData['valuetyp'], $gaData['vmin'], $gaData['vmax'], $gaData['vstep'], $gaData['vlist'], $gaData['vcsv']);
                                if ($gaValue !== false) {
                                    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid,value,local) VALUES (2,1,'" . $receivePA . "'," . $gaData['id'] . ",'" . sql_encodeValue($gaValue) . "',1)");
                                    writeToMonLog(1, 2, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValue, true);
                                    $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.con / Typ: Write / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValue . ' / Raw: ' . $this->bytesToHex($response));
                                } else {
                                    sql_call("UPDATE edomiLive.RAMko SET visuts='" . getTimestampVisu() . "' WHERE (id=" . $gaData['id'] . ")");
                                    writeToMonLog(1, -1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValueRaw, true);
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.con / Typ: Write / ErrMsg: Ungültiger Wert / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValueRaw . ' / Raw: ' . $this->bytesToHex($response));
                                }
                            } else {
                                writeToMonLog(1, 2, 1, 0, $receivePA, $receiveGA, '', 'HEX_' . $this->bytesToHex($receiveRawData));
                            }
                            $this->procData[4]++;
                        }
                    } else if ($receiveMsgTyp == 0x29) {
                        if (($this->byte($response, 21) & 0xC0) == 0x00) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid) VALUES (0,1,'" . $receivePA . "'," . $gaData['id'] . ")");
                                writeToMonLog(1, 0, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], '(Read)');
                                $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Read / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' / Raw: ' . $this->bytesToHex($response));
                            } else {
                                writeToMonLog(1, 0, 1, 0, $receivePA, $receiveGA, '', '(Read)');
                                if (global_knxUnknownGA & 1) {
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Read / ErrMsg: Unbekannte GA / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' / Raw: ' . $this->bytesToHex($response));
                                    $this->procData[2]++;
                                }
                            }
                            $this->procData[5]++;
                        }
                        if (($this->byte($response, 21) & 0xC0) == 0x40) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                $gaValueRaw = $this->rawDataToGaValue($receiveRawData, $gaData['valuetyp']);
                                $gaValue = verifyGaValue($gaValueRaw, $gaData['valuetyp'], $gaData['vmin'], $gaData['vmax'], $gaData['vstep'], $gaData['vlist'], $gaData['vcsv']);
                                if ($gaValue !== false) {
                                    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid,value) VALUES (1,1,'" . $receivePA . "'," . $gaData['id'] . ",'" . sql_encodeValue($gaValue) . "')");
                                    writeToMonLog(1, 1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValue);
                                    $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Response / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValue . ' / Raw: ' . $this->bytesToHex($response));
                                } else {
                                    sql_call("UPDATE edomiLive.RAMko SET visuts='" . getTimestampVisu() . "' WHERE (id=" . $gaData['id'] . ")");
                                    writeToMonLog(1, -1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValueRaw);
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Response / ErrMsg: Ungültiger Wert / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValueRaw . ' / Raw: ' . $this->bytesToHex($response));
                                }
                            } else {
                                writeToMonLog(1, 1, 1, 0, $receivePA, $receiveGA, '', 'HEX_' . $this->bytesToHex($receiveRawData));
                                if (global_knxUnknownGA & 1) {
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Response / ErrMsg: Unbekannte GA / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' / Raw: ' . $this->bytesToHex($response));
                                    $this->procData[2]++;
                                }
                            }
                            $this->procData[5]++;
                        }
                        if (($this->byte($response, 21) & 0xC0) == 0x80) {
                            if ($gaData = getGADataFromGA($receiveGA, 1)) {
                                $gaValueRaw = $this->rawDataToGaValue($receiveRawData, $gaData['valuetyp']);
                                $gaValue = verifyGaValue($gaValueRaw, $gaData['valuetyp'], $gaData['vmin'], $gaData['vmax'], $gaData['vstep'], $gaData['vlist'], $gaData['vcsv']);
                                if ($gaValue !== false) {
                                    sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,pa,gaid,value) VALUES (2,1,'" . $receivePA . "'," . $gaData['id'] . ",'" . sql_encodeValue($gaValue) . "')");
                                    writeToMonLog(1, 2, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValue);
                                    $this->trace(false, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Write / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValue . ' / Raw: ' . $this->bytesToHex($response));
                                } else {
                                    sql_call("UPDATE edomiLive.RAMko SET visuts='" . getTimestampVisu() . "' WHERE (id=" . $gaData['id'] . ")");
                                    writeToMonLog(1, -1, 1, $gaData['id'], $receivePA, $receiveGA, $gaData['name'], $gaValueRaw);
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Write / ErrMsg: Ungültiger Wert / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' = ' . $gaValueRaw . ' / Raw: ' . $this->bytesToHex($response));
                                }
                            } else {
                                writeToMonLog(1, 2, 1, 0, $receivePA, $receiveGA, '', 'HEX_' . $this->bytesToHex($receiveRawData));
                                if (global_knxUnknownGA & 1) {
                                    $this->trace(true, 'DE < | TUNNELING_REQUEST:L_Data.ind / Typ: Write / ErrMsg: Unbekannte GA / SeqCounter: ' . $receiveRtg . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / PA: ' . $receivePA . ' / GA: ' . $receiveGA . ' / Raw: ' . $this->bytesToHex($response));
                                    $this->procData[2]++;
                                }
                            }
                            $this->procData[5]++;
                        }
                    } else {
                        $this->trace(true, 'DE < | TUNNELING_REQUEST / ErrMsg: Unbekannter MessageCode: ' . dechex($receiveMsgTyp) . 'h / Raw: ' . $this->bytesToHex($response));
                    }
                } else if ($receiveRtg == ($this->dE_tunneling_request_seqCounter_receive - 1)) {
                    $ACK = true;
                    $INC = false;
                    if (global_knxLogSeqErr) {
                        $this->trace(true, 'DE < | TUNNELING_REQUEST / Hinweis: SequenceCounter abweichend (ACK): Ist-Wert=' . $receiveRtg . ', Soll-Wert=' . $this->dE_tunneling_request_seqCounter_receive . ' / Raw: ' . $this->bytesToHex($response));
                        $this->procData[6]++;
                    }
                } else {
                    $ACK = false;
                    $INC = false;
                    if (global_knxLogSeqErr) {
                        $this->trace(true, 'DE < | TUNNELING_REQUEST / Hinweis: SequenceCounter abweichend (noACK): Ist-Wert=' . $receiveRtg . ', Soll-Wert=' . $this->dE_tunneling_request_seqCounter_receive . ' / Raw: ' . $this->bytesToHex($response));
                        $this->procData[6]++;
                    }
                    if (global_knxReconnectOnSeqErr) {
                        $this->reconnect = true;
                        $this->procData[7]++;
                    }
                }
            }
        } else {
            $this->trace(true, 'DE < | TUNNELING_REQUEST / ErrMsg: ChannelId abweichend: Ist-Wert=' . $this->byte($response, 8) . ', Soll-Wert=' . $this->cE_channelId . ' / Raw: ' . $this->bytesToHex($response));
            $this->procData[6]++;
        }
        if ($ACK) {
            $this->send_TUNNELING_ACK($receiveRtg);
            if ($INC) {
                $this->dE_tunneling_request_seqCounter_receive = ($this->dE_tunneling_request_seqCounter_receive + 1) & 0xff;
            }
        }
    }

    private function DATA_TUNNELING_ACK($response)
    {
        $err = $this->byte($response, 10);
        if ($this->byte($response, 8) == $this->cE_channelId && $err == 0x00) {
            $this->trace(false, 'DE < | TUNNELING_ACK / SeqCounter: ' . $this->byte($response, 9) . ' (' . $this->dE_tunneling_request_seqCounter_send . ') / Raw: ' . $this->bytesToHex($response));
            $this->dE_tunneling_ack = true;
            $this->dE_tunneling_request_repeat = false;
            $this->dE_tunneling_request_seqCounter_send = ($this->dE_tunneling_request_seqCounter_send + 1) & 0xff;
            $this->procData[3]++;
        } else {
            $errMsg = '(unbekannt)';
            if ($err == 0x04) {
                $errMsg = 'E_SEQUENCE_NUMBER (The received sequence number is out of order)';
            }
            if (!$this->dE_tunneling_request_repeat) {
                $this->trace(true, 'DE < | TUNNELING_ACK / ErrCode: ' . dechex($err) . 'h / ErrMsg: ' . $errMsg . ' / SeqCounter: ' . $this->byte($response, 9) . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / Wiederholen / Raw: ' . $this->bytesToHex($response));
                $this->dE_tunneling_request_repeat = true;
            } else {
                $this->trace(true, 'DE < | TUNNELING_ACK / ErrCode: ' . dechex($err) . 'h / ErrMsg: ' . $errMsg . ' / SeqCounter: ' . $this->byte($response, 9) . ' (' . $this->dE_tunneling_request_seqCounter_receive . ') / Verwerfen / Raw: ' . $this->bytesToHex($response));
                $this->dE_tunneling_request_repeat = false;
                $this->dE_tunneling_ack = true;
            }
            $this->procData[6]++;
        }
    }

    private function DATA_UNKNOWN($response)
    {
        $this->trace(true, 'DE < | UNKNOWN / Unbekannter CRD: ' . dechex($this->word($response, 3)) . 'h / Raw: ' . $this->bytesToHex($response));
    }

    private function send_DESCRIPTION_REQUEST()
    {
        $cmd = '0610' . '0203' . '000E';
        $cmd .= '0801';
        $cmd .= $this->bytesToHex($this->cE_serverIpPort);
        $this->trace(false, 'CE > | DESCRIPTION_REQUEST an ' . $this->cE_routerIp . ':' . $this->cE_routerPort . ' / Raw: ' . $cmd);
        $this->cE_sendCmd($this->hexToBytes($cmd));
    }

    private function send_CONNECT_REQUEST()
    {
        $cmd = '0610' . '0205' . '001A';
        $cmd .= '0801';
        $cmd .= $this->bytesToHex($this->cE_serverIpPort);
        $cmd .= '0801';
        $cmd .= $this->bytesToHex($this->dE_serverIpPort);
        $cmd .= '04040200';
        $this->trace(false, 'CE > | CONNECT_REQUEST an ' . $this->cE_routerIp . ':' . $this->cE_routerPort . ' / Raw: ' . $cmd);
        $this->cE_sendCmd($this->hexToBytes($cmd));
    }

    private function send_CONNECTIONSTATE_REQUEST()
    {
        $cmd = '0610' . '0207' . '0010';
        $cmd .= $this->bytesToHex(chr($this->cE_channelId & 0xff)) . '00';
        $cmd .= '0801';
        $cmd .= $this->bytesToHex($this->cE_serverIpPort);
        $this->trace(false, 'CE > | CONNECTIONSTATE_REQUEST / Raw: ' . $cmd);
        $this->cE_sendCmd($this->hexToBytes($cmd));
    }

    private function send_DISCONNECT_REQUEST()
    {
        $cmd = '0610' . '0209' . '0010';
        $cmd .= $this->bytesToHex(chr($this->cE_channelId & 0xff)) . '00';
        $cmd .= '0801';
        $cmd .= $this->bytesToHex($this->cE_serverIpPort);
        $this->trace(false, 'CE > | DISCONNECT_REQUEST / Raw: ' . $cmd);
        $this->cE_sendCmd($this->hexToBytes($cmd));
    }

    private function send_TUNNELING_REQUEST($ga, $prio, $gaValue, $data, $mode)
    {
        $sizeData = strlen($data) & 0xff;
        $size = 20 + $sizeData;
        $paRaw = $this->PAtoRaw($this->cE_tunnelPA);
        $gaRaw = $this->GAtoRaw($ga);
        if ($paRaw !== false && $gaRaw !== false && $sizeData > 0) {
            $cmd = '0610' . '0420' . $this->bytesToHex(chr($size >> 8) . chr($size & 0xff));
            $cmd .= '04' . $this->bytesToHex(chr($this->cE_channelId & 0xff)) . $this->bytesToHex(chr($this->dE_tunneling_request_seqCounter_send & 0xff)) . '00';
            $cmd .= '1100' . 'BCE0' . $this->bytesToHex($paRaw) . $this->bytesToHex($gaRaw);
            $cmd .= $this->bytesToHex(chr($sizeData)) . '00' . $this->bytesToHex($data);
            if ($mode == 0) {
                $this->trace(false, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Read / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' / Prio: ' . $prio . ' / Raw: ' . $cmd);
            } else if ($mode == 1) {
                $this->trace(false, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Response / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' = ' . $gaValue . ' / Prio: ' . $prio . ' / Raw: ' . $cmd);
            } else if ($mode == 2) {
                $this->trace(false, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Write / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' = ' . $gaValue . ' / Prio: ' . $prio . ' / Raw: ' . $cmd);
            }
            $this->dE_tunneling_request_data = $this->hexToBytes($cmd);
            $this->dE_sendCmd($this->dE_tunneling_request_data);
            $this->dE_tunneling_ack = false;
            $this->dE_tunneling_ack_timeout = getMicrotime();
        } else {
            if ($mode == 0) {
                $this->trace(true, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Read / ErrMsg: PA, GA oder Payload ungültig / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' / Prio: ' . $prio);
            } else if ($mode == 1) {
                $this->trace(true, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Response / ErrMsg: PA, GA oder Payload ungültig / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' = ' . $gaValue . ' / Prio: ' . $prio);
            } else if ($mode == 2) {
                $this->trace(true, 'DE > | TUNNELING_REQUEST:L_Data.req / Typ: Write / ErrMsg: PA, GA oder Payload ungültig / SeqCounter: ' . $this->dE_tunneling_request_seqCounter_send . ' / PA: ' . $this->cE_tunnelPA . ' / GA: ' . $ga . ' = ' . $gaValue . ' / Prio: ' . $prio);
            }
            $this->dE_tunneling_request_data = false;
        }
    }

    private function repeat_TUNNELING_REQUEST()
    {
        if ($this->dE_tunneling_request_data !== false) {
            $this->dE_sendCmd($this->dE_tunneling_request_data);
            $this->trace(false, 'DE > | TUNNELING_REQUEST:L_Data.req / REPEAT / Raw: ' . $this->bytesToHex($this->dE_tunneling_request_data));
            $this->dE_tunneling_ack = false;
            $this->dE_tunneling_ack_timeout = getMicrotime();
        }
        $this->dE_tunneling_request_data = false;
    }

    private function send_TUNNELING_ACK($seqCounter)
    {
        $cmd = '0610' . '0421' . '000A';
        $cmd .= '04' . $this->bytesToHex(chr($this->cE_channelId & 0xff)) . $this->bytesToHex(chr($seqCounter & 0xff)) . '00';
        $this->trace(false, 'DE > | TUNNELING_ACK / SeqCounter: ' . $seqCounter . ' / Raw: ' . $cmd);
        $this->dE_sendCmd($this->hexToBytes($cmd));
    }

    private function cE_sendCmd($cmd)
    {
        socket_sendto($this->cE_socket, $cmd, strlen($cmd), 0, $this->cE_routerIp, $this->cE_routerPort);
    }

    private function dE_sendCmd($cmd)
    {
        socket_sendto($this->dE_socket, $cmd, strlen($cmd), 0, $this->dE_routerIp, $this->dE_routerPort);
    }

    private function ipPortToRaw($ip, $port)
    {
        $n = explode('.', $ip);
        $n1 = (chr(intval($n[0])) . chr(intval($n[1])) . chr(intval($n[2])) . chr(intval($n[3])));
        $n2 = (chr($port >> 8) . chr($port & 255));
        if (strlen($n1 . $n2) == 6) {
            return ($n1 . $n2);
        }
        return false;
    }

    private function rawToPA($raw)
    {
        if (strlen($raw) == 2) {
            $i = intval($this->byte($raw, 1) * 256 + $this->byte($raw, 2));
            return (($i >> 12) & 0x0f) . '.' . (($i >> 8) & 0x0f) . '.' . (($i >> 0) & 0xff);
        }
        return false;
    }

    private function PAtoRaw($pa)
    {
        $n = explode('.', $pa);
        if (count($n) == 3) {
            $r = ((intval(trim($n[0])) & 0x0f) << 12) | ((intval(trim($n[1])) & 0x0f) << 8) | ((intval(trim($n[2])) & 0xff));
            return (chr($r >> 8) . chr($r & 255));
        }
        return false;
    }

    private function rawToGA($raw)
    {
        if (strlen($raw) == 2) {
            $i = intval($this->byte($raw, 1) * 256 + $this->byte($raw, 2));
            return (($i >> 11) & 0x1f) . '/' . (($i >> 8) & 0x07) . '/' . (($i >> 0) & 0xff);
        }
        return false;
    }

    private function GAtoRaw($ga)
    {
        $n = explode('/', $ga);
        if (count($n) == 3) {
            $r = ((intval(trim($n[0])) & 0x1f) << 11) | ((intval(trim($n[1])) & 0x07) << 8) | ((intval(trim($n[2])) & 0xff));
            return (chr($r >> 8) . chr($r & 255));
        }
        return false;
    }

    private function rawDataToGaValue($raw, $valueTyp)
    {
        if ($valueTyp == 1) {
            $b1 = $this->byte($raw, 1);
            return ($b1 & 0x3f);
        }
        if ($valueTyp == 2) {
            $b1 = $this->byte($raw, 1);
            return ($b1 & 0x3f);
        }
        if ($valueTyp == 3) {
            $b1 = $this->byte($raw, 1);
            return ($b1 & 0x3f);
        }
        if ($valueTyp == 4) {
            $b2 = $this->byte($raw, 2);
            return chr($b2 & 0xff);
        }
        if ($valueTyp == 5) {
            $b2 = $this->byte($raw, 2);
            return ($b2 & 0xff);
        }
        if ($valueTyp == 6) {
            $b2 = $this->byte($raw, 2);
            $S = $b2 & 0x80;
            if ($S != 0) {
                $b2 = $b2 ^ 0xFF;
            }
            $M = ($b2 & 0x7F);
            if ($S != 0) {
                $M = -$M - 1;
            }
            return intval($M);
        }
        if ($valueTyp == 7) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            return intval(($b2 << 8) + $b3);
        }
        if ($valueTyp == 8) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            $S = $b2 & 0x80;
            if ($S != 0) {
                $b2 = $b2 ^ 0xFF;
                $b3 = $b3 ^ 0xFF;
            }
            $M = (($b2 & 0x7F) << 8) + $b3;
            if ($S != 0) {
                $M = -$M - 1;
            }
            return intval($M);
        }
        if ($valueTyp == 9) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            $S = $b2 & 0x80;
            if ($S != 0) {
                $b2 = ($b2 ^ (1 << 0));
                $b2 = ($b2 ^ (1 << 1));
                $b2 = ($b2 ^ (1 << 2));
                $b3 = $b3 ^ 0xFF;
            }
            $E = ($b2 & 0x78) >> 3;
            $M = (($b2 & 0x07) << 8) | $b3;
            if ($S != 0) {
                $M = -$M - 1;
            }
            return round(floatval((1 << $E) * 0.01 * $M), 2);
        }
        if ($valueTyp == 10) {
            return strval((($this->byte($raw, 2) & 224) >> 5) . '.' . sprintf("%02d", ($this->byte($raw, 2) & 31)) . ':' . sprintf("%02d", $this->byte($raw, 3)) . ':' . sprintf("%02d", $this->byte($raw, 4)));
        }
        if ($valueTyp == 11) {
            return strval(($this->byte($raw, 4) + 2000) . '-' . sprintf("%02d", $this->byte($raw, 3)) . '-' . sprintf("%02d", $this->byte($raw, 2)));
        }
        if ($valueTyp == 12) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            $b4 = $this->byte($raw, 4);
            $b5 = $this->byte($raw, 5);
            return intval(($b2 << 24) + ($b3 << 16) + ($b4 << 8) + $b5);
        }
        if ($valueTyp == 13) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            $b4 = $this->byte($raw, 4);
            $b5 = $this->byte($raw, 5);
            $S = $b2 & 0x80;
            if ($S != 0) {
                $b2 = $b2 ^ 0xFF;
                $b3 = $b3 ^ 0xFF;
                $b4 = $b4 ^ 0xFF;
                $b5 = $b5 ^ 0xFF;
            }
            $M = (($b2 & 0x7F) << 24) + ($b3 << 16) + ($b4 << 8) + ($b5);
            if ($S != 0) {
                $M = -$M - 1;
            }
            return intval($M);
        }
        if ($valueTyp == 14) {
            $b2 = $this->byte($raw, 2);
            $b3 = $this->byte($raw, 3);
            $b4 = $this->byte($raw, 4);
            $b5 = $this->byte($raw, 5);
            $V = ($b2 << 24) + ($b3 << 16) + ($b4 << 8) + $b5;
            $r = unpack('f', pack('i', $V));
            return floatval($r[1]);
        }
        if ($valueTyp == 16) {
            return str_replace(chr(0), '', $this->chars($raw, 2, 14));
        }
        if ($valueTyp == 17) {
            $b2 = $this->byte($raw, 2);
            // Mask the last 6 bits
            // See https://www.knx.org/wAssets/docs/downloads/Certification/Interworking-Datapoint-types/03_07_02-Datapoint-Types-v02.02.01-AS.pdf
            // Section "3.18 Datapoint Type Scene Number"
            return ($b2 & 0x3f);
        }
        if ($valueTyp == 232) {
            $n = ($this->byte($raw, 2) * 256 * 256) + ($this->byte($raw, 3) * 256) + $this->byte($raw, 4);
            return sprintf("%06X", $n);
        }
        if ($valueTyp == 99999) {
            $b1 = $this->byte($raw, 1);
            $n = chr($b1 & 0x3f) . $this->chars($raw, 2, strlen($raw));
            return rtrim(chunk_split($this->bytesToHex($n), 2, ','), ',');
        }
        return NULL;
    }

    private function gaValueToRawData($sendTyp, $value, $valueTyp)
    {
        if ($valueTyp == 1) {
            if ($value == 0) {
                $r1 = 0 & 0x3f;
            } else {
                $r1 = 1 & 0x3f;
            }
            return chr(($r1 | $sendTyp) & 0xff);
        }
        if ($valueTyp == 2) {
            $r1 = $value & 0x3f;
            return chr(($r1 | $sendTyp) & 0xff);
        }
        if ($valueTyp == 3) {
            $r1 = $value & 0x3f;
            return chr(($r1 | $sendTyp) & 0xff);
        }
        if ($valueTyp == 4) {
            $r1 = ord($value) & 0xFF;
            return chr($sendTyp) . chr($r1 & 0xff);
        }
        if ($valueTyp == 5) {
            $r1 = intval($value & 0xFF);
            return chr($sendTyp) . chr($r1 & 0xff);
        }
        if ($valueTyp == 6) {
            if ($value >= 0) {
                $r1 = intval(abs($value) & 0x7F);
            } else {
                $r1 = intval((128 - abs($value)) & 0x7F);
                $r1 = $r1 | 0x80;
            }
            return chr($sendTyp) . chr($r1 & 0xff);
        }
        if ($valueTyp == 7) {
            $r1 = intval(($value >> 8) & 0xFF);
            $r2 = intval($value & 0xFF);
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff);
        }
        if ($valueTyp == 8) {
            if ($value >= 0) {
                $r1 = intval((abs($value) >> 8) & 0xFF);
                $r2 = intval(abs($value) & 0xFF);
                $r1 = $r1 & 0x7F;
            } else {
                $r1 = intval(((32768 - abs($value)) >> 8) & 0xFF);
                $r2 = intval((32768 - abs($value)) & 0xFF);
                $r1 = $r1 | 0x80;
            }
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff);
        }
        if ($valueTyp == 9) {
            $value = $value * 100;
            $raw = 0;
            $E = 0;
            if ($value < 0) {
                $raw = 0x08000;
                $value = -$value;
            }
            while ($value > 0x07ff) {
                $value = $value >> 1;
                $E++;
            }
            if ($raw != 0) {
                $value = -$value;
            }
            $raw = $raw | ($value & 0x7ff);
            $raw = $raw | (($E << 11) & 0x07800);
            $r1 = ($raw >> 8) & 255;
            $r2 = $raw & 255;
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff);
        }
        if ($valueTyp == 10) {
            $r1 = (intval(substr($value, 0, 1)) << 5) + intval(substr($value, 2, 2));
            $r2 = intval(substr($value, 5, 2));
            $r3 = intval(substr($value, 8, 2));
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff);
        }
        if ($valueTyp == 11) {
            $r1 = intval(substr($value, 8, 2));
            $r2 = intval(substr($value, 5, 2));
            $r3 = intval(substr($value, 0, 4)) - 2000;
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff);
        }
        if ($valueTyp == 12) {
            $r1 = intval((abs($value) >> 24) & 0xFF);
            $r2 = intval((abs($value) >> 16) & 0xFF);
            $r3 = intval((abs($value) >> 8) & 0xFF);
            $r4 = intval(abs($value) & 0xFF);
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff) . chr($r4 & 0xff);
        }
        if ($valueTyp == 13) {
            if ($value >= 0) {
                $r1 = intval((abs($value) >> 24) & 0xFF);
                $r2 = intval((abs($value) >> 16) & 0xFF);
                $r3 = intval((abs($value) >> 8) & 0xFF);
                $r4 = intval(abs($value) & 0xFF);
                $r1 = $r1 & 0x7F;
            } else {
                $r1 = intval(((2147483648 - abs($value)) >> 24) & 0xFF);
                $r2 = intval(((2147483648 - abs($value)) >> 16) & 0xFF);
                $r3 = intval(((2147483648 - abs($value)) >> 8) & 0xFF);
                $r4 = intval((2147483648 - abs($value)) & 0xFF);
                $r1 = $r1 | 0x80;
            }
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff) . chr($r4 & 0xff);
        }
        if ($valueTyp == 14) {
            $V = unpack('i', pack('f', $value));
            $r1 = intval($V[1] >> 24);
            $r2 = intval($V[1] >> 16);
            $r3 = intval($V[1] >> 8);
            $r4 = intval($V[1]);
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff) . chr($r4 & 0xff);
        }
        if ($valueTyp == 16) {
            $r1 = substr($value . str_repeat(chr(0), 14), 0, 14);
            return chr($sendTyp) . $r1;
        }
        if ($valueTyp == 17) {
            // Mask the last 6 bits
            // See https://www.knx.org/wAssets/docs/downloads/Certification/Interworking-Datapoint-types/03_07_02-Datapoint-Types-v02.02.01-AS.pdf
            // Section "3.18 Datapoint Type Scene Number"
            $r1 = intval($value & 0x3F) - 1;
            return chr($sendTyp) . chr($r1 & 0x3f);
        }
        if ($valueTyp == 232) {
            $r1 = hexdec(substr($value, 0, 2));
            $r2 = hexdec(substr($value, 2, 2));
            $r3 = hexdec(substr($value, 4, 2));
            return chr($sendTyp) . chr($r1 & 0xff) . chr($r2 & 0xff) . chr($r3 & 0xff);
        }
        if ($valueTyp == 99999) {
            $n = explode(',', rtrim($value, ','));
            $r1 = $n[0] & 0x3f;
            $r2 = '';
            for ($t = 1; $t < count($n); $t++) {
                $r2 .= chr(hexdec($n[$t]) & 0xff);
            }
            return chr(($r1 | $sendTyp) & 0xff) . $r2;
        }
        return NULL;
    }

    private function byte($n, $pos)
    {
        return intval(ord(substr($n, $pos - 1, 1)));
    }

    private function word($n, $pos)
    {
        return intval((ord(substr($n, $pos - 1, 1)) * 256) + ord(substr($n, $pos, 1)));
    }

    private function chars($n, $pos, $anz)
    {
        return substr($n, $pos - 1, $anz);
    }

    private function hexToBytes($n)
    {
        return pack('H*', $n);
    }

    private function bytesToHex($n)
    {
        $nn = unpack('H*', $n);
        return array_shift($nn);
    }

    private function statistic_telegramRate()
    {
        if (is_null($this->knxRate_timer) || getMicrotime() >= ($this->knxRate_timer + global_knxRateInterval)) {
            $this->procData[13] = ($this->procData[3] - $this->knxRate_refTx) / global_knxRateInterval;
            $this->procData[15] = ($this->procData[5] - $this->knxRate_refRx) / global_knxRateInterval;
            $this->knxRate_refTx = $this->procData[3];
            $this->knxRate_refRx = $this->procData[5];
            $this->knxRate_timer = getMicrotime();
        }
    }

    private function statistic_queueSize()
    {
        $this->procData[0] = sql_getCount('edomiLive.RAMknxWrite', '1=1');
        if ($this->procData[0] > $this->procData[10] || is_null($this->procData[10])) {
            $this->procData[10] = $this->procData[0];
        }
    }

    private function trace($error, $n)
    {
        if (global_logTraceLevelKnx == 4) {
            writeToCustomLog('PROC_KNX', (($error) ? 'ERROR' : 'OK'), $n);
        } else if ($error) {
            if (global_logTraceLevelKnx == 1 || global_logTraceLevelKnx == 3) {
                writeToLog(3, false, $n);
            }
            if (global_logTraceLevelKnx == 2 || global_logTraceLevelKnx == 3) {
                writeToCustomLog('PROC_KNX', 'ERROR', $n);
            }
        }
    }

    public function exitSelf()
    {
        writeToLog(3, sql_disconnect(), 'Datenbank: Verbindung schließen');
        writeToLog(3, true, 'Prozess KNX beendet');
        exit();
    }
} ?>
