<?
function writeToLog($procId, $condition, $logMsg, $errMsg = null, $cssClass = null)
{
    if (global_logSysEnabled > 0 || global_logErrEnabled > 0) {
        global $global_procNames;
        $ts = getTimestamp();
        if (!$pid = getmypid()) {
            $pid = '?';
        }
        if (isEmpty($logMsg)) {
            $logMsg = '-';
        }
        if ($procId >= 0) {
            $procName = $global_procNames[$procId];
        } else {
            $procName = '?';
        }
        if (global_logSysEnabled == 1 || global_logErrEnabled == 1) {
            $SEP = global_logTextSeparator;
            if (isEmpty($SEP)) {
                $SEP = chr(9);
            }
            if ($condition === false) {
                $txtStatus = $errMsg;
                if (isEmpty($txtStatus)) {
                    $txtStatus = 'ERROR';
                }
            } else {
                $txtStatus = 'Ok';
            }
            $txt = $ts[0] . $SEP . $ts[1] . $SEP . $procName . $SEP . $pid . $SEP . $logMsg . $SEP . $txtStatus;
        }
        if (global_logSysEnabled == 2 || global_logErrEnabled == 2) {
            if ($condition === false) {
                $html = '<tr class="sErr">';
                $txtStatus = $errMsg;
                if (isEmpty($txtStatus)) {
                    $txtStatus = 'ERROR';
                }
            } else {
                if (isEmpty($cssClass)) {
                    $html = '<tr>';
                } else {
                    $html = '<tr class="' . $cssClass . '">';
                }
                $txtStatus = 'Ok';
            }
            $html .= '<td>' . $ts[0] . '</td>';
            $html .= '<td>' . $ts[1] . '</td>';
            $html .= '<td>' . $procName . '</td>';
            $html .= '<td>' . $pid . '</td>';
            $html .= '<td>' . htmlspecialchars($logMsg) . '</td>';
            $html .= '<td>' . htmlspecialchars($txtStatus) . '</td>';
            $html .= '</tr>';
        }
        if (global_logSysEnabled > 0 && $procId >= 0) {
            if (global_logSysEnabled == 1) {
                writeToLogSave(0, 'SYSLOG_' . date("Y-m"), $txt, 1);
            }
            if (global_logSysEnabled == 2) {
                writeToLogSave(0, 'SYSLOG_' . date("Y-m"), $html, 2);
            }
        }
        if (global_logErrEnabled > 0 && $condition === false) {
            if (global_logErrEnabled == 1) {
                writeToLogSave(0, 'ERRLOG_' . date("Y-m"), $txt, 1);
            }
            if (global_logErrEnabled == 2) {
                writeToLogSave(0, 'ERRLOG_' . date("Y-m"), $html, 2);
            }
            $fh = fopen(MAIN_PATH . '/www/data/tmp/errorcount.txt', 'a');
            fwrite($fh, 'X');
            fclose($fh);
        }
    }
    if ($procId == 1) {
        if (strlen($logMsg) > 60) {
            echo "\33[2K" . date('d.m.Y H:i:s', strtotime($ts[0])) . ' ' . substr($logMsg, 0, 55) . '[...]' . "\n";
        } else {
            echo "\33[2K" . date('d.m.Y H:i:s', strtotime($ts[0])) . ' ' . substr($logMsg, 0, 60) . "\n";
        }
    }
    return $condition;
}

