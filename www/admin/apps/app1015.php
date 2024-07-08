<?
/*
*/
?><? ?><? require("../../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
require(MAIN_PATH . "/www/admin/include/php/config.php");
require(MAIN_PATH . "/www/admin/include/php/base.php");
sql_connect();
if (checkAdmin($sid)) {
    cmd($cmd);
}
function cmd($cmd)
{
    global $appId, $winId, $data, $dataArr, $phpdata, $phpdataArr, $sid;
    if ($cmd == 'irtransLearn') { ?>
        document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<b>Bitte eine Taste auf der Fernbedienung betätigen...</b><br>
        <div class="pbAnim" style="width:300px; height:3px; background:#505050; margin:5px; -webkit-animation-duration:<? echo global_irTimeout; ?>s;"></div>';
        ajax("irtransLearnStart","<? echo $appId; ?>","<? echo $winId; ?>","<? echo $data; ?>","<? ajaxValue($phpdata); ?>");
    <? }
    if ($cmd == 'irtransLearnStart') {
        if ($phpdataArr[8] == 0) {
            if ($phpdataArr[6] == 0 && $phpdataArr[7] == 0) {
                $hexcode = irLearn(1);
            }
            if ($phpdataArr[6] == 1 && $phpdataArr[7] == 0) {
                $hexcode = irLearn(2);
            }
            if ($phpdataArr[6] == 0 && $phpdataArr[7] == 1) {
                $hexcode = irLearn(3);
            }
            if ($phpdataArr[6] == 1 && $phpdataArr[7] == 1) {
                $hexcode = irLearn(4);
            }
        } else {
            if ($phpdataArr[7] == 0) {
                $hexcode = irLearn(11);
            }
            if ($phpdataArr[7] == 1) {
                $hexcode = irLearn(12);
            }
        }
        if ($hexcode !== false) { ?>
            document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<span
                style="background:#80e000; padding:5px;"><b>IR-Befehl wurde übernommen</b></span>';
            document.getElementById("<? echo $winId; ?>-fd4").value='<? echo $hexcode; ?>';
        <? } else { ?>
            document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<span
                style="color:#ffffff; background:#ff0000; padding:5px;"><b>Timeout oder Fehler</b></span>';
        <? }
    }
    if ($cmd == 'irtransSend' && !isEmpty(global_irIp)) {
        if (!isEmpty($phpdataArr[4])) {
            $ir = 'sndhex LB,H' . $phpdataArr[4];
            if ($ipSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                socket_sendto($ipSocket, $ir, strlen($ir), 0, global_irIp, global_irPort);
                socket_close($ipSocket);
                usleep(50 * 1000); ?>
                document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<span
                    style="background:#80e000; padding:5px;"><b>IR-Befehl wurde gesendet</b></span>';
            <? }
        }
    }
    if ($cmd == 'irtransReset' && !isEmpty(global_irIp)) {
        $ir = 'reset';
        if ($ipSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
            socket_sendto($ipSocket, $ir, strlen($ir), 0, global_irIp, global_irPort);
            socket_close($ipSocket); ?>
            document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<b>IRtrans wird neu gestartet...</b><br>
            <div class="pbAnim" style="width:300px; height:3px; background:#505050; margin:5px; -webkit-animation-duration:7s;"></div>';
            ajax("irtransReset2","<? echo $appId; ?>","<? echo $winId; ?>","<? echo $data; ?>","<? ajaxValue($phpdata); ?>");
        <? }
    }
    if ($cmd == 'irtransReset2') {
        sleep(7); ?>
        document.getElementById("<? echo $winId; ?>-irinfo").innerHTML='<span
            style="background:#80e000; padding:5px;"><b>IRtrans wurde neugestartet</b></span>';
    <? }
}

sql_disconnect();
function irLearn($mode)
{
    if (isEmpty(global_irIp)) {
        return false;
    }
    $irCode = '';
    if ($mode == 1) {
        $ir = 'learn M0,W' . global_irTimeout;
    }
    if ($mode == 2) {
        $ir = 'learn M0,X1,W' . global_irTimeout;
    }
    if ($mode == 3) {
        $ir = 'learn M1,W' . global_irTimeout;
    }
    if ($mode == 4) {
        $ir = 'learn M1,X1,W' . global_irTimeout;
    }
    if ($mode == 11) {
        $ir = hexToBytes('c80000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000007125210803000');
    }
    if ($mode == 12) {
        $ir = hexToBytes('c8000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000792d210803000');
    }
    if ($socketUDP = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        socket_sendto($socketUDP, $ir, strlen($ir), 0, global_irIp, global_irPort);
        socket_close($socketUDP);
    }
    if ($socketUDP = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        if (socket_bind($socketUDP, 0, global_irPort)) {
            socket_set_option($socketUDP, SOL_SOCKET, SO_RCVTIMEO, array("sec" => global_irTimeout, "usec" => 0));
            $bytes = socket_recv($socketUDP, $r, 1024, 0);
            if ($bytes !== false) {
                if ($mode < 11) {
                    if (strpos($r, 'LEARN ') !== false) {
                        $irCode = str_replace('LEARN ', '', $r);
                    }
                } else {
                    if (substr($r, 0, 1) == chr(242)) {
                        $irCode = strtoupper(bytesToHex(substr($r, 1, strlen($r) - 1)));
                    }
                }
            }
        }
    }
    $ir = hexToBytes('c8000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000500000000');
    if ($socketUDP = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        socket_sendto($socketUDP, $ir, strlen($ir), 0, global_irIp, global_irPort);
        socket_close($socketUDP);
    }
    if ($irCode != '') {
        return $irCode;
    } else {
        return false;
    }
}

function hexToBytes($n)
{
    return pack('H*', $n);
}

function bytesToHex($n)
{
    $nn = unpack('H*', $n);
    $r = array_shift($nn);
    return $r;
} ?>
