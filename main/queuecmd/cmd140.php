<?
require(dirname(__FILE__) . "/../../www/shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_dbinit.php");
require(MAIN_PATH . "/main/include/php/config.php");
require(MAIN_PATH . "/main/include/php/base.php");
require(MAIN_PATH . "/main/include/php/incl_log.php");
require(MAIN_PATH . "/main/include/php/incl_ga.php");
set_time_limit(30);
sql_connect();
sendIpTelegram($argv[4]);
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function sendIpTelegram($ipId)
{
    $ss1 = sql_call("SELECT * FROM edomiLive.ip WHERE (id=" . $ipId . ")");
    if ($n = sql_result($ss1)) {
        if ($n['iptyp'] == 1) {
            if (!isEmpty($n['url']) && $n['httptimeout'] >= 1) {
                writeToMonLog(2, 1, null, null, null, null, 'HTTP-GET', 'db.ip.id=' . $ipId);
                if ($n['httperrlog'] == 0) {
                    restore_error_handler();
                    error_reporting(0);
                }
                $ctx = stream_context_create(array('http' => array('timeout' => $n['httptimeout'])));
                $r = file_get_contents(parseGAValues($n['url']), false, $ctx, 0, 10000);
                if ($r === false) {
                    if ($n['outgaid2'] > 0) {
                        writeGA($n['outgaid2'], 1);
                    }
                } else {
                    if ($n['outgaid'] > 0) {
                        writeGA($n['outgaid'], substr($r, 0, 10000));
                    }
                }
            }
        } else if ($n['iptyp'] == 2) {
            if (!isEmpty($n['url'])) {
                writeToMonLog(2, 1, null, null, null, null, 'SHELL', 'db.ip.id=' . $ipId);
                $r = trim(shell_exec($n['url']));
            }
        } else if ($n['iptyp'] == 3) {
            if (!isEmpty($n['url']) && !isEmpty($n['data'])) {
                writeToMonLog(2, 1, null, null, null, null, 'UDP', 'db.ip.id=' . $ipId);
                $ip = explode(':', $n['url']);
                if (count($ip) == 2 && $ipSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                    $ip[0] = parseGAValues($ip[0]);
                    $ip[1] = parseGAValues($ip[1]);
                    $data = explode("\n", $n['data']);
                    for ($t = 0; $t < count($data); $t++) {
                        $paket = parseGAValues($data[$t]);
                        if (!isEmpty($paket)) {
                            if ($n['udpraw'] == 0) {
                                socket_sendto($ipSocket, $paket, strlen($paket), 0, $ip[0], $ip[1]);
                            } else if ($n['udpraw'] == 1) {
                                $bytes = '';
                                $tmp = explode(',', rtrim($paket, ','));
                                for ($tt = 0; $tt < count($tmp); $tt++) {
                                    $bytes .= chr(hexdec($tmp[$tt]) & 0xff);
                                }
                                if (strlen($bytes) > 0) {
                                    socket_sendto($ipSocket, $bytes, strlen($bytes), 0, $ip[0], $ip[1]);
                                }
                            }
                            usleep(1000);
                        }
                    }
                    socket_close($ipSocket);
                }
            }
        }
    }
    sql_close($ss1);
}
?>