function writeToVisuLog($condition, $visu, $account, $logMsg, $cssClass = null)
{
    if (global_logVisuEnabled > 0) {
        $ts = getTimestamp();
        if (isEmpty($visu)) {
            $visu = '-';
        }
        if (isEmpty($account)) {
            $account = '-';
        }
        if (isEmpty($logMsg)) {
            $logMsg = '-';
        }
        if (global_logVisuEnabled == 1) {
            $SEP = global_logTextSeparator;
            if (isEmpty($SEP)) {
                $SEP = chr(9);
            }
            if ($condition === false) {
                $txtStatus = 'ERROR';
            } else {
                $txtStatus = 'Ok';
            }
            $txt = $ts[0] . $SEP . $ts[1] . $SEP . $visu . $SEP . $account . $SEP . $logMsg . $SEP . $txtStatus;
            writeToLogSave(3, 'VISULOG_' . date("Y-m"), $txt, 1);
        }
        if (global_logVisuEnabled == 2) {
            if ($condition === false) {
                $html = '<tr class="sErr">';
                $txtStatus = 'ERROR';
            } else {
                if (isEmpty($cssClass)) {
                    $html = '<tr>';
                } else {
                    $html = '<tr class="' . $cssClass . '">';
                }
                $txtStatus = 'Ok';
            }
            $html .= '<td>' . $ts[0] . '</td>';
            $html .= '<td>' . $ts[1] . '</td>';
            $html .= '<td>' . htmlspecialchars($visu) . '</td>';
            $html .= '<td>' . htmlspecialchars($account) . '</td>';
            $html .= '<td>' . htmlspecialchars($logMsg) . '</td>';
            $html .= '<td>' . htmlspecialchars($txtStatus) . '</td>';
            $html .= '</tr>';
            writeToLogSave(3, 'VISULOG_' . date("Y-m"), $html, 2);
        }
    }
}

function writeToLogicLog($logFn, $lbsId, $logMsg, $list, $raw, $cssClass = 0)
{
    if (global_logLogicEnabled > 0) {
        $ts = getTimestamp();
        if (!$pid = getmypid()) {
            $pid = '?';
        }
    }
    if (global_logLogicEnabled == 1) {
        $SEP = global_logTextSeparator;
        if (isEmpty($SEP)) {
            $SEP = chr(9);
        }
        if (is_array($list)) {
            $tmp = '';
            foreach ($list as $k => $v) {
                $v = str_replace("\n", '[LF]', $v);
                $v = str_replace("\r", '[CR]', $v);
                $tmp .= $v . "\r";
            }
        } else {
            $tmp = $list;
            $tmp = str_replace("\n", '[LF]', $tmp);
            $tmp = str_replace("\r", '[CR]', $tmp);
        }
        $logMsg = str_replace("\n", '[LF]', $logMsg);
        $logMsg = str_replace("\r", '[CR]', $logMsg);
        $raw = str_replace("\n", '[LF]', $raw);
        $raw = str_replace("\r", '[CR]', $raw);
        $txt = $ts[0] . $SEP . $ts[1] . $SEP . $pid . $SEP . $lbsId . (($cssClass == 3) ? ' [EXEC]' : '') . $SEP . $logMsg . $SEP . $tmp . $SEP . $raw;
        writeToLogSave(4, 'LOGICLOG_' . $logFn, $txt, 1, false);
    } else if (global_logLogicEnabled == 2) {
        if (is_array($list)) {
            $tmp = '';
            foreach ($list as $k => $v) {
                $tmp .= ((!isEmpty($v)) ? htmlspecialchars($v) : '&nbsp;') . '<br>';
            }
        } else {
            $tmp = ((!isEmpty($list)) ? htmlspecialchars($list) : '&nbsp;');
        }
        $html = '<tr' . (($cssClass != 0) ? ' class="c' . $cssClass . '"' : '') . '><td>' . $ts[0] . '</td><td>' . $ts[1] . '</td><td>' . $pid . '</td><td>' . ((!isEmpty($lbsId)) ? htmlspecialchars($lbsId) : '&nbsp;') . '</td><td>' . ((!isEmpty($logMsg)) ? htmlspecialchars($logMsg) : '&nbsp;') . '</td><td>' . $tmp . '</td><td>' . $raw . '</td></tr>';
        writeToLogSave(4, 'LOGICLOG_' . $logFn, $html, 2);
    }
}

