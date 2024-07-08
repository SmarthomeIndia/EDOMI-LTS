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
require(MAIN_PATH . "/main/include/php/incl_telnet.php");
require(MAIN_PATH . "/main/include/php/incl_fritzbox.php");
set_time_limit(120);
sql_connect();
phoneCallFromDB($argv[4], $argv[5]);
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function phoneCallFromDB($phoneId, $delay)
{
    if (global_phoneGatewayActive) {
        if ($phoneId > 0) {
            $ss1 = sql_call("SELECT * FROM edomiLive.phoneBook WHERE (id=" . $phoneId . ")");
            if ($n = sql_result($ss1)) {
                writeToMonLog(2, 4, null, null, null, null, 'Ausgehender Anruf', 'db.phoneBook.id=' . $phoneId . ' / Nr: (' . $n['phone1'] . ')' . $n['phone2']);
                if (!fritzbox_Call($n['phone1'] . $n['phone2'], $delay)) {
                    writeToLog(0, false, 'Telefon: Anruf gescheitert (db.phoneBook.id=' . $phoneId . ')');
                }
            }
            sql_close($ss1);
        } else {
            writeToMonLog(2, 4, null, null, null, null, 'Auflegen', '');
            if (!fritzbox_HangUp()) {
                writeToLog(0, false, 'Telefon: Auflegen gescheitert');
            }
        }
    }
} ?>

