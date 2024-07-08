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
set_time_limit(30);
sql_connect();
sendIpTelegram($argv[4], $argv[5]);
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function sendIpTelegram($irId, $led)
{
    if (global_irGatewayActive && !isEmpty(global_irIp)) {
        $ss1 = sql_call("SELECT * FROM edomiLive.ir WHERE (id=" . $irId . ")");
        if ($n = sql_result($ss1)) {
            if (!isEmpty($n['data'])) {
                $ir = '';
                if ($led == 1) {
                    writeToMonLog(2, 2, null, null, null, null, 'Kanal 1', 'db.ir.id=' . $irId);
                    $ir = 'sndhex LI,H' . $n['data'];
                }
                if ($led == 2) {
                    writeToMonLog(2, 2, null, null, null, null, 'Kanal 2', 'db.ir.id=' . $irId);
                    $ir = 'sndhex LE,H' . $n['data'];
                }
                if ($led == 3) {
                    writeToMonLog(2, 2, null, null, null, null, 'Kanal 1+2', 'db.ir.id=' . $irId);
                    $ir = 'sndhex LB,H' . $n['data'];
                }
                if ($ir != '') {
                    if ($ipSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                        socket_sendto($ipSocket, $ir, strlen($ir), 0, global_irIp, global_irPort);
                        socket_close($ipSocket);
                        usleep(50 * 1000);
                    }
                }
            }
        }
        sql_close($ss1);
    }
} ?>

