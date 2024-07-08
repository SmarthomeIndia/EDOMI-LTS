<?
/*
*/
?><? ?><? require("../shared/php/config.php");
require(MAIN_PATH . "/www/shared/php/base.php");
require(MAIN_PATH . "/www/shared/php/incl_http.php");
error_reporting(0);
set_time_limit(30);
sql_connect();
$gaId = preg_replace("/[^0-9]/", '', httpGetVar('koid'));
header("Content-Type: text/plain");
if (checkRemoteLoginPass(httpGetVar('login'), httpGetVar('pass'))) {
    if (isset($_GET['kovalue'])) {
        $gaValue = httpGetVar('kovalue');
        if (httpKoSet($gaId, $gaValue)) {
            echo 'OK;' . $gaId . ';' . $gaValue . ';';
        } else {
            echo 'ERROR;' . $gaId . ';FORBIDDEN;';
        }
    } else {
        $n = httpKoGet($gaId);
        if ($n !== false) {
            echo $n;
        } else {
            echo 'ERROR;' . $gaId . ';FORBIDDEN;';
        }
    }
} else {
    echo 'ERROR;LOGIN;';
}
sql_disconnect();
function checkRemoteLoginPass($login, $pass)
{
    $ss1 = sql_call("SELECT id FROM edomiAdmin.user WHERE (typ=10 AND login='" . sql_encodeValue($login) . "' AND pass='" . sql_encodeValue($pass) . "')");
    if ($n = sql_result($ss1)) {
        sql_call("UPDATE edomiAdmin.user SET actiondate=" . sql_getNow() . ",loginip='" . $_SERVER['REMOTE_ADDR'] . "' WHERE id=" . $n['id']);
        return true;
    }
    return false;
}

function httpKoSet($gaId, $gaValue)
{
    $ss1 = sql_call("SELECT id FROM edomiLive.httpKo WHERE (gaid=" . $gaId . ")");
    if ($n = sql_result($ss1)) {
        sql_call("INSERT INTO edomiLive.RAMknxRead (mode,gatyp,gaid,value) VALUES (4,0," . $gaId . ",'" . sql_encodeValue($gaValue) . "')");
        return true;
    }
    return false;
}

function httpKoGet($gaId)
{
    $ss1 = sql_call("SELECT id FROM edomiLive.httpKo WHERE (gaid=" . $gaId . ")");
    if ($n = sql_result($ss1)) {
        $ss2 = sql_call("SELECT value FROM edomiLive.RAMko WHERE (id=" . $gaId . ")");
        if ($nn = sql_result($ss2)) {
            return $nn['value'];
        }
    }
    return false;
} ?>
