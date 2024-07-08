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
require(MAIN_PATH . "/main/include/php/incl_mail.php");
set_time_limit(30);
sql_connect();
sendMailFromDB($argv[4]);
sql_call("DELETE FROM edomiLive.RAMcmdQueue WHERE (id=" . $argv[1] . " AND status=1)");
sql_disconnect();
function sendMailFromDB($mailId)
{
    if (global_emailGatewayActive) {
        $ss1 = sql_call("SELECT * FROM edomiLive.email WHERE (id=" . $mailId . ")");
        if ($n = sql_result($ss1)) {
            writeToMonLog(2, 3, null, null, null, null, 'Empfänger (leer=Defaultadresse): ' . $n['mailaddr'], 'db.email.id=' . $mailId);
            $tmp = date('d.m.Y H:i:s');
            $n['subject'] = str_replace('{DATETIME}', $tmp, $n['subject']);
            $n['body'] = str_replace('{DATETIME}', $tmp, $n['body']);
            sendMail($n['mailaddr'], parseGAValues($n['subject']), parseGAValues($n['body']));
        }
        sql_close($ss1);
    }
} ?>