function writeToCustomLog($logName, $logLevel, $logMsg)
{
    if (global_logCustomEnabled > 0) {
        $ts = getTimestamp();
        if (!$pid = getmypid()) {
            $pid = '?';
        }
        $logName = substr(preg_replace("/[^a-zA-Z0-9\ \-\_äöüÄÖÜß]/", '', $logName), 0, 100);
    }
    if (global_logCustomEnabled == 1) {
        if (!isEmpty($logName)) {
            $SEP = global_logTextSeparator;
            if (isEmpty($SEP)) {
                $SEP = chr(9);
            }
            $txt = $ts[0] . $SEP . $ts[1] . $SEP . $pid . $SEP . $logLevel . $SEP . $logMsg;
            writeToLogSave(1, 'CUSTOMLOG_' . $logName, $txt, 1);
        }
    } else if (global_logCustomEnabled == 2) {
        if (!isEmpty($logName)) {
            $html = '<tr><td>' . $ts[0] . '</td><td>' . $ts[1] . '</td><td>' . $pid . '</td><td>' . ((!isEmpty($logLevel)) ? htmlspecialchars($logLevel) : '&nbsp;') . '</td><td>' . ((!isEmpty($logMsg)) ? htmlspecialchars($logMsg) : '&nbsp;') . '</td></tr>';
            writeToLogSave(1, 'CUSTOMLOG_' . $logName, $html, 2);
        }
    }
}

function writeToMonLog($mode, $gaMode, $gaTyp, $gaId, $pa, $ga, $gaName, $gaValue, $serviceConfirm = false)
{
    if ($mode == 1 && $gaTyp == 1) {
        if ($gaId >= 0 && ((global_knxUnknownGA & 2) || $gaId != 0)) {
            $ts = getTimestamp();
            $tsid = getTimestampId();
            if ($serviceConfirm) {
                $tmpPa = 'EDOMI';
            } else {
                $tmpPa = $pa;
            }
            sql_call("UPDATE edomiLive.RAMlivemon SET ts='" . $tsid . "',datetime='" . $ts[0] . "',ms='" . $ts[1] . "',pa='" . sql_encodeValue($tmpPa) . "',gamode='" . $gaMode . "',gatyp='" . $gaTyp . "',gaid='" . $gaId . "',ga='" . sql_encodeValue($ga) . "',ganame='" . sql_encodeValue(substr($gaName, 0, 200)) . "',gavalue='" . sql_encodeValue(substr($gaValue, 0, 200)) . "' ORDER BY ts ASC LIMIT 1");
        }
    }
    if (global_logMonEnabled > 0 && ((global_knxUnknownGA & 2) || $gaId != 0)) {
        if (global_logMonForce > 0) {
            $sysKO6 = global_logMonForce;
        } else {
            if ($tmp = getGADataFromID(6, 2)) {
                $sysKO6 = $tmp['value'];
            } else {
                $sysKO6 = 0;
            }
        }
        if ($sysKO6 != 0) {
            $ts = getTimestamp();
            $cssClass = null;
            $info = '?';
            $infoTyp = 0;
            if ($mode == 0) {
                if ($gaMode == 1) {
                    $info = 'INIT';
                    $cssClass = 'sI';
                }
                if ($gaMode == 2) {
                    $info = 'START';
                    $cssClass = 'sS';
                }
                if ($gaMode == 3) {
                    $info = 'ENDE';
                    $cssClass = 'sX';
                }
            } else if ($mode == 1) {
                if ($gaTyp == 1) {
                    $infoTyp = 1;
                    if ($gaId == 0) {
                        $gaId = '';
                        $gaName = 'unknown GA';
                        $cssClass = 'sErr';
                    }
                    if ($gaMode == 0) {
                        $info = 'KNX REQUEST';
                    }
                    if ($gaMode == 1) {
                        $info = 'KNX RESPONSE';
                    }
                    if ($gaMode == 2) {
                        $info = 'KNX WRITE';
                    }
                    if ($gaMode == -1) {
                        $info = 'KNX WRITE';
                        $gaValue = 'invalid value: ' . $gaValue;
                        $cssClass = 'sErr';
                    }
                }
                if ($gaTyp == 2) {
                    $infoTyp = 2;
                    if ($gaMode == 2) {
                        $info = 'INTERN';
                    }
                    if ($gaMode == -1) {
                        $info = 'INTERN';
                        $gaValue = 'invalid value: ' . $gaValue;
                        $cssClass = 'sErr';
                    }
                }
            } else if ($mode == 2) {
                if ($gaMode == 1) {
                    $info = 'IP-TELEGRAMM';
                }
                if ($gaMode == 2) {
                    $info = 'IR-BEFEHL';
                }
                if ($gaMode == 3) {
                    $info = 'EMAIL';
                }
                if ($gaMode == 4) {
                    $info = 'TELEFON';
                }
            }
            if (isEmpty($ga)) {
                $ga = '-';
            }
            if (isEmpty($gaValue)) {
                $gaValue = '-';
            }
            if (isEmpty($gaName)) {
                $gaName = '-';
            }
            if ($infoTyp == 0 || ($infoTyp == 1 && ($sysKO6 & 1)) || ($infoTyp == 2 && ($sysKO6 & 2))) {
                if (global_logMonEnabled == 1) {
                    $SEP = global_logTextSeparator;
                    if (isEmpty($SEP)) {
                        $SEP = chr(9);
                    }
                    $txt = $ts[0] . $SEP . $ts[1] . $SEP . $info . $SEP;
                    if ($serviceConfirm) {
                        $txt .= 'EDOMI' . $SEP;
                    } else {
                        $txt .= $pa . $SEP;
                    }
                    $txt .= $ga . $SEP . $gaId . $SEP . $gaName . $SEP . $gaValue;
                    writeToLogSave(2, 'MONLOG_' . date("Y-m-d"), $txt, 1);
                    return true;
                } else if (global_logMonEnabled == 2) {
                    if (isEmpty($pa)) {
                        $pa = '&nbsp;';
                    }
                    if (isEmpty($gaId)) {
                        $gaId = '&nbsp;';
                    }
                    if (isEmpty($cssClass)) {
                        $html = '<tr>';
                    } else {
                        $html = '<tr class="' . $cssClass . '">';
                    }
                    $html .= '<td>' . $ts[0] . '</td>';
                    $html .= '<td>' . $ts[1] . '</td>';
                    $html .= '<td>' . htmlspecialchars($info) . '</td>';
                    if ($serviceConfirm) {
                        $html .= '<td>EDOMI</td>';
                    } else {
                        $html .= '<td>' . $pa . '</td>';
                    }
                    $html .= '<td>' . htmlspecialchars($ga) . '</td>';
                    $html .= '<td>' . $gaId . '</td>';
                    $html .= '<td>' . htmlspecialchars($gaName) . '</td>';
                    $html .= '<td>' . htmlspecialchars($gaValue) . '</td>';
                    $html .= '</tr>';
                    writeToLogSave(2, 'MONLOG_' . date("Y-m-d"), $html, 2);
                    return true;
                }
            }
        }
    }
    return false;
}

function writeToLogSave($logTyp, $logName, $entry, $format, $convertLfCr = true)
{
    $fileName = '';
    $header = '';
    if ($convertLfCr) {
        $entry = str_replace("\n", '[LF]', $entry);
        $entry = str_replace("\r", '[CR]', $entry);
    }
    if ($format == 1) {
        $fileName = MAIN_PATH . '/www/data/log/' . $logName . '.log';
        if (!file_exists($fileName)) {
            $ts = getTimestamp();
            if (!$pid = getmypid()) {
                $pid = '?';
            }
            $header = '{EDOMI,' . $logName . '.log,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
            if ($logTyp == 0) {
                $header .= '{Zeitstempel,ms,Prozess,PID,Meldung,Status}';
            } else if ($logTyp == 1) {
                $header .= '{Zeitstempel,ms,PID,LogLevel,Meldung}';
            } else if ($logTyp == 2) {
                $header .= '{Zeitstempel,ms,Typ,PA,GA/KO,KO-ID,Name,Wert}';
            } else if ($logTyp == 3) {
                $header .= '{Zeitstempel,ms,Visualisierung,Account,Meldung,Status}';
            } else if ($logTyp == 4) {
                $header .= '{Zeitstempel,ms,PID,LBSID,Befehl/Ereignis,Ergebnis,Funktion/Bemerkung}';
            }
        }
    } else if ($format == 2) {
        $fileName = MAIN_PATH . '/www/data/log/' . $logName . '.htm';
        if (!file_exists($fileName)) {
            $ts = getTimestamp();
            if (!$pid = getmypid()) {
                $pid = '?';
            }
            if ($logTyp == 0) {
                $meta = '{EDOMI,' . $logName . '.htm,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
                $header = '<html><head><style>body {background:#ffffff;} .log {background:#ffffff; font-family:EDOMIfontMono,Menlo,Courier,monospace; line-height:12px; font-size:10px; color:#393930; margin:0; padding:0;} .log td {padding:1px 5px 1px 5px; border-right:1px solid #a9a9a0;} ';
                $header .= '.sErr {color:#ff0000;}';
                $header .= '.sL {background:#c0c0ff;}';
                $header .= '.sP {background:#e0e000;}';
                $header .= '.sI {background:-webkit-repeating-linear-gradient(top,#e0e000,#e0e000 2px,#80e000 2px,#80e000 4px);}';
                $header .= '.sS {background:#80e000;}';
                $header .= '.sE {background:#ff8080;}';
                $header .= '.sX {background:#b0b0b0;}';
                $header .= '</style></head><body>';
                $header .= '<table class="log" border="0" cellspacing="0" cellpadding="0" style="border-left:1px solid #a9a9a0; border-bottom:1px solid #a9a9a0; white-space:nowrap;">';
                $header .= '<tr><td colspan="6" style="color:#ffffff; background:#393930; padding:5px;">' . $meta . '</td></tr>';
                $header .= '<tr style="background:#c9c9c0;"><td>Zeitstempel</td><td>ms</td><td>Prozess</td><td>PID</td><td>Meldung</td><td>Status</td></tr>';
            } else if ($logTyp == 1) {
                $meta = '{EDOMI,' . $logName . '.htm,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
                $header = '<html><head><style>body {background:#ffffff;} .log {background:#ffffff; font-family:EDOMIfontMono,Menlo,Courier,monospace; line-height:12px; font-size:10px; color:#393930; margin:0; padding:0;} .log td {padding:1px 5px 1px 5px; border-right:1px solid #a9a9a0;}';
                $header .= '</style></head><body>';
                $header .= '<table class="log" border="0" cellspacing="0" cellpadding="0" style="border-left:1px solid #a9a9a0; border-bottom:1px solid #a9a9a0; white-space:nowrap;">';
                $header .= '<tr><td colspan="5" style="color:#ffffff; background:#393930; padding:5px;">' . $meta . '</td></tr>';
                $header .= '<tr style="background:#c9c9c0;"><td>Zeitstempel</td><td>ms</td><td>PID</td><td>LogLevel</td><td>Meldung</td></tr>';
            } else if ($logTyp == 2) {
                $meta = '{EDOMI,' . $logName . '.htm,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
                $header = '<html><head><style>body {background:#ffffff;} .log {background:#ffffff; font-family:EDOMIfontMono,Menlo,Courier,monospace; line-height:12px; font-size:10px; color:#393930; margin:0; padding:0;} .log td {padding:1px 5px 1px 5px; border-right:1px solid #a9a9a0;}';
                $header .= '.sErr {color:#ffffff; background:#ff0000;}';
                $header .= '.sI {background:-webkit-repeating-linear-gradient(top,#e0e000,#e0e000 2px,#80e000 2px,#80e000 4px);}';
                $header .= '.sS {background:#80e000;}';
                $header .= '.sX {background:#b0b0b0;}';
                $header .= '</style></head><body>';
                $header .= '<table class="log" border="0" cellspacing="0" cellpadding="0" style="border-left:1px solid #a9a9a0; border-bottom:1px solid #a9a9a0; white-space:nowrap;">';
                $header .= '<tr><td colspan="8" style="color:#ffffff; background:#393930; padding:5px;">' . $meta . '</td></tr>';
                $header .= '<tr style="background:#c9c9c0;"><td>Zeitstempel</td><td>ms</td><td>Typ</td><td>PA</td><td>GA/KO</td><td>KO-ID</td><td>Name</td><td>Wert</td></tr>';
            } else if ($logTyp == 3) {
                $meta = '{EDOMI,' . $logName . '.htm,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
                $header = '<html><head><style>body {background:#ffffff;} .log {background:#ffffff; font-family:EDOMIfontMono,Menlo,Courier,monospace; line-height:12px; font-size:10px; color:#393930; margin:0; padding:0;} .log td {padding:1px 5px 1px 5px; border-right:1px solid #a9a9a0;} ';
                $header .= '.sErr {color:#ff0000;}';
                $header .= '.sL {background:#c0c0ff;}';
                $header .= '.sP {background:#e0e000;}';
                $header .= '.sI {background:-webkit-repeating-linear-gradient(top,#e0e000,#e0e000 2px,#80e000 2px,#80e000 4px);}';
                $header .= '.sS {background:#80e000;}';
                $header .= '.sE {background:#ff8080;}';
                $header .= '.sX {background:#b0b0b0;}';
                $header .= '</style></head><body>';
                $header .= '<table class="log" border="0" cellspacing="0" cellpadding="0" style="border-left:1px solid #a9a9a0; border-bottom:1px solid #a9a9a0; white-space:nowrap;">';
                $header .= '<tr><td colspan="6" style="color:#ffffff; background:#393930; padding:5px;">' . $meta . '</td></tr>';
                $header .= '<tr style="background:#c9c9c0;"><td>Zeitstempel</td><td>ms</td><td>Visualisierung</td><td>Account</td><td>Meldung</td><td>Status</td></tr>';
            } else if ($logTyp == 4) {
                $meta = '{EDOMI,' . $logName . '.htm,' . date('d.m.Y', strtotime($ts[0])) . ',' . date('H:i:s', strtotime($ts[0])) . ',' . $ts[1] . ',' . $pid . '}';
                $header = '<html><head><style>body {background:#ffffff;} .log {background:#ffffff; font-family:EDOMIfontMono,Menlo,Courier,monospace; line-height:12px; font-size:10px; color:#393930; margin:0; padding:0;} .log td {vertical-align:top; padding:1px 5px 1px 5px; border-right:1px solid #a9a9a0;} .log tr:nth-child(odd) {background: #f0f0f0;}';
                $header .= '.c-1 {background:#e0e000 !important;}';
                $header .= '.c-2 {background:#80e000 !important;}';
                $header .= '.c-3 {background:#b0b0b0 !important;}';
                $header .= '.c1 {color:#00a000;}';
                $header .= '.c2 {color:#a0a0a0;} .c2 td {border-bottom:1px solid #a0a0a0;}';
                $header .= '.c3 {color:#4040ff;}';
                $header .= '.c4 {color:#ff4040;}';
                $header .= '</style></head><body>';
                $header .= '<table class="log" border="0" cellspacing="0" cellpadding="0" style="border-left:1px solid #a9a9a0; border-bottom:1px solid #a9a9a0; white-space:nowrap;">';
                $header .= '<tr><td colspan="7" style="color:#ffffff; background:#393930; padding:5px;">' . $meta . '</td></tr>';
                $header .= '<tr style="background:#c9c9c0;"><td>Zeitstempel</td><td>ms</td><td>PID</td><td>LBSID</td><td>Befehl/Ereignis</td><td>Ergebnis</td><td>Funktion/Bemerkung</td></tr>';
            }
        }
    }
    if (!isEmpty($fileName)) {
        $fh = fopen($fileName, 'a');
        if (!isEmpty($header)) {
            fwrite($fh, $header . "\n");
        }
        fwrite($fh, $entry . "\n");
        fclose($fh);
    }
}
?>